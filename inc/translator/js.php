<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * wpLingua translate : Get translated JS
 *
 * @param string $js
 * @param array $translations
 * @return string
 */
function wplng_translate_js( $js, $args = array() ) {

	if ( empty( trim( $js ) ) ) {
		return $js;
	}

	$json = array();

	preg_match_all(
		'#(var\s|let\s|window\._)(.*)\s?=\s?(\{.*\});?#Ui',
		$js,
		$json
	);

	if ( ! empty( $json[2][0] ) && ! empty( $json[3][0] ) ) {

		$var_name = $json[2][0];
		$var_json = $json[3][0];

		wplng_args_setup( $args );

		$args['parents'] = array( $var_name );

		$json_translated = wplng_translate_json(
			$var_json,
			$args
		);

		if ( $var_json != $json_translated ) {
			$js = str_replace(
				$var_json,
				$json_translated,
				$js
			);
		}
	}

	return $js;
}
