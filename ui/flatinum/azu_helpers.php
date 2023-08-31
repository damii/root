<?php
/**
 * @author   	Damii
 * @copyright	Copyright (c) 2014
 * @package  	Azu
 * @version  	0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists('azu_helpers') ) :
class azu_helpers extends AzuCoreHelpers {


    
/**
 * Override Register font for Theme.
 * 
 * @return string
 */
function azu_font_url() {
        $font_url = add_query_arg( 'family', urlencode( AZZU_THEME_DEFAULT_FONT.':400,700,400italic,700italic' ), "//fonts.googleapis.com/css" );
        return esc_url_raw($font_url);
}

}
endif; // style