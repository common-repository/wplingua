<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Validate a wpLingua API key
 *
 * @param string $api_key
 * @return bool
 */
function wplng_is_valid_api_key_format( $api_key ) {

	$regex = '/^[a-zA-Z1-9]{42}$/s';

	if (
		empty( $api_key )
		|| ! is_string( $api_key )
		|| $api_key !== preg_quote( $api_key )
		|| false === preg_match( $regex, $api_key )
	) {
		return false;
	}

	return true;
}


/**
 * Get the wpLingua registered API key
 *
 * @return string
 */
function wplng_get_api_key() {

	$api_key = trim( get_option( 'wplng_api_key' ) );

	if ( ! wplng_is_valid_api_key_format( $api_key ) ) {
		$api_key = '';
	}

	return $api_key;
}


/**
 * Get wpLingua API key data
 * - Website language (id or 'all')
 * - Target language(s) (array)
 * - Enabled features
 *
 * @return array
 */
function wplng_get_api_data() {

	if ( empty( wplng_get_api_key() ) ) {
		return array();
	}

	$data_checked = array();
	$data         = get_transient( 'wplng_api_key_data' );
	$data         = json_decode( $data, true );

	/**
	 * Check the API key data
	 */

	if ( ! empty( $data['language_original'] )
		&& (
			wplng_is_valid_language_id( $data['language_original'] )
			|| 'all' === $data['language_original']
		)
		&& ! empty( $data['languages_target'] )
		&& wplng_is_valid_language_ids( $data['languages_target'] )
		&& isset( $data['features'] )
		&& is_array( $data['features'] )
		&& ! empty( $data['status'] )
	) {

		/**
		 * Sanitize languages target
		 */

		$languages_target = array();

		foreach ( $data['languages_target'] as $id ) {

			if ( ! wplng_is_valid_language_id( $id ) ) {
				continue;
			}

			$languages_target[] = sanitize_key( $id );
		}

		/**
		 * Sanitize features list
		 */

		$features = array();

		foreach ( $data['features'] as $key => $allow ) {

			if ( ! is_string( $key ) || ! is_bool( $allow ) ) {
				continue;
			}

			$key   = sanitize_key( $key );
			$allow = ( true === $allow );

			$features[ $key ] = $allow;
		}

		/**
		 * Sanitize status
		 */

		$status = 'FREE';

		if ( 'PREMIUM' === $data['status']
			|| 'VIP' === $data['status']
		) {
			$status = $data['status'];
		}

		/**
		 * Make the checked response
		 */

		$data_checked = array(
			'language_original' => sanitize_key( $data['language_original'] ),
			'languages_target'  => $languages_target,
			'features'          => $features,
			'status'            => $status,
		);

		/**
		 * Add expiration
		 */

		if ( ! empty( $data['expiration'] )
			&& is_string( $data['expiration'] )
		) {
			$data_checked['expiration'] = $data['expiration'];
		}
	} else {

		/**
		 * Get sanitized data from API call
		 */

		$data_checked = wplng_api_call_validate_api_key();

		if ( empty( $data_checked ) ) {
			return array();
		}

		set_transient(
			'wplng_api_key_data',
			wp_json_encode( $data_checked ),
			60 * 60 * 12
		);
	}

	return $data_checked;
}


/**
 * Get website language from wpLingua API data
 *
 * @return string Language ID, 'all' or ''
 */
function wplng_get_api_language_website() {

	$data = wplng_get_api_data();

	if (
		! empty( $data['language_original'] )
		&& (
			wplng_is_valid_language_id( $data['language_original'] )
			|| 'all' === $data['language_original']
		)
	) {
		return $data['language_original'];
	}

	return '';
}


/**
 * Get target languages from wpLingua API data
 *
 * @return mixed Language IDs array, 'all' or false
 */
function wplng_get_api_languages_target() {

	$data = wplng_get_api_data();

	if ( empty( $data['languages_target'] ) ) {
		return false;
	} elseif ( 'all' === $data['languages_target'] ) {
		return 'all';
	} elseif ( is_array( $data['languages_target'] ) ) {

		$all_languages        = wplng_get_languages_all();
		$languages_id_ordered = array();

		foreach ( $all_languages as $language ) {
			foreach ( $data['languages_target'] as $language_id ) {

				if (
					! wplng_is_valid_language_id( $language_id )
					|| empty( $language['id'] )
					|| $language['id'] !== $language_id
				) {
					continue;
				}

				$languages_id_ordered[] = $language_id;
				break;

			}
		}

		return $languages_id_ordered;
	}

	return false;
}


/**
 * Get enabled features from wpLingua API data
 * ('search', 'commercial')
 *
 * @return array
 */
function wplng_get_api_feature() {

	$data     = wplng_get_api_data();
	$all      = array( 'search', 'commercial' );
	$features = array();

	if ( ! empty( $data['features'] ) && is_array( $data['features'] ) ) {
		foreach ( $data['features'] as $feature_name => $feature_allow ) {
			if ( $feature_allow && in_array( $feature_name, $all ) ) {
				$features[] = $feature_name;
			}
		}
	}

	return $features;
}


/**
 * Return true if the feature is enable in wpLingua API key data
 *
 * @param string $feature_name
 * @return bool
 */
function wplng_api_feature_is_allow( $feature_name ) {
	return in_array( $feature_name, wplng_get_api_feature() );
}


/**
 * Clear wpLingua API key data if wpLingua key is changed
 *
 * @param string $old_value JSON
 * @param string $new_value JSON
 * @return void
 */
function wplng_on_update_option_wplng_api_key( $old_value, $new_value ) {

	delete_transient( 'wplng_api_key_data' );

	delete_option( 'wplng_website_language' );
	delete_option( 'wplng_website_flag' );

	wplng_clear_translations_cache();
	wplng_clear_slugs_cache();

}
