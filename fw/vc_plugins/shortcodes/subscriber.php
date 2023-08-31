<?php
/**
 * Subscriber shortcode.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode subscriber class.
 *
 */
class AZU_Shortcode_Subscriber extends AZU_Shortcode {

    static protected $instance;

    protected $shortcode_name = 'azu_subscriber';

    public static function get_instance() {
        if ( !self::$instance ) {
            self::$instance = new AZU_Shortcode_Subscriber();
        }
        return self::$instance;
    }

    public function __construct() {

        $this->addShortCode( $this->shortcode_name, array($this, 'shortcode') );
        $this->azu_vc_map();
    }

    public function shortcode( $atts, $content = null ) {
        $attributes = shortcode_atts( array(
            'plugins'   => '',
            'align'     => 'left'
        ), $atts );
        // sanitize attributes
        
	$attributes['plugins'] = in_array($attributes['plugins'], array('mailchimp', 'mailpoet') ) ? $attributes['plugins'] : '';
        $attributes['align'] = in_array($attributes['align'], array('left', 'center', 'right') ) ? $attributes['align'] : 'left';
        
        $class = 'azu-subscriber';
        
        $class .= ' align'.$attributes['align'];
        
        if(function_exists('mc4wp_get_forms') && ($attributes['plugins'] == 'mailchimp' || $attributes['plugins'] === '') ) {
            // query first available form and go there
            $forms = mc4wp_get_forms( array( 'numberposts' => 1 ) );
            if( $forms ) {
                    // if we have a post, go to the "edit form" screen
                    $form = array_pop( $forms );
                    $content = sprintf( '[mc4wp_form id="%d"]', $form->ID );
                    $attributes['plugins'] = 'mailchimp';
            }
        }
                
        $output = '<div class="' . esc_attr( $class ) . '">';
        if(!empty($content)) {
            $output .= do_shortcode($content);
        }
        $output .= '</div>';
       
        return $output;
    }
    
        
    // VC map function
    public function azu_vc_map() {
        if(!function_exists('vc_map')){ return; }
            // ! Subscriber
            vc_map( array(
                    "name" => __("Email subscriber", 'azzu'.LANG_DN),
                    "base" => "azu_subscriber",
                    "icon" => "azu_vc_ico_subscriber",
                    "class" => "azu_vc_sc_subscriber",
                    "category" => __('by Theme', 'azzu'.LANG_DN),
                    "show_settings_on_create" => true,
                    "description" => __('Newsletter', 'azzu'.LANG_DN),
                    "params" => array(
                                //direction
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Plugins", 'azzu'.LANG_DN),
                                        "admin_label" => true,
                                        "param_name" => "plugins",
                                        "value" => array(
                                               __("Auto", 'azzu'.LANG_DN) => "",
                                               __("MailChimp", 'azzu'.LANG_DN) => "mailchimp",
                                               //__("MailPoet Newsletters", 'azzu'.LANG_DN) => "mailpoet"
                                        ),
                                        "description" => '<span style="display: block;">MailChimp for WordPress <a href="//goo.gl/HHU1Oz" target="_blank">http://goo.gl/HHU1Oz</a></span>'
                                ),
                                //align
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Alignment", 'azzu'.LANG_DN),
                                        "admin_label" => true,
                                        "param_name" => "align",
                                        "value" => array(
                                               __("Left", 'azzu'.LANG_DN) => "left",
                                               __("Center", 'azzu'.LANG_DN) => "center",
                                               __("Right", 'azzu'.LANG_DN) => "right"
                                        ),
                                        "description" => ""
                                ),
                    )
            ) );
    }

}

// create shortcode
AZU_Shortcode_Subscriber::get_instance();