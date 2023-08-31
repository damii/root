<?php
/**
 * Theme metaboxes.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


// define global metaboxes array
global $AZU_META_BOXES;
$AZU_META_BOXES = array();

// Get widgetareas
$widgetareas_list = azuf()->azzu_get_widgetareas_options();
if ( !$widgetareas_list ) {
	$widgetareas_list = array('none' => _x('None', 'backend metabox', 'azzu'.LANG_DN));
}

$rev_sliders = $layer_sliders = $royal_sliders = $master_sliders = array( 'none' => _x('none', 'backend metabox', 'azzu'.LANG_DN) );

if ( class_exists('RevSlider') ) {

	$rev = new RevSlider();

	$arrSliders = $rev->getArrSliders();
	foreach ( (array) $arrSliders as $revSlider ) { 
		$rev_sliders[ $revSlider->getAlias() ] = $revSlider->getTitle();
	}
}

if ( function_exists('lsSliders') ) {

	$layerSliders = lsSliders();

	foreach ( $layerSliders as $lSlide ) {

		$layer_sliders[ $lSlide['id'] ] = $lSlide['name'];
	}
}

if ( class_exists('MSP_DB') ) {
        $master_list = new MSP_DB(); //global $mspdb;
	$mastersliders = $master_list->get_sliders_list();
	foreach ( $mastersliders as $mSlide ) {
		$master_sliders[ $mSlide['ID'] ] = $mSlide['title'];
	}
}


if ( class_exists('NewRoyalSliderMain') ) {
        global $wpdb;
        $royalsliders = $wpdb->get_results("SELECT * FROM " . NewRoyalSliderMain::get_sliders_table_name() . " ORDER BY id");
	foreach ( $royalsliders as $ySlide ) {
		$royal_sliders[ $ySlide->id ] = $ySlide->name;
	}
}

$slideshow_posts = array();
$slideshow_query = new WP_Query( array(
	'no_found_rows'		=> true,
	'posts_per_page'	=> -1,
	'post_type'			=> 'azu_slideshow',
	'post_status'		=> 'publish',
) );

if ( $slideshow_query->have_posts() ) {

	foreach ( $slideshow_query->posts as $slidehsow_post ) {

		$slideshow_posts[ $slidehsow_post->ID ] = wp_kses( $slidehsow_post->post_title, array() );
	}
}

/***********************************************************/
// Sidebar options
/***********************************************************/

$prefix = '_azu_sidebar_';

$default_sidebar = array();

if(of_get_option('sidebar_position') =='disabled'){
    $default_sidebar = array("{$prefix}widgetarea_id","{$prefix}widgetarea_id2", "{$prefix}wide");
}
else if(of_get_option('sidebar_position') !='dual')
    $default_sidebar[] = "{$prefix}widgetarea_id2";
    
$AZU_META_BOXES['azu_page_box-sidebar'] = array(
	'id'		=> 'azu_page_box-sidebar',
	'title' 	=> _x('Sidebar Options', 'backend metabox', 'azzu'.LANG_DN),
	'pages' 	=> array( 'page', 'azu_portfolio', 'post', 'product' ),
	'context' 	=> 'side',
	'priority' 	=> 'low',
	'fields' 	=> array(

		// Sidebar option
		array(
			'name'    	=> _x('Sidebar position:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "{$prefix}position",
			'type'    	=> 'radio',
			'std'		=> 'default',
			'options'	=> array(
                                'default' 		=> array( _x('Global option', 'backend metabox', 'azzu'.LANG_DN), array('f0.png', 75, 50) ),
                                'disabled'	=> array( _x('Disabled', 'backend metabox', 'azzu'.LANG_DN), array('admin-full-width.png', 75, 50) ),
				'left' 		=> array( _x('Left', 'backend metabox', 'azzu'.LANG_DN), array('admin-left-sidebar.png', 75, 50) ),
				'right' 	=> array( _x('Right', 'backend metabox', 'azzu'.LANG_DN), array('admin-right-sidebar.png', 75, 50) ),
                                'dual'          => array( _x('Dual', 'backend metabox', 'azzu'.LANG_DN), array('admin-dual-sidebar.png', 75, 50) ),
				
			),
			'hide_fields'	=> array(
				'disabled'	=> array("{$prefix}widgetarea_id","{$prefix}widgetarea_id2", "{$prefix}wide", "{$prefix}sticky"),
                                'left'	=> array("{$prefix}widgetarea_id2"),
                                'right'	=> array("{$prefix}widgetarea_id2"),
                                'default'	=> $default_sidebar
			)
		),
   
		// Sidebar widget area
		array(
			'name'     		=> _x('Sidebar widget area:', 'backend metabox', 'azzu'.LANG_DN),
			'id'       		=> "{$prefix}widgetarea_id",
			'type'     		=> 'select',
			'options'  		=> $widgetareas_list,
			'std'			=> 'azu-sidebar',
			'top_divider'	=> true
		),
                                
		// Sidebar widget area2
		array(
			'name'     		=> _x('Left sidebar widget area:', 'backend metabox', 'azzu'.LANG_DN),
			'id'       		=> "{$prefix}widgetarea_id2",
			'type'     		=> 'select',
			'options'  		=> $widgetareas_list,
			'std'			=> 'azu-sidebar',
			'top_divider'	=> true
		),
                                
                // Enable wide sidebar
		array(
			'name'    		=> _x('Enable wide sidebar:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      		=> "{$prefix}wide",
			'type'    		=> 'checkbox',
			'std'			=> 0,
			'top_divider'	=> true
		),
            	// Sticky sidebar
		array(
			'name'    	=> _x('Sticky sidebar:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "{$prefix}sticky",
			'type'    		=> 'radio',
			'std'			=> 'global',
			'options'	=> array(
				'global' =>  _x('Global option', 'backend metabox', 'azzu'.LANG_DN),
				'on'	=>  _x('On', 'backend metabox', 'azzu'.LANG_DN),
                                'off'	=>  _x('Off', 'backend metabox', 'azzu'.LANG_DN)
			),
                        'top_divider'	=> true,
		),        
	)
);

/***********************************************************/
// Footer options
/***********************************************************/

$prefix = '_azu_footer_';

$AZU_META_BOXES['azu_page_box-footer'] = array(
	'id'		=> 'azu_page_box-footer',
	'title' 	=> _x('Footer Options', 'backend metabox', 'azzu'.LANG_DN),
	'pages' 	=> array( 'page', 'azu_portfolio', 'post', 'product' ),
	'context' 	=> 'side',
	'priority' 	=> 'low',
	'fields' 	=> array(

		// Footer option
            	array(
			'name'    		=> _x('Footer columns:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      		=> "{$prefix}show",
			'type'    		=> 'radio',
			'std'			=> 'default',
			'options'		=> array(
                                'default'	=> array( _x('Global option', 'backend metabox', 'azzu'.LANG_DN), array('f0.png', 75, 50) ),
				'disabled'	=> array( _x('Disabled', 'backend metabox', 'azzu'.LANG_DN), array('bg-none.png', 75, 50) ),
                                'one'	=> array( _x('One', 'backend metabox', 'azzu'.LANG_DN), array('f1.png', 75, 50) ),
                                'two'	=> array( _x('Two', 'backend metabox', 'azzu'.LANG_DN), array('f2.png', 75, 50) ),
                                'three1'	=> array( _x('Three', 'backend metabox', 'azzu'.LANG_DN), array('f3.png', 75, 50) ),
                                'three2'	=> array( _x('Three', 'backend metabox', 'azzu'.LANG_DN), array('f4.png', 75, 50) ),
                                'three'	=> array( _x('Three', 'backend metabox', 'azzu'.LANG_DN), array('f5.png', 75, 50) ),
                                'four'	=> array( _x('four', 'backend metabox', 'azzu'.LANG_DN), array('f6.png', 75, 50) ),
                                'six'	=> array( _x('Six', 'backend metabox', 'azzu'.LANG_DN), array('f7.png', 75, 50) ),
			),
                        'hide_fields'	=> array(
				'disabled'	=> array( "{$prefix}widgetarea_id" ),
			)
		),

		// Sidebar widgetized area
		array(
			'name'     		=> _x('Footer widget area:', 'backend metabox', 'azzu'.LANG_DN),
			'id'       		=> "{$prefix}widgetarea_id",
			'type'     		=> 'select',
			'options'  		=> $widgetareas_list,
			'std'			=> 'azu-footer',
			'top_divider'	=> true
		),
	)
);

/***********************************************************/
// General Options
/***********************************************************/

$prefix = '_azu_header_';

$AZU_META_BOXES['azu_page_box-header_options'] = array(
	'id'		=> 'azu_page_box-header_options',
	'title' 	=> _x('General Options', 'backend metabox', 'azzu'.LANG_DN),
	'pages' 	=> array( 'page', 'azu_portfolio', 'post' ),
	'context' 	=> 'normal',
	'priority' 	=> 'high',
	'fields' 	=> array(
            	// Page title
		array(
			'name'    	=> _x('Show Title:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "{$prefix}title",
			'type'    		=> 'checkbox',
			'std'			=> 1,
		),  
                //  Remove content padding
		array(
			'name'    	=> _x('Stick Template:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "{$prefix}padding",
			'type'    		=> 'checkbox',
			'std'			=> 0,
                        'after'	=> '<p><small>Enabling this option will remove padding after header and before footer.</small></p>',
                        'top_divider'	=> true
                ),
            	// Show slider
		array(
			'name'    	=> _x('Show Slideshow:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "{$prefix}slideshow",
			'type'    		=> 'checkbox',
			'std'			=> 0,
			'hide_fields'	=> array( "{$prefix}type", "{$prefix}mode", "{$prefix}revolution_slider", "{$prefix}royal_slider", "{$prefix}master_slider", "{$prefix}layer_slider" ),
			'after'	=> '<p><small>You can enable slideshow for this Post and choose which items to slide.</small></p>',
                        'top_divider'	=> true
		),  
            
		// Slideshow mode
		array(
			'id'      	=> "{$prefix}mode",
			'type'    	=> 'radio',
			'std'		=> 'revolution',
                        'top_divider'	=> true,
			'options'	=> array(
				'revolution'	=>  _x('Revolution Slider', 'backend metabox', 'azzu'.LANG_DN),
				'layer'	=>  _x('Layer Slider', 'backend metabox', 'azzu'.LANG_DN),
                                'royal'	=>  _x('Royal Slider', 'backend metabox', 'azzu'.LANG_DN),
                                'master'=>  _x('Master Slider', 'backend metabox', 'azzu'.LANG_DN)
			),
			'hide_fields'	=> array(
				'revolution'	=> array(  "{$prefix}layer_slider", "{$prefix}royal_slider", "{$prefix}master_slider" ),
				'layer'	=> array( "{$prefix}revolution_slider", "{$prefix}royal_slider", "{$prefix}master_slider"),
                                'royal'	=> array( "{$prefix}revolution_slider", "{$prefix}layer_slider", "{$prefix}master_slider" ),
                                'master'=> array( "{$prefix}revolution_slider", "{$prefix}royal_slider", "{$prefix}layer_slider" )
			)
		),
                                
		// Royal slider
		array(
			'name'     		=> _x('Select slider: ', 'backend metabox', 'azzu'.LANG_DN),
			'id'       		=> "{$prefix}royal_slider",
			'type'     		=> 'select',
			'std'			=>'none',
			'options'  		=> $royal_sliders,
			'multiple' 		=> false,
			'top_divider'	=> true
		),       
                                
                // Master slider
		array(
			'name'     		=> _x('Select slider: ', 'backend metabox', 'azzu'.LANG_DN),
			'id'       		=> "{$prefix}master_slider",
			'type'     		=> 'select',
			'std'			=>'none',
			'options'  		=> $master_sliders,
			'multiple' 		=> false,
			'top_divider'	=> true
		),     
                                        
		// Revolution slider
		array(
			'name'     		=> _x('Select slider: ', 'backend metabox', 'azzu'.LANG_DN),
			'id'       		=> "{$prefix}revolution_slider",
			'type'     		=> 'select',
			'std'			=>'none',
			'options'  		=> $rev_sliders,
			'multiple' 		=> false,
			'top_divider'	=> true
		),

		// LayerSlider
		array(
			'name'     		=> _x('Select slider:', 'backend metabox', 'azzu'.LANG_DN),
			'id'       		=> "{$prefix}layer_slider",
			'type'     		=> 'select',
			'std'			=>'none',
			'options'  		=> $layer_sliders,
			'multiple' 		=> false,
			'top_divider'	=> true
		),
                // Slider type
		array(
			'name'    		=> '',
			'id'      		=> "{$prefix}type",
			'type'    		=> 'radio',
			'std'			=> 'full',
                        'top_divider'	=> true,
			'options'		=> array(
                                'full'	=> array( _x('Full width', 'backend metabox', 'azzu'.LANG_DN), array('wide.png', 75, 50) ),
				'boxed'	=> array( _x('Boxed width', 'backend metabox', 'azzu'.LANG_DN), array('boxed.png', 75, 50) ),
				'transparent'	=> array( _x('Transparent menu', 'backend metabox', 'azzu'.LANG_DN), array('transparent.png', 75, 50) ),
                                'top'	=> array( _x('Above nav menu', 'backend metabox', 'azzu'.LANG_DN), array('bottom_menu.png', 75, 50) ),
			),
		),           
                                
	)
);

// additional option include
$additional_option = apply_filters( 'azzu_page_options_metaboxes', array() );
foreach ( $additional_option as $op_name=>$add_op ) {
    $AZU_META_BOXES[$op_name] = $add_op;
}

$page_option_dir = AZZU_OPTIONS_DIR . '/page-option/';
// include metaboxes
$metaboxes = array(
	'page' => $page_option_dir.'page-option-pages.php',
        'posttype' => $page_option_dir.'page-option-posttype.php',
);

$metaboxes = apply_filters( 'azzu_page_options_list', $metaboxes );
foreach ( $metaboxes as $filename =>$metabox ) {
	require_once( $metabox );
}
