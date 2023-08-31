<?php
/**
 * Dropcaps shortcode.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode dropcaps class.
 *
 */
class AZU_Shortcode_Dropcaps extends AZU_Shortcode {

    static protected $instance;

    protected $shortcode_name = 'azu_dropcaps';

    public static function get_instance() {
        if ( !self::$instance ) {
            self::$instance = new AZU_Shortcode_Dropcaps();
        }
        return self::$instance;
    }

    public function __construct() {

        $this->addShortCode( $this->shortcode_name, array($this, 'shortcode') );
        $this->azu_vc_map();
    }

    public function shortcode( $atts, $content = null ) {
        $attributes = shortcode_atts( array(
            'dropcaps_text' => '',
            'padding' => 15,
            'font_size' => 36,
            'font_color' => '',
            'color_bg' => '',
            'border_color' => '',
            'border_radius' => 0,
            'border_width' => 2,
            'dc_italic' => '0',
            'font_weight' => '',  
            'dc_tag' => '',
            'el_class' => ''
        ), $atts );
        $style = "";
        // sanitize attributes
        $attributes['dropcaps_text'] = trim(esc_html($attributes['dropcaps_text']));
        $attributes['font_color'] = esc_attr($attributes['font_color']);
        $attributes['color_bg'] = esc_attr($attributes['color_bg']);
        $attributes['border_color'] = esc_attr($attributes['border_color']);
	$attributes['padding'] = absint($attributes['padding']);
        $attributes['font_size'] = absint($attributes['font_size']);
        $attributes['border_radius'] = absint($attributes['border_radius']);
        $attributes['border_width'] = absint($attributes['border_width']);
        $attributes['font_weight'] = in_array($attributes['font_weight'], array('100','300', '400', '600', '700', '900') ) ? $attributes['font_weight'] : '';
        $attributes['dc_tag'] = in_array($attributes['dc_tag'], array("h1","h2","h3","h4","h5","h6") ) ? $attributes['dc_tag'] : '';
        $attributes['dc_italic'] = apply_filters('azu_sanitize_flag', $attributes['dc_italic']);
        $attributes['el_class'] = esc_attr($attributes['el_class']);
        
        $class = 'azu_dropcaps_text';
        $class_dropcaps = 'azu_dropcaps';
        $dc_tag = 'span';
        if(!empty($attributes['el_class']))
                $class .= ' '.$attributes['el_class'];
        $style .= 'padding: '.$attributes['padding'].'px;';
        $style .= 'font-size: '.$attributes['font_size'].'px;';
        $width = $attributes['font_size'] + $attributes['padding'] * 2;
        if(strlen ($attributes['dropcaps_text'])==1){
            $style .= 'width: '.$width.'px;';
        }
        $style .= 'height: '.$width.'px;';
        $style .= 'border-radius: '.$attributes['border_radius'].'px;';
        if(!empty($attributes['font_color'])){
            $style .= 'color: '.$attributes['font_color'].';';
        }
        if(empty($attributes['color_bg'])){
            $attributes['color_bg'] = 'transparent';
        }
        $style .= 'background-color: '.$attributes['color_bg'].';';
        if(!empty($attributes['border_color'])){
            $style .= 'border-style: solid;';
            $style .= 'border-color: '.$attributes['border_color'].';';
            $style .= 'border-width: '.$attributes['border_width'].'px;';
        }

        if(!empty($attributes['dc_tag'])){
            $dc_tag = $attributes['dc_tag'];
        }
        else if(!empty($attributes['font_weight'])){
            $style .= 'font-weight: '.$attributes['font_weight'].';';
        }
        
        if($attributes['dc_italic']){
            $attributes['dropcaps_text'] = '<i>'.$attributes['dropcaps_text'].'</i>';
        }
        
        $output = '<p class="' . esc_attr( $class ) . '">';
        $output .= '<'.$dc_tag.' class="' . esc_attr( $class_dropcaps ) . '" style="'.esc_attr($style).'">'.$attributes['dropcaps_text'].'</'.$dc_tag.'>'.$content;
        $output .= '</p>';
       
        return $output;
    }
    
        
    // VC map function
    public function azu_vc_map() {
        if(!function_exists('vc_map')){ return; }
            // ! Dropcaps
            vc_map( array(
                    "name" => __("Dropcaps", 'azzu'.LANG_DN),
                    "base" => "azu_dropcaps",
                    "icon" => "azu_vc_ico_dropcaps",
                    "class" => "azu_vc_sc_dropcaps",
                    "description" => __("A large capital letter at the beginning of a text block",'azzu'.LANG_DN),
                    "category" => __('by Theme', 'azzu'.LANG_DN),
                    "params" => array(
                                array(			
                                        "type" => "textfield",
                                        "admin_label" => true,
                                        "class" => "",
                                        "heading" => __("Dropcaps Character", 'azzu'.LANG_DN),
                                        "param_name" => "dropcaps_text",
                                        "value" => ""
                                ),
                                // Gap
                                array(
                                        "type" => "azu_range",
                                        "class" => "",
                                        "heading" => __("Padding", 'azzu'.LANG_DN),
                                        "param_name" => "padding",
                                        "value" => 15,
                                        "min" => 0,
                                        "max" => 50,
                                        "suffix" => "px",
                                        "description" => __("Element paddings", 'azzu'.LANG_DN),
                                ),
                                // tag
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Tag Name", 'azzu'.LANG_DN),
                                        "param_name" => "dc_tag",
                                        "std" => "",
                                        "value" => array(
                                                __("Span",'azzu'.LANG_DN) => "",
                                                __("H1",'azzu'.LANG_DN) => "h1",
                                                __("H2",'azzu'.LANG_DN) => "h2",
                                                __("H3",'azzu'.LANG_DN) => "h3",
                                                __("H4",'azzu'.LANG_DN) => "h4",
                                                __("H5",'azzu'.LANG_DN) => "h5",
                                                __("H6",'azzu'.LANG_DN) => "h6"
                                        ),
                                        "description" => __("For SEO reasons you might need to define your titles tag names according to priority. Please note that H1 can only be used only once in a page due to the SEO reasons. So try to use lower than H2 to meet SEO best practices.",'azzu'.LANG_DN),
                                ),
                                // Font Size
                                array(
                                        "type" => "azu_range",
                                        "class" => "",
                                        "heading" => __("Font Size", 'azzu'.LANG_DN),
                                        "param_name" => "font_size",
                                        "value" => 36,
                                        "min" => 10,
                                        "max" => 72,
                                        "suffix" => "px"
                                ),
                                //font weight
                                array(
                                    "type" => "dropdown",
                                    "heading" => __("Font Weight", 'azzu'.LANG_DN) ,
                                    "param_name" => "font_weight",
                                    "value" => array(
                                        __('Default', 'azzu'.LANG_DN) => "",
                                        __('Thin', 'azzu'.LANG_DN) => "100",
                                        __('Light', 'azzu'.LANG_DN) => "300",
                                        __('Normal', 'azzu'.LANG_DN) => "400",
                                        __('SemiBold', 'azzu'.LANG_DN) => "600",
                                        __('Bold', 'azzu'.LANG_DN) => "700",
                                        __('Black', 'azzu'.LANG_DN) => "900",
                                    ) ,
                                    "dependency" => array(
                                            "element" => "dc_tag",
                                            "value" => array( "" )
                                    ),
                                    "description" => ""
                                ) ,
                                //italic
                                array(
                                    "type" => "azu_toggle",
                                    "heading" => __("Italic", 'azzu'.LANG_DN) ,
                                    "param_name" => "dc_italic",
                                    "std" => "no",
                                    "value" => array(
                                            "" => "yes"
                                    ),
                                    "description" => __("This option sets the background image is fixed", 'azzu'.LANG_DN) ,
                                ) ,
                                //Font Color
                                array(
                                        "type" => "colorpicker",
                                        "class" => "",
                                        "heading" => __("Font Color", 'azzu'.LANG_DN),
                                        "param_name" => "font_color",
                                        "value" => "",
                                        "description" => __("Select font color for dropcaps.", 'azzu'.LANG_DN)
                                ),
                                // Background Color
                                array(
                                        "type" => "colorpicker",
                                        "class" => "",
                                        "heading" => __("Background Color", 'azzu'.LANG_DN),
                                        "param_name" => "color_bg",
                                        "value" => "",
                                        "description" => __("Select background color for dropcaps.", 'azzu'.LANG_DN)
                                ),
                                // Border Color
                                array(
                                        "type" => "colorpicker",
                                        "class" => "",
                                        "heading" => __("Border Color", 'azzu'.LANG_DN),
                                        "param_name" => "border_color",
                                        "value" => "",
                                        "description" => __("Select border color for dropcaps.", 'azzu'.LANG_DN)
                                ),

                                // Border width
                                array(
                                        "type" => "azu_range",
                                        "class" => "",
                                        "heading" => __("Border width", 'azzu'.LANG_DN),
                                        "param_name" => "border_width",
                                        "value" => 2,
                                        "min" => 0,
                                        "max" => 10,
                                        "dependency" => Array("element" => "border_color", "not_empty" => true),
                                        "suffix" => "px"
                                ),
                                // Border Radius
                                array(
                                        "type" => "azu_number",
                                        "class" => "",
                                        "heading" => __("Border Radius", 'azzu'.LANG_DN),
                                        "param_name" => "border_radius",
                                        "value" => 0,
                                        "min" => 0,
                                        "max" => 9999,
                                        "suffix" => "px",
                                        "description" => __("set border radius for dropcaps.", 'azzu'.LANG_DN),
                                ),
                                // Add some description
                                array(
                                        "type" => "textarea_html",
                                        "class" => "",
                                        "admin_label" => true,
                                        "heading" => __("Text", 'azzu'.LANG_DN),
                                        "param_name" => "content",
                                        "value" => "",
                                        "description" => __("Text Wrap Around Drop Caps.", 'azzu'.LANG_DN)
                                ),
                                array(			
                                        "type" => "textfield",
                                        "class" => "",
                                        "heading" => __("Custom CSS Class", 'azzu'.LANG_DN),
                                        "param_name" => "el_class",
                                        "value" => "",
                                        "description" => __("Ran out of options? Need more styles? Write your own CSS and mention the class name here.", 'azzu'.LANG_DN),
                                )
                    )
            ) );
    }

}

// create shortcode
AZU_Shortcode_Dropcaps::get_instance();