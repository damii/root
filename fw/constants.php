<?php
/**
 * azzu define
 *
 * @package azzu
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if ( !defined( 'AZZU_DESIGN' ) ) 
	define( 'AZZU_DESIGN',  str_replace("-child","",$options['theme_name']));

//Visual composer field prefix
if ( !defined( 'AZZU_VC' ) ) 
	define( 'AZZU_VC', "wpb_js_");

/* Sets the framework version number. */
define( 'AZZU_VERSION', '1.0.0' );

/* Sets the path to the parent theme directory. */
if ( !defined( 'AZZU_THEME_DIR' ) ) {
	define( 'AZZU_THEME_DIR', get_template_directory() );
}

/* Sets the path to the parent theme directory URI. */
if ( !defined( 'AZZU_THEME_URI' ) ) {
	define( 'AZZU_THEME_URI', get_template_directory_uri() );
}

/* Sets the path to the child theme directory. */
if ( !defined( 'AZZU_CHILD_THEME_DIR' ) ) {
	define( 'AZZU_CHILD_THEME_DIR', get_stylesheet_directory() );
}

/* Sets the path to the child theme directory URI. */
if ( !defined( 'AZZU_CHILD_THEME_URI' ) ) {
	define( 'AZZU_CHILD_THEME_URI', get_stylesheet_directory_uri() );
}

/* Sets the path to the core framework directory. */
if ( !defined( 'AZZU_DIR' ) ) {
	define( 'AZZU_DIR', trailingslashit( AZZU_THEME_DIR ) . basename( dirname( __FILE__ ) ) );
}

/* Sets the path to the core framework directory URI. */
if ( !defined( 'AZZU_URI' ) ) {
	define( 'AZZU_URI', trailingslashit( AZZU_THEME_URI ) . basename( dirname( __FILE__ ) ) );
}

/* Sets the path to the core framework admin directory. */
if ( !defined( 'AZZU_OPTIONS_DIR' ) ) {
	define( 'AZZU_OPTIONS_DIR', trailingslashit( AZZU_DIR ) . 'options' );
}

if ( !defined( 'AZZU_OPTIONS_URI' ) ) {
	define( 'AZZU_OPTIONS_URI', trailingslashit( AZZU_URI ) . 'options' );
}

/* Sets the path to the core framework classes directory. */
if ( !defined( 'AZZU_CLASSES_DIR' ) ) {
	define( 'AZZU_CLASSES_DIR', trailingslashit( AZZU_DIR ) . 'classes' );
}

if ( !defined( 'AZZU_LIBRARY_DIR' ) ) {
	define( 'AZZU_LIBRARY_DIR', trailingslashit( AZZU_DIR ) . 'library' );
}

if ( !defined( 'AZZU_LIBRARY_URI' ) ) {
	define( 'AZZU_LIBRARY_URI', trailingslashit( AZZU_URI ) . 'library' );
}

if ( !defined( 'AZZU_PLUGINS_DIR' ) ) {
	define( 'AZZU_PLUGINS_DIR', trailingslashit( AZZU_THEME_DIR ) . 'plugins' );
}

if ( !defined( 'AZZU_PLUGINS_URI' ) ) {
	define( 'AZZU_PLUGINS_URI', trailingslashit( AZZU_THEME_URI ) . 'plugins' );
}

if ( !defined( 'AZZU_FUNCTION_DIR' ) ) {
	define( 'AZZU_FUNCTION_DIR', trailingslashit( AZZU_DIR ).'functions' );
}

if ( !defined( 'AZZU_FUNCTION_URI' ) ) {
	define( 'AZZU_FUNCTION_URI', trailingslashit( AZZU_URI ).'functions' );
}

if ( !defined( 'AZZU_UI_DIR' ) ) {
	define( 'AZZU_UI_DIR', trailingslashit( AZZU_THEME_DIR ).'ui' );
}

if ( !defined( 'AZZU_UI_URI' ) ) {
	define( 'AZZU_UI_URI', trailingslashit( AZZU_THEME_URI ).'ui' );
}

if ( !defined( 'AZZU_JS_URI' ) ) {
	define( 'AZZU_JS_URI', trailingslashit( AZZU_THEME_URI ).'js' );
}

if ( !defined( 'ULTIMATE_USE_BUILTIN' ) ) {
	define( 'ULTIMATE_USE_BUILTIN', true );
}

if ( !defined( 'DEFAULT_TRANSPORT_MODE' ) ) {
	define( 'DEFAULT_TRANSPORT_MODE', 'postMessage' ); //refresh
}

/* Set language domain */
if ( !defined( 'LANG_DN' ) ) {
	define( 'LANG_DN', AZZU_DESIGN);
}
if ( !defined( 'LANG_PREFIX' ) ) {
	define( 'LANG_PREFIX', 'azzu');
}


/* Sets the path to the core framework extensions directory. */
if ( !defined( 'AZZU_WIDGETS_DIR' ) ) {
	define( 'AZZU_WIDGETS_DIR', trailingslashit( AZZU_DIR ) . 'widgets' );
}

/* shortcodes dir and url */
if ( !defined( 'AZZU_SHORTCODES_DIR' ) ) {
	define( 'AZZU_SHORTCODES_DIR', trailingslashit( AZZU_DIR ) . 'vc_plugins/shortcodes' );
}

if ( !defined( 'AZZU_SHORTCODES_URI' ) ) {
	define( 'AZZU_SHORTCODES_URI', trailingslashit( AZZU_URI ) . 'vc_plugins/shortcodes' );
}

/**
 * Force use php vars instead those in less files.
 */
if ( !defined( 'AZU_LESS_USE_PHP_VARS' ) ) {
	define( 'AZU_LESS_USE_PHP_VARS', true );
}

// Re-define meta box path and URL
if ( !defined( 'RWMB_URL' ) ) {
	define( 'RWMB_URL', trailingslashit( trailingslashit( AZZU_LIBRARY_URI ) . 'meta-box' ) );
}

if ( !defined( 'RWMB_DIR' ) ) {
	define( 'RWMB_DIR', trailingslashit( trailingslashit( AZZU_LIBRARY_DIR ) . 'meta-box' ) );
}

/* Sets the widget prefix */
if ( !defined( 'AZU_WIDGET_PREFIX' ) ) {
	define( 'AZU_WIDGET_PREFIX', $options['theme_slug'].'-' );
}

// include theme special constants
require_once( AZZU_UI_DIR . '/'.AZZU_DESIGN. '/theme_constant.php' );

/**
 * Include classes.
 *
 */
$all_classes = array(
        'base',
	'category_walker',
	'styles',
        'helpers',
        'theme_functions',
        'template_tags',
	'azzu_config',
        'love_this',
	'breadcrumbs',
	'pagination',
        'posttype',
        'components'
);

foreach ( $all_classes as $filename ) {
	require_once( trailingslashit( AZZU_CLASSES_DIR ) . $filename . '.class.php' );
}
