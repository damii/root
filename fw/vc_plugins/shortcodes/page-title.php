<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode page_title class.
 *
 */
class AZU_Shortcode_Page_Title extends AZU_Shortcode {

    static protected $instance;

    protected $shortcode_name = 'azu_page_title';

    public static function get_instance() {
        if ( !self::$instance ) {
            self::$instance = new AZU_Shortcode_Page_Title();
        }
        return self::$instance;
    }

    public function __construct() {

        $this->addShortCode( $this->shortcode_name, array($this, 'shortcode') );
        $this->azu_vc_map();
    }

    public function shortcode( $atts, $content = null ) {
        
            $attributes = shortcode_atts( array(
                'page_title' => '',
                'page_subtitle' => '',
                'text_align' => '',
                'section_padding' => 80,
                'bg_color' => '#fff',
                'bg_image' => '',
                'bg_position' => '',
                'bg_cover' => '0',
                'bg_attach' => '0',
                'font_size' => 36,
                'letter_spacing' => 0,
                'font_color' => '#ccc',
                'font_weight' => '',
                'title_line' => '',
                'sub_font_size' => 18,
                'padding' => 10,
                'sub_font_color' => '#eee',
                'sub_font_weight' => '',
                'el_class' => ''
            ), $atts ); 
            
            $attributes['text_align'] = in_array($attributes['text_align'], array('left', 'right', 'center') ) ? $attributes['text_align'] : 'center';
            $attributes['font_weight'] = in_array($attributes['font_weight'], array('100','300', '400', '600', '700', '900') ) ? $attributes['font_weight'] : '';
            $attributes['sub_font_weight'] = in_array($attributes['sub_font_weight'], array('300', '400', '600', '700', '900') ) ? $attributes['sub_font_weight'] : '';
            $attributes['bg_position'] = in_array($attributes['bg_position'], array('left top', 'center top', 'right top','left center', 'right center', 'left bottom','center bottom', 'right bottom') ) ? $attributes['bg_position.'] : 'center center';
            $attributes['title_line'] = in_array($attributes['title_line'], array('vlong', 'vertical', 'underline') ) ? $attributes['title_line'] : '';
            $attributes['section_padding'] = absint($attributes['section_padding']);
            $attributes['font_size'] = absint($attributes['font_size']);
            $attributes['sub_font_size'] = absint($attributes['sub_font_size']);
            $attributes['padding'] = absint($attributes['padding']);
            $attributes['letter_spacing'] = intval($attributes['letter_spacing']);
            $attributes['el_class'] = esc_attr($attributes['el_class']);
            $attributes['bg_cover'] = apply_filters('azu_sanitize_flag', $attributes['bg_cover']);
            $attributes['bg_attach'] = apply_filters('azu_sanitize_flag', $attributes['bg_attach']);
            $attributes['page_title'] = empty($attributes['page_title']) ? get_the_title() : esc_html($attributes['page_title']);
            
            $class = 'azu-vc-pt';
            if(!empty($attributes['el_class']))
                $class .= ' '.$attributes['el_class'];
            $class .= ' azu-align-'.$attributes['text_align'];
            $class_title = 'azu-vc-pt-title';
            $bottom_padding = $attributes['section_padding'];
            if(!empty($attributes['title_line'])){
                $class_title .= ' azu-pt-'.$attributes['title_line']; 
                if($attributes['title_line']=='vlong')
                    $bottom_padding += 50;
            }
            $media_url ='';
            if(!empty($attributes['bg_image'])){
                $media_url = wp_get_attachment_image_src( $attributes['bg_image'], 'full' ); // returns an array 
                if($media_url)
                    $media_url = $media_url[0];
            }
            
            $style ='';
            $style .= 'background-color: '.$attributes['bg_color'].';';
            if($media_url)
                $style .= 'background-image: url("'.$media_url.'");';
            if($attributes['bg_attach'])
                $style .= 'background-attachment: fixed;';
            if($attributes['bg_cover'])
                $style .= 'background-size: cover;';
            $style .= 'background-position: '.$attributes['bg_position'].';';
            
            $style_content = '';
            $style_content .= 'padding-top: '.$attributes['section_padding'].'px;';

            $style_title = '';
            $style_title .= 'font-size: '.$attributes['font_size'].'px;';
            $style_title .= 'padding-bottom: '.$bottom_padding.'px;';
            $style_title .= 'color: '.$attributes['font_color'].';';
            if(!empty($attributes['font_weight']))
                $style_title .= 'font-weight: '.$attributes['font_weight'].';';
            if(!empty($attributes['letter_spacing']))
                $style_title .= 'letter-spacing: '.$attributes['letter_spacing'].'px;';
   
            $style_subtitle = '';
            $style_subtitle .= 'font-size: '.$attributes['sub_font_size'].'px;';
            $style_subtitle .= 'padding-bottom: '.$attributes['padding'].'px;';
            $style_subtitle .= 'color: '.$attributes['sub_font_color'].';';
            if(!empty($attributes['sub_font_weight']))
                $style_subtitle .= 'font-weight: '.$attributes['sub_font_weight'].';';
            
            $output  =  '<div class="'.esc_attr($class).'" style="'.esc_attr($style).'">';
            $output .= '    <div class="container">';
            $output .= '        <div class="azu-vc-pt-content" style="'.esc_attr($style_content).'">';

            if(!empty($attributes['page_subtitle']))
                $output .= '            <div class="azu-vc-pt-subtitle" style="'.esc_attr($style_subtitle).'">' .$attributes['page_subtitle']. '</div>';
            $output .= '                <h1 class="'.esc_attr($class_title).'" style="'.esc_attr($style_title).'" >' .$attributes['page_title']. '</h1>';
            $output .= '        </div>';
            $output .= '    </div>';
            $output .= '</div>';
            
            $output = '<div class="azu-full-width" style="overflow: visible;">' . $output . '</div>';
	    return $output;
    }
    
    // VC map function
    public function azu_vc_map() {
            if(!function_exists('vc_map')){ return; }
            
            //// ! Page_Title
            vc_map(
                array(
                   "name" => __("Page Title",'azzu'.LANG_DN),
                   "base" => "azu_page_title",
                   "class" => "azu_vc_sc_page_title",
                   "icon" => "azu_vc_ico_page_title",
                   "description" => __("Page Title in Header",'azzu'.LANG_DN),
                   "category" => __("by Theme",'azzu'.LANG_DN),
                   "content_element" => true,
                   "show_settings_on_create" => true,
                   "params" => array(
                                    array(
                                        "type" => "textfield",
                                        "heading" => __("Page Title", 'azzu'.LANG_DN) ,
                                        "param_name" => "page_title",
                                        "value" => "",
                                        "description" => __("Get default title when stay empty", 'azzu'.LANG_DN)
                                    ) ,
                                    array(
                                        "type" => "textfield",
                                        "heading" => __("Page Subtitle", 'azzu'.LANG_DN) ,
                                        "param_name" => "page_subtitle",
                                        "value" => "",
                                        "description" => __("Enter the subtitle of your page (optional)", 'azzu'.LANG_DN)
                                    ) ,
                                    array(
                                        "type" => "azu_range",
                                        "heading" => __("Section Padding", 'azzu'.LANG_DN) ,
                                        "param_name" => "section_padding",
                                        "value" => "80",
                                        "min" => "0",
                                        "max" => "800",
                                        "step" => "1",
                                        "unit" => 'px',
                                        "description" => ""
                                    ) ,
                                    array(
                                        "type" => "colorpicker",
                                        "heading" => __("Background color", 'azzu'.LANG_DN) ,
                                        "param_name" => "bg_color",
                                        "value" => "#fff",
                                        "description" => ""
                                    ) ,
                                    array(
                                        "type" => "attach_image",
                                        "heading" => __("Background Image", 'azzu'.LANG_DN) ,
                                        "param_name" => "bg_image",
                                        "value" => "",
                                        "description" => "" 
                                    ) ,
                                    array(
                                        "type" => "dropdown",
                                        "heading" => __("Background Position", 'azzu'.LANG_DN) ,
                                        "param_name" => "bg_position",
                                        "std" => "center center",
                                        "value" => array(
                                            __('Left Top', 'azzu'.LANG_DN) => "left top",
                                            __('Center Top', 'azzu'.LANG_DN) => "center top",
                                            __('Right Top', 'azzu'.LANG_DN) => "right top",
                                            __('Left Center', 'azzu'.LANG_DN) => "left center",
                                            __('Center Center', 'azzu'.LANG_DN) => "center center",
                                            __('Right Center', 'azzu'.LANG_DN) => "right center",
                                            __('Left Bottom', 'azzu'.LANG_DN) => "left bottom",
                                            __('Center Bottom', 'azzu'.LANG_DN) => "center bottom",
                                            __('Right Bottom', 'azzu'.LANG_DN) => "right bottom"
                                        ) ,
                                        "description" => __("First value defines horizontal position and second vertical position.", 'azzu'.LANG_DN) ,
                                        "dependency" => array(
                                            'element' => "bg_image",
                                            'not_empty' => true
                                        )
                                    ) ,
                                    array(
                                        "type" => "azu_toggle",
                                        "heading" => __('Cover whole background', 'azzu'.LANG_DN) ,
                                        "description" => __("Scale the background image to be as large as possible so that the background area is completely covered by the background image. Some parts of the background image may not be in view within the background positioning area.", 'azzu'.LANG_DN) ,
                                        "param_name" => "bg_cover",
                                        "std" => "no",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "dependency" => array(
                                            'element' => "bg_image",
                                            'not_empty' => true
                                        )
                                    ) ,
                                    array(
                                        "type" => "azu_toggle",
                                        "heading" => __("Background Attachment", 'azzu'.LANG_DN) ,
                                        "param_name" => "bg_attach",
                                        "std" => "no",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "dependency" => array(
                                            'element' => "bg_image",
                                            'not_empty' => true
                                        ),
                                        "description" => __("This option sets the background image is fixed", 'azzu'.LANG_DN) ,
                                    ) ,
                                    array(
                                        "type" => "dropdown",
                                        "heading" => __("Text Align", 'azzu'.LANG_DN) ,
                                        "param_name" => "text_align",
                                        "value" => array(
                                            __('Center', 'azzu'.LANG_DN) => "center",
                                            __('Left', 'azzu'.LANG_DN) => "left",
                                            __('Right', 'azzu'.LANG_DN) => "right"
                                        ) ,
                                        "description" => ""
                                    ) ,
                                    array(
                                        "type" => "azu_range",
                                        "heading" => __("Title Font Size", 'azzu'.LANG_DN) ,
                                        "param_name" => "font_size",
                                        "min" => "0",
                                        "max" => "100",
                                        "step" => "1",
                                        "unit" => 'px',
                                        "value" => "40"
                                    ) ,
                                    array(
                                        "type" => "azu_range",
                                        "heading" => __("Title Letter Spacing", 'azzu'.LANG_DN) ,
                                        "param_name" => "letter_spacing",
                                        "min" => "-20",
                                        "max" => "20",
                                        "step" => "1",
                                        "unit" => 'px/10',
                                        "value" => "0"
                                    ) ,
                                    array(
                                        "type" => "colorpicker",
                                        "heading" => __("Title Color", 'azzu'.LANG_DN) ,
                                        "param_name" => "font_color",
                                        "value" => "#ccc",
                                        "description" => ""
                                    ) ,
                                    array(
                                        "type" => "dropdown",
                                        "heading" => __("Title Font Weight", 'azzu'.LANG_DN) ,
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
                                        "description" => ""
                                    ) ,
                                    array(
                                        "type" => "dropdown",
                                        "heading" => __('Title line', 'azzu'.LANG_DN) ,
                                        "description" => "" ,
                                        "param_name" => "title_line",
                                        "value" => array(
                                            __('None', 'azzu'.LANG_DN) => "",
                                            __('Underline', 'azzu'.LANG_DN) => "underline",
                                            __('Vertical line', 'azzu'.LANG_DN) => "vertical",
                                            __('Vertical long line', 'azzu'.LANG_DN) => "vlong"
                                        ) 
                                    ) ,

                                    array(
                                        "type" => "azu_range",
                                        "heading" => __("Subtitle Font Size", 'azzu'.LANG_DN) ,
                                        "param_name" => "sub_font_size",
                                        "min" => "10",
                                        "max" => "100",
                                        "step" => "1",
                                        "unit" => 'px',                                        
                                        "dependency" => array(
                                            'element' => "page_subtitle",
                                            'not_empty' => true
                                        ),
                                        "value" => "18"
                                    ) ,
                                    array(
                                        "type" => "azu_range",
                                        "heading" => __("Subtitle Padding", 'azzu'.LANG_DN) ,
                                        "param_name" => "padding",
                                        "min" => "0",
                                        "max" => "50",
                                        "step" => "1",
                                        "unit" => 'px',
                                        "dependency" => array(
                                            'element' => "page_subtitle",
                                            'not_empty' => true
                                        ),
                                        "value" => "10"
                                    ) ,
                                    array(
                                        "type" => "colorpicker",
                                        "heading" => __("Subtitle Color", 'azzu'.LANG_DN) ,
                                        "param_name" => "sub_font_color",
                                        "value" => "#eee",
                                        "dependency" => array(
                                            'element' => "page_subtitle",
                                            'not_empty' => true
                                        ),
                                        "description" => ""
                                    ) ,
                                    array(
                                        "type" => "dropdown",
                                        "heading" => __("Subtitle Font Weight", 'azzu'.LANG_DN) ,
                                        "param_name" => "sub_font_weight",
                                        "value" => array(
                                            __('Default', 'azzu'.LANG_DN) => "",
                                            __('Light', 'azzu'.LANG_DN) => "300",
                                            __('Normal', 'azzu'.LANG_DN) => "400",
                                            __('SemiBold', 'azzu'.LANG_DN) => "600",
                                            __('Bold', 'azzu'.LANG_DN) => "700",
                                            __('Extra Bold', 'azzu'.LANG_DN) => "900",
                                        ) ,
                                        "dependency" => array(
                                            'element' => "page_subtitle",
                                            'not_empty' => true
                                        ),
                                        "description" => ""
                                    ) ,
                                    array(
                                        "type" => "textfield",
                                        "heading" => __("Extra class name", 'azzu'.LANG_DN) ,
                                        "param_name" => "el_class",
                                        "value" => "",
                                        "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'azzu'.LANG_DN)
                                    )
                                )   
            ));
    }

}

// create shortcode
AZU_Shortcode_Page_Title::get_instance();
