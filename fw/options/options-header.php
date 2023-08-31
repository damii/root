<?php
/**
 * Header.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Page definition.
 */
$options[] = array(
	"page_title"	=> _x( "Header & Footer", 'theme-options', 'azzu'.LANG_DN ),
	"menu_title"	=> _x( "Header & Footer", 'theme-options', 'azzu'.LANG_DN ),
	"menu_slug"		=> "of-section-header",
	"type"			=> "page"
);

/**
 * Heading definition.
 */
$options[] = array( "name" => _x('Header', 'theme-options', 'azzu'.LANG_DN), "type" => "heading" );

/**
 * Show top bar.
 */
$options[] = array(	"name" => _x('Topbar', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

	// checkbox
	$options[] = array(
		"desc"  => '',
		"name"  => _x( 'Show top bar', 'theme-options', 'azzu'.LANG_DN ),
		"id"    => 'top_bar-show',
		"type"  => 'checkbox',
		'std'   => 1
	);
        
        // radio
	$options[] = array(
		"name"		=> _x("Widget area alignment", "theme-options", 'azzu'.LANG_DN),
		"id"		=> "top_bar-content_alignment",
		"std"		=> "right",
		"type"		=> "radio",
		"options"	=> array(
                        "left" => _x("left", "theme-options", 'azzu'.LANG_DN),
                        "center" => _x("center", "theme-options", 'azzu'.LANG_DN),
			"right" => _x("right", "theme-options", 'azzu'.LANG_DN)
		)
	);
        
        // textarea
        $options[] = array(
                "desc"		=> '',
                "name"		=> _x('Top bar text', 'theme-options', 'azzu'.LANG_DN),
                "id"		=> "top-bar-text",
                "std"		=> false,
                "type"		=> 'textarea'
        );
        
        // checkbox
	$options[] = array(
		"desc"  => '',
		"name"  => _x( 'Top bar expand/collapse', 'theme-options', 'azzu'.LANG_DN ),
		"id"    => 'top_bar-arrow',
		"type"  => 'checkbox',
		'std'   => 1
	);

$options[] = array(	"type" => "block_end");

/**
 * Floating menu.
 */
$options[] = array(	"name" => _x('Floating menu', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

	// radio
	$options[] = array(
		"name"		=> _x('Sticky menu', 'theme-options', 'azzu'.LANG_DN),
		"id"		=> 'header-show_floating_menu',
		"std"		=> 1,
		"type"  	=> 'checkbox',
		"interface"	=> $on_off_options,
	);


$options[] = array(	"type" => "block_end");


/**
 * Header layout.
 */
$options[] = array(	"name" => _x('Header layout', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );
	// images
	$options[] = array(
		"desc"      => '',
		"name"      => _x('Header layout', 'theme-options', 'azzu'.LANG_DN),
		"id"        => "header-layout",
		"std"       => 'left',
                "transport" => "refresh",
		"type"      => "images",
                "show_hide"	=> array( 'left' => true, 'right' => true , 'side'=> true),
		"options"   => array(
			'left'				=> '/fw/options/assets/images/h2.png',
                        'middle'			=> '/fw/options/assets/images/h4.png',
			'center'			=> '/fw/options/assets/images/h3.png',
			'right'			=> '/fw/options/assets/images/h1.png',
                        'side'                  => '/fw/options/assets/images/h5.png',
		)
	);
        
        // hidden area
	$options[] = array( "type" => "js_hide_begin" );
        // radio
        $options[] = array(
                "name"		=> _x("Menu alignment", "theme-options", 'azzu'.LANG_DN),
                "id"		=> "header-menu_alignment",
                "std"		=> "left",
                "type"		=> "radio",
                "less_builder"      => true,
                "options"	=> array(
                        "left" => _x("left", "theme-options", 'azzu'.LANG_DN),
                        "center" => _x("center", "theme-options", 'azzu'.LANG_DN),
                        "right" => _x("right", "theme-options", 'azzu'.LANG_DN)
                )
        );
        $options[] = array( 'type' => 'js_hide_end' );

//        // checkbox
//	$options[] = array(
//		"name"      => _x( 'Show search', 'theme-options', 'azzu'.LANG_DN ),
//		"id"    	=> 'header-search_show',
//		"std"		=> '1',
//		"type"		=> 'checkbox'
//	);
        
        
        
        //slider
        $options[] = array(
                "desc"      => '',
                "name"      => _x( 'Menu width (px)', 'theme-options', 'azzu'.LANG_DN ),
                "id"        => 'menu-side-width',
                "wrap"		=> array('', 'px'),
                "std"       => 300, 
                "type"      => "slider",
                "sanitize"	=> 'slider',// integer value
                "options"   => array( 'min' => 250, 'max' => 350 )
        );
        
        //slider
        $options[] = array(
                "desc"      => '',
                "name"      => _x( 'Menu height (px)', 'theme-options', 'azzu'.LANG_DN ),
                "id"        => 'menu-bg-height',
                "wrap"		=> array('', 'px'),
                "std"       => 60, 
                "type"      => "slider",
                "sanitize"	=> 'slider',// integer value
                "options"   => array( 'min' => 40, 'max' => 150 )
        );

$options[] = array(	"type" => "block_end");



/**
 * Menu.
 */
$options[] = array(	"name" => _x('Menu item', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );
	

	
	// checkbox
	$options[] = array(
		"name"      => _x( 'Hover style', 'theme-options', 'azzu'.LANG_DN ),
		"id"    	=> "header-hover_style",
		"type"  	=> 'select',
		'std'   	=> 'text',
                'less_builder'  => true,
		'options'	=> array(
			'none' => _x( 'none', 'theme-options', 'azzu'.LANG_DN ),
                        'though' => _x( 'line though', 'theme-options', 'azzu'.LANG_DN ),
			'top' => _x( 'top border', 'theme-options', 'azzu'.LANG_DN ),
			'bottom' => _x( 'bottom border', 'theme-options', 'azzu'.LANG_DN ),
			'underline' => _x( 'underline', 'theme-options', 'azzu'.LANG_DN ),
                        'text' => _x( 'text', 'theme-options', 'azzu'.LANG_DN ),
                        'border' => _x( 'border', 'theme-options', 'azzu'.LANG_DN ),
                        'bg' => _x( 'background', 'theme-options', 'azzu'.LANG_DN )
		)
	);
        
        // checkbox
	$options[] = array(
		"name"      => _x( 'menu caret style', 'theme-options', 'azzu'.LANG_DN ),
		"id"    	=> "header-caret-style",
		"type"  	=> 'radio',
		'std'   	=> 'effect',
                'less_builder' => true,
		'options'	=> array(
			'none' => _x( 'none', 'theme-options', 'azzu'.LANG_DN ),
			'caret' => _x( 'caret', 'theme-options', 'azzu'.LANG_DN ),
			'effect' => _x( 'caret with animation', 'theme-options', 'azzu'.LANG_DN )
		)
	);
	
	// images
	$options[] = array(
		"desc"      => '',
		"name"      => _x('Menu image position', 'theme-options', 'azzu'.LANG_DN),
		"id"        => "menu-image-position",
		"std"       => 'left',
                "less_builder" => true,
		"type"      => "radio",
		"options"   => array(
			'left'		=> _x( 'left', 'theme-options', 'azzu'.LANG_DN ),
			'top'		=> _x( 'top', 'theme-options', 'azzu'.LANG_DN ),
			'right'		=> _x( 'right', 'theme-options', 'azzu'.LANG_DN ),
			'bottom'	=> _x( 'bottom', 'theme-options', 'azzu'.LANG_DN ),
		)
	);
	

        //slider
        $options[] = array(
                "desc"      => '',
                "name"      => _x( 'menu item padding (px)', 'theme-options', 'azzu'.LANG_DN ),
                "id"        => 'menu-item-padding',
                "wrap"		=> array('', 'px'),
                "std"       => 15, 
                "type"      => "slider",
                "sanitize"	=> 'slider',// integer value
                "options"   => array( 'min' => 0, 'max' => 40 )
        );
	
        // checkbox
	$options[] = array(
		"name"      => _x( 'menu item style', 'theme-options', 'azzu'.LANG_DN ),
		"id"    	=> "menu-item-style",
		"type"  	=> 'radio',
		'std'   	=> 'none',
                'less_builder' => true,
		'options'	=> array(
			'none' => _x( 'None', 'theme-options', 'azzu'.LANG_DN ),
			'border' => _x( 'Border', 'theme-options', 'azzu'.LANG_DN ),
			'divider' => _x( 'Divider', 'theme-options', 'azzu'.LANG_DN )
		)
	);


	// square size
	$options[] = array(
		"name"      => _x( 'Thumbnail proportion (px)', 'theme-options', 'azzu'.LANG_DN ),
		"id"    	=> "header-icons_size",
                "less_builder"  => true,
		"type"  	=> 'square_size',
		'std'   	=> array('width' => 16, 'height' => 16)
	);

	// text
	$options[] = array(
		"name"		=> _x( 'Distance between menu items', 'theme-options', 'azzu'.LANG_DN ),
		"id"		=> "menu-items_distance",
		"wrap"		=> array( '', 'px' ),
                "std"       => 30, 
                "type"      => "slider",
                "sanitize"	=> 'slider',// integer value
                "options"   => array( 'min' => 0, 'max' => 100 )
	);

$options[] = array(	"type" => "block_end");

/**
 * Submenu.
 */
$options[] = array(	"name" => _x('Submenu', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

	// checkbox
	$options[] = array(
		"name"      => _x( 'Show next level indicator arrows', 'theme-options', 'azzu'.LANG_DN ),
		"id"    	=> "header-submenu_next_level_indicator",
		"type"  	=> 'checkbox',
		'std'   	=> 1
	);

	// square size
	$options[] = array(
		"name"      => _x( 'Submenu thumbnail proportion (px)', 'theme-options', 'azzu'.LANG_DN ),
		"id"    	=> "header-submenu_icons_size",
		"type"  	=> 'square_size',
		'std'   	=> array('width' => 12, 'height' => 12)
	);

$options[] = array(	"type" => "block_end");


/**
 * Heading definition.
 */
$options[] = array( "name" => _x('Sidebar', 'theme-options', 'azzu'.LANG_DN), "type" => "heading" );


/**
 * Sidebar.
 */
$options[] = array(	"name" => _x('Sidebar', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );
                // images
                $options[] = array(
                        "desc"      => '',
                        "name"      => _x('Sidebar position', 'theme-options', 'azzu'.LANG_DN),
                        "id"        => "sidebar_position",
                        "std"       => 'right',
                        "type"      => "images",
                        "show_hide"	=> array( 'left' => true, 'right' => true, 'dual' => true ),
                        "options"   => array(
                                'disabled'		=> '/fw/options/assets/images/admin-full-width.png',
                                'left'			=> '/fw/options/assets/images/admin-left-sidebar.png',
                                'right'			=> '/fw/options/assets/images/admin-right-sidebar.png',
                                'dual'			=> '/fw/options/assets/images/admin-dual-sidebar.png',
                        )
                );
                // hidden area
                $options[] = array( "type" => "js_hide_begin" );
                
                // checkbox
                $options[] = array(
                        "name"		=> _x( 'Enable wide sidebar', 'theme-options', 'azzu'.LANG_DN ),
                        "desc"		=> '',
                        "id"		=> 'sidebar_wide',
                        "type"		=> 'checkbox',
                        'std'		=> 0
                );
                
                $options[] = array( 'type' => 'js_hide_end' );
                
                
                // slider
		$options[] = array(
			"name"		=> _x( 'Distance between content & sidebar', 'theme-options', 'azzu'.LANG_DN ),
			"id"		=> 'sidebar_distance',
			"wrap"		=> array('', 'px'),
			"std"		=> 80,
			"type"          => "slider",
			"options"       => array( 'min' => 30, 'max' => 100, 'step' => 2 ),
			"sanitize"      => 'slider'
		);
                
                // checkbox
                $options[] = array(
                        "name"		=> _x( 'Enable Sticky sidebar', 'theme-options', 'azzu'.LANG_DN ),
                        "desc"		=> '',
                        "id"		=> 'sidebar_sticky',
                        "transport"     => "refresh",
                        "type"		=> 'checkbox',
                        'std'		=> 1
                );

$options[] = array(	"type" => "block_end");


/**
 * Heading definition.
 */
$options[] = array( "name" => _x('Footer', 'theme-options', 'azzu'.LANG_DN), "type" => "heading" );

/**
 * Scroll up
 */
$options[] = array( "name" => _x("Scroll up arrow:", "theme-options", 'azzu'.LANG_DN), "type" => "block_begin" );
        
        // radio
	$options[] = array(
		"name"		=> _x("Enable scroll up", "theme-options", 'azzu'.LANG_DN),
		"id"		=> "general-scrollup",
		"std"		=> "1",
		"type"		=> "radio",
		"options"	=> array(
                        "2" => _x("Enable", "theme-options", 'azzu'.LANG_DN),
                        "1" => _x("Disable on mobile", "theme-options", 'azzu'.LANG_DN),
			"0" => _x("Disable", "theme-options", 'azzu'.LANG_DN)
		)
	);


$options[] = array( "type" => "block_end" );

/**
 * Footer.
 */
$options[] = array(	"name" => _x('Footer', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

                // images
                $options[] = array(
                        "desc"      => '',
                        "name"      => _x('Footer position', 'theme-options', 'azzu'.LANG_DN),
                        "id"        => "footer_show",
                        "std"       => 'four',
                        "type"      => "images",
                        "options"   => array(
                                'disabled'		=> '/fw/options/assets/images/bg-none.png',
                                'one'			=> '/fw/options/assets/images/f1.png',
                                'two'			=> '/fw/options/assets/images/f2.png',
                                'three1'		=> '/fw/options/assets/images/f3.png',
                                'three2'		=> '/fw/options/assets/images/f4.png',
                                'three'			=> '/fw/options/assets/images/f5.png',
                                'four'			=> '/fw/options/assets/images/f6.png',
                                'six'			=> '/fw/options/assets/images/f7.png',
                        )
                );
$options[] = array(	"type" => "block_end");

/**
 * Copyright information.
 */
$options[] = array(	"name" => _x('Bottombar', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );
                // radio
                $options[] = array(
                        "name"		=> _x("Widget area alignment", "theme-options", 'azzu'.LANG_DN),
                        "id"		=> "bottom_bar-content_alignment",
                        "std"		=> "right",
                        "type"		=> "radio",
                        "options"	=> array(
                                "left" => _x("left", "theme-options", 'azzu'.LANG_DN),
                                "center" => _x("center", "theme-options", 'azzu'.LANG_DN),
                                "right" => _x("right", "theme-options", 'azzu'.LANG_DN)
                        )
                );
                

                // textarea
                $options[] = array(
                        "desc"		=> '',
                        "name"		=> _x('Copyright information', 'theme-options', 'azzu'.LANG_DN),
                        "id"		=> "bottom_bar-copyrights",
                        "std"		=> false,
                        "type"		=> 'textarea'
                );
                
                //slider
                $options[] = array(
                        "desc"      => '',
                        "name"      => _x( 'Top & bottom padding', 'theme-options', 'azzu'.LANG_DN ),
                        "id"        => 'bottom-bar-padding',
                        "wrap"		=> array('', 'px'),
                        "std"       => 24, 
                        "type"      => "slider",
                        "sanitize"	=> 'slider',// integer value
                        "options"   => array( 'min' => 0, 'max' => 50 )
                );

$options[] = array(	"type" => "block_end");