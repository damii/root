<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *  
 */


// checkbox cover/auto
$fullscreen_options = array(
	'1' => 'cover',
	'' => 'auto'
);

// checkbox on/off
$on_off_options = array(
	'1'	=> 'on',
	''	=> 'off',
);

// Radio yes/no
$yes_no_options = array(
	'1'	=> _x('Yes', 'theme-options', 'azzu'.LANG_DN),
	'0'	=> _x('No', 'theme-options', 'azzu'.LANG_DN),
);

$repeat_arr = array(
	'repeat'    => _x( 'repeat', 'backend options', 'azzu'.LANG_DN ),
	'repeat-x'  => _x( 'repeat-x', 'backend options', 'azzu'.LANG_DN ),
	'repeat-y'  => _x( 'repeat-y', 'backend options', 'azzu'.LANG_DN ),
	'no-repeat' => _x( 'no-repeat', 'backend options', 'azzu'.LANG_DN )
);

$repeat_x_arr = array(
	'no-repeat' => _x( 'no-repeat', 'backend options', 'azzu'.LANG_DN ),
	'repeat-x'  => _x( 'repeat-x', 'backend options', 'azzu'.LANG_DN )
);

$y_position_arr = array(
	'center'    => _x( 'center', 'backend options', 'azzu'.LANG_DN ),
	'top'       => _x( 'top', 'backend options', 'azzu'.LANG_DN ),
	'bottom'    => _x( 'bottom', 'backend options', 'azzu'.LANG_DN )
);

$x_position_arr = array(
	'center'    => _x( 'center', 'backend options', 'azzu'.LANG_DN ),
	'left'      => _x( 'left', 'backend options', 'azzu'.LANG_DN ),
	'right'     => _x( 'right', 'backend options', 'azzu'.LANG_DN )
);


// Background Defaults
$background_defaults = array(
	'image' 		=> '',
	'repeat' 		=> 'repeat',
	'position_x' 	=> 'center',
	'position_y'	=> 'center'
);



// Divider
$divider_html = '<div class="divider"></div>';



$google_fonts = azu_get_google_fonts_list();

$web_fonts = azu_stylesheet_get_websafe_fonts();

$merged_fonts = array_merge( $web_fonts, $google_fonts ); //ksort(

$dir = trailingslashit( dirname(__FILE__) );

$option_files = array(
	// always stay ontop
	'general' => $dir . 'options-general.php',

	// submenu section
	'layout' => $dir . 'options-header.php',
	'colors' => $dir . 'options-colors.php',
        'fonts' => $dir . 'options-typography.php',
        'images' => $dir . 'options-images.php',
	'blog' => $dir . 'options-blog.php',
        'import' => $dir . 'options-import.php'
);

$option_files = apply_filters( 'azzu_options_list', $option_files );

foreach ( $option_files as $filename =>$file ) {
	require_once( $file );
        // additional option include
        $additional_option = apply_filters( 'azzu_options_list_'.$filename, array() );
        foreach ( $additional_option as $add_op ) {
            $options[] = $add_op;
        }
}
