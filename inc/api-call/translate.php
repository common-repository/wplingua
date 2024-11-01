<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get data from wpLingua API : Translated texts
 *
 * Return an error message with error code
 * or an array of translated texts
 *
 * API terms : https://wplingua.com/terms/
 *
 * ---------------------------------------------------
 * Data sent :
 * ---------------------------------------------------
 * - request : 'translate'
 * - api_key : the API key of website
 * - version : API compatile version
 * - context : Sends the URL of the page calling
 * - source  : A language ID
 * - target  : A languages ID
 * - texts   : Array of untranslated texts of website
 *
 * ---------------------------------------------------
 * Data received if successful :
 * ---------------------------------------------------
 * - translations : Array of translated texts
 *
 * ---------------------------------------------------
 * Data received in case of failure
 * ---------------------------------------------------
 * - error   : true
 * - code    : An integer
 * - message : Error description
 *
 * @param array $texts
 * @param string $language_source_id
 * @param string $language_target_id
 * @return array
 */
function wplng_api_call_translate(
	$texts,
	$language_source_id = '',
	$language_target_id = ''
) {

	/**
	 * Get and check data
	 */

	// Ckeck and sanitize texts list

	if ( empty( $texts ) || ! is_array( $texts ) ) {
		return array();
	}

	foreach ( $texts as $key => $text ) {
		if ( ! is_string( $text ) ) {
			$texts[ $key ] = '';
		} else {
			$texts[ $key ] = esc_html( $text );
		}
	}

	// Get API key

	$api_key = wplng_get_api_key();

	if ( empty( $api_key ) ) {
		return $texts;
	}

	// Get and sanitize the API key

	if ( empty( $language_target_id ) ) {
		$language_target_id = wplng_get_language_current_id();
	} elseif ( ! wplng_is_valid_language_id( $language_target_id ) ) {
		return $texts;
	}

	if ( empty( $language_source_id ) ) {
		$language_source_id = wplng_get_language_website_id();
	} elseif ( ! wplng_is_valid_language_id( $language_source_id ) ) {
		return $texts;
	}

	/**
	 * Add dictionary tag
	 */

	$dictionary_entries = wplng_dictionary_get_entries();

	$texts_tagged = wplng_dictionary_add_tags(
		$texts,
		$dictionary_entries
	);

	/**
	 * Get texts list as JSON
	 */

	$json_texts = wp_json_encode( $texts_tagged );

	if ( empty( $json_texts ) ) {
		return $texts;
	}

	/**
	 * Make the context
	 */

	$context = 'UNKNOW';

	if ( defined( 'DOING_AJAX' )
		&& DOING_AJAX
		&& ! empty( $_SERVER['HTTP_REFERER'] )
	) {
		$context = $_SERVER['HTTP_REFERER'];
		$context = sanitize_url( $context );
	} elseif ( isset( $_SERVER['HTTPS'] )
		&& isset( $_SERVER['HTTP_HOST'] )
		&& isset( $_SERVER['REQUEST_URI'] )
	) {
		$context  = ( empty( $_SERVER['HTTPS'] ) ? 'http' : 'https' );
		$context .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$context  = sanitize_url( $context );
	}

	$context = apply_filters( 'wplng_api_call_translate_context', $context );

	/**
	 * Get the API call
	 */

	$body = array(
		'request' => 'translate',
		'api_key' => $api_key,
		'version' => WPLNG_API_VERSION,
		'context' => $context,
		'source'  => $language_source_id,
		'target'  => $language_target_id,
		'texts'   => $json_texts,
	);

	$args = array(
		'method'    => 'POST',
		'timeout'   => 99, // 1 min 29 s
		'sslverify' => WPLNG_API_SSLVERIFY,
		'body'      => $body,
	);

	$request = wp_remote_post(
		WPLNG_API_URL . '/app/',
		$args
	);

	/**
	 * Check if the API call worked
	 */

	if ( is_wp_error( $request )
		|| wp_remote_retrieve_response_code( $request ) != 200
	) {
		return $texts;
	}

	/**
	 * Check and sanitize the API response
	 */

	$response = json_decode( wp_remote_retrieve_body( $request ), true );

	if ( isset( $response['error'] )
		|| empty( $response['translations'] )
		|| ! is_array( $response['translations'] )
	) {
		// API returned an error or an unexpected response
		return $texts;
	}

	/**
	 * Here, API returned the list of translations
	 */

	// Replace dictionary tag

	$texts_untagged = wplng_dictionary_replace_tags(
		$response['translations'],
		$dictionary_entries,
		$language_target_id
	);

	// Check and sanitize each translation

	$translations = array();

	foreach ( $texts_untagged as $key => $translation ) {
		if ( is_string( $translation ) ) {
			$translations[] = wp_kses( $translation, array() );
		} elseif ( isset( $texts[ $key ] ) ) {
			$translations = $texts[ $key ];
		}
	}

	return $translations;
}
