<?php 

// ! File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;


// ! Removing unwanted shortcodes
vc_remove_element("vc_basic_grid");
vc_remove_element("vc_icon");
vc_remove_element("vc_posts_slider");
vc_remove_element("vc_media_grid");
vc_remove_element("vc_masonry_grid");
vc_remove_element("vc_masonry_media_grid");
vc_remove_element("vc_gallery");
vc_remove_element("vc_images_carousel");
vc_remove_element("vc_button");
vc_remove_element("vc_button2");
vc_remove_element("vc_cta_button");
vc_remove_element("vc_cta_button2");
vc_remove_element("vc_tabs");
vc_remove_element("vc_tour");
vc_remove_element("vc_accordion");

/**
 * Add class to VC shortcodes.
 *
 * @return string
 */
function azzu_shortcode_custom_css_filter_tag($class,$settings,$atts){
        if (strpos($class,'vc_progress_bar') !== false && isset($atts['text_position'])){
            if($atts['text_position'])
                $class .= ' azu-above-progress-bar';
        }
    
    
    return $class;
}
add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'azzu_shortcode_custom_css_filter_tag',3, 99 );

// ! Changing rows and columns classes
function custom_css_classes_for_vc_row_and_vc_column($class_string, $tag) {
	if ($tag=='vc_row' || $tag=='vc_row_inner') {
		$class_string = str_replace('vc_row-fluid', 'container-fluid nopadding', $class_string);
	}

	if ($tag=='vc_column' || $tag=='vc_column_inner') {
		if ( !(function_exists('vc_is_inline') && vc_is_inline()) ) {
			$class_string = preg_replace('/vc_span(\d{1,2})/', 'col-sm-$1', $class_string);
		}
	}

	return $class_string;
}
add_filter('vc_shortcodes_css_class', 'custom_css_classes_for_vc_row_and_vc_column', 10, 2);


// ! Adding our classes to paint standard VC shortcodes
function custom_css_accordion($class_string, $tag) {
	if ( in_array( $tag, array('vc_accordion', 'vc_toggle', 'vc_progress_bar', 'vc_tabs', 'vc_tour', 'vc_posts_slider') ) ) {
		$class_string .= ' azu-style';
	}

	return $class_string;
}
add_filter('vc_shortcodes_css_class', 'custom_css_accordion', 10, 2);

//// ! Background for widgetized area
//vc_add_param("vc_widget_sidebar", array(
//	"type" => "dropdown",
//	"class" => "",
//	"heading" => __("Show background", 'azzu'.LANG_DN),
//	"admin_label" => true,
//	"param_name" => "show_bg",
//	"value" => array(
//		"Yes" => "true",
//		"No" => "false"
//	),
//	"description" => ""
//));

//********************************************************************************************
// ROW START
//********************************************************************************************

vc_add_param("vc_row", array(
	"type" => "textfield",
	"heading" => __("Anchor", 'azzu'.LANG_DN),
	"param_name" => "anchor"
));

vc_add_param("vc_row", array(
	"type" => "textfield",
	"heading" => __("Minimum height", 'azzu'.LANG_DN),
	"param_name" => "min_height",
	"description" => __("You can use pixels (px) or percents (%).", 'azzu'.LANG_DN)
));

$group_name = 'Background'; //Design options

vc_add_param("vc_row", array(
	"type" => "dropdown",
	"heading" => __("Blurred background", 'azzu'.LANG_DN),
	"param_name" => "azu_blur",
        "value" => array(
                __("None", 'azzu'.LANG_DN) => "",
                __("Row", 'azzu'.LANG_DN) => "row",
                __("Columns", 'azzu'.LANG_DN) => "col"
        ),
        "group" => $group_name, 
	"description" => __("You can use blur background.", 'azzu'.LANG_DN)
));



//********************************************************************************************
// ROW END
//********************************************************************************************



// ! Progress bar
vc_add_param("vc_progress_bar", array(
	"type" => "dropdown",
	"heading" => __("Text position", 'azzu'.LANG_DN),
	"param_name" => "text_position",
        "value" => array(
            "On the bar" => "0",
            "Above the bar" => "1",
            ),
        "description" => ""
));




// ! Round Chart
vc_remove_param('vc_pie', 'color');
vc_add_param("vc_pie", 
		array(
			'type' => 'dropdown',
			'heading' => __( 'Color', 'azzu'.LANG_DN ),
			'param_name' => 'color',
			'value' => getVcShared( 'colors-dashed' ) + array( __( 'Accent', 'azzu'.LANG_DN ) => 'azu_accent', __( 'Custom', 'azzu'.LANG_DN ) => 'custom' ),
			'description' => __( 'Select pie chart color.', 'azzu'.LANG_DN ),
			'admin_label' => true,
			'param_holder_class' => 'vc_colored-dropdown',
			'std' => 'grey'
		)
);

vc_remove_param('vc_pie', 'custom_color');
vc_add_param("vc_pie", 
		array(
			'type' => 'colorpicker',
			'heading' => __( 'Custom color', 'azzu'.LANG_DN ),
			'param_name' => 'custom_color',
			'description' => __( 'Select custom color.', 'azzu'.LANG_DN ),
			'dependency' => array(
				'element' => 'color',
				'value' => array( 'custom' )
			),
		)
);


vc_add_param("vc_pie",                     
        // Icon
        array(
                "type" => "azu_iconpicker",
                "class" => "",
                "heading" => __("Icon", 'azzu'.LANG_DN),
                "param_name" => "azu_icon",
                "value" => '',
        )
);


// Theme icons for VC
add_filter( 'vc_iconpicker-type-linecons', array( 'azu_functions','azu_iconpicker_for_vc'),999 );

//add_action( 'vc_after_init', 'add_vc_icon_theme' );
//function add_vc_icon_theme() {
//  //Get current values param
//  $param = WPBMap::getParam( 'vc_icon', 'type' );
//  //Append new value to the 'value' array
//  $param['value'][__( 'By Theme', 'azzu'.LANG_DN )] = 'linecons';
//  //unset($param['value'][__( 'Linecons', 'js_composer' )]);
//  //Finally "mutate" param with new values
//  vc_update_shortcode_param( 'vc_icon', $param );
//}

