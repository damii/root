<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode gap class.
 *
 */
class AZU_Shortcode_Gallery extends AZU_Shortcode {

    static protected $instance;

    protected $shortcode_name = 'azu_gallery';

    public static function get_instance() {
        if ( !self::$instance ) {
            self::$instance = new AZU_Shortcode_Gallery();
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
		'images' => 0,
                'type' => 'grid',
                'item_padding' => '10',
                'lightbox' => '0',
                'columns' => '1',
                'proportion' => '',
                'css'  => '',
                'el_class' => '',
	    ), $atts  );
            
            // sanitize attributes
            $attributes['type'] = in_array($attributes['type'], array('masonry', 'grid') ) ? $attributes['type'] : 'grid';
            $attributes['item_padding'] = absint($attributes['item_padding']);
            $attributes['column_width'] = azuf()->azu_calculate_width_size(in_array($attributes['columns'], array('2', '3', '4', '5', '6')) ? absint($attributes['columns']) : 1);
            $attributes['lightbox'] = apply_filters('azu_sanitize_flag', $attributes['lightbox']);
            $attributes['el_class'] = esc_attr($attributes['el_class']);
            if ( $attributes['proportion'] ) {
                    $wh = array_map( 'absint', explode(':', $attributes['proportion']) );
                    if ( 2 == count($wh) && !empty($wh[0]) && !empty($wh[1]) ) {
                            $attributes['proportion'] = $wh[0]/$wh[1];
                    } else {
                            $attributes['proportion'] = '';
                    }
            }
            
            $output ='';
	    $imagearray = explode(",", $attributes['images']);
            $thumb_options = array( 'w' => $attributes['column_width'], 'z' => 1 );
            
            if($attributes['lightbox'])
                $href = '%HREF%';
            else
                $href = 'href="javascript:void(0);"';
            
            foreach ($imagearray as $image){
                $thumb_meta = wp_get_attachment_image_src( $image, 'full' ); // returns an array 
                $thumb_args = array(
			'img_meta' 	=> $thumb_meta,
			'img_id'	=> $image,
			'class'		=> $attributes['lightbox'] ? 'azu-mfp-item azu-gallery-mfp-popup azu-rollover mfp-image' : '',
			'options'	=> $thumb_options,
			'custom'	=> '',
			'echo'		=> false,
			'wrap'		=> '<a '.$href.' %CLASS% %CUSTOM% title="%RAW_ALT%" data-azu-img-description="%RAW_TITLE%"><img %IMG_CLASS% %SRC% %IMG_TITLE% %ALT% %SIZE% /></a>',
		);
                $output .='<div class="iso-item">'.azuf()->azu_get_thumb_img( $thumb_args ).'</div>';
            }  
            
                    

            $class = 'isotope azu-vc-gallery';
            $class .= apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $attributes['css'], ' ' ), "azu_button", $atts );
            
            
            if($attributes['lightbox'])
                $class .= ' azu-gallery-container';
            

            switch ( $attributes['type'] ) {
                    case 'grid':
                            $class .= ' iso-grid';
                            break;
                    case 'masonry':
                            $class .= ' iso-container';
                            break;
            }


            $masonry_container_data_attr = array(
                    'data-padding="' . intval($attributes['item_padding']) . 'px"',
                    azuh()->azzu_get_share_buttons_for_photo('photo'),
                    'data-min-width="200"',
                    'data-columns="' . intval($attributes['columns']) . '"',
                    'data-ratio="' . $attributes['proportion'] . '"'
            );

            // data attribute
            $masonry_container_data_attr = ' ' . implode(' ', $masonry_container_data_attr);

            // wrap output
            $output = sprintf( '<div class="%s" %s>%s</div>',
                    esc_attr($class),
                    $masonry_container_data_attr,
                    $output
            );
            
            if($attributes['title'])
                $output = '<'.AZU_TITLE_H.'>'.$attributes['title'].'</'.AZU_TITLE_H.'>'.$output;
	    return $output;
    }
    
    // VC map function
    public function azu_vc_map() {
            if(!function_exists('vc_map')){ return; }
            // ! Gallery Azu
            vc_map( array(
                    "name" => __("Gallery", 'azzu'.LANG_DN),
                    "base" => "azu_gallery",
                    "icon" => "azu_vc_ico_gallery",
                    "class" => "azu_vc_sc_gallery",
                    "description" => __("Image gallery",'azzu'.LANG_DN),
                    "category" => __('by Theme', 'azzu'.LANG_DN),
                    "params" => array(
                                array(
                                        'type' => 'textfield',
                                        'heading' => __( 'Widget title', 'azzu'.LANG_DN ),
                                        'param_name' => 'title',
                                        'description' => __( 'Enter text which will be used as widget title. Leave blank if no title is needed.', 'azzu'.LANG_DN )
                                ),
                                array(
                                        "type" => "attach_images",
                                        "class" => "",
                                        "heading" => __("Choose image id", 'azzu'.LANG_DN),
                                        "admin_label" => true,
                                        "param_name" => "images",
                                        "value" => "0",
                                        "description" => __("image id.", 'azzu'.LANG_DN)
                                ),
                                // Appearance
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Appearance", 'azzu'.LANG_DN),
                                        "param_name" => "type",
                                        "std" => "grid",
                                        "value" => array(
                                                __("Masonry", 'azzu'.LANG_DN) => "masonry",
                                                __("Grid", 'azzu'.LANG_DN) => "grid"
                                        ),
                                        "description" => ""
                                ),
                                // Column number
                                array(
                                        "type" => "azu_range",
                                        "class" => "",
                                        "heading" => __("Column number", 'azzu'.LANG_DN),
                                        "param_name" => "columns",
                                        "value" => 1,
                                        "min" => 1,
                                        "max" => 6,
                                        "description" => __("How many columns?", 'azzu'.LANG_DN),
                                ),
                                // Gap
                                array(
                                        "type" => "azu_number",
                                        "class" => "",
                                        "heading" => __("Gap between images (px)", 'azzu'.LANG_DN),
                                        "param_name" => "item_padding",
                                        "value" => 10,
                                        "min" => 0,
                                        "max" => 300,
                                        "description" => __("Image paddings (e.g. 5 pixel padding will give you 10 pixel gaps between posts)", 'azzu'.LANG_DN),
                                ),
                                // open in lightbox
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Open in lighbox", 'azzu'.LANG_DN),
                                        "param_name" => "lightbox",
                                        "std" => "no",
                                        "value" => array(
                                                "std" => "yes",
                                        ),
                                        "description" => __("If selected, larger image will be opened on click.", 'azzu'.LANG_DN),
                                ),

                                // Proportions
                                array(
                                        "type" => "textfield",
                                        "class" => "",
                                        "heading" => __("Post proportions", 'azzu'.LANG_DN),
                                        "param_name" => "proportion",
                                        "value" => "",
                                        "description" => __("Width:height (e.g. 4:3). Leave this field empty to preserve original image proportions.", 'azzu'.LANG_DN)
                                ),
                                //Extra class
                                array(
                                        'type' => 'textfield',
                                        'heading' => __( 'Extra class name', 'azzu'.LANG_DN ),
                                        'param_name' => 'el_class',
                                        'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'azzu'.LANG_DN )
                                ),
                                array(
                                    'type' => 'css_editor',
                                    'heading' => __( 'Css', 'azzu'.LANG_DN ),
                                    'param_name' => 'css',
                                    'group' => __( 'Design', 'azzu'.LANG_DN ),
                                    'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border',
                                ),
                    )
             ));
    }
    
}

// create shortcode
AZU_Shortcode_Gallery::get_instance();