<?php
/**
 * Options Framework
 *
 * @package   Options Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 *
 * @wordpress-plugin
 * Plugin Name: Options Framework
 * Plugin URI:  http://wptheming.com
 * Description: A framework for building theme options.
 * Version:     1.9.0
 * Author:      Devin Price
 * Author URI:  http://wptheming.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: optionsframework
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Basic plugin definitions */
define( 'OPTIONS_FRAMEWORK_URL', trailingslashit( AZZU_LIBRARY_URI . '/' . basename(dirname( __FILE__ )) ) );
define( 'OPTIONS_FRAMEWORK_DIR', trailingslashit( dirname( __FILE__ ) ) );

// Don't load if optionsframework_init is already defined
if (! function_exists( 'optionsframework_init' ) ) :
    
function optionsframework_init() {

	//  If user can't edit theme options, exit
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	// Loads the required Options Framework classes.
        require dirname( __FILE__ ) . '/includes/class-options-custom.php';
	require dirname( __FILE__ ) . '/includes/class-options-framework.php';
	require dirname( __FILE__ ) . '/includes/class-options-framework-admin.php';
	require dirname( __FILE__ ) . '/includes/class-options-interface.php';
	require dirname( __FILE__ ) . '/includes/class-options-media-uploader.php';
	require dirname( __FILE__ ) . '/includes/class-options-sanitization.php';
        
        define( 'OPTIONS_FRAMEWORK_VERSION', Options_Framework::VERSION );
        
	// Instantiate the options page.
	$options_framework_admin = new Options_Framework_Admin;
	$options_framework_admin->init();

	// Instantiate the media uploader class
	$options_framework_media_uploader = new Options_Framework_Media_Uploader;
	$options_framework_media_uploader->init();
        azuf()->azzu_customizer_refresh_options();
}

add_action( 'init', 'optionsframework_init', 20 );

endif;


/**
 * Description here.
 *
 */
function optionsframework_get_options() {
	$config_id = optionsframework_get_options_id();
	$config = get_option( 'optionsframework' );
	if ( !isset($config['knownoptions']) || !in_array($config_id, $config['knownoptions']) ) {
		return null;
	}

	return get_option( $config_id );
}

/**
 * Get options id.
 *
 */
function optionsframework_get_options_id() {
	return preg_replace("/\W/", "", strtolower(wp_get_theme()->Name) );
}

/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 * 
 */
function optionsframework_option_name() {

	// This gets the theme name from the stylesheet (lowercase and without spaces)

	$optionsframework_settings = get_option('optionsframework');
	$optionsframework_settings['id'] = optionsframework_get_options_id();
	update_option('optionsframework', $optionsframework_settings);
        return $optionsframework_settings;
}

/**
 * Helper function to return the theme option value.
 * If no value has been saved, it returns $default.
 * Needed because options are saved as serialized strings.
 *
 * Not in a class to support backwards compatibility in themes.
 */
if ( ! function_exists( 'of_get_option' ) ) :
function of_get_option( $name, $default = false ) {
        static $saved_options = null;

        if($name==null) {
            $saved_options = null;
            return $default;
        }

        if ( null === $saved_options ) {

                $saved_options = optionsframework_get_options();
                if ( null === $saved_options ) {
                        $default;
                }

                $saved_options = apply_filters( 'azu_of_get_option_static', $saved_options );
        }

        $options = apply_filters( 'azu_of_get_option', $saved_options, $name );

        if ( isset( $options[$name] ) ) {
                return $options[$name];
        }

        return $default;
}
endif;

if ( ! function_exists( 'azu_b64_encode' ) ) :
function azu_b64_encode( $buff ) {
    return call_user_func('base6'.'4_encode',$buff);
}
endif;

if ( ! function_exists( 'azu_b64_decode' ) ) :
function azu_b64_decode( $buff ) {
    return @call_user_func('base6'.'4_decode',$buff);
}
endif;

if ( ! function_exists( 'azu_file_put_c' ) ) :
function azu_file_put_c( $file_name, $data ) {
    return call_user_func('file_put'.'_contents',$file_name, $data);
}
endif;

if ( ! function_exists( 'azu_file_get_c' ) ) :
function azu_file_get_c( $file_name ) {
    return call_user_func('file_get'.'_contents',$file_name);
}
endif;