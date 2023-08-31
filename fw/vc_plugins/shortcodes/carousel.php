<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode gap class.
 *
 */
class AZU_Shortcode_Carousel extends AZU_Shortcode {

    static protected $instance;

    protected $shortcode_name = 'azu_carousel';

    public static function get_instance() {
        if ( !self::$instance ) {
            self::$instance = new AZU_Shortcode_Carousel();
        }
        return self::$instance;
    }

    public function __construct() {

        $this->addShortCode( $this->shortcode_name, array($this, 'shortcode') );
        $this->azu_vc_map();
    }

    public function shortcode( $atts, $content = null ) {
       extract( shortcode_atts( array(
		'images' => '',
                'padding' => '10',
                'lightbox' => '0',
                'proportion' => '',
                'slides' => '1',
                'autoplay_speed' => '0',
                'loop' => '1',
                'pagination' => '0',
                'azu_arrow' => '1',
                'el_class' => ''
	    ), $atts ) );
            $pagination = apply_filters('azu_sanitize_flag', $pagination);
            $loop = apply_filters('azu_sanitize_flag', $loop);
            $azu_arrow = apply_filters('azu_sanitize_flag', $azu_arrow);
            $lightbox = apply_filters('azu_sanitize_flag', $lightbox);
            $slides = in_array($slides, array('2', '3', '4', '5', '6')) ? absint($slides) : 1;
            $padding = absint($padding);
            $autoplay_speed = absint($autoplay_speed);
            $el_class = esc_attr($el_class);
            $column_width = azuf()->azu_calculate_width_size(in_array($slides, array('2', '3', '4', '5', '6')) ? absint($slides) : 1);
            if ( $proportion ) {
                    $wh = array_map( 'absint', explode(':', $proportion) );
                    if ( 2 == count($wh) && !empty($wh[0]) && !empty($wh[1]) ) {
                            $proportion = $wh[0]/$wh[1];
                    } else {
                            $proportion = '';
                    }
            }
            
	    $imagearray = explode(",", $images);
            
            $default_options = array(
                    'class'	=> array(),
                    'style'	=> ' style="width: 100%"'
            );
            $container_classes = array('azu-vc-carousel','azu-carousel-container');
            if(!empty($el_class))
                $container_classes[] = $el_class;
            if($lightbox)
                $container_classes[] = 'azu-gallery-container';
            
            $slider_args = array(
                    'height'	=> '',
                    'img_width'	=> $column_width,
                    'padding'    => $padding,
                    'proportion' => $proportion,
                    'class'     => $container_classes,
                    'custom'    => azuh()->azzu_get_share_buttons_for_photo('photo'),
                    'swiper'        => array(
                            'slidesPerView'=> $slides > 1 ? 'auto' : 1,
                            'slidesPerGroup' => $slides,
                            //'loopAdditionalSlides' => 1,
                            'loopedSlides' => $slides > 1 ? $slides : 1,
                            'enable_arrow' => $azu_arrow,
                            'loop' => $loop,
                            'calculateHeight' => true,
                            'autoplay' => $autoplay_speed
                        ),
                    'style' => $default_options['style']
            );
            
            if( $pagination ){
                    $slider_args['swiper']['paginationClickable'] = true;
					//$slider_args['swiper']['paginationHide'] = false;
                    $slider_args['swiper']['pagination'] = '.azu-swiper-container .carousel-indicator';
            }
            
            $attachments_data = array();
            if($lightbox)
                $href = '%HREF%';
            else
                $href = 'href="javascript:void(0);"';
            foreach ($imagearray as $image){
                $attachment_id = $image; // attachment ID
                $image_attributes = wp_get_attachment_image_src( $attachment_id , 'full'); // returns an array 
                if( $image_attributes ) {
                    $attachments_data[] = array(
                        'full' => $image_attributes[0],
                        'width' => $image_attributes[1],
                        'height' => $image_attributes[2],
                        'class' => $lightbox ? 'azu-mfp-item azu-gallery-mfp-popup azu-rollover mfp-image':'',
                        'wrap' => '<a '.$href.' %CLASS% %CUSTOM% title="%RAW_ALT%" data-azu-img-description="%RAW_TITLE%"><img %IMG_CLASS% %SRC% %IMG_TITLE% %ALT% %SIZE% /></a>'
                    );
                } 
            }       

            $output = azuh()->azzu_get_carousel_slider( $attachments_data, $slider_args );
	    return $output;
    }
    
    // VC map function
    public function azu_vc_map() {
            if(!function_exists('vc_map')){ return; }
            // ! Image Carousel
            vc_map( array(
                    "name" => __("Image carousel", 'azzu'.LANG_DN),
                    "base" => "azu_carousel",
                    "icon" => "azu_vc_ico_carousel",
                    "class" => "azu_vc_sc_carousel",
                    "description" => __("Image slider",'azzu'.LANG_DN),
                    "category" => __('by Theme', 'azzu'.LANG_DN),
                    "params" => array(
                            array(
                                    "type" => "attach_images",
                                    "class" => "",
                                    "heading" => __("Choose image id", 'azzu'.LANG_DN),
                                    "admin_label" => true,
                                    "param_name" => "images",
                                    "value" => "0",
                                    "description" => __("image id.", 'azzu'.LANG_DN)
                            ),
                            // Gap
                            array(
                                    "type" => "azu_number",
                                    "class" => "",
                                    "heading" => __("Gap between images (px)", 'azzu'.LANG_DN),
                                    "param_name" => "padding",
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
                                            "" => "yes"
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
                            // slides number
                            array(
                                    "type" => "azu_range",
                                    "class" => "",
                                    "heading" => __("Slides number", 'azzu'.LANG_DN),
                                    "param_name" => "slides",
                                    "value" => 1,
                                    "min" => 1,
                                    "max" => 6,
                                    "description" => __("How many slides show?", 'azzu'.LANG_DN)
                            ),
                            array(
                                    "type" => "azu_number",
                                    "class" => "",
                                    "heading" => __("Autoplay Speed",'azzu'.LANG_DN),
                                    "param_name" => "autoplay_speed",
                                    "value" => "0",
                                    "min" => "100",
                                    "max" => "30000",
                                    "step" => "100",
                                    "suffix" => "ms",
                                    "description" => __("Example: 1000ms = 1 second", 'azzu'.LANG_DN)
                            ),
                            // loop
                            array(
                                    "type" => "azu_toggle",
                                    "class" => "",
                                    "heading" => __("Loop", 'azzu'.LANG_DN),
                                    "param_name" => "loop",
                                    "std" => "yes",
                                    "value" => array(
                                            "" => "yes"
                                    ),
                                    "description" => __("Loop the slides", 'azzu'.LANG_DN),
                            ),
                             // arrow
                            array(
                                    "type" => "azu_toggle",
                                    "class" => "",
                                    "heading" => __("Show arrow", 'azzu'.LANG_DN),
                                    "param_name" => "azu_arrow",
                                    "std" => "yes",
                                    "value" => array(
                                            "" => "yes"
                                    ),
                                    "description" => __("left and right arrow", 'azzu'.LANG_DN),
                            ),
                            // pagination
                            array(
                                    "type" => "azu_toggle",
                                    "class" => "",
                                    "heading" => __("Pagination", 'azzu'.LANG_DN),
                                    "param_name" => "pagination",
                                    "std" => "no",
                                    "value" => array(
                                            "" => "yes"
                                    ),
                                    "description" => "",
                            ),

                            //Extra class
                            array(
                                    'type' => 'textfield',
                                    'heading' => __( 'Extra class name', 'azzu'.LANG_DN ),
                                    'param_name' => 'el_class',
                                    'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'azzu'.LANG_DN )
                            ),
                    )
             ));
    }
    
}

// create shortcode
AZU_Shortcode_Carousel::get_instance();