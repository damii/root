<?php
/**
 * Page meta boxes.
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$nav_menus = get_terms( 'nav_menu' );
$nav_menus_clear = array( 0 => _x('Primary location menu', 'backend metabox', 'azzu'.LANG_DN), -1 => _x('Default menu', 'backend metabox', 'azzu'.LANG_DN) );

foreach ( $nav_menus as $nav_menu ) {
	$nav_menus_clear[ $nav_menu->term_id ] = $nav_menu->name;
}

// Image settings
$repeat_options = array(
	'repeat'	=> _x('repeat', 'backend', 'azzu'.LANG_DN),
	'repeat-x'	=> _x('repeat-x', 'backend', 'azzu'.LANG_DN),
	'repeat-y'	=> _x('repeat-y', 'backend', 'azzu'.LANG_DN),
	'no-repeat'	=> _x('no-repeat', 'backend', 'azzu'.LANG_DN),
);

$position_x_options = array(
	'center'	=> _x('center', 'backend', 'azzu'.LANG_DN),
	'left'		=> _x('left', 'backend', 'azzu'.LANG_DN),
	'right'		=> _x('right', 'backend', 'azzu'.LANG_DN),
);

$position_y_options = array(
	'center'	=> _x('center', 'backend', 'azzu'.LANG_DN),
	'top'		=> _x('top', 'backend', 'azzu'.LANG_DN),
	'bottom'	=> _x('bottom', 'backend', 'azzu'.LANG_DN),
);


$prefix = '_azu_page_';

$AZU_META_BOXES[] = array(
	'id'		=> 'azu_page_box-page',
	'title' 	=> _x('Page Options', 'backend metabox', 'azzu'.LANG_DN),
	'pages' 	=> array( 'page' ),
	'context' 	=> 'normal',
	'priority' 	=> 'default',
	'fields' 	=> array(
                //  Global option override
		array(
			'name'    	=> _x('Override Global Settings:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "{$prefix}override",
			'type'    		=> 'checkbox',
			'std'			=> 0,
                        'after'	=> '<p><small>You should enable this option if you want to override global values defined in Theme Options.</small></p>'
		),
		// Page layout
            	array(
			'name'    		=> _x('Page layout:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      		=> "{$prefix}page_layout",
			'type'    		=> 'radio',
			'std'			=> 'default',
			'options'		=> array(
                                'default'	=> array( _x('Global option', 'backend metabox', 'azzu'.LANG_DN), array('f0.png', 75, 50) ),
                                'wide'	=> array( _x('full-width', 'backend metabox', 'azzu'.LANG_DN), array('w1.png', 75, 50) ),
				'boxed'	=> array( _x('boxed', 'backend metabox', 'azzu'.LANG_DN), array('w2.png', 75, 50) ),
                                'menu'	=> array( _x('menu wide', 'backend metabox', 'azzu'.LANG_DN), array('w3.png', 75, 50) ),
                                'header'	=> array( _x('header & footer wide', 'backend metabox', 'azzu'.LANG_DN), array('w4.png', 75, 50) ),
                                'top'	=> array( _x('top & bottom bars wide', 'backend metabox', 'azzu'.LANG_DN), array('w5.png', 75, 50) ),
			),
                        'top_divider'	=> true
		),

		// Hide contemt
		array(
			'name' => _x('Hide:', 'backend metabox', 'azzu'.LANG_DN),
			'id'   => "{$prefix}hidden_parts",
			'type' => 'checkbox_list',
			'options' => array(
				'top_bar' => _x('top bar', 'backend metabox', 'azzu'.LANG_DN),
				'header' => _x('header', 'backend metabox', 'azzu'.LANG_DN),
                                'menu' => _x('menu', 'backend metabox', 'azzu'.LANG_DN),
				'floating_menu' => _x('floating menu', 'backend metabox', 'azzu'.LANG_DN),
                                'page_title' => _x('page title & breadcrumb', 'backend metabox', 'azzu'.LANG_DN),
				'bottom_bar' => _x('bottom bar', 'backend metabox', 'azzu'.LANG_DN)
			),
			'top_divider'	=> true
		),


		// Primary menu list
		array(
			'name'     		=> _x('Primary menu:','backend metabox', 'azzu'.LANG_DN),
			'id'       		=> "{$prefix}primary_menu",
			'type'     		=> 'select',
			'options'  		=> $nav_menus_clear,
			'std'			=> 0,
			'top_divider'	=> true
		),
                
                
                // Header logo
                array(
                        'name'			=> _x('Logo', 'backend metabox', 'azzu'.LANG_DN),
                        'id'               	=> "{$prefix}header_logo",
                        'type'             	=> 'image_advanced',
                        'top_divider'	=> true,
                        'max_file_uploads'	=> 1
                ),

                                
                // bottom logo
                array(
                        'name'             => _x('Logo bottom', 'backend metabox', 'azzu'.LANG_DN),
                        'id'               => "{$prefix}bottom_logo",
                        'type'             => 'image_advanced',
                        'top_divider'	=> true,
                        'max_file_uploads'	=> 1
                ),
                                
		// Link
		array(
			'name'	=> _x('Logo link:', 'backend metabox', 'azzu'.LANG_DN),
			'id'    => "{$prefix}logo_link",
			'type'  => 'text',
			'std'   => '',
			'top_divider'	=> true
		),
                                
                // Background color
		array(
			'name'    		=> _x('Background color:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      		=> "{$prefix}bg_color",
			'type'    		=> 'color',
			'std'			=> '#ffffff',
			'top_divider'	=> true,
		),

		// Background image
		array(
			'name'             	=> _x('Background image:', 'backend metabox', 'azzu'.LANG_DN),
			'id'               	=> "{$prefix}bg_image",
			'type'             	=> 'image_advanced',
			'max_file_uploads'	=> 1,
		),

		// Repeat options
		array(
			'name'     	=> _x('Repeat options:', 'backend metabox', 'azzu'.LANG_DN),
			'id'       	=> "{$prefix}bg_repeat",
			'type'     	=> 'select',
			'options'  	=> $repeat_options,
			'std'		=> 'no-repeat'
		),

		// Position x
		array(
			'name'     	=> _x('Position x:', 'backend metabox', 'azzu'.LANG_DN),
			'id'       	=> "{$prefix}bg_position_x",
			'type'     	=> 'select',
			'options'  	=> $position_x_options,
			'std'		=> 'center'
		),

		// Position y
		array(
			'name'     	=> _x('Position y:', 'backend metabox', 'azzu'.LANG_DN),
			'id'       	=> "{$prefix}bg_position_y",
			'type'     	=> 'select',
			'options'  	=> $position_y_options,
			'std'		=> 'center'
		),

		// Fullscreen
		array(
			'name'    		=> _x('Fullscreen:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      		=> "{$prefix}bg_fullscreen",
			'type'    		=> 'checkbox',
			'std'			=> 1,
		),


	),
);