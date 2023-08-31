<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode single_meta class.
 *
 */
class AZU_Shortcode_Single_Meta extends AZU_Shortcode {

    static protected $instance;

    protected $shortcode_name = 'azu_single_meta';

    public static function get_instance() {
        if ( !self::$instance ) {
            self::$instance = new AZU_Shortcode_Single_Meta();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->addShortCode( $this->shortcode_name, array($this, 'shortcode') );
        $this->azu_vc_map();
    }

    public function shortcode( $atts, $content = null ) {
            $attributes = shortcode_atts( array(
                'title' => '',
                'directios' => '0',
                'custom_fields' => '',
                'date' => '1',
                'sticky' => '1',
                'like' => '1',
                'category' => '1',
                'share' => '1',
                'worker' => '0',
                'morelink' =>'',
            ), $atts ); 
            
            $attributes['title'] = empty($attributes['title']) && !empty($content) ? get_the_title() : esc_html($attributes['title']);
            $attributes['directios'] = apply_filters('azu_sanitize_flag', $attributes['directios']);
            $attributes['custom_fields'] = json_decode(urldecode($attributes['custom_fields']), true);
            $attributes['sticky'] = apply_filters('azu_sanitize_flag', $attributes['sticky']);
            $attributes['morelink'] = esc_attr($attributes['morelink']);
            $attributes['date'] = apply_filters('azu_sanitize_flag', $attributes['date']);
            $attributes['like'] = apply_filters('azu_sanitize_flag', $attributes['like']);
            $attributes['category'] = apply_filters('azu_sanitize_flag', $attributes['category']);
            $attributes['share'] = apply_filters('azu_sanitize_flag', $attributes['share']);
            $attributes['worker'] = apply_filters('azu_sanitize_flag', $attributes['worker']);
            
            $class ='azu-portfolio-single-info';
            $class_meta = 'azu-portfolio-single-details';
            $output ='';
            $class_content ='azu-single-meta-content';
            $content_text ='';
            if(!$attributes['directios']){
                $class .=' row';
                $class_meta .= ' col-sm-3';
                $class_content .= ' col-sm-9';
                $title_tag = AZU_PORTFOLIO_TITLE_H; //AZU_POST_TITLE_H;
            }
            else {
                $title_tag = AZU_PORTFOLIO_TITLE_H;
            }
            if(is_array($attributes['custom_fields'])){
                foreach ($attributes['custom_fields'] as $val){
                    $custom_link = isset($val['link']) ? $val['link'] : '';
                    $custom_value = isset($val['value']) ? $val['value'] : '';
                    if(isset($val['label']))
                        $output .='<div><h6>'.$val['label'].'</h6><span><a href="'.$custom_link.'">'.$custom_value.'</a></span></div>';
                }
            }
            if($attributes['sticky'])
                $class .= ' azu-sticky-js';
            
            if(!empty($attributes['title']))
                $content_text .='<'.$title_tag.'>'.$attributes['title'].'</'.$title_tag.'>';
            if(!empty($content))
                $content_text .='<p>'.$content.'</p>';      
            if(!empty($content_text))
                $content_text ='<div class="'.esc_attr($class_content).'">'.$content_text.'</div>';    

            if($attributes['date'])
                $output .='<div><h6>'.__( 'Date', 'azzu'.LANG_DN ).'</h6><span>'.azut()->azzu_get_post_date().'</span></div>';
            if($attributes['worker'])
                $output .='<div><h6>'.__( 'Author', 'azzu'.LANG_DN ).'</h6><span>'.azut()->azzu_get_post_author().'</span></div>';
            if($attributes['like'])
                $output .='<div><h6>'.__( 'Like', 'azzu'.LANG_DN ).'</h6><span>'.azu_love_this('',false).'</span></div>';
            if($attributes['category'])
                $output .='<div><h6>'.__( 'Category', 'azzu'.LANG_DN ).'</h6><span>'.azut()->azzu_get_post_categories(null).'</span></div>';
            if($attributes['share'])
                $output .='<div><h6>'.__( 'Share', 'azzu'.LANG_DN ).'</h6><span>'.azuh()->azzu_display_share_buttons(str_replace( 'azu_', '', get_post_type() ), array('echo' => false, 'extended' => true, 'share' => '')).'</span></div>';
            
            
            if(empty($attributes['morelink']))
            {
                global $post;
                $attributes['morelink'] = esc_url(get_post_meta( $post->ID, '_azu_project_options_link', true ));
            }
            if(!empty($attributes['morelink']))
                $output .='<div class="azu-meta-more-btn">'.azuh()->azzu_get_button_html( array( 'title' => __( 'Get More Info', 'azzu'.LANG_DN ), 'href'=>$attributes['morelink'],'class' => 'btn azu-btn-round' ) ).'</div>';
            
            $output = $content_text.'<div class="'.esc_attr($class_meta).'">'.$output.'</div>';
            
            $output = '<div class="'.esc_attr($class).'">' . $output . '</div>';
	    return $output;
    }
    
    // VC map function
    public function azu_vc_map() {
            if(!function_exists('vc_map')){ return; }
            //// ! Single_Meta
            vc_map(
                array(
                   "name" => __("Single Meta",'azzu'.LANG_DN),
                   "base" => "azu_single_meta",
                   "class" => "azu_vc_sc_single_meta",
                   "icon" => "azu_vc_ico_single_meta",
                   "description" => __("For portfolio",'azzu'.LANG_DN),
                   "category" => __("by Theme",'azzu'.LANG_DN),
                   "content_element" => true,
                   "show_settings_on_create" => true,
                   "params" => array(							
                                // Title
                                array(
                                        'type' => 'textfield',
                                        'heading' => __( 'Title', 'azzu'.LANG_DN ),
                                        'param_name' => 'title',
                                        'description' => __( 'Get default title when stay empty with content text.', 'azzu'.LANG_DN )
                                ),
                                // Add some description
                                array(
                                        "type" => "textarea_html",
                                        "class" => "",
                                        "heading" => __("Description", 'azzu'.LANG_DN),
                                        "param_name" => "content",
                                        "value" => "",
                                        "description" => __("Provide the description for portfolio.", 'azzu'.LANG_DN)
                                ),
                                // Show Vertical
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Vertical", 'azzu'.LANG_DN),
                                        "param_name" => "directios",
                                        "std" => "no",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),
                                array(
                                        'type' => 'param_group',
                                        'heading' => __( 'Custom fields', 'azzu'.LANG_DN ),
                                        'param_name' => 'custom_fields',
                                        'description' => __( 'Enter values for custom field type.', 'azzu'.LANG_DN ),
                                        'value' => urlencode( json_encode( array(
                                                array(
                                                        'label' => __( 'Client', 'azzu'.LANG_DN ),
                                                        'value' => '',
                                                        'link' => '',
                                                ),
                                        ) ) ),
                                        'params' => array(
                                                array(
                                                        'type' => 'textfield',
                                                        'heading' => __( 'Label', 'azzu'.LANG_DN ),
                                                        'param_name' => 'label',
                                                        'description' => __( 'Enter text used as title of field.', 'azzu'.LANG_DN ),
                                                        'admin_label' => true,
                                                ),
                                                array(
                                                        'type' => 'textfield',
                                                        'heading' => __( 'Value', 'azzu'.LANG_DN ),
                                                        'param_name' => 'value',
                                                        'description' => __( 'Enter value of custom field.', 'azzu'.LANG_DN ),
                                                        'admin_label' => true,
                                                ),
                                                array(
                                                        'type' => 'textfield',
                                                        'heading' => __( 'Link', 'azzu'.LANG_DN ),
                                                        'param_name' => 'link',
                                                        'description' => __( 'Enter link of custom field.', 'azzu'.LANG_DN ),
                                                        'admin_label' => true,
                                                ),
                                        ),
                                ),
                                // Show Sticky
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Sticky", 'azzu'.LANG_DN),
                                        "param_name" => "sticky",
                                        "std" => "yes",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),
                                array(
                                        'type' => 'textfield',
                                        'heading' => __( 'Link', 'azzu'.LANG_DN ),
                                        'param_name' => 'morelink',
                                        "value" => "",
                                        'description' => __( 'Enter more info link.', 'azzu'.LANG_DN ),
                                ),
                                // Show Date
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Date", 'azzu'.LANG_DN),
                                        "param_name" => "date",
                                        "std" => "yes",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),
                                // Show Author
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Author", 'azzu'.LANG_DN),
                                        "param_name" => "worker",
                                        "std" => "no",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),
                                // Like Date
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Like", 'azzu'.LANG_DN),
                                        "param_name" => "like",
                                        "std" => "yes",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),
                                // Show Category
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Category", 'azzu'.LANG_DN),
                                        "param_name" => "category",
                                        "std" => "yes",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),
                                // Show Share
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Share", 'azzu'.LANG_DN),
                                        "param_name" => "share",
                                        "std" => "yes",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),
                   ),
                )
            );
    }

}

// create shortcode
AZU_Shortcode_Single_Meta::get_instance();
