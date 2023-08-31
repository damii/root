<?php
/**
 * General.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Heading definition.
 */
$options[] = array( "name" => _x('General', 'theme-options', 'azzu'.LANG_DN), "type" => "heading" );

	/**
	 * Layout.
	 */
	$options[] = array(	"name" => _x('Layout', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

		// images
		$options[] = array(
			"name"		=> _x('Layout', 'theme-options', 'azzu'.LANG_DN),
			"id"		=> 'general-layout',
			"std"		=> 'wide',
			"type"		=> 'images',
			"less_builder"		=> true,
			"options"	=> array(
                                'wide'	=> '/fw/options/assets/images/w1.png',
                                'boxed'	=> '/fw/options/assets/images/w2.png',
                                'menu'	=> '/fw/options/assets/images/w3.png',
                                'header'    => '/fw/options/assets/images/w4.png',
                                'top'	=> '/fw/options/assets/images/w5.png'
                                ),
			
		);
                
                // slider
		$options[] = array(
			"name"		=> _x( 'The main container max-width', 'theme-options', 'azzu'.LANG_DN ),
			"id"		=> 'azu-layout-width',
			"wrap"		=> array('', 'px'),
			"std"		=> 1230,
			"type"          => "slider",
			"options"       => array( 'min' => 960, 'max' => 1600, 'step' => 2 ),
			"sanitize"      => 'slider'
		);


	$options[] = array(	"type" => "block_end");


	
	/**
	 * Style.
	 */
	$options[] = array(	"name" => _x('Style', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );


        // images
	$options[] = array(
		"name"      => 'Content style',
		"id"        => "general-layout-style",
		"std"       => 'none',
		"type"      => "images",
		"show_hide"	=> array( 'full' => true, 'boxed' => true, 'divider' => true, 'none' => true ),
		"options"   => array(
                        'none'		=> '/fw/options/assets/images/bg-none.png',
			'divider'	=> '/fw/options/assets/images/bg-0.png',
			//'full'		=> '/fw/options/assets/images/bg-1.png',
			'boxed'	=> '/fw/options/assets/images/bg-2.png'
		)
	);
        
        // hidden area
	$options[] = array( "type" => "js_hide_begin" );

                // slider
		$options[] = array(
			"name"		=> _x( 'General shadow', 'theme-options', 'azzu'.LANG_DN ),
			"id"		=> 'general-shadow',
			"wrap"		=> array('', 'px'),
			"std"		=> 0,
                        "transport"     => "refresh",
			"type"          => "slider",
			"options"       => array( 'min' => 0, 'max' => 5 ),
			"sanitize"      => 'slider'
		);
            
        $options[] = array( 'type' => 'js_hide_end' );
        
        // radio
	$options[] = array(
		"name"		=> _x('General hover', 'theme-options', 'azzu'.LANG_DN),
		"id"		=> 'hover-style',
		"std"		=> 'color',
		"type"		=> 'radio',
                "less_builder"  => true,
		"options"	=> array(
			'none' => _x('None', 'theme-options', 'azzu'.LANG_DN),
			'grayscale' => _x('Grayscale', 'theme-options', 'azzu'.LANG_DN),
			'color' => _x('Accent color', 'theme-options', 'azzu'.LANG_DN),
                        'zoom' => _x('Zoom', 'theme-options', 'azzu'.LANG_DN),
			'blur' => _x('Blur', 'theme-options', 'azzu'.LANG_DN),
                )
	);        
        
        // checkbox
        $options[] = array(
                "name"	=> _x( "Hover icon", "theme-options", 'azzu'.LANG_DN ),
                "id"	=> "general-hover_icon",
                "type"	=> "checkbox",
                "transport"     => "refresh",
                "std"	=> 1,
                "interface"	=> $on_off_options,
        );
        
        $options[] = array(	"type" => "block_end");


	/**
	 * Border radius.
	 */
	$options[] = array(	"name" => _x('Border radius', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );
		// input
		$options[] = array(
			"name"		=> _x( 'Border Radius small (px)', 'theme-options', 'azzu'.LANG_DN ),
			"id"		=> 'general-border-small-radius',
			"wrap"		=> array('', 'px'),
			"std"		=> '3',
                        "transport"     => "refresh",
			"type"		=> 'text',
			"sanitize"	=> 'dimensions'
		);
                
		// input
		$options[] = array(
			"name"		=> _x( 'Border Radius normal (px)', 'theme-options', 'azzu'.LANG_DN ),
			"id"		=> 'general-border-normal-radius',
			"wrap"		=> array('', 'px'),
			"std"		=> '4',
                        "transport"     => "refresh",
			"type"		=> 'text',
			"sanitize"	=> 'dimensions'
		);

                $options[] = array(
			"name"		=> _x( 'Border Radius large (px)', 'theme-options', 'azzu'.LANG_DN ),
			"id"		=> 'general-border-large-radius',
			"wrap"		=> array('', 'px'),
			"std"		=> '6',
                        "transport"     => "refresh",
			"type"		=> 'text',
			"sanitize"	=> 'dimensions'
		);
                                

	$options[] = array(	"type" => "block_end");
        
        
        /**
	 * Button:
	 */
	$options[] = array( "name" => _x("Button", "theme-options", 'azzu'.LANG_DN), "type" => "block_begin" );

                // radio
                $options[] = array(
                        "name"		=> _x('Button type', 'theme-options', 'azzu'.LANG_DN),
                        "id"		=> 'general-button-style',
                        "std"		=> 'default',
                        "type"		=> 'radio',
                        "options"	=> array(
                                'default' => _x('Default', 'theme-options', 'azzu'.LANG_DN),
                                'blur' => _x('Semi-transparent', 'theme-options', 'azzu'.LANG_DN),
                                'ghost' => _x('Ghost', 'theme-options', 'azzu'.LANG_DN),
                        )

                );

	$options[] = array( "type" => "block_end" );
        
        
	/**
	 * Dividers.
	 */
	$options[] = array(	"name" => _x('Dividers', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

		// bg image
		$options[] = array(
			"name"      => _x('Divider style', 'theme-options', 'azzu'.LANG_DN),
			"id"        => "general-thin_divider_style",
			"std"       => 'style-1',
			"type"      => "images",
			"options"   => array(
				'style-1'	=> '/fw/options/assets/images/dividers/div-sm-01.jpg',
				'style-2'	=> '/fw/options/assets/images/dividers/div-sm-02.jpg',
				'style-3'	=> '/fw/options/assets/images/dividers/div-sm-03.jpg',
			)
		);


	$options[] = array(	"type" => "block_end");

        
/**
 * Heading definition.
 */
$options[] = array( "name" => _x("Custom CSS & JS", "theme-options", 'azzu'.LANG_DN), "type" => "heading" );

        /**
	 * Custom css
	 */
	$options[] = array(	"name" => _x('Custom CSS', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

		// textarea
		$options[] = array(
			"settings"	=> array( 'rows' => 16 ),
			"id"		=> "general-custom_css",
			"std"		=> false,
			"type"		=> 'textarea',
			"sanitize"	=> 'without_sanitize'
		);

	$options[] = array(	"type" => "block_end");


	/**
	 * Custom js
	 */
	$options[] = array(	"name" => _x('Custom JS (e.g. Google analytics)', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

		// textarea
		$options[] = array(
			"settings"	=> array( 'rows' => 16 ),
			"id"		=> "general-tracking_code",
			"std"		=> false,
			"type"		=> 'textarea',
			"sanitize"	=> 'js'
		);

	$options[] = array(	"type" => "block_end");

/**
 * Heading definition.
 */
$options[] = array( "name" => _x("Advanced", "theme-options", 'azzu'.LANG_DN), "type" => "heading" );
	/**
	 * Title.
	 */
	$options[] = array(	"name" => _x('Site Title', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );
		// checkbox
		$options[] = array(
			"name"		=> _x('Show site title text', 'theme-options', 'azzu'.LANG_DN),
			"id"		=> 'general-site-title',
                        "std"		=> 1,
                        "type"  	=> 'checkbox'
		);
	$options[] = array(	"type" => "block_end");
	/**
	 * Page Title.
	 */
	$options[] = array(	"name" => _x('Page titles &amp; breadcrumbs', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

		// radio
		$options[] = array(
			"name"		=> _x('Show page titles under header', 'theme-options', 'azzu'.LANG_DN),
			"id"		=> 'general-show_titles',
			"std"		=> '1',
                        "transport"         => "refresh",
			"type"		=> 'radio',
			"options"	=> $yes_no_options,
			"show_hide"	=> array( '1' => true ),
		);

		// hidden area
		$options[] = array( 'type' => 'js_hide_begin' );

			// bg image
			$options[] = array(
				"name"      => _x('Title align', 'theme-options', 'azzu'.LANG_DN),
				"id"        => "general-title_align",
				"std"       => 'center',
                                "transport"         => "refresh",
				"less_builder"	=> true,
				"type"      => "radio",
				"options"   => array(
                                        'left'      => _x( 'Left', 'backend options', 'azzu'.LANG_DN ),
					'center'    => _x( 'Center', 'backend options', 'azzu'.LANG_DN ),
					'right'     => _x( 'Right', 'backend options', 'azzu'.LANG_DN )
				)
			);

		$options[] = array( 'type' => 'js_hide_end' );
                
                // input
                $options[] = array(
                        "name"		=> _x( 'Background height (px)', 'theme-options', 'azzu'.LANG_DN ),
                        "id"		=> 'title-bg-height',
                        "wrap"		=> array('', 'px'),
                        "std"		=> 100,
                        "type"		=> 'text',
                        "class"		=> 'mini',
                        "sanitize"	=> 'slider'// integer value
                );

		// checkbox
		$options[] = array(
			"name"		=> _x('Breadcrumbs', 'theme-options', 'azzu'.LANG_DN),
			"id"		=> 'general-show_breadcrumbs',
                        "std"		=> 1,
                        "transport"     => "refresh",
                        "type"  	=> 'checkbox'
		);

	$options[] = array(	"type" => "block_end");

        
        /**
	 * Pre-loader:
	 */
	$options[] = array( "name" => _x("Loader", "theme-options", 'azzu'.LANG_DN), "type" => "block_begin" );

              	// checkbox
		$options[] = array(
			"name"		=> _x('Pre-loader', 'theme-options', 'azzu'.LANG_DN),
			"id"		=> 'general-preloader',
                        "std"		=> 1,
                        "type"  	=> 'checkbox'
		);  

	$options[] = array( "type" => "block_end" );


        /**
	 * Scrollbar:
	 */
	$options[] = array( "name" => _x("Scrollbar", "theme-options", 'azzu'.LANG_DN), "type" => "block_begin" );

              	// checkbox
		$options[] = array(
			"name"		=> _x('Thin scrollbar', 'theme-options', 'azzu'.LANG_DN),
			"id"		=> 'general-scrollbar',
                        "std"		=> 1,
                        "type"  	=> 'checkbox',
                        "interface"	=> $on_off_options,
		);  

	$options[] = array( "type" => "block_end" );
        
        /**
	 * Contact form sends emails to:
	 */
	$options[] = array( "name" => _x("Human time difference", "theme-options", 'azzu'.LANG_DN), "type" => "block_begin" );

            // radio
            $options[] = array(
                    "name"		=> _x("Human time difference", "theme-options", 'azzu'.LANG_DN),
                    "id"		=> "general-human-time",
                    "std"		=> "0",
                    "transport"         => "refresh",
                    "type"		=> "radio",
                    "options"	=> array(
                            0 => _x("Normal", "theme-options", 'azzu'.LANG_DN),
                            2 => _x("Hybrid", "theme-options", 'azzu'.LANG_DN),
                            1 => _x("Human time (e.g. 2 days ago)", "theme-options", 'azzu'.LANG_DN)
                    )
            );
        $options[] = array( "type" => "block_end" );
        
        if(defined('AZU_ALWAYS_REGENERATE_DYNAMIC_CSS') && AZU_ALWAYS_REGENERATE_DYNAMIC_CSS)
        {
            /**
             * General gutter:
             */
            $options[] = array( "name" => _x("Theme hidden option", "theme-options", 'azzu'.LANG_DN), "type" => "block_begin" );
                    // slider
                    $options[] = array(
                            "name"		=> _x( 'Mobile screen width', 'theme-options', 'azzu'.LANG_DN ),
                            "id"		=> 'azu-layout-mobile-width',
                            "wrap"		=> array('', 'px'),
                            "std"		=> AZZU_THEME_MOBILE_WIDTH,
                            "transport"         => "refresh",
                            "type"          => "slider",
                            "options"       => array( 'min' => 500, 'max' => 960, 'step' => 2 ),
                            "sanitize"      => 'slider'
                    );
                    // slider
                    $options[] = array(
                            "name"		=> _x( 'General gutter width', 'theme-options', 'azzu'.LANG_DN ),
                            "id"		=> 'general-gutter-width',
                            "wrap"		=> array('', 'px'),
                            "std"		=> AZZU_THEME_GUTTER,
                            "transport"         => "refresh",
                            "type"          => "slider",
                            "options"       => array( 'min' => 10, 'max' => 60, 'step' => 2 ),
                            "sanitize"      => 'slider'
                    );

                    /**
                    * Visual composer settings:
                    */
                    // input
                    $options[] = array(
                            "name"		=> _x( 'Elements bottom margin (px)', 'theme-options', 'azzu'.LANG_DN ),
                            "id"		=> 'vc-bottom-margin',
                            "wrap"		=> array('', 'px'),
                            "std"		=> 35,
                            "transport"         => "refresh",
                            "type"		=> 'text',
                            "sanitize"	=> 'dimensions'
                    );
            $options[] = array( "type" => "block_end" );
        }
        if(azu_check_custom_posttype()){
            /**
             * Custom PostTypes:
             */
            $options[] = array( "name" => _x("Custom PostTypes", "theme-options", 'azzu'.LANG_DN), "type" => "block_begin" );
                    $custom_posttype_arr = array(
                        'portfolio' => _x('Portfolio', 'theme-options', 'azzu'.LANG_DN),
                        'team' => _x('Team', 'theme-options', 'azzu'.LANG_DN),
                        'testimonials' => _x('Testimonials', 'theme-options', 'azzu'.LANG_DN),
                    );
            
                    foreach($custom_posttype_arr as $type=>$text){
                            // checkbox
                            $options[] = array(
                                    "name"		=> $text,
                                    "id"		=> 'posttype-'.$type,
                                    "std"		=> 1,
                                    "type"  	=> 'checkbox'
                            );
                    }
            $options[] = array( "type" => "block_end" );
        }
        
        
        

