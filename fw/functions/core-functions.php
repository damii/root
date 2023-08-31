<?php
/**
 * Core functions.
 *
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



if ( ! function_exists( 'azzu_add_theme_options' ) ) :
     /**
     * Set theme options path.
     *
     */
    function azzu_add_theme_options() {
            return array( 'fw/options/options.php' );
    }
endif;

if ( ! function_exists( 'azu_is_login_page' ) ) :
    /**
     * Check if current page is login page.
     *
     * @return boolean
     */
    function azu_is_login_page() {
            return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );
    }
endif;

if ( ! function_exists( 'azzu_return_empty_string' ) ) :

	/**
	 * Return empty string.
	 *
	 * @return string
	 */
	function azzu_return_empty_string() {
		return '';
	}

endif;

// azus: style
if ( ! function_exists('azus') ) :
    require_once( AZZU_UI_DIR . '/'.AZZU_DESIGN. '/style_class.php' );
    function azus(){
        return style_class::get_instance('style_class');
    }
endif; // azus

// azuf: function
if ( ! function_exists('azuf') ) :
    require_once( AZZU_UI_DIR . '/'.AZZU_DESIGN. '/azu_functions.php' );
    function azuf(){
        return azu_functions::get_instance('azu_functions');
    }
    azuf()->init();
endif; // azuf

// azuh: helpers
if ( ! function_exists('azuh') ) :
    require_once( AZZU_UI_DIR . '/'.AZZU_DESIGN. '/azu_helpers.php' );
    function azuh(){
        return azu_helpers::get_instance('azu_helpers');
    }
endif; // azuh

// azum: post metabox config
if ( ! function_exists('azum') ) :
    function azum(){
        return Azzu_Config::get_instance('Azzu_Config',AZZU_CLASSES_DIR.'/azzu_config.class.php');
    }
endif; // azum


// azut: template tags
if ( ! function_exists('azut') && !((is_admin() && $GLOBALS['pagenow']!='admin-ajax.php') || azu_is_login_page()) ) :
    $class_file_path = apply_filters( 'azut_file_path', AZZU_UI_DIR . '/'.AZZU_DESIGN. '/azu_tags.php');
    require_once( $class_file_path );
    function azut(){
        return azu_tags::get_instance('azu_tags');
    }
    azut()->init();
endif; // azut

// azum: azu love this post
if ( ! function_exists('azu_love_this') ) :
     global $azu_love_this;
     $azu_love_this = love_this::get_instance('love_this',AZZU_CLASSES_DIR.'/love_this.class.php');
    function azu_love_this($echo = '',$tooltip = true){
        global $azu_love_this;
        $return_value = $azu_love_this->send_love($tooltip);
        if ( $echo == 'echo' )
            echo $return_value;
        else 
            return $return_value;
    }
endif; // azu love this
 
 
 if ( ! function_exists( 'azzu_get_blank_image' ) ) :

	/**
	 * Get blank image.
	 *
	 */
	function azzu_get_blank_image() {
		return AZZU_THEME_URI . '/images/1px.gif';
	}

endif; // azzu_get_blank_image


/**
 * Description here.
 *
 */
function azu_stylesheet_get_image( $img_1 ) {
    if( !$img_1 || 'none' == $img_1 )
        return 'none';
    

    $output = azuf()->azu_get_of_uploaded_image( $img_1 );
    
    $output = sprintf( "url('%s')", esc_url($output) );
    return $output;
}

/**
 * Description here.
 *
 */
function azu_stylesheet_get_bg_position ( $y, $x ) {
    return sprintf( '%s %s !important;', $y, $x );
}

/**
 * Description here.
 *
 */
function azu_stylesheet_get_opacity( $opacity = 0 ) {
	$opacity = ($opacity > 0) ? $opacity/100 : 0;
	return $opacity;
}

/**
 * Description here.
 *
 */
function azu_stylesheet_color_hex2rgb( $_color, $raw = false ) {
    
    if( is_array($_color) ) {
        $rgb_array = array_map('intval', $_color);    
    }else {

        $color = str_replace( '#', '', trim($_color) );

        if ( count($color) < 6 ) {
            $color .= $color;
        }

        $rgb_array = sscanf($color, '%2x%2x%2x');     

        if( is_array($rgb_array) && count($rgb_array) == 3 ) {
            $rgb_array = array_map('absint', $rgb_array);
        }else {
            return '';
        }
    }

    if ( !$raw ) {
        return sprintf( 'rgb(%d,%d,%d)', $rgb_array[0], $rgb_array[1], $rgb_array[2] );
    }
    return $rgb_array;
}

/**
 * HEX to RGBA.
 *
 */
function azu_stylesheet_color_hex2rgba( $color, $opacity = 0 ) {

    if ( !$color ) return '';

    $rgb_array = azu_stylesheet_color_hex2rgb( $color, true );

    return sprintf( 'rgba(%d,%d,%d,%s)', $rgb_array[0], $rgb_array[1], $rgb_array[2], azu_stylesheet_get_opacity( $opacity ) );
}

/**
 *  RGBA to HEX.
 *
 */
function azu_stylesheet_color_rgb2hex( $color ) {
    $is_match = preg_match('/^rgba\(\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*(0|[1-9]\d?|1\d\d?|2[0-4]\d|25[0-5])\s*,\s*((0.[1-9])|[01])\s*\)$/', $color);
    if ( !$color || !$is_match ) return '';

    $color = str_replace(array('rgba(', ')', ' ','rgb('), '', strtolower($color));
    $rgb_arr = explode(',', $color);
    
    $hex = "";
    $hex.= str_pad(dechex($rgb_arr[0]), 2, "0", STR_PAD_LEFT);
    $hex.= str_pad(dechex($rgb_arr[1]), 2, "0", STR_PAD_LEFT);
    $hex.= str_pad(dechex($rgb_arr[2]), 2, "0", STR_PAD_LEFT);
 
    return $hex;
}


/**
 * Return web font properties array.
 *
 * @param string $font
 * @return object/bool Returns object{'font_name', 'bold', 'italic'} or false.
 */
function azu_stylesheet_make_web_font_object( $font, $defaults = array() ) {
    // defaults
    $weight = $style = 'normal';
    $family = AZZU_THEME_DEFAULT_FONT;

    if ( !empty($defaults) ) { extract((array)$defaults); }

    $clear = explode('&', $font);
    $clear = explode(':', $clear[0]);
    
    if ( isset($clear[1]) ) {
        $vars = explode('italic', $clear[1]);
        
        if ( isset($vars[1]) ) $style = 'italic';
        
        if ( '700' == $vars[0] || 'bold' == $vars[0] ) {
            $weight = 'bold';
        } else if( '400' == $vars[0] || 'normal' == $vars[0] ) {
            $weight = 'normal';
        } else if( $vars[0] ) {
            $weight = $vars[0];
        }   
    }

    if ( '' != $clear[0] ) {
        $family = $clear[0];
    }

    $font = new stdClass();
    $font->family = $family;
    $font->style = $style;
    $font->weight = $weight;

    return $font;
}

/**
 * Description here.
 *
 */
function azu_stylesheet_maybe_web_font( $font ) {
    $websafe_fonts = array_keys( azu_stylesheet_get_websafe_fonts() );
    return !in_array( $font, $websafe_fonts );
}

/**
 * Returns array( 'rgba', 'ie_color' ).
 *
 * @param string $color.
 * @param string $ie_color.
 * @param int $opacity.
 *
 * @return array.
 */
function azu_stylesheet_make_ie_compat_rgba( $color, $ie_color, $opacity ) {
    $return = array(
        'rgba' => azu_stylesheet_color_hex2rgba( $color, $opacity ),
        'ie_color' => $ie_color
    );

    if ( $opacity == 100 ) {
        $return['ie_color'] = $color;
    }

    return $return;
}

if ( ! function_exists( 'azu_stylesheet_get_websafe_fonts' ) ) :

    /**
     * Web Safe fonts.
     *
     * @return array.
     */
    function azu_stylesheet_get_websafe_fonts() {
        $fonts = array(
            'Arial'                         => 'Arial',
            'Arial Black'                   => 'Arial Black',
            'Comic Sans MS'                 => 'Comic Sans MS',
            'Courier New'                   => 'Courier New',
            'Georgia'                       => 'Georgia',
            'Impact Lucida Console'         => 'Impact Lucida Console',
            'Lucida Sans Unicode'           => 'Lucida Sans Unicode',
            'Marlett'                       => 'Marlett',
            'Minion Web'                    => 'Minion Web',
            'Times New Roman'               => 'Times New Roman',
            'Tahoma'                        => 'Tahoma',
            'Trebuchet MS'                  => 'Trebuchet MS',
            'Verdana'                       => 'Verdana',
            'Webdings'                      => 'Webdings'
        );
        return apply_filters( 'azu_stylesheet_get_websafe_fonts', $fonts );
    }

endif;


if ( ! function_exists( 'azzu_layerslider_set_properties' ) ) :
function azzu_layerslider_set_properties() {

	if(isset($_POST['posted_add']) && strstr($_SERVER['REQUEST_URI'], 'layerslider')) {

		if(!isset($_POST['layerslider-slides'])) {
			return;
		}

		$_POST['layerslider-slides']['properties']['bodyinclude'] = 'on';
	}
}
endif; // azzu_layerslider_set_properties

if ( ! function_exists( 'retina_detection_js' ) ) :
        function retina_detection_js() {
                ?><script type="text/javascript">
                            // Retina detection
                            var device_ratio = window.devicePixelRatio === undefined ? 1 : window.devicePixelRatio;
                            var device_height = window.screen.height === undefined ? 0 : window.screen.height;
                            var device_width = window.screen.width === undefined ? 0 : window.screen.width;
                            var c=new Date;c.setTime(c.getTime()+864E5);
                            document.cookie = 'devicePixelRatio='+device_ratio+';expires='+c.toGMTString()+';path=/';
                            document.cookie = 'deviceHeight='+device_height+';expires='+c.toGMTString()+';path=/';
                            document.cookie = 'deviceWidth='+device_width+';expires='+c.toGMTString()+';path=/';
                </script><?php
        }
endif; // retina_detection_js

if ( ! function_exists( 'azuint' ) ) :
function azuint( $maybeint ) {
    return  intval( $maybeint );
}
endif;


if ( ! function_exists( 'azu_check_custom_posttype' ) ) :
function azu_check_custom_posttype($posttype = ''){
    $re = false;
    if(function_exists('azzu_register_posttypes')){
        if(empty($posttype) || of_get_option('posttype-'.$posttype, 1 ))
            $re = true;
    }
    return $re;
}
endif;
