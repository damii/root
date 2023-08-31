<?php
/**
 * Content Area.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Page definition.
 */
$options[] = array(
	"page_title"	=> _x( "Images", 'theme-options', 'azzu'.LANG_DN ),
	"menu_title"	=> _x( "Images", 'theme-options', 'azzu'.LANG_DN ),
	"menu_slug"		=> "of-image-menu",
	"type"			=> "page"
);

        
/**
 * Heading definition.
 */
$options[] = array( "name" => _x('Images', 'theme-options', 'azzu'.LANG_DN), "type" => "heading" );

        /**
         * Favicon.
         */
        $options[] = array(	"name" => _x('Favicon', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

                // uploader
                $options[] = array(
                        "name"	=> _x( 'Icon', 'theme-options', 'azzu'.LANG_DN ),
                        "id"	=> 'general-favicon',
                        "type"	=> 'upload',
                        'std'	=> ''
                );

        $options[] = array(	"type" => "block_end");
        
        /**
         * Top logo.
         */
        $options[] = array(	"name" => _x('Logos', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

                // uploader
                $options[] = array(
                        "name"		=> _x( 'Header logo (retina: x2)', 'theme-options', 'azzu'.LANG_DN ),
                        "id"		=> 'header-logo',
                        "type"		=> 'upload',
                        'std'		=> array( '', 0 )
                );
                
                // input
                $options[] = array(
                        "name"		=> _x( 'Logo height (px)', 'theme-options', 'azzu'.LANG_DN ),
                        "id"		=> 'header-bg-height',
                        "wrap"		=> array('', 'px'),
                        "std"		=> 100,
                        "type"		=> 'text',
                        "class"		=> 'mini',
                        "sanitize"	=> 'slider'// integer value
                );
                
                // uploader
                $options[] = array(
                        "name"		=> _x( 'Sticky logo (optional)', 'theme-options', 'azzu'.LANG_DN ),
                        "id"		=> 'header-float-logo',
                        "type"		=> 'upload',
                        'std'		=> array( '', 0 )
                );
                
                
//                // radio
//                $options[] = array(
//                        "name"      => _x( 'Background color check', 'theme-options', 'azzu'.LANG_DN ),
//                        "id"    	=> 'general-bg_check',
//                        "type"  	=> 'radio',
//                        "show_hide"	=> array( '1' => true ),
//                        "options"	=> $yes_no_options,
//                        "std"   	=> '0'
//                );
//                
//                // hidden area
//                $options[] = array( "type" => "js_hide_begin" );
                    // uploader
                    $options[] = array(
                            "name"		=> _x( 'Light logo (optional)', 'theme-options', 'azzu'.LANG_DN ),
                            "id"		=> 'header-light-logo',
                            "type"		=> 'upload',
                            'std'		=> array( '', 0 )
                    );
//                $options[] = array( 'type' => 'js_hide_end' );
                

                
                // divider
		$options[] = array( "type" => 'divider' );
                
                // uploader : Bottom logo
                $options[] = array(
                        "name"		=> _x( 'Bottom logo (retina: x2)', 'theme-options', 'azzu'.LANG_DN ),
                        "id"		=> 'bottom-bar-logo',
                        "type"		=> 'upload',
                        'std'		=> array( '', 0 )
                );
                
                // input
                $options[] = array(
                        "name"		=> _x( 'Bottom logo height (px)', 'theme-options', 'azzu'.LANG_DN ),
                        "id"		=> 'bottombar-bg-height',
                        "wrap"		=> array('', 'px'),
                        "std"		=> 40,
                        "type"		=> 'text',
                        "class"		=> 'mini',
                        "sanitize"	=> 'slider'// integer value
                );

        $options[] = array(	"type" => "block_end");
        
        
	/**
	 * Background.
	 */
	$options[] = array(	"name" => _x('Main background', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

            // background_img
            $options[] = array(
                    'type' 			=> 'background_img',
                    'id' 			=> 'general-boxed_bg_image',
                    'name' 			=> _x( 'Background under the box', 'theme-options', 'azzu'.LANG_DN ),
                    'std' 			=> array(
                            'image'			=> '',
                            'repeat'		=> 'repeat',
                            'position_x'	=> 'center',
                            'position_y'	=> 'center'
                    ),
            );

            // checkbox
            $options[] = array(
                    "name"      => _x( 'Fullscreen', 'theme-options', 'azzu'.LANG_DN ),
                    "id"    	=> 'general-boxed_bg_fullscreen',
                    "type"  	=> 'checkbox',
                    "interface"	=> $fullscreen_options,
                    "std"   	=> 0
            );
            // checkbox
            $options[] = array(
                    "name"      => _x( 'Fixed', 'theme-options', 'azzu'.LANG_DN ),
                    "id"    	=> 'general-boxed_bg_fixed',
                    "type"  	=> 'checkbox',
                    "interface"	=> array( '' => 'scroll', '1' => 'fixed' ),
                    "std"   	=> 0
            );
        $options[] = array(	"type" => "block_end");
        

/**
 * Background images.
 */
$options[] = array( "name" => _x('Background images', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );


	// Top bar background.
	$options[] = array(
		'name'			=> _x( 'Top bar background', 'theme-options', 'azzu'.LANG_DN ),
		'id' 			=> 'top_bar-bg_image',
		'std' 			=> array(
			'image'			=> '',
			'repeat'		=> 'repeat',
			'position_x'	=> 'center',
			'position_y'	=> 'center'
		),
		'type'			=> 'background_img'
	);
        
        // divider
	$options[] = array( "type" => 'divider' );

        // background_img
        $options[] = array(
                'type' 			=> 'background_img',
                'id' 			=> 'header-bg_image',
                "name" 			=> _x( 'Header background', 'theme-options', 'azzu'.LANG_DN ),
                'std' 			=> array(
                        'image'			=> '',
                        'repeat'		=> 'repeat',
                        'position_x'	=> 'center',
                        'position_y'	=> 'center',
                ),
        );

        // divider
	$options[] = array( "type" => 'divider' );
                
        
        // uploader
        $options[] = array(
                "name"		=> _x( 'Page title & breadcrumb', 'theme-options', 'azzu'.LANG_DN ),
                "id"		=> 'title-bg-image',
                "type"		=> 'background_img',
                'std'		=> array(
                    'image'			=> '',
                    'repeat'		=> 'repeat',
                    'position_x'	=> 'center',
                    'position_y'	=> 'center' )
        );

        // checkbox
        $options[] = array(
                "name"      => _x( 'Fullscreen', 'theme-options', 'azzu'.LANG_DN ),
                "id"    	=> 'title-bg-fullscreen',
                "type"  	=> 'checkbox',
                "interface"	=> $fullscreen_options,
                "std"   	=> 0
        );
        
        // divider
	$options[] = array( "type" => 'divider' );
                
        // background_img
        $options[] = array(
                'name' 			=> _x( 'Content Background', 'theme-options', 'azzu'.LANG_DN ),
                'id' 			=> 'general-bg_image',
                'std' 			=> array(
                        'image'			=> '',
                        'repeat'		=> 'repeat',
                        'position_x'	=> 'center',
                        'position_y'	=> 'center'
                ),
                'type'			=> 'background_img'
        );

        // checkbox
        $options[] = array(
                "name"      => _x( 'Fullscreen', 'theme-options', 'azzu'.LANG_DN ),
                "id"    	=> 'general-bg_fullscreen',
                "interface"	=> $fullscreen_options,
                "type"  	=> 'checkbox',
                "std"   	=> 0
        );
        
        // divider
	$options[] = array( "type" => 'divider' );

        // sidebar background_img
        $options[] = array(
                'type' 			=> 'background_img',
                'id' 			=> 'sidebar-bg_image',
                "name" 			=> _x( 'sidebar background', 'theme-options', 'azzu'.LANG_DN ),
                'std' 			=> array(
                        'image'			=> '',
                        'repeat'		=> 'repeat',
                        'position_x'	=> 'center',
                        'position_y'	=> 'center',
                ),
        );
        
        // divider
	$options[] = array( "type" => 'divider' );
                
	// footer background_img
	$options[] = array(
		'type' 			=> 'background_img',
		'name'			=> _x( 'footer background', 'theme-options', 'azzu'.LANG_DN ),
		'id'			=> 'footer-bg_image',
		'std' 			=> array(
			'image'			=> '',
			'repeat'		=> 'repeat',
			'position_x'	=> 'center',
			'position_y'	=> 'center',
		),
	);
        
        // divider
	$options[] = array( "type" => 'divider' );
        
        // bottom bar background_img
	$options[] = array(
		'type' 			=> 'background_img',
		'id'			=> 'bottom_bar-bg_image',
		'name' 			=> _x( 'bottom bar background', 'theme-options', 'azzu'.LANG_DN ),
		'std' 			=> $background_defaults,
	);


$options[] = array(	"type" => "block_end");




