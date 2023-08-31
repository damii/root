<?php
/**
 * Meta Box connection
 *
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Add assistive text to text input.
 */
function azzu_meta_box_text_assistive_text( $field_html, $field, $meta ) {
	if ( !empty($field['assistive_text']) ) {
		$field_html .= '&nbsp;<small>' . $field['assistive_text'] . '</small>';
	}
	return $field_html;
}
add_filter( 'rwmb_text_html', 'azzu_meta_box_text_assistive_text', 10, 3 );

/**
 * Add some classes to meta box wrap.
 */
function azzu_meta_box_classes( $begin, $field, $meta ) {
	$classes = array(
		'rwmb-input-'.esc_attr($field['id'])
	);

	// compatibility with old scripts and styles
	switch ( $field['type'] ) {
		case 'radio':
			foreach( $field['options'] as $option ) {
				if ( is_array($option) ) { $classes[] = 'azu_radio-img'; break; }
			}
			
			break;
	}

	if ( !empty($field['show_on']) ) {
		$begin = str_replace('class="rwmb-field', 'data-show-on="' . esc_attr(implode(',', (array) $field['show_on'])) . '" class="rwmb-field hide-if-js', $begin);
	}

	if ( !empty($field['top_divider']) ) {
		$begin = '<div class="azu_hr"></div>' . $begin;
	}

	return str_replace('class="rwmb-input', 'class="rwmb-input ' . implode(' ', $classes), $begin);
}
add_filter('rwmb_begin_html', 'azzu_meta_box_classes', 10, 3);

/**
 * Add some classes to meta box wrap.
 */
function azzu_meta_box_classes_end_html( $end, $field, $meta ) {
	
	if ( !empty($field['bottom_divider']) ) {
		$end .= '<div class="azu_hr"></div>';
	}

	return $end;
}
add_filter('rwmb_end_html', 'azzu_meta_box_classes_end_html', 10, 3);

/**
 * Include Meta-Box framework.
 *
 */
require_once( RWMB_DIR . 'meta-box.php' );


/**
 * Register meta boxes
 */
function azzu_register_meta_boxes() {
	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( !class_exists( 'RW_Meta_Box' ) ) {
		return;
	}

	global $AZU_META_BOXES;
	foreach ( $AZU_META_BOXES as $meta_box ) {
		new RW_Meta_Box( $meta_box );
	}
}
add_action( 'admin_init', 'azzu_register_meta_boxes' );

/**
 * Localize meta boxes
 */
function azzu_localize_meta_boxes() {
	global $AZU_META_BOXES;

	$localized_meta_boxes = array();

	foreach ( $AZU_META_BOXES as $meta_box ) {
		$localized_meta_boxes[ $meta_box['id'] ] = isset($meta_box['only_on'], $meta_box['only_on']['template']) ? (array) $meta_box['only_on']['template'] : array(); 
	}
	wp_localize_script( 'azu-metabox', 'azuMetaboxes', $localized_meta_boxes );
}
add_action( 'admin_enqueue_scripts', 'azzu_localize_meta_boxes', 15 );

/**
 * Define default meta boxes for templates
 * 
 * @param  array $hidden Hidden Meta Boxes
 * @param  string|WP_Screen $screen Current screen
 * @param  bool $use_defaults Use default Meta Boxes or not
 * 
 * @return array Hidden Meta Boxes
 */
function azzu_hidden_meta_boxes( $hidden, $screen, $use_defaults ) {
	static $extra_hidden = null;

	// return saved result
	if ( null !== $extra_hidden ) return $extra_hidden;

	global $AZU_META_BOXES;
	$template = azuf()->azu_get_template_name();
	$meta_boxes = array();

	foreach ( $AZU_META_BOXES as $meta_box ) {

		// if field 'only_on' is empty - show metabox everywhere
		// if current template in templates list - show metabox
		if ( 
			empty($meta_box['only_on']) ||
			empty($meta_box['only_on']['template']) ||
			in_array($template, (array) $meta_box['only_on']['template'] )
		) {

			// find metabox id in hidden list
			$bad_key = array_search( $meta_box['id'], $hidden );

			// show current metabox
			if ( false !== $bad_key ) { unset($hidden[ $bad_key ]); }

			continue;
		}

		$meta_boxes[] = $meta_box['id'];
	}

	// save result
	$extra_hidden = $hidden;
	if( !empty($meta_boxes) ) {
		$extra_hidden = array_unique( array_merge($hidden, $meta_boxes) );
	}

	return $extra_hidden;
}
add_filter('hidden_meta_boxes', 'azzu_hidden_meta_boxes', 99, 3);
