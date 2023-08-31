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
	"page_title"	=> _x( "Typography", 'theme-options', 'azzu'.LANG_DN ),
	"menu_title"	=> _x( "Typography", 'theme-options', 'azzu'.LANG_DN ),
	"menu_slug"		=> "of-typography-menu",
	"type"			=> "page"
);




/**
 * Heading definition.
 */
$options[] = array( "name" => _x('Typography', 'theme-options', 'azzu'.LANG_DN), "type" => "heading" );

/**
 * Fonts.
 */
$options[] = array( "name" => _x('Fonts', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

	$listbox_font = azuf()->azzu_themeoptions_get_typography_defaults();
        $listbox_font = apply_filters( 'azu_listbox_font', $listbox_font );
		
        $listbox_font_std = azuf()->azzu_themeoptions_get_typography_group();
	$listbox_font_std = apply_filters( 'azu_listbox_font_std', $listbox_font_std );
		// title
		$options[] = array(
			"type" => 'title',
			"name" =>_x( 'Drag & Drop', 'theme-options', 'azzu'.LANG_DN ),
		);
		
	foreach ( $listbox_font_std as $i=>$std ) {
		// divider
		if($i>1)
			$options[] = array(
				"type" => 'divider'
			);
		// select
		$options[] = array(
			"desc"      => '',
			"name"      => _x( 'Choose: font group ', 'theme-options', 'azzu'.LANG_DN ).$i,
			"id"        => "azu-font-family".$i,
			"std"       => $std['font'],
			"type"      => "web_fonts",
			"options"   => $merged_fonts,
		);

		// listbox
		$options[] = array(
			"desc" => '',
			"name"	=> '&nbsp;',
			"id"	=> "listbox_font".$i,
			"std"	=> $std['group'],
			"options" => $listbox_font,
                        "mode" => 'font',
			"type"	=> "listbox"
		);
	
	}
	
$options[] = array( "type" => "block_end");


/**
 * font sizes.
 */
$options[] = array( "name" => _x('Font sizes', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

	// font sizes
	$font_sizes = azuf()->azzu_themeoptions_get_font_size_defaults();
        
        $index_divi = 0;
	foreach ( $font_sizes as $id=>$data ) {
            	if($index_divi > 0 )
		// divider
		$options[] = array(
			"type" => 'divider'
		);
                $index_divi++;
		// slider
		$options[] = array(
			"desc"      => '',
			"name"      => $data['desc'],
			"id"        => "fonts-" . $id."-size",
			"wrap"		=> array('', 'px'),
			"std"       => $data['std'], 
			"type"      => "slider",
			"options"   => array( 'min' => 9, 'max' => 71 ),
			"sanitize"  => 'font_size'
		);
		
			// slider
		$options[] = array(
			"desc"      => '',
			"name"      => _x( 'Line-height', 'theme-options', 'azzu'.LANG_DN ),
			"id"        => "fonts-line-".$id."-height",
			"wrap"		=> array('', 'px'),
			"std"       => round($data['lh']), 
			"type"      => "slider",
			"options"   => array( 'min' => 9, 'max' => 71 )
		);

	}
        $options[] = array( "type" => "block_end");

        /**
         * Header font sizes.
         */
        

	// headers
	$headers = azuf()->azzu_themeoptions_get_headers_defaults();


	foreach ( $headers as $id=>$opts ) {
                $options[] = array( "name" => $opts['desc'], "type" => "block_begin" );

		// slider
		$options[] = array(
			"desc"      => '',
			"name"      => _x( 'Font-size', 'theme-options', 'azzu'.LANG_DN ),
			"id"        => "fonts-" . $id . "_font_size",
			"wrap"		=> array('', 'px'),
			"std"       => $opts['fs'], 
			"type"      => "slider",
			"options"   => array( 'min' => 9, 'max' => 71 ),
			"sanitize"  => 'font_size'
		);

		// slider
		$options[] = array(
			"desc"        => '',
			"name"        => _x( 'Line-height', 'theme-options', 'azzu'.LANG_DN ),
			"id"        => "fonts-" . $id ."_line_height",
			"wrap"		=> array('', 'px'),
			"std"        => $opts['lh'], 
			"type"        => "slider",
		);

//		// checkbox
//		$options[] = array(
//			"desc"      => '',
//			"name"      => _x( 'Uppercase', 'theme-options', 'azzu'.LANG_DN ),
//			"id"        => 'fonts-' . $id . '_uppercase',
//			"interface"	=> array( '' => 'none', '1' => 'uppercase' ),
//			"type"      => 'checkbox',
//			"std"       => $opts['uc']
//		);
                
                $options[] = array( "type" => "block_end");

	}
        
        $options[] = array( "name" => "Custom fonts", "type" => "block_begin" );
            // Manual fonts
            $options[] = array(
                    "name"	=> _x( 'Font upload', 'theme-options', 'azzu'.LANG_DN ),
                    "id"	=> 'manual-fonts',
                    "type"	=> 'custom_fonts',
                    "desc"        => '',
                    'std'	=> array()
            );
        $options[] = array( "type" => "block_end");




        



