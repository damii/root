<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode social_icons class.
 *
 */
class AZU_Shortcode_Social_Icons extends AZU_Shortcode {

    static protected $instance;

    protected $shortcode_name = 'azu_social_icons';

    public static function get_instance() {
        if ( !self::$instance ) {
            self::$instance = new AZU_Shortcode_Social_Icons();
        }
        return self::$instance;
    }

    public function __construct() {

        $this->addShortCode( $this->shortcode_name, array($this, 'shortcode') );
        $this->azu_vc_map();
    }

    public function shortcode( $atts, $content = null ) {
            $s_data = azuf()->azzu_get_social_icons_data();
            $ico = array();
            foreach ($atts as $i => $val) {
                if(array_key_exists($i, $s_data))
                        $ico[] = array(
                            'icon' => $i,
                            'url' => $val
                        );
            }
            if(count($ico) > 0)
                $atts['icons'] = $ico;
        
            $attributes = shortcode_atts( array(
                'icons' => '',
                'align' => 'inline',
                'size' => 'default',
                'border' => '0',
                'reverse' => '0'
            ), $atts ); 
            
            $attributes['align'] = in_array($attributes['align'], array('inline','left','center', 'right') ) ? $attributes['align'] : 'inline';
            $attributes['size'] = in_array($attributes['size'], array('default','large', 'normal', 'small') ) ? $attributes['size'] : 'default';
            $attributes['border'] = apply_filters('azu_sanitize_flag', $attributes['border']);
            $attributes['reverse'] = apply_filters('azu_sanitize_flag', $attributes['reverse']);
            
            $output = azuh()->azzu_get_topbar_social_icons($attributes);
            $class = 'azu-social-icons azu-vc-social';
            if($attributes['size']=='large')
                $class .= ' azu-soc-large';
            else if($attributes['size']=='normal')
                $class .= ' azu-soc-normal';
            else if($attributes['size']=='small')
                $class .= ' azu-soc-small';

            if($attributes['border'])
                $class .= ' azu-soc-border';

            if($attributes['reverse'])
                $class .= ' azu-social-reverse';
                
            $class .= ' azu-align-'.$attributes['align'];
            
            $output = '<div class="'.esc_attr($class).'">' . $output . '</div>';
	    return $output;
    }
    
    // VC map function
    public function azu_vc_map() {
            if(!function_exists('vc_map')){ return; }
            
            $s_fields = array(   
                            //align
                            array(
                                    "type" => "dropdown",
                                    "class" => "",
                                    "heading" => __("Alignment", 'azzu'.LANG_DN),
                                    "admin_label" => true,
                                    "param_name" => "align",
                                    "value" => array(
                                           __("Inline", 'azzu'.LANG_DN) => "inline",
                                           __("Left", 'azzu'.LANG_DN) => "left",
                                           __("Center", 'azzu'.LANG_DN) => "center",
                                           __("Right", 'azzu'.LANG_DN) => "right"
                                    ),
                                    "description" => ""
                            ),
                            //size
                            array(
                                    "type" => "dropdown",
                                    "class" => "",
                                    "heading" => __("Size", 'azzu'.LANG_DN),
                                    "admin_label" => true,
                                    "param_name" => "size",
                                    "value" => array(
                                           __("Default", 'azzu'.LANG_DN) => "default",
                                           __("Large", 'azzu'.LANG_DN) => "large",
                                           __("Normal", 'azzu'.LANG_DN) => "normal",
                                           __("Small", 'azzu'.LANG_DN) => "small"
                                    ),
                                    "description" => ""
                            ),
                            // border
                            array(
                                    "type" => "azu_toggle",
                                    "class" => "",
                                    "heading" => __("With border", 'azzu'.LANG_DN),
                                    "param_name" => "border",
                                    "std" => "no",
                                    "value" => array(
                                            "" => "yes"
                                    ),
                            ),
                            // reverse
                            array(
                                    "type" => "azu_toggle",
                                    "class" => "",
                                    "heading" => __("Reverse", 'azzu'.LANG_DN),
                                    "param_name" => "reverse",
                                    "std" => "no",
                                    "value" => array(
                                            "" => "yes"
                                    )
                            )
            );
            $s_data = azuf()->azzu_get_social_icons_data();
            //create all fields
            foreach ($s_data as $key => $value) {
                    $s_fields[] = array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => $value,
                            "param_name" => $key,
                            "value" => "",
                            "description" => '',
                    );
            }
            
            //// ! Social_Icons
            vc_map(
                array(
                   "name" => __("Social Icons",'azzu'.LANG_DN),
                   "base" => "azu_social_icons",
                   "class" => "azu_vc_sc_social_icons",
                   "icon" => "azu_vc_ico_social_icons",
                   "description" => __("Social media icons",'azzu'.LANG_DN),
                   "category" => __("by Theme",'azzu'.LANG_DN),
                   "content_element" => true,
                   "show_settings_on_create" => true,
                   "params" => $s_fields,
                )
            );
    }

}

// create shortcode
AZU_Shortcode_Social_Icons::get_instance();
