<?php
/**
 * Price shortcode.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode price class.
 *
 */
class AZU_Shortcode_Price extends AZU_Shortcode {

    static protected $instance;

    protected $shortcode_name = 'azu_price';

    public static function get_instance() {
        if ( !self::$instance ) {
            self::$instance = new AZU_Shortcode_Price();
        }
        return self::$instance;
    }

    public function __construct() {

        $this->addShortCode( $this->shortcode_name, array($this, 'shortcode') );
        $this->azu_vc_map();
    }

    public function shortcode( $atts, $content = null ) {
        $attributes = shortcode_atts( array(
            'icon'      => '',
            'p_heading' => '',
            'p_sub_heading' => '',
            'p_price' => '',
            'p_unit' => '',
            'p_btn_text' => '',
            'p_link' => '',
            'p_featured' => '0',
            'min_ht' => '',
            'el_class' => '',
        ), $atts );
        $style = "";
        // sanitize attributes
        $attributes['icon'] = esc_attr($attributes['icon']);
        $attributes['p_heading'] = esc_attr( $attributes['p_heading'] );
        $attributes['p_sub_heading'] = esc_attr( $attributes['p_sub_heading'] );
        $attributes['p_price'] = esc_attr( $attributes['p_price'] );
        $attributes['p_unit'] = esc_attr( $attributes['p_unit'] );
        $attributes['p_btn_text'] = esc_attr( $attributes['p_btn_text'] );
        $attributes['p_link'] = esc_attr( $attributes['p_link'] );
        $attributes['el_class'] = esc_attr( $attributes['el_class'] );
        $attributes['min_ht'] = absint( $attributes['min_ht'] );
        $attributes['p_featured'] = apply_filters('azu_sanitize_flag', $attributes['p_featured']);
        $class_wrap = 'azu-price-table-wrap';
        $class = 'azu-price-table';
        
        $link = $target = '';
        if($attributes['p_link'] !== ""){
            $link = vc_build_link($attributes['p_link']);
            if(isset($link['target'])){
                    $target = 'target="'.$link['target'].'"';
            } else {
                    $target = '';
            }
            $link = $link['url'];
        } else {
                $link = "#";
        }

        if(!empty($attributes['p_sub_heading']))
                $attributes['p_sub_heading'] = '<h5>'.$attributes['p_sub_heading'].'</h5>';
        
        if(!empty($attributes['el_class']))
                $class .= ' '.$attributes['el_class'];
        
        if($attributes['min_ht']>0)
            $style .= 'min-heigth: '.$attributes['min_ht'].'px;';
        
        $class_btn = 'btn azu-btn-default';
        if($attributes['p_featured']){
            $class .=  ' azu-price-featured';
        }
        else {
            $class_btn .= ' azu-btn-reverse';
        }
        $price_content = $price_body = $price_button = '';
        $price_heading = '<div class="azu-price-heading"><i class="'.$attributes['icon'].'" ></i><h4>'.$attributes['p_heading'].'</h4>'.$attributes['p_sub_heading'].'</div>';
        if(!(empty($attributes['p_price']) && empty($attributes['p_unit']) )){
            $price_body = '<div class="azu-price-body-wrap"><div class="azu-price-body"><div class="azu-price-text">'.$attributes['p_price'].'</div><div class="azu-price-unit">'.$attributes['p_unit'].'</div></div></div>' ;
        }
        if(!empty($content)){
            $price_content = '<div class="azu-price-content">'.$content.'</div>';
        }
        if(!empty($attributes['p_btn_text'])){
            $price_button = '<div class="azu-price-button"><a class="'.$class_btn.'" href="'.$link.'" '.$target.'>'.$attributes['p_btn_text'].'</a></div>';
        }
        $output = '<div class="' . esc_attr( $class_wrap ) . '"><div class="' . esc_attr( $class ) . '" style="'.esc_attr($style).'">';
        $output .= $price_heading.$price_body.$price_content.$price_button;
        $output .= '</div></div>';
       
        return $output;
    }
    
        
    // VC map function
    public function azu_vc_map() {
        if(!function_exists('vc_map')){ return; }
            // ! Price
            vc_map( array(
                    "name" => __("Price", 'azzu'.LANG_DN),
                    "base" => "azu_price",
                    "icon" => "azu_vc_ico_price",
                    "class" => "azu_vc_sc_price",
                    "description" => __("Price box",'azzu'.LANG_DN),
                    "category" => __('by Theme', 'azzu'.LANG_DN),
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
                                        "type" => "textfield",
                                        "heading" => __("Package Name / Title", 'azzu'.LANG_DN),
                                        "param_name" => "p_heading",
                                        "admin_label" => true,
                                        "value" => "",
                                        "description" => __("Enter the package name or table heading", 'azzu'.LANG_DN),
                                ),
                                array(
                                        "type" => "textfield",
                                        "heading" => __("Sub Heading", 'azzu'.LANG_DN),
                                        "param_name" => "p_sub_heading",
                                        "value" => "",
                                        "description" => __("Enter short description for this package", 'azzu'.LANG_DN),
                                ),
                                array(
                                        "type" => "textfield",
                                        "heading" => __("Package Price", 'azzu'.LANG_DN),
                                        "param_name" => "p_price",
                                        "value" => "",
                                        "description" => __("Enter the price for this package. e.g. $179", 'azzu'.LANG_DN),
                                ),
                                array(
                                        "type" => "textfield",
                                        "heading" => __("Price Unit", 'azzu'.LANG_DN),
                                        "param_name" => "p_unit",
                                        "value" => "",
                                        "description" => __("Enter the price unit for this package. e.g. per month", 'azzu'.LANG_DN),
                                ),
                                array(
                                        "type" => "textarea_html",
                                        "heading" => __("Features", 'azzu'.LANG_DN),
                                        "param_name" => "content",
                                        "value" => "",
                                        "description" => __("Create the features list using un-ordered list elements.", 'azzu'.LANG_DN),
                                ),
                                array(
                                        "type" => "textfield",
                                        "heading" => __("Button Text", 'azzu'.LANG_DN),
                                        "param_name" => "p_btn_text",
                                        "value" => "",
                                        "description" => __("Enter call to action button text", 'azzu'.LANG_DN),
                                ),
                                array(
                                        "type" => "vc_link",
                                        "heading" => __("Button Link", 'azzu'.LANG_DN),
                                        "param_name" => "p_link",
                                        "value" => "",
                                        "description" => __("Enter the link for call to action button", 'azzu'.LANG_DN),
                                ),
                                array(
                                        "type" => "azu_toggle",
                                        "heading" => __("Featured", 'azzu'.LANG_DN),
                                        "param_name" => "p_featured",
                                        "std" => "no",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => __("Make this pricing item as featured", 'azzu'.LANG_DN)
                                ),
                                array(
                                        "type" => "azu_number",
                                        "heading" => __("Minimum Height For Price item", 'azzu'.LANG_DN),
                                        "param_name" => "min_ht",
                                        "min" => "",
                                        "suffix" => "px",
                                        "description" => __("Adjust height of your price table.", 'azzu'.LANG_DN)
                                        ),
                                array(			
                                        "type" => "textfield",
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
AZU_Shortcode_Price::get_instance();