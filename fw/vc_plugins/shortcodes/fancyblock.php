<?php
/**
 * Fancyblock shortcode.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode fancyblock class.
 *
 */
class AZU_Shortcode_Fancyblock extends AZU_Shortcode {

    static protected $instance;

    protected $shortcode_name = 'azu_fancyblock';

    public static function get_instance() {
        if ( !self::$instance ) {
            self::$instance = new AZU_Shortcode_Fancyblock();
        }
        return self::$instance;
    }

    public function __construct() {

        $this->addShortCode( $this->shortcode_name, array($this, 'shortcode') );
        $this->azu_vc_map();
    }

    public function shortcode( $atts, $content = null ) {
        $attributes = shortcode_atts( array(
            'animation' => '',
            'ani_distance' => 100,
            'ani_duration' => 300,
            'width' => '',
            'height' => '',
            'position' => 0,  
            'pos_vertical' => 0,
            'pos_horizontal' => 0,  
            'pos_rotate' => 0,
            'ani_delay' => 0,
            'ani_count' => 1,
            'overflow' => 1,
            'zindex' => 0,
            'bg_color' => '',
            'border_color' => '',
            'border_width' => 1,
            'border_radius' => 0,
            //'opacity' => '0',
            'viewport_pos' => 80,
            'el_class' => '',
            'css' => ''
        ), $atts );
        
        
        // sanitize attributes
        $attributes['animation'] = in_array($attributes['animation'], array('slideup', 'slidedown', 'slideleft', 'slideright', 'infinitezoom', 'parallax', 'parallax grayscale', 'parallax inside') ) ? $attributes['animation'] : '';
        $attributes['ani_duration'] = absint($attributes['ani_duration'])/1000;
        $attributes['ani_distance'] = intval($attributes['ani_distance']);
        $attributes['pos_rotate'] = intval($attributes['pos_rotate']);
        $attributes['pos_vertical'] = intval($attributes['pos_vertical']);
        $attributes['pos_horizontal'] = intval($attributes['pos_horizontal']);
        $attributes['zindex'] = intval($attributes['zindex']);
        $attributes['bg_color'] = esc_attr( $attributes['bg_color'] );
        $attributes['border_color'] = esc_attr( $attributes['border_color'] );
        $attributes['border_width'] = absint( $attributes['border_width'] );
        $attributes['border_radius'] = absint( $attributes['border_radius'] );
        $attributes['position'] = absint($attributes['position']);
        $attributes['width'] = esc_attr($attributes['width']);
        $attributes['height'] = esc_attr($attributes['height']);
        $attributes['ani_delay'] = absint($attributes['ani_delay'])/1000;
        $attributes['overflow'] = apply_filters('azu_sanitize_flag', $attributes['overflow']);
        $attributes['ani_count'] = absint($attributes['ani_count']);
        //$attributes['opacity'] = apply_filters('azu_sanitize_flag', $attributes['opacity']);
        $attributes['viewport_pos'] = absint($attributes['viewport_pos']);
        $attributes['el_class'] = esc_attr($attributes['el_class']);
        
        $style = "";
        $style_mask = "";
        $style_container = "";
        $class = 'azu_fancyblock';
        $class_wrap = 'azu_fb_wrap';
        
        $class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $attributes['css'], ' ' ), "azu_fancyblock", $atts );
        
        if($attributes['position'] == 0 || $attributes['position'] == 1) {
            $style .= ' top: '.$attributes['pos_vertical']. 'px;';
        }
        if($attributes['position'] == 2 || $attributes['position'] == 3) {
            $style .= ' bottom: '.$attributes['pos_vertical']. 'px;';
        }
        if($attributes['position'] == 0 || $attributes['position'] == 3) {
            $style .= ' left: '.$attributes['pos_horizontal']. 'px;';
        }
        if($attributes['position'] == 1 || $attributes['position'] == 2) {
            $style .= ' right: '.$attributes['pos_horizontal']. 'px;';
        }
        
        if(!empty($attributes['width'])) {
            $style .= ' width: '.$attributes['width'].(is_numeric($attributes['width']) ? "px" : "").';';
        }
        if(!empty($attributes['height'])) {
            if($attributes['position'] < 4 && $attributes['height'] !== 'auto'){
                if( $attributes['animation'] == "slideup") {
                    $style_container .= ' padding-bottom: calc('.$attributes['height'].(is_numeric($attributes['height']) ? "px" : "").' - '.absint($attributes['pos_vertical']).'px);';
                }
                else {
                    $style_container .= ' padding-bottom: '.$attributes['height'].(is_numeric($attributes['height']) ? "px" : "").';';
                }
                if (strpos($attributes['height'], '%') !== FALSE)
                        $attributes['height'] = '100%';
            }
            $style .= ' height: '.$attributes['height'].(is_numeric($attributes['height']) ? "px" : "").';';
        }
        
        if($attributes['ani_delay'] > 0) {
            $style .= ' -webkit-transition-delay: '.$attributes['ani_delay'].'s;transition-delay: '.$attributes['ani_delay'].'s;';
        }
        
        if(!empty($attributes['border_color'])){
            $style .= ' border-style: solid;';
            $style .= ' border-color: '.$attributes['border_color'].';';
            $style .= ' border-width: '.$attributes['border_width'].'px;';
        }
        
        if(!empty($attributes['animation'])){
            $class .= " azu-".$attributes['animation'];
        }
        if(empty($attributes['animation']) || $attributes['animation']=="infinitezoom"){
                $class .= " disable-animation";
        }
        
        if(!empty($attributes['bg_color'])){
                $style .= ' background-color: '.$attributes['bg_color'].';';
                $style_mask .= ' background-color: '.$attributes['bg_color'].';';
        }
        
        
        if($attributes['animation']=="slideup"){
                $style .= ' -webkit-transform: translate3d(0, '.$attributes['ani_distance'].'px, 0);transform: translate3d(0, '.$attributes['ani_distance'].'px, 0);';
        }
        
        if(!$attributes['overflow']) {
            $style .= ' overflow: visible;';
        }
        
        $style .= ' -webkit-transition-duration: '.$attributes['ani_duration'].'s;transition-duration: '.$attributes['ani_duration'].'s;';
        if($attributes['zindex'] != 0){
            $style .= ' z-index: '.$attributes['zindex'].';';
        }
        if($attributes['pos_rotate'] > 0) {
            $style_container .= ' transform: rotate('.$attributes['pos_rotate'].'deg);';
        }
        
        if($attributes['position'] < 4) {
            $style .= ' position: absolute;';
        }
        else {
            $style .= ' margin-left: auto;margin-right: auto;';
        }
        
        
        $output = '<div class="azu_fb_container" style="'.trim($style_container).'"><div class="' . esc_attr( $class ) . $attributes['el_class'] . '" style="'.trim($style).'" data-viewport-pos="'.$attributes['viewport_pos'].'" data-ani-count="'.$attributes['ani_count'].'" data-distance="'.$attributes['ani_distance'].'" ><div class="' . esc_attr( $class_wrap ).'" >';
        $output .= do_shortcode($content);
        $output .= '</div><div class="azu_fb_mask" style="'.trim($style_mask).'"></div></div></div>';
       
        return $output;
    }
    
        
    // VC map function
    public function azu_vc_map() {
        if(!function_exists('vc_map')){ return; }
            // ! Fancyblock
            vc_map( array(
                    "name" => __("Fancy Block", 'azzu'.LANG_DN),
                    "base" => "azu_fancyblock",
                    "icon" => "azu_vc_ico_fancyblock",
                    "class" => "azu_vc_sc_fancyblock",
                    "as_parent" => array('except' => 'azu_fancyblock'),
                    "content_element" => true,
                    "controls" => "full",
                    "show_settings_on_create" => true,
                    "category" => __('by Theme', 'azzu'.LANG_DN),
                    "description" => __("Different element positions",'azzu'.LANG_DN),
                    "js_view" => 'VcColumnView',
                    "params" => array(
                                // add params same as with any other content element
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Animation",'azzu'.LANG_DN),
                                        "param_name" => "animation",
                                        "value" => array(
                                                __("None", 'azzu'.LANG_DN) => "",
                                                __("SlideUp", 'azzu'.LANG_DN) => "slideup",
                                                __("SlideDown", 'azzu'.LANG_DN) => "slidedown",
                                                __("SlideLeft", 'azzu'.LANG_DN) => "slideleft",
                                                __("SlideRight", 'azzu'.LANG_DN) => "slideright",
                                                __("Infinite Zoom", 'azzu'.LANG_DN) => "infinitezoom",
                                                __("Parallax", 'azzu'.LANG_DN) => "parallax",
                                                __("Parallax Grayscale", 'azzu'.LANG_DN) => "parallax grayscale",
                                                __("Parallax inside", 'azzu'.LANG_DN) => "parallax inside",
                                        ),
                                ),
                                array(
                                        "type" => "azu_number",
                                        "class" => "",
                                        "heading" => __("Animation Distance", 'azzu'.LANG_DN),
                                        "param_name" => "ani_distance",
                                        "suffix" => "px",
                                        "min" => -1000,
                                        "max" => 1000,
                                        "value" => 100,
                                        "dependency" => array(
                                                "element" => "animation",
                                                "value" => array(
                                                        "parallax grayscale",
                                                        "parallax inside",
                                                        "slideup",
                                                        "parallax"
                                                )
                                        ),
                                        "description" => __("Distance between the start and end points.", 'azzu'.LANG_DN)
                                ),
                                array(
                                        "type" => "azu_number",
                                        "class" => "",
                                        "heading" => __("Animation Duration",'azzu'.LANG_DN),
                                        "param_name" => "ani_duration",
                                        "value" => 300,
                                        "min" => 0,
                                        "max" => 60000,
                                        "suffix" => "ms",
                                        "description" => __("How long the animation effect should last. Decides the speed of effect.",'azzu'.LANG_DN),
                                ),
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Position",'azzu'.LANG_DN),
                                        "param_name" => "position",
                                        "value" => array(
                                                __("top left", 'azzu'.LANG_DN) => "0",
                                                __("top right", 'azzu'.LANG_DN) => "1",
                                                __("bottom right", 'azzu'.LANG_DN) => "2",
                                                __("bottom left", 'azzu'.LANG_DN) => "3",
                                                __("center", 'azzu'.LANG_DN) => "4"
                                        ),
                                ),
                                array(			
                                        "type" => "azu_range",
                                        "class" => "",
                                        "heading" => __("Vertical", 'azzu'.LANG_DN),
                                        "param_name" => "pos_vertical",
                                        "value" => 0,
                                        "min" => -1000,
                                        "max" => 1000,
                                        "suffix" => "px"
                                ),
                                array(			
                                        "type" => "azu_range",
                                        "class" => "",
                                        "heading" => __("Horizontal", 'azzu'.LANG_DN),
                                        "param_name" => "pos_horizontal",
                                        "value" => 0,
                                        "min" => -1000,
                                        "max" => 1000,
                                        "suffix" => "px"
                                ),
                                array(			
                                        "type" => "textfield",
                                        "class" => "",
                                        "heading" => __("Width", 'azzu'.LANG_DN),
                                        "param_name" => "width",
                                        "value" => "",
                                        "description" => __("Enter values with respective unites. Example - 15px, 15em, 15%, etc.", 'azzu'.LANG_DN),
                                ),
                                array(			
                                        "type" => "textfield",
                                        "class" => "",
                                        "heading" => __("Height", 'azzu'.LANG_DN),
                                        "param_name" => "height",
                                        "value" => "",
                                        "description" => __("Enter values with respective unites. Example - 15px, 15em, 15%, etc.", 'azzu'.LANG_DN),
                                ),
                                array(			
                                        "type" => "azu_range",
                                        "class" => "",
                                        "heading" => __("Rotate", 'azzu'.LANG_DN),
                                        "param_name" => "pos_rotate",
                                        "value" => 0,
                                        "min" => 0,
                                        "max" => 359,
                                        "suffix" => "deg"
                                ),
                                array(
                                        "type" => "azu_number",
                                        "class" => "",
                                        "heading" => __("Animation Delay",'azzu'.LANG_DN),
                                        "param_name" => "ani_delay",
                                        "value" => 0,
                                        "min" => 0,
                                        "max" => 10000,
                                        "suffix" => "ms",
                                        "description" => __("Delays the animation effect for seconds you enter above.",'azzu'.LANG_DN),
                                        "group" => "Advanced"
                                ),
                                array(
                                        "type" => "azu_number",
                                        "class" => "",
                                        "heading" => __("Animation Repeat Count",'azzu'.LANG_DN),
                                        "param_name" => "ani_count", //iteration
                                        "value" => 1,
                                        "min" => 0,
                                        "max" => 100,
                                        "suffix" => "",
                                        "dependency" => array(
                                                "element" => "animation",
                                                "value" => array(
                                                        "slideup",
                                                        "slidedown",
                                                        "slideleft",
                                                        "slideright"
                                                )
                                        ),
                                        "description" => __("The animation effect will repeat to the count you enter above. Enter 0 if you want to repeat it infinitely.",'azzu'.LANG_DN),
                                        "group" => "Advanced"
                                ),
                                array(
                                        "type" => "colorpicker",
                                        "heading" => __( "Background color", 'azzu'.LANG_DN ),
                                        "param_name" => "bg_color",
                                        "value" => "",
                                        "description" => __( "Select custom background color.", 'azzu'.LANG_DN ),
                                        "group" => "Advanced"
                                ),
                                // Border Color
                                array(
                                        "type" => "colorpicker",
                                        "heading" => __( "Border color", 'azzu'.LANG_DN ),
                                        "param_name" => "border_color",
                                        "value" => "",
                                        "description" => __( "Select custom border color.", 'azzu'.LANG_DN ),
                                        "group" => "Advanced"
                                ),
                                // Border width
                                array(
                                        "type" => "azu_range",
                                        "class" => "",
                                        "heading" => __("Border width", 'azzu'.LANG_DN),
                                        "param_name" => "border_width",
                                        "value" => 1,
                                        "min" => 0,
                                        "max" => 10,
                                        "dependency" => Array("element" => "border_color", "not_empty" => true),
                                        "suffix" => "px",
                                        "group" => "Advanced"
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
                                        "dependency" => Array("element" => "border_color", "not_empty" => true),
                                        "suffix" => "px",
                                        "description" => __("set border radius.", 'azzu'.LANG_DN),
                                        "group" => "Advanced"
                                ),
//                                array(
//                                        "type" => "azu_toggle",
//                                        "class" => "",
//                                        "heading" => __("Hide Elements Until Delay", 'azzu'.LANG_DN),
//                                        "param_name" => "opacity",
//                                        "admin_label" => true,
//                                        "std" => "no",
//                                        "value" => array(
//                                                "" => "yes"
//                                        ),
//                                        "group" => "Advanced"
//                                ),
                                // Overflow
//                                array(
//                                        "type" => "azu_toggle",
//                                        "class" => "",
//                                        "heading" => __("Overflow hidden", 'azzu'.LANG_DN),
//                                        "param_name" => "overflow",
//                                        "std" => "yes",
//                                        "value" => array(
//                                                "" => "yes"
//                                        ),
//                                        "description" => "",
//                                        "group" => "Advanced"
//                                ),
                                array(
                                        "type" => "azu_number",
                                        "class" => "",
                                        "heading" => __("Z-index", 'azzu'.LANG_DN),
                                        "param_name" => "zindex",
                                        "value" => 0,
                                        "description" => __("The z-index property specifies the stack order of an element.", 'azzu'.LANG_DN),
                                        "group" => "Advanced"
                                ),
                                array(
                                        "type" => "azu_number",
                                        "class" => "",
                                        "heading" => __("Viewport Position", 'azzu'.LANG_DN),
                                        "param_name" => "viewport_pos",
                                        "suffix" => "%",
                                        "min" => 0,
                                        "max" => 100,
                                        "value" => 80,
                                        "description" => __("The area of screen from top where animation effects will start working.", 'azzu'.LANG_DN),
                                        "group" => "Advanced"
                                ),
                                array(			
                                        "type" => "textfield",
                                        "class" => "",
                                        "heading" => __("Custom CSS Class", 'azzu'.LANG_DN),
                                        "param_name" => "el_class",
                                        "value" => "",
                                        "description" => __("Ran out of options? Need more styles? Write your own CSS and mention the class name here.", 'azzu'.LANG_DN),
                                        "group" => "Advanced"
                                ),
                                array(
                                    'type' => 'css_editor',
                                    'heading' => __( 'Css', 'azzu'.LANG_DN ),
                                    'param_name' => 'css',
                                    'group' => __( 'Design', 'azzu'.LANG_DN ),
                                    'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border',
                                ),
                    )
            ) );
    }

}

// create shortcode
AZU_Shortcode_Fancyblock::get_instance();

	if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
		class WPBakeryShortCode_azu_fancyblock extends WPBakeryShortCodesContainer {
		}
	}