<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode icon class.
 *
 */
class AZU_Shortcode_Icon extends AZU_Shortcode {

    static protected $instance;

    protected $shortcode_name = 'azu_icon';

    public static function get_instance() {
        if ( !self::$instance ) {
            self::$instance = new AZU_Shortcode_Icon();
        }
        return self::$instance;
    }

    public function __construct() {

        $this->addShortCode( $this->shortcode_name, array($this, 'shortcode') );
        $this->azu_vc_map();
    }

    public function shortcode( $atts, $content = null ) {
       $attributes = shortcode_atts( array(
		'icon' => '',
                'icon_size' => 20,
                'icon_color' => '#777777',
                'icon_link' => '',
                'icon_hover' => '',
                    'icon_style' => 'none',
                    'icon_color_bg' => '#ffffff',
                    'icon_border_style' => '',
                    'icon_color_border' => '#777777',
                    'icon_border_size' => 1,
                    'icon_border_radius' => 0,
                    'icon_border_spacing' => 10,
                'icon_type' => '',
                'title' => '',
                'title_tag' => '',
                'text_align' => '',
                'text_color' => '#777777',
                'css'  => '',
                'el_class' => ''
	    ), $atts );
            
            $attributes['icon'] = esc_attr($attributes['icon']);
            $attributes['icon_size'] = absint($attributes['icon_size']);
            $attributes['icon_color'] = empty($attributes['icon_color']) ? '#777777' : $attributes['icon_color'];
            $attributes['icon_hover'] = in_array($attributes['icon_hover'], array("accent","zoom","spin","mix") ) ? $attributes['icon_hover'] : '';
            $attributes['icon_style'] = in_array($attributes['icon_style'], array("none","advanced") ) ? $attributes['icon_style'] : 'none';
            $attributes['icon_color_bg'] = empty($attributes['icon_color_bg']) ? '' : $attributes['icon_color_bg'];
            $attributes['icon_border_style'] = in_array($attributes['icon_border_style'], array("solid","dashed","dotted","double","inset","outset") ) ? $attributes['icon_border_style'] : '';
            $attributes['icon_color_border'] = empty($attributes['icon_color_border']) ? '' : $attributes['icon_color_border'];
            $attributes['icon_border_size'] = absint($attributes['icon_border_size']);
            $attributes['icon_border_radius'] = absint($attributes['icon_border_radius']);
            $attributes['icon_border_spacing'] = absint($attributes['icon_border_spacing']);
            $attributes['icon_type'] = in_array($attributes['icon_type'], array("icon","infobox","iconbox") ) ? $attributes['icon_type'] : 'icon';
            $attributes['title'] = esc_html($attributes['title']);
            $attributes['title_tag'] = in_array($attributes['title_tag'], array("h1","h2","h3","h4","h5","h6") ) ? $attributes['title_tag'] : 'h5';
            $attributes['text_align'] = in_array($attributes['text_align'], array('left', 'right', 'center') ) ? $attributes['text_align'] : 'left';
            $attributes['el_class'] = esc_attr($attributes['el_class']);
            
            $container_style = 'color: ' . esc_attr($attributes['icon_color']) . ';';
            
            
            $class = 'azu-icon';
            $class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $attributes['css'], ' ' ), "azu_button", $atts );
            
            if(!empty($attributes['el_class']))
                $class .= ' '.$attributes['el_class'];
            $class .= ' azu-align-'.$attributes['text_align'];
            $class .= ' azu-ico-'.$attributes['icon_type'];
            $style = 'font-size: ' . absint($attributes['icon_size']) . 'px;';
            $style_wrap = "";
            $size = $attributes['icon_size'];
            if($attributes['icon_style']=='advanced'){
                if(!empty($attributes['icon_color_bg']))
                    $container_style .='background-color: '.$attributes['icon_color_bg'].';';
                $style .= 'padding: '.$attributes['icon_border_spacing'].'px;';
                $size += $attributes['icon_border_spacing']*2;
                if(!empty($attributes['icon_border_style'])){
                    if(!empty($attributes['icon_color_border']))
                        $style .='border-color: '.$attributes['icon_color_border'].';';
                    $style .= 'border-width: '.$attributes['icon_border_size'].'px;';
                    $style .= 'border-style: '.$attributes['icon_border_style'].';';
                    $style .= 'border-radius: '.$attributes['icon_border_radius'].'px;';
                    $size += $attributes['icon_border_size']*2;
                }
                $style .= 'height: '.$size.'px; width:'.$size.'px;';
            }
            $link_sufix = $link_prefix ='';
            if($attributes['icon_link'] !== ''){
                    $href = vc_build_link($attributes['icon_link']);
                    $target = (isset($href['target']) && !empty($href['target'])) ? 'target="'.esc_attr($href['target']).'"' : '';
                    $link_prefix .= '<a href = "'.$href['url'].'" '.$target.' title="'.$href['title'].'">';
                    $link_sufix .= '</a>';
            }
            
            if($attributes['icon_type'] == 'iconbox'){
                $style .= 'max-width: ' . ($attributes['icon_size'] + 5) . 'px;';
                $style_wrap .= 'width: ' . $attributes['icon_size'] . 'px;';
            }
            
            
            $text = '';
            $text_style ='color: '.$attributes['text_color'].';';
            if(!empty($attributes['title']))
            {
                $text .= sprintf('<%2$s style="%3$s">%1$s</%2$s>' ,$attributes['title'],$attributes['title_tag'],$text_style);
            }
            if(!empty($content))
            {
                $text .= sprintf('<div style="%2$s">%1$s</div>' ,$content,$text_style);
            }
            if(!empty($text))
            {
                $text = sprintf('<div class="azu-text-wrapper">%s</div>' ,$text);
            }
            if(!empty($attributes['icon_hover']))
                    $class .= ' azu-icon-hover-'.$attributes['icon_hover'];
	    $output = $link_prefix.'<div class="'.$class.'" style="'.$container_style.'"><div class="azu-icon-wrapper" style="'.esc_attr($style_wrap).'"><div style="'.esc_attr($style).'"><i class="'.$attributes['icon'].'" ></i></div></div>'.$text.'</div>'.$link_sufix;
	    
            if(!empty($attributes['title'])){
                $output = '<div class="vc-row">'.$output.'</div>';
            }
            
	    return $output;
    }
    
    // VC map function
    public function azu_vc_map() {
            if(!function_exists('vc_map')){ return; }
            //// ! Icon
            vc_map(
                array(
                   "name" => __("Icon",'azzu'.LANG_DN),
                   "base" => "azu_icon",
                   "class" => "azu_vc_sc_icon",
                   "icon" => "azu_vc_ico_icon",
                   "description" => __("Just icon, info box & icon box",'azzu'.LANG_DN),
                   "category" => __("by Theme",'azzu'.LANG_DN),
                   "content_element" => true,
                   "show_settings_on_create" => true,
                   "params" => array(							
                                // Icon
                                array(
                                        "type" => "azu_iconpicker",
                                        "class" => "",
                                        "heading" => __("Icon", 'azzu'.LANG_DN),
                                        "param_name" => "icon",
                                        "value" => '',
                                ),
                                array(
                                        "type" => "azu_range",
                                        "class" => "",
                                        "heading" => __("Size of Icon", 'azzu'.LANG_DN),
                                        "param_name" => "icon_size",
                                        "value" => 20,
                                        "min" => 10,
                                        "max" => 72,
                                        "suffix" => "px",
                                        "description" => __("How big would you like it?", 'azzu'.LANG_DN),
                                ),
                                array(
                                        "type" => "colorpicker",
                                        "class" => "",
                                        "heading" => __("Color", 'azzu'.LANG_DN),
                                        "param_name" => "icon_color",
                                        "value" => "#777777",
                                        "description" => __("Give it a nice paint!", 'azzu'.LANG_DN),
                                ),
                                array(
                                        "type" => "vc_link",
                                        "class" => "",
                                        "heading" => __("Link ", 'azzu'.LANG_DN),
                                        "param_name" => "icon_link",
                                        "value" => "",
                                        "description" => __("Add a custom link or select existing page. You can remove existing link as well.", 'azzu'.LANG_DN),
                                ),
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Hover", 'azzu'.LANG_DN),
                                        "param_name" => "icon_hover",
                                        "value" => array(
                                                __("None",'azzu'.LANG_DN) => "",
                                                __("Accent color",'azzu'.LANG_DN) => "accent",
                                                __("Zoom",'azzu'.LANG_DN) => "zoom",
                                                __("Spin",'azzu'.LANG_DN) => "spin",
                                                __("Mix",'azzu'.LANG_DN) => "mix",
                                        ),
                                        "description" => __("Hover effects with icon & text.",'azzu'.LANG_DN),
                                ),
                                        array(
                                                "type" => "dropdown",
                                                "class" => "",
                                                "heading" => __("Icon Style", 'azzu'.LANG_DN),
                                                "param_name" => "icon_style",
                                                "value" => array(
                                                        __("Simple",'azzu'.LANG_DN) => "none",
                                                        __("Design your own",'azzu'.LANG_DN) => "advanced",
                                                ),
                                                "description" => __("Create your own with various options.", 'azzu'.LANG_DN),
                                        ),
                                        array(
                                                "type" => "colorpicker",
                                                "class" => "",
                                                "heading" => __("Background Color", 'azzu'.LANG_DN),
                                                "param_name" => "icon_color_bg",
                                                "value" => "#ffffff",
                                                "description" => __("Select background color for icon.", 'azzu'.LANG_DN),	
                                                "dependency" => Array("element" => "icon_style", "value" => array("advanced")),
                                        ),
                                        array(
                                                "type" => "dropdown",
                                                "class" => "",
                                                "heading" => __("Icon Border Style", 'azzu'.LANG_DN),
                                                "param_name" => "icon_border_style",
                                                "value" => array(
                                                        __("None",'azzu'.LANG_DN) => "",
                                                        __("Solid",'azzu'.LANG_DN) => "solid",
                                                        __("Dashed",'azzu'.LANG_DN) => "dashed",
                                                        __("Dotted",'azzu'.LANG_DN) => "dotted",
                                                        __("Double",'azzu'.LANG_DN) => "double",
                                                        __("Inset",'azzu'.LANG_DN) => "inset",
                                                        __("Outset",'azzu'.LANG_DN) => "outset",
                                                ),
                                                "description" => __("Select the border style for icon.",'azzu'.LANG_DN),
                                                "dependency" => Array("element" => "icon_style", "value" => array("advanced")),
                                        ),
                                        array(
                                                "type" => "colorpicker",
                                                "class" => "",
                                                "heading" => __("Border Color", 'azzu'.LANG_DN),
                                                "param_name" => "icon_color_border",
                                                "value" => "#777777",
                                                "description" => __("Select border color for icon.", 'azzu'.LANG_DN),	
                                                "dependency" => Array("element" => "icon_border_style", "not_empty" => true),
                                        ),
                                        array(
                                                "type" => "azu_range",
                                                "class" => "",
                                                "heading" => __("Border Width", 'azzu'.LANG_DN),
                                                "param_name" => "icon_border_size",
                                                "value" => 1,
                                                "min" => 1,
                                                "max" => 10,
                                                "suffix" => "px",
                                                "description" => __("Thickness of the border.", 'azzu'.LANG_DN),
                                                "dependency" => Array("element" => "icon_border_style", "not_empty" => true),
                                        ),
                                        array(
                                                "type" => "azu_number",
                                                "class" => "",
                                                "heading" => __("Border Radius", 'azzu'.LANG_DN),
                                                "param_name" => "icon_border_radius",
                                                "value" => 0,
                                                "min" => 1,
                                                "max" => 1000,
                                                "suffix" => "px",
                                                "description" => __("0 pixel value will create a square border. As you increase the value, the shape convert in circle slowly. (e.g 1000 pixels).", 'azzu'.LANG_DN),
                                                "dependency" => Array("element" => "icon_border_style", "not_empty" => true),
                                        ),
                                        array(
                                                "type" => "azu_number",
                                                "class" => "",
                                                "heading" => __("Background padding", 'azzu'.LANG_DN),
                                                "param_name" => "icon_border_spacing",
                                                "value" => 10,
                                                "min" => 30,
                                                "max" => 1000,
                                                "suffix" => "px",
                                                "description" => __("Spacing from the icon till the boundary of border / background", 'azzu'.LANG_DN),
                                                "dependency" => Array("element" => "icon_style", "value" => array("advanced")),
                                        ),
                                array(
                                    "type" => "dropdown",
                                    "heading" => __("Element type", 'azzu'.LANG_DN) ,
                                    "param_name" => "icon_type",
                                    "value" => array(
                                        __('Just Icon', 'azzu'.LANG_DN) => "icon",
                                        __('Info box', 'azzu'.LANG_DN) => "infobox",
                                        __('Icon box', 'azzu'.LANG_DN) => "iconbox"
                                    ) ,
                                    "description" => ""
                                ) ,
                                array(
                                    "type" => "textfield",
                                    "heading" => __("Title", 'azzu'.LANG_DN),
                                    "param_name" => "title",
                                    "value" => "",
                                    "dependency" => Array("element" => "icon_type", "value" => array("infobox","iconbox")),
                                    "description" => __("Enter the title", 'azzu'.LANG_DN)
                                ) ,
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Tag Name", 'azzu'.LANG_DN),
                                        "param_name" => "title_tag",
                                        "std" => "h5",
                                        "value" => array(
                                                __("H1",'azzu'.LANG_DN) => "h1",
                                                __("H2",'azzu'.LANG_DN) => "h2",
                                                __("H3",'azzu'.LANG_DN) => "h3",
                                                __("H4",'azzu'.LANG_DN) => "h4",
                                                __("H5",'azzu'.LANG_DN) => "h5",
                                                __("H6",'azzu'.LANG_DN) => "h6"
                                        ),
                                        "description" => __("For SEO reasons you might need to define your titles tag names according to priority. Please note that H1 can only be used only once in a page due to the SEO reasons. So try to use lower than H2 to meet SEO best practices.",'azzu'.LANG_DN),
                                        "dependency" => Array("element" => "title", "not_empty" => true),
                                ),
                                array(
                                    "type" => "dropdown",
                                    "heading" => __("Text Align", 'azzu'.LANG_DN) ,
                                    "param_name" => "text_align",
                                    "value" => array(
                                        __('Left', 'azzu'.LANG_DN) => "left",
                                        __('Center', 'azzu'.LANG_DN) => "center",
                                        __('Right', 'azzu'.LANG_DN) => "right"
                                    ) ,
                                    "dependency" => Array("element" => "icon_type", "value" => array("infobox","iconbox")),
                                    "description" => ""
                                ) ,
                                // Add some description
                                array(
                                        "type" => "textarea_html",
                                        "class" => "",
                                        "heading" => __("Description", 'azzu'.LANG_DN),
                                        "param_name" => "content",
                                        "value" => "",
                                        "dependency" => Array("element" => "icon_type", "value" => array("infobox","iconbox")),
                                        "description" => __("Provide the description for this icon.", 'azzu'.LANG_DN)
                                ),
                                array(
                                        "type" => "colorpicker",
                                        "class" => "",
                                        "heading" => __("Text color", 'azzu'.LANG_DN),
                                        "param_name" => "text_color",
                                        "value" => "#777777",
                                        "dependency" => Array("element" => "icon_type", "value" => array("infobox","iconbox")),
                                        "description" => __("Color of title & content text", 'azzu'.LANG_DN),
                                ),
                                array(			
                                        "type" => "textfield",
                                        "class" => "",
                                        "heading" => __("Custom CSS Class", 'azzu'.LANG_DN),
                                        "param_name" => "el_class",
                                        "value" => "",
                                        "description" => __("Ran out of options? Need more styles? Write your own CSS and mention the class name here.", 'azzu'.LANG_DN),
                                ),
                                array(
                                    'type' => 'css_editor',
                                    'heading' => __( 'Css', 'azzu'.LANG_DN ),
                                    'param_name' => 'css',
                                    'group' => __( 'Design', 'azzu'.LANG_DN ),
                                    'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border',
                                ),
                        ),
                )
            );
    }

}

// create shortcode
AZU_Shortcode_Icon::get_instance();
