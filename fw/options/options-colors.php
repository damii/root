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
	"page_title"	=> _x( "Colors", 'theme-options', 'azzu'.LANG_DN ),
	"menu_title"	=> _x( "Colors", 'theme-options', 'azzu'.LANG_DN ),
	"menu_slug"		=> "of-color-menu",
	"type"			=> "page"
);

/**
 * Heading definition.
 */
$options[] = array( "name" => _x('Colors', 'theme-options', 'azzu'.LANG_DN), "type" => "heading" );

/**
 * Text.
 */
$options[] = array(	"name" => _x('Color chooser: Drag & Drop', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );
	
	$listbox_color = azuf()->azzu_themeoptions_get_color_defaults();
        
        $listbox_color = apply_filters( 'azu_listbox_color', $listbox_color );
	
	$listbox_color_std = azuf()->azzu_themeoptions_get_color_group();
        
        $listbox_color_std = apply_filters( 'azu_listbox_color_std', $listbox_color_std );
        
	
		// title
		$options[] = array(
			"type" => 'title',
			"name" =>_x( 'Default color values', 'theme-options', 'azzu'.LANG_DN ),
		);
	
	foreach ( $listbox_color_std as $i=>$std ) {
                $listbox_default= false;
		// divider
		if($i>1)
			$options[] = array(
				"type" => 'divider'
			);
                else 
                       $listbox_default= true; 
                
		// colorpicker
		$options[] = array(
			"desc" => '',
			"name"	=> _x( 'Color group ', 'theme-options', 'azzu'.LANG_DN ).$i,
			"id"	=> "color".$i,
			"std"	=> isset($std['color']) ? $std['color'] : "#ffffff",
                        "theme_customizer" => !$listbox_default,
                        "listbox_default" => $listbox_default,
			"type"	=> "color"
		);
		
		// listbox
		$options[] = array(
			"desc" => '',
			"name"	=> '', //&nbsp;
			"id"	=> "listbox_color".$i,
			"std"	=> $std['group'],
			"options" => $listbox_color,
			"mode" => 'color',
			"type"	=> "listbox"
		);
	}

$options[] = array(	"type" => "block_end");


