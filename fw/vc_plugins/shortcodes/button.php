<?php
/**
 * Button shortcode.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode button class.
 *
 */
class AZU_Shortcode_Button extends AZU_Shortcode {

	static protected $instance;

	protected $shortcode_name = 'azu_button';

	public static function get_instance() {

		if ( !self::$instance ) {
			self::$instance = new AZU_Shortcode_Button();
		}
		return self::$instance;
	}

	public function __construct() {

		$this->addShortCode( $this->shortcode_name, array($this, 'shortcode') );
                $this->azu_vc_map();
	}

	public function shortcode( $atts, $content = null ) {
                
                $attributes = shortcode_atts( array(
			'size'          => 'medium',
                        'reverse'       => '0',
                        'round'         => '0',
                        'align'         => 'inline',
                        'type'          => 'default',
			'color'         => 'azu_accent',
                        'custom_color'  => '#999',
			'button_link'          => '',
			'icon'			=> '',
			'icon_align'	=> 'left',
                        'css'  => '',
			'el_class'		=> ''
                ), $atts ); 

                $attributes['align'] = in_array($attributes['align'], array('inline','left','center', 'right') ) ? $attributes['align'] : 'inline';
                $attributes['reverse'] = apply_filters('azu_sanitize_flag', $attributes['reverse']);
                $attributes['round'] = apply_filters('azu_sanitize_flag', $attributes['round']);
                $attributes['type'] = in_array($attributes['type'], array('default','blur', 'ghost','white','link') ) ? $attributes['type'] : 'default';
                $attributes['size'] = in_array($attributes['size'], array('xsmall','small', 'medium', 'big') ) ? $attributes['size'] : 'default';
		$attributes['color'] = in_array( $attributes['color'], array('azu_accent', 'azu_custom') ) ? $attributes['color'] : 'azu_accent';
		$attributes['icon_align'] = in_array($attributes['icon_align'], array('icon_align', 'right') ) ? $attributes['icon_align'] : 'left';

		$attributes['el_class'] = sanitize_html_class( $attributes['el_class'] );
                $href = array( 'url' => '', 'title'=>'','target'=>'');
                if($attributes['button_link'] !== '')
                        $href = vc_build_link($attributes['button_link']);
                
                $css_class = 'azu-vc-btn azu-align-'.$attributes['align'];
                $css_class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $attributes['css'], ' ' ), "azu_button", $atts );
		$icon = '';
		if ( preg_match('/^icon-(\w)/', $attributes['icon']) ) {
			$icon = '<i class="' . esc_attr( $attributes['icon'] ) . '"></i>';
		}

		$classes = array('');
                $class_temp = '';
                if($attributes['type'] == 'link'){
                    $class_temp = 'link-';
                }
                else {
                    $classes[] = 'btn';
                }
                
                switch( $attributes['size'] ) {
                        case 'small':  $class_temp .= 'btn-sm'; break;
                        case 'xsmall':  $class_temp .= 'btn-xs'; break;
                        case 'medium': $class_temp .= 'btn-md'; break;
                        case 'big':  $class_temp .= 'btn-lg'; break;
                        default: $class_temp .= 'btn-md'; break;
                }
                $classes[] = $class_temp;
                
                if($attributes['reverse'])
                    $classes[] = 'azu-btn-reverse';
                if($attributes['round'])
                    $classes[] = 'azu-btn-round';
                
                $custom='';
                
                switch( $attributes['type'] ) {
                    case 'blur':  $classes[] = 'azu-btn-blur'; break;
                    case 'ghost': $classes[] = 'azu-btn-ghost'; break;
                    case 'white': $classes[] = 'azu-btn-white'; break;
                    case 'link': $classes[] = 'btn-link'; break;
                    default:  $classes[] = 'azu-btn-default'; break;
                }
		if ( $attributes['color'] === 'azu_custom' ) {
                    if($attributes['reverse'])
                    {
                        switch( $attributes['type'] ) {
                            case 'blur':  $custom = 'border-color: %2$s; background-color: '.azu_stylesheet_color_hex2rgba($attributes['custom_color'],95).'; color: %2$s;'; break;
                            case 'ghost': $custom = 'border-color: %1$s; background-color: transparent; color: %1$s;'; break;
                            case 'white': $custom = 'border-color: %2$s; background-color: %2$s; color: %1$s;'; break;
                            case 'link': $custom = 'color: %1$s;'; break;
                            default: $custom = 'border-color: %1$s; background-color: transparent; color: %1$s;'; break;
                        }
                    }
                    else {
                        switch( $attributes['type'] ) {
                            case 'blur':  $custom = 'border-color: %1$s; background-color: '.azu_stylesheet_color_hex2rgba($attributes['custom_color'],50).'; color: %2$s;'; break;
                            case 'ghost': $custom = 'border-color: %1$s; background-color: transparent; color: %1$s;'; break;
                            case 'white': $custom = 'border-color: %1$s; background-color: %1$s; color: %2$s;'; break;
                            case 'link': $custom = 'color: %1$s;'; break;
                            default: $custom = 'border-color: %1$s; background-color: %1$s; color: %2$s;'; break;
                        }
                    }
                    
                    $custom = ' style="'.sprintf($custom,$attributes['custom_color'],'#ffffff').'"';
                }
                    
		// add icon
		if ( $icon && 'right' == $attributes['icon_align'] ) {
			$content .= $icon;
			$classes[] = 'ico-right-side';
		} else if ( $icon ) {
			$content = $icon . $content;
		}

		if ( $attributes['el_class'] ) {
			$classes[] = $attributes['el_class'];
		}

		// class
		$classes = implode( ' ', $classes );
                
		$output = '<div class="'.esc_attr($css_class).'">'.azuh()->azzu_get_button_html( array( 'href' => $href['url'], 'title' => $content, 'class' => $classes, 'target' => $href['target'], 'custom'=> $custom ) ).'</div>';

		return $output;
	}
        
        // VC map function
        public function azu_vc_map() {
                if(!function_exists('vc_map')){ return; }
                // ! Button
                vc_map( array(
                        "name" => __("Button", 'azzu'.LANG_DN),
                        "base" => "azu_button",
                        "icon" => "azu_vc_ico_button",
                        "class" => "azu_vc_sc_button",
                        "description" => __("Simple button",'azzu'.LANG_DN),
                        "category" => __('by Theme', 'azzu'.LANG_DN),
                        "params" => array(

                                // Caption
                                array(
                                        "type" => "textfield",
                                        "class" => "",
                                        "heading" => __("Caption", 'azzu'.LANG_DN),
                                        "admin_label" => true,
                                        "param_name" => "content",
                                        "value" => "",
                                        "description" => ""
                                ),

                                // Link Url
                                array(
                                        "type" => "vc_link",
                                        "class" => "",
                                        "heading" => __("Link ", 'azzu'.LANG_DN),
                                        "param_name" => "button_link",
                                        "value" => "",
                                        "description" => __("Add a custom link or select existing page. You can remove existing link as well.", 'azzu'.LANG_DN),
                                ),
                            
                                // Size
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Style", 'azzu'.LANG_DN),
                                        "param_name" => "size",
                                        "std" => "medium",
                                        "value" => array(
                                                __("Extra Small button", 'azzu'.LANG_DN) => "xsmall",
                                                __("Small button", 'azzu'.LANG_DN) => "small",
                                                __("Medium button", 'azzu'.LANG_DN) => "medium",
                                                __("Big button", 'azzu'.LANG_DN) => "big"
                                        ),
                                        "description" => ""
                                ),

                                // Style
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Style", 'azzu'.LANG_DN),
                                        "param_name" => "type",
                                        "admin_label" => true,
                                        "value" => array(
                                                __("Default", 'azzu'.LANG_DN) => "default",
                                                __("Semi-Transparent", 'azzu'.LANG_DN) => "blur",
                                                __("White", 'azzu'.LANG_DN) => "white",
                                                __("Ghost", 'azzu'.LANG_DN) => "ghost",
                                                __("Link", 'azzu'.LANG_DN) => "link"
                                        ),
                                        "description" => ""
                                ),
                                // reverse
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Reverse", 'azzu'.LANG_DN),
                                        "param_name" => "reverse",
                                        "std" => "no",
                                        "dependency" => array(
                                                "element" => "type",
                                                "value" => array(
                                                        "default",
                                                        "blur",
                                                        "ghost",
                                                        "white"
                                                )
                                        ),
                                        "value" => array(
                                                "" => "yes"
                                        )
                                ),
                                // round
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Rounded corner", 'azzu'.LANG_DN),
                                        "param_name" => "round",
                                        "std" => "no",
                                        "dependency" => array(
                                                "element" => "type",
                                                "value" => array(
                                                        "default",
                                                        "blur",
                                                        "ghost",
                                                        "white"
                                                )
                                        ),
                                        "value" => array(
                                                "" => "yes"
                                        )
                                ),
                                //align
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Alignment", 'azzu'.LANG_DN),
                                        "param_name" => "align",
                                        "value" => array(
                                               __("Inline", 'azzu'.LANG_DN) => "inline",
                                               __("Left", 'azzu'.LANG_DN) => "left",
                                               __("Center", 'azzu'.LANG_DN) => "center",
                                               __("Right", 'azzu'.LANG_DN) => "right"
                                        ),
                                        "description" => ""
                                ),
                                // Button color
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Button color", 'azzu'.LANG_DN),
                                        "param_name" => "color",
                                        "value" => array(
                                                __( 'Accent', 'azzu'.LANG_DN ) => "azu_accent",
                                                __( 'Custom', 'azzu'.LANG_DN ) => "azu_custom"
                                        ),
                                        "description" => "",
                                ),

                                 array(
                                        "type" => "colorpicker",
                                        "class" => "",
                                        "heading" => __("Color", 'azzu'.LANG_DN),
                                        "param_name" => "custom_color",
                                        "value" => "#999",
                                        "description" => __("Give it a nice paint!", 'azzu'.LANG_DN),
                                        "dependency" => array(
                                                "element" => "color",
                                                "value" => array(
                                                        "azu_custom"
                                                )
                                        )
                                ),

                                // Icon
                                array(
                                        "type" => "azu_iconpicker",
                                        "class" => "",
                                        "heading" => __("Icon", 'azzu'.LANG_DN),
                                        "param_name" => "icon",
                                        "value" => ''
                                ),

                                // Icon align
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Icon align", 'azzu'.LANG_DN),
                                        "param_name" => "icon_align",
                                        "value" => array(
                                                "Left" => "left",
                                                "Right" => "right"
                                        ),
                                        "dependency" => Array("element" => "icon", "not_empty" => true),
                                        "description" => ""
                                ),
                                // Extra class name
                                array(
                                        "type" => "textfield",
                                        "heading" => __("Extra class name", 'azzu'.LANG_DN),
                                        "param_name" => "el_class",
                                        "value" => "",
                                        "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'azzu'.LANG_DN)
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
AZU_Shortcode_Button::get_instance();
