<?php
/**
 * @package   Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Social Buttons */

function of_sanitize_social_buttons($input) {
	$social_buttons = (array)apply_filters('optionsframework_interface-social_buttons', array());

	$clear = array();

	foreach ( (array)$input as $v=>$button ) {
		if ( array_key_exists($v, $social_buttons) ) 
                {
			$clear[$v] = $button;
		}
	}

	return $clear;
}
add_filter( 'of_sanitize_social_buttons', 'of_sanitize_social_buttons' );



/* Without sanitize */

function of_sanitize_without_sanitize($input) {
	return $input;
}
add_filter( 'of_sanitize_without_sanitize', 'of_sanitize_without_sanitize' );

/**
 * Square sanitize.
 *
 */
function of_sanitize_square_size($input) {
	$defaults = array('width' => 0, 'height' => 0);
	if ( !is_array($input) ) {
		return $defaults;
	}

	$sanitized = array_intersect_key($input, $defaults);
	if ( empty($sanitized) ) {
		return $defaults;
	}
	return array_map('absint', $sanitized);
}
add_filter( 'of_sanitize_square_size', 'of_sanitize_square_size' );

/* custom fonts */

function of_sanitize_custom_fonts($input, $option) {
    if(empty($input))
            $input = array();
    else if(!is_array($input))
        $input = json_decode(wp_unslash($input), true);
    
    foreach ($input as $key => $item) {
            $filetype = wp_check_filetype( $item );
            if ( $filetype["ext"] ) {
                    //todo check in wp folder installation
                    $_url = parse_url( $item );
                    $scheme = isset($_url['scheme']) ? $_url['scheme'] : null;

                    $input[$key] = str_replace( site_url('', $scheme), '', $item );
            }
    }
    
    return $input;
}
add_filter( 'of_sanitize_custom_fonts', 'of_sanitize_custom_fonts', 10, 2 );


/* Listbox */

function of_sanitize_listbox($input, $option) {
	if(empty($input))
		$input = array();
    else if(!is_array($input))
        $input = json_decode(wp_unslash($input), true);
	return $input;
}
add_filter( 'of_sanitize_listbox', 'of_sanitize_listbox', 10, 2 );


/* Slider */

function of_sanitize_slider($input, $option) {

	if ( !is_numeric($input) && isset($option['std']) ) {
		$input = intval($option['std']);
	} else {
		$input = intval($input);
	}
	return $input;
}
add_filter( 'of_sanitize_slider', 'of_sanitize_slider', 10, 2 );

/* posts per page */

function of_sanitize_ppp($input, $option) {
	$input = of_sanitize_slider( $input, $option );
	if ( $input < -1 ) $input = -1;
	return $input;
}
add_filter( 'of_sanitize_ppp', 'of_sanitize_ppp', 10, 2 );


/* Sanitize url */
function of_sanitize_url($input) {

	return esc_attr( $input );
}
add_filter( 'of_sanitize_url', 'of_sanitize_url' );

/* Text */

add_filter( 'of_sanitize_text', 'sanitize_text_field' );

/* Password */

add_filter( 'of_sanitize_password', 'sanitize_text_field' );

/* Textarea */

function of_sanitize_textarea($input) {
	global $allowedposttags;

	$additional_tags = array( 'br' => array(), 'p' => array() );
	$allowed_tags = array_merge( $allowedposttags, $additional_tags );

	$output = str_replace(
		array( 'callto://' ),
		array( '%callto%' ),
		$input
	);

	add_filter( 'safe_style_css', 'of_add_safe_style_css' );
	$output = wp_kses( $output, $allowed_tags );
	remove_filter( 'safe_style_css', 'of_add_safe_style_css' );

	$output = str_replace(
		array( '%callto%' ),
		array( 'callto://' ),
		$output
	);

	return $output;
}

function of_add_safe_style_css( $allowed_attr = array() ) {

	$of_allowed_attr = array(
		'max-width'
	);

	return array_merge( $allowed_attr, $of_allowed_attr );
}

add_filter( 'of_sanitize_textarea', 'of_sanitize_textarea' );

/* Select */

add_filter( 'of_sanitize_select', 'of_sanitize_enum', 10, 2);

/* Web Fonts */

add_filter( 'of_sanitize_web_fonts', 'of_sanitize_enum', 10, 2);

/* Radio */

add_filter( 'of_sanitize_radio', 'of_sanitize_enum', 10, 2);

/* Images */

add_filter( 'of_sanitize_images', 'of_sanitize_enum', 10, 2);

/* Checkbox */

function of_sanitize_checkbox( $input ) {
	if ( $input ) {
		$output = '1';
	} else {
		$output = false;
	}
	return $output;
}
add_filter( 'of_sanitize_checkbox', 'of_sanitize_checkbox' );

/* Multicheck */

function of_sanitize_multicheck( $input, $option ) {
	$output = '';
	if ( is_array( $input ) ) {
		foreach( $option['options'] as $key => $value ) {
			$output[$key] = false;
		}
		foreach( $input as $key => $value ) {
			if ( array_key_exists( $key, $option['options'] ) && $value ) {
				$output[$key] = "1";
			}
		}
	}
	return $output;
}
add_filter( 'of_sanitize_multicheck', 'of_sanitize_multicheck', 10, 2 );

/* Color Picker */

add_filter( 'of_sanitize_color', 'of_sanitize_color', 10, 2 );


/* Uploader */

function of_sanitize_upload( $input, $option = array() ) {
	$output = '';

	if ( is_array( $input ) ) {
		$output = array();

		$val = array_key_exists('uri',$input) ? $input['uri'] : '';
		$id = array_key_exists('id',$input) ? absint($input['id']) : 0;

		if ( $val ) {
			$filetype = wp_check_filetype($val);
			if ( $filetype["ext"] ) {
				//todo check in wp folder installation

				$_url = parse_url( $val );

				$scheme = isset($_url['scheme']) ? $_url['scheme'] : null;
				$site_url = site_url('', $scheme);

				$url = explode( $site_url, $val );

				if ( ! empty( $_url['scheme'] ) && ! empty( $url[0] ) ) {
					$val = '';
				}else { 
					$val = str_replace( $site_url, '', $val );
				}
			}
		}
		$output['uri'] = $val;
		$output['id'] = $id;
	}else {

		$filetype = wp_check_filetype( $input );
		if ( $filetype["ext"] ) {
			//todo check in wp folder installation

			$_url = parse_url( $input );
			$scheme = isset($_url['scheme']) ? $_url['scheme'] : null;

			$output = str_replace( site_url('', $scheme), '', $input );
		}
	}

	return $output;
}
add_filter( 'of_sanitize_upload', 'of_sanitize_upload', 10, 2 );

/* Editor */

function of_sanitize_editor($input) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		$output = $input;
	}
	else {
		global $allowedtags;
		$output = wpautop(wp_kses( $input, $allowedtags));
	}
	return $output;
}
add_filter( 'of_sanitize_editor', 'of_sanitize_editor' );

/* Allowed Tags */

function of_sanitize_allowedtags($input) {
	global $allowedtags;
	$output = wpautop(wp_kses( $input, $allowedtags));
	return $output;
}

/* Allowed Post Tags */

function of_sanitize_allowedposttags($input) {
	global $allowedposttags;
	$output = wpautop(wp_kses( $input, $allowedposttags));
	return $output;
}

add_filter( 'of_sanitize_info', 'of_sanitize_allowedposttags' );

/**
 * Sanitize email
 */
function of_sanitize_email( $input ) {
	return sanitize_email( $input );
}
add_filter( 'of_sanitize_email', 'of_sanitize_email' );

/* Check that the key value sent is valid */

function of_sanitize_enum( $input, $option ) {
	$output = '';
	if ( array_key_exists( $input, $option['options'] ) ) {
		$output = $input;
	}
	return $output;
}

/* Background */

function of_sanitize_background( $input ) {
	$output = wp_parse_args( $input, array(
		'color' => '',
		'image'  => '',
		'repeat'  => 'repeat',
		'position' => 'top center',
		'attachment' => 'scroll'
	) );

	$output['color'] = apply_filters( 'of_sanitize_color', $input['color'] );
	$output['image'] = apply_filters( 'of_sanitize_upload', $input['image'] );
	$output['repeat'] = apply_filters( 'of_background_repeat', $input['repeat'] );
	$output['position'] = apply_filters( 'of_background_position', $input['position'] );
	$output['attachment'] = apply_filters( 'of_background_attachment', $input['attachment'] );

	return $output;
}
add_filter( 'of_sanitize_background', 'of_sanitize_background' );

function of_sanitize_background_repeat( $value ) {
	$recognized = of_recognized_background_repeat();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'of_default_background_repeat', current( $recognized ) );
}
add_filter( 'of_background_repeat', 'of_sanitize_background_repeat' );

function of_sanitize_background_position( $value ) {
	$recognized = of_recognized_background_position();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'of_default_background_position', current( $recognized ) );
}
add_filter( 'of_background_position', 'of_sanitize_background_position' );

function of_sanitize_background_attachment( $value ) {
	$recognized = of_recognized_background_attachment();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'of_default_background_attachment', current( $recognized ) );
}
add_filter( 'of_background_attachment', 'of_sanitize_background_attachment' );

/* Typography */

function of_sanitize_typography( $input, $option ) {

	$output = wp_parse_args( $input, array(
		'size'  => '',
		'face'  => '',
		'style' => '',
		'color' => ''
	) );

	if ( isset( $option['options']['faces'] ) && isset( $input['face'] ) ) {
		if ( !( array_key_exists( $input['face'], $option['options']['faces'] ) ) ) {
			$output['face'] = '';
		}
	}
	else {
		$output['face']  = apply_filters( 'of_font_face', $output['face'] );
	}

	$output['size']  = apply_filters( 'of_font_size', $output['size'] );
	$output['style'] = apply_filters( 'of_font_style', $output['style'] );
	$output['color'] = apply_filters( 'of_sanitize_color', $output['color'] );
	return $output;
}
add_filter( 'of_sanitize_typography', 'of_sanitize_typography', 10, 2 );

function of_sanitize_font_size( $value ) {
	$recognized = of_recognized_font_sizes();
	$value_check = preg_replace('/px/','', $value);
	if ( in_array( (int) $value_check, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'of_default_font_size', $recognized );
}
add_filter( 'of_font_size', 'of_sanitize_font_size' );


function of_sanitize_font_style( $value ) {
	$recognized = of_recognized_font_styles();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'of_default_font_style', current( $recognized ) );
}
add_filter( 'of_font_style', 'of_sanitize_font_style' );


function of_sanitize_font_face( $value ) {
	$recognized = of_recognized_font_faces();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'of_default_font_face', current( $recognized ) );
}
add_filter( 'of_font_face', 'of_sanitize_font_face' );

/**
 * Get recognized background repeat settings
 *
 * @return   array
 *
 */
function of_recognized_background_repeat() {
	$default = array(
		'no-repeat' => _x('no repeat', 'theme-options', 'azzu'.LANG_DN),
		'repeat-x'  => _x('repeat x', 'theme-options', 'azzu'.LANG_DN),
		'repeat-y'  => _x('repeat y', 'theme-options', 'azzu'.LANG_DN),
		'repeat'    => _x('repeat', 'theme-options', 'azzu'.LANG_DN),
		);
	return apply_filters( 'of_recognized_background_repeat', $default );
}

/**
 * Get recognized background vertical position settings
 *
 * @return   array
 *
 */
function of_recognized_background_vertical_position() {
	$default = array(
		'top' 		=> _x('top', 'theme-options', 'azzu'.LANG_DN),
		'bottom'	=> _x('bottom', 'theme-options', 'azzu'.LANG_DN),
		'center'	=> _x('center', 'theme-options', 'azzu'.LANG_DN),
		);
	return apply_filters( 'of_recognized_background_vertical_position', $default );
}

/**
 * Get recognized background horizontal position settings
 *
 * @return   array
 *
 */
function of_recognized_background_horizontal_position() {
	$default = array(
		'left'		=> _x('left', 'theme-options', 'azzu'.LANG_DN),
		'right'		=> _x('right', 'theme-options', 'azzu'.LANG_DN),
		'center'	=> _x('center', 'theme-options', 'azzu'.LANG_DN),
		);
	return apply_filters( 'of_recognized_background_horizontal_position', $default );
}

/**
 * Get recognized background positions
 *
 * @return   array
 *
 */
function of_recognized_background_position() {
	$default = array(
		'top left'      => _x('Top Left', 'theme-options', 'azzu'.LANG_DN),
		'top center'    => _x('Top Center', 'theme-options', 'azzu'.LANG_DN),
		'top right'     => _x('Top Right', 'theme-options', 'azzu'.LANG_DN),
		'center left'   => _x('Middle Left', 'theme-options', 'azzu'.LANG_DN),
		'center center' => _x('Middle Center', 'theme-options', 'azzu'.LANG_DN),
		'center right'  => _x('Middle Right', 'theme-options', 'azzu'.LANG_DN),
		'bottom left'   => _x('Bottom Left', 'theme-options', 'azzu'.LANG_DN),
		'bottom center' => _x('Bottom Center', 'theme-options', 'azzu'.LANG_DN),
		'bottom right'  => _x('Bottom Right', 'theme-options', 'azzu'.LANG_DN)
		);
	return apply_filters( 'of_recognized_background_position', $default );
}

/**
 * Get recognized background attachment
 *
 * @return   array
 *
 */
function of_recognized_background_attachment() {
	$default = array(
		'scroll' => _x('Scroll Normally', 'theme-options', 'azzu'.LANG_DN),
		'fixed'  => _x('Fixed in Place', 'theme-options', 'azzu'.LANG_DN)
		);
	return apply_filters( 'of_recognized_background_attachment', $default );
}

/**
 * Sanitize a color represented in hexidecimal notation.
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @param    string    The value that this function should return if it cannot be recognized as a color.
 * @return   string
 *
 */

function of_sanitize_hex( $hex, $default = '' ) {
	if ( of_validate_hex( $hex ) ) {
		return $hex;
	}
	return $default;
}

/**
 * Sanitize color.
 *
 */
function of_sanitize_color( $input, $option = array() ) {
	return of_sanitize_hex( $input, isset($option['std']) ? $option['std'] : '' );
}


/**
 * Get recognized font sizes.
 *
 * Returns an indexed array of all recognized font sizes.
 * Values are integers and represent a range of sizes from
 * smallest to largest.
 *
 * @return   array
 */

function of_recognized_font_sizes() {
	$sizes = range( 9, 71 );
	$sizes = apply_filters( 'of_recognized_font_sizes', $sizes );
	$sizes = array_map( 'absint', $sizes );
	return $sizes;
}

/**
 * Get recognized font faces.
 *
 * Returns an array of all recognized font faces.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
function of_recognized_font_faces() {
	$default = array(
		'arial'     => 'Arial',
		'verdana'   => 'Verdana, Geneva',
		'trebuchet' => 'Trebuchet',
		'georgia'   => 'Georgia',
		'times'     => 'Times New Roman',
		'tahoma'    => 'Tahoma, Geneva',
		'palatino'  => 'Palatino',
		'helvetica' => 'Helvetica*'
		);
	return apply_filters( 'of_recognized_font_faces', $default );
}

/**
 * Get recognized font styles.
 *
 * Returns an array of all recognized font styles.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 *
 */
function of_recognized_font_styles() {
	$default = array(
		'normal'      => _x( 'Normal', 'theme-options', 'azzu'.LANG_DN ),
		'italic'      => _x( 'Italic', 'theme-options', 'azzu'.LANG_DN ),
		'bold'        => _x( 'Bold', 'theme-options', 'azzu'.LANG_DN ),
		'bold italic' => _x( 'Bold Italic', 'theme-options', 'azzu'.LANG_DN )
		);
	return apply_filters( 'of_recognized_font_styles', $default );
}

/**
 * Is a given string a color formatted in hexidecimal notation?
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @return   bool
 *
 */

function of_validate_hex( $hex ) {
	$hex = trim( $hex );
	/* Strip recognized prefixes. */
	if ( 0 === strpos( $hex, '#' ) ) {
		$hex = substr( $hex, 1 );
	}
	elseif ( 0 === strpos( $hex, '%23' ) ) {
		$hex = substr( $hex, 3 );
	}
	/* Regex match. */
	if ( 0 === preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
		return false;
	}
	else {
		return true;
	}
}

/* Background image */

function of_sanitize_background_img( $input ) {
	$output = wp_parse_args( $input, array(
		'image'			=> '',
		'repeat'		=> 'repeat',
		'position_x'	=> 'center',
		'position_y'	=> 'center'
	) );

	$output['image'] = apply_filters( 'of_sanitize_upload', $output['image'] );
	$output['repeat'] = apply_filters( 'of_background_repeat', $output['repeat'] );
	$output['position_x'] = apply_filters( 'of_background_position_x', $output['position_x'] );
	$output['position_y'] = apply_filters( 'of_background_position_y', $output['position_y'] );

	return $output;
}
add_filter( 'of_sanitize_background_img', 'of_sanitize_background_img' );

function of_sanitize_background_position_x( $value ) {
	$recognized = of_recognized_background_horizontal_position();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'of_default_background_position_x', current( $recognized ) );
}
add_filter( 'of_background_position_x', 'of_sanitize_background_position_x' );

function of_sanitize_background_position_y( $value ) {
	$recognized = of_recognized_background_vertical_position();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'of_default_background_position_y', current( $recognized ) );
}
add_filter( 'of_background_position_y', 'of_sanitize_background_position_y' );

/**
 * Description here.
 *
 */
function of_sanitize_absint( $input = '' ) {
	return absint($input);
}
add_filter( 'of_sanitize_dimensions', 'of_sanitize_absint' );
add_filter( 'of_sanitize_pages_list', 'of_sanitize_absint' );

/**
 * Sanitize css width.
 *
 */
function of_sanitize_css_width( $input = '' ) {

	preg_match( '/(\d*)(px|%)?/', (string) $input, $matches );

	if ( array_key_exists(2, $matches) && in_array( $matches[2], array( 'px', '%' ) ) ) {
		$input = absint( $matches[1] ) . $matches[2];
	} else {
		$input = absint( $input ) . 'px';
	}

	return $input;
}
add_filter( 'of_sanitize_css_width', 'of_sanitize_css_width' );


/**
 * Sanitize js tag.
 *
 */
function of_sanitize_js( $input ) {
        $input = preg_replace('#<script(.*?)>(.*?)</script>#is', '$2', $input);
	return $input;
}
add_filter( 'of_sanitize_js', 'of_sanitize_js' );

