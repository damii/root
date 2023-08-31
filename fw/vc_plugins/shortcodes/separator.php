<?php
/**
 * Separator shortcode.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode separator class.
 *
 */
class AZU_Shortcode_Separator extends AZU_Shortcode {

    static protected $instance;

    protected $shortcode_name = 'azu_separator';

    public static function get_instance() {
        if ( !self::$instance ) {
            self::$instance = new AZU_Shortcode_Separator();
        }
        return self::$instance;
    }

    public function __construct() {

        $this->addShortCode( $this->shortcode_name, array($this, 'shortcode') );
        $this->azu_vc_map();
    }

    public function shortcode( $atts, $content = null ) {
        $attributes = shortcode_atts( array(
            'direction' => 'down',
            'type' => 'triangle',
            'full_width' => '0',
            'customcolor' => '#ffffff'
        ), $atts );
        $style = "";
        // sanitize attributes
        $attributes['customcolor'] = esc_attr( $attributes['customcolor'] );
	$attributes['direction'] = in_array($attributes['direction'], array('up', 'down') ) ? $attributes['direction'] : 'down';
        $attributes['type'] = in_array($attributes['type'], array('separator', 'invert', 'divider','triangle') ) ? $attributes['type'] : 'triangle';
        $attributes['full_width'] = apply_filters('azu_sanitize_flag', $attributes['full_width']);
        $class = 'azu-section-separator';
        if($attributes['type'] == 'invert') {
            $class .= ' azu-invert';
            if($attributes['full_width'])
                $class .= ' azu-full-width';
            else
                $class .= ' azu-gutter-margin';
            $sp = '<div class="azu-separator-back" style="background-color:'.$attributes['customcolor'].';" ></div><div class="azu-separator-svg" style="border-color: '.$attributes['customcolor'].';"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="72px" height="24px" viewBox="0 0 72 24" version="1.1"><g stroke="none" stroke-width="0" fill="none" fill-rule="evenodd"><path fill="'.$attributes['customcolor'].'" d="M0 24 L72.0038605 24 C72.0038605 24 72 9.8 72 0 C60.0007743 0 48 23 36 23 C24.0083239 23 12.1 0 0 0 C0.00464556284 12 0 24 0 24 Z"/></g></svg></div><div class="azu-separator-back" style="background-color:'.$attributes['customcolor'].';"></div>';
        }
        else if($attributes['type'] == 'separator'){
            $sp = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="72px" height="24px" viewBox="0 0 72 24" version="1.1"><path fill="'.$attributes['customcolor'].'" stroke="'.$attributes['customcolor'].'" stroke-width="0" d="M72 0 C60 0 48 24 36 24 C24.1 24 12.1 0 0 0 C-12 0 84 0 72 0 Z" /></svg>';
            $style = ' border-color: '.$attributes['customcolor'].';';
        }
        else if($attributes['type'] == 'divider'){
            $sp = '<span class="azu-holder-left"><span></span></span><span class="azu-divider-svg"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="34px" height="12px" viewBox="0 0 34 12" version="1.1"><g stroke="none" stroke-width="0" fill-rule="evenodd" ><g ><path d="M0.5 0 C6 0 12.4 11.1 17 11.1 C21.6 11.1 28.1 0 33.5 0 C33.5 0.3 33.5 0.6 33.5 1 C28 1 22.5 12 17 12 C11.5 12 6 1 0.5 1 C0.5 0.6 0.5 0.3 0.5 0 Z" /></g></g></svg></span><span class="azu-holder-right"><span></span></span>';
            $class = 'azu-section-divider';
        }
        else {
            $style = 'border-color: '.$attributes['customcolor'].';';
            $sp = '<div class="azu-sp-triangle-arrow" style="'.$style.'"></div><div class="azu-sp-triangle" style="'.$style.'"></div>';
            $class = 'azu-section-triangle';
        }
        $class .= ' azu-sp-'.$attributes['direction'];
       
        $output = '<div class="' . esc_attr( $class ) . '" style="'.esc_attr($style).'">';
        $output .= $sp;
        $output .= '</div>';
       
        return $output;
    }
    
        
    // VC map function
    public function azu_vc_map() {
        if(!function_exists('vc_map')){ return; }
            // ! Separator
            vc_map( array(
                    "name" => __("Separator", 'azzu'.LANG_DN),
                    "base" => "azu_separator",
                    "icon" => "azu_vc_ico_separator",
                    "class" => "azu_vc_sc_separator",
                    "description" => __("Row Separators",'azzu'.LANG_DN),
                    "category" => __('by Theme', 'azzu'.LANG_DN),
                    "params" => array(
                            //direction
                            array(
                                    "type" => "dropdown",
                                    "class" => "",
                                    "heading" => __("Direction", 'azzu'.LANG_DN),
                                    "admin_label" => true,
                                    "param_name" => "direction",
                                    "value" => array(
                                           __("Down", 'azzu'.LANG_DN) => "down",
                                           __("Up", 'azzu'.LANG_DN) => "up"
                                    ),
                                    "description" => ""
                            ),
                            // Invert
                            array(
                                    "type" => "dropdown",
                                    "class" => "",
                                    "heading" => __("Type", 'azzu'.LANG_DN),
                                    "param_name" => "type",
                                    "value" => array(
                                            __("Triangle", 'azzu'.LANG_DN) => "triangle",
                                            __("Half round", 'azzu'.LANG_DN) => "separator",
                                            __("Half round reverse", 'azzu'.LANG_DN) => "invert",
                                            __("Divider", 'azzu'.LANG_DN) => "divider"
                                    ),
                                    "description" => ""
                            ),
                            // fullwidth
                            array(
                                    "type" => "azu_toggle",
                                    "class" => "",
                                    "heading" => __("fullwidth", 'azzu'.LANG_DN),
                                    "param_name" => "full_width",
                                    "std" => "no",
                                    "value" => array(
                                            "" => "yes"
                                    ),
                                    "dependency" => array(
                                                "element" => "type",
                                                "value" => array(
                                                        "invert"
                                                )
                                    ),
                            ),
                            array(
                                    "type" => "colorpicker",
                                    "heading" => __( "Color", 'azzu'.LANG_DN ),
                                    "param_name" => "customcolor",
                                    "value" => "#ffffff",
                                    "dependency" => array(
                                                "element" => "type",
                                                "value" => array(
                                                        "triangle",
                                                        "separator",
                                                        "invert"
                                                )
                                    ),
                                    "description" => __( "Select custom background color for separator.", 'azzu'.LANG_DN )
                            )
                    )
            ) );
    }

}

// create shortcode
AZU_Shortcode_Separator::get_instance();