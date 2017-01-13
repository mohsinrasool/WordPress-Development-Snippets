<?php
/*
Plugin Name: Display URL dependant text
Description: This plugin display the provided text if another text (in "check") is available in the URL. Usage: [url_dependant_text check="checkout"]Thanks![/url_dependant_text]
Author: Mohsin Rasool
Version: 1.0
Author URI: http://meticulousolutions.com/
*/

function url_dependant_text_shortcode( $atts, $display ) {
	$atts = shortcode_atts( array(
		'check' => ''
	), $atts );


	if( $atts['check'] == '' || $display == '' )
		return '';

	global $wp;
	$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

	if(stripos($current_url, $atts['check']) !== false ){
		return do_shortcode( $display );
	}
	return '';
}
add_shortcode( 'url_dependant_text','url_dependant_text_shortcode' );
