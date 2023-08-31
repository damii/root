<?php
/**
 * One click demo import.
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Page definition.
 */
$options[] = array(
		"page_title"	=> _x( "Import & Export", 'theme-options', 'azzu'.LANG_DN ),
		"menu_title"	=> _x( "Import & Export", 'theme-options', 'azzu'.LANG_DN ),
		"menu_slug"		=> "of-importexport-menu",
		"type"			=> "page"
);

/**
 * Heading definition.
 */
$options[] = array( "name" => _x('Import &amp; Export', 'theme-options', 'azzu'.LANG_DN), "type" => "heading" );

/**
 * Import / export.
 */
$options[] = array(	"name" => _x('Import &amp; export', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

	$options[] = array(
		"settings"	=> array( 'rows' => 16 ),
		"id"		=> 'import_export',
		"std"		=> '',
                "theme_customizer" => false,
		"type"		=> 'import_export_options',
	);

$options[] = array(	"type" => "block_end");