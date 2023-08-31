<?php
/**
 * Blog masonry shortcode.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode Blog masonry class.
 *
 */
class AZU_Shortcode_BlogPosts extends AZU_Shortcode {

	static protected $instance;

	protected $shortcode_name = 'azu_blog_posts';
	protected $post_type = 'post';
	protected $taxonomy = 'category';
        
	public static function get_instance() {
		if ( !self::$instance ) {
			self::$instance = new AZU_Shortcode_BlogPosts();
		}
		return self::$instance;
	}

	public function __construct() {
		$this->addShortCode( $this->shortcode_name, array($this, 'shortcode') );
                $this->azu_vc_map();
	}

	public function shortcode( $atts, $content = null ) {

                
		$attributes = shortcode_atts( array(
			'type'                  => 'masonry',
                        'azu_arrow'          => '1',
                        'image_size'            => '12',
			'category'              => '',
			'order'                 => 'desc',
			'orderby'               => 'date',
			'number'                => '12',
			'proportion'            => '',
                        'loading_mode'          => '0',
			'same_width'            => '0',
                        'align'                 => '0',
                        'readmore'               => '1',
			'padding'               => intval(of_get_option('general-gutter-width',AZZU_THEME_GUTTER)/2),
			'slides'                => '1',
                        'columns'               => '1'

		), $atts );
		
		// sanitize attributes
		$attributes['type'] = in_array($attributes['type'], array('masonry', 'grid', 'slider') ) ? $attributes['type'] : 'masonry';
                $attributes['image_size'] = in_array($attributes['image_size'], array('3', '4', '6', '8', '12') ) ? $attributes['image_size'] : '12';
		$attributes['order'] = apply_filters('azu_sanitize_order', $attributes['order']);
		$attributes['orderby'] = apply_filters('azu_sanitize_orderby', $attributes['orderby']);
		$attributes['number'] = apply_filters('azu_sanitize_posts_per_page', $attributes['number']);
		$attributes['same_width'] = apply_filters('azu_sanitize_flag', $attributes['same_width']);
                $attributes['loading_mode'] = in_array($attributes['loading_mode'], array('1', '2', '3')) ? absint($attributes['loading_mode']) : 0;
                $attributes['azu_arrow'] = apply_filters('azu_sanitize_flag', $attributes['azu_arrow']);
                
                $attributes['readmore'] = in_array($attributes['readmore'], array('1','2', '3', '4', '5')) ? absint($attributes['readmore']) : 0;
                $attributes['align'] = in_array($attributes['align'], array('1', '2', '3')) ? absint($attributes['align']) : 0;
                
                $attributes['padding'] = absint($attributes['padding']);
                $attributes['slides'] = in_array($attributes['slides'], array('2', '3', '4','5')) ? absint($attributes['slides']) : 1;
                $attributes['columns'] = in_array($attributes['columns'], array('2', '3', '4', '5', '6')) ? absint($attributes['columns']) : 1;
		$attributes['column_width'] = azuf()->azu_calculate_width_size($attributes['columns']);
                
                if($attributes['type'] !== 'slider' && $attributes['loading_mode'] > 0) {
                    wp_enqueue_style( 'wp-mediaelement' );
                    wp_enqueue_script( 'wp-mediaelement' );
                }
                
		if ( $attributes['category']) {
			$attributes['category'] = $this->azu_category($attributes['category']);
			$attributes['select'] = 'only';
		} else {
			$attributes['select'] = 'all';
		}

		if ( $attributes['proportion'] ) {
			$wh = array_map( 'absint', explode(':', $attributes['proportion']) );
			if ( 2 == count($wh) && !empty($wh[0]) && !empty($wh[1]) ) {
				$attributes['proportion'] = $wh[0]/$wh[1];
			} else {
				$attributes['proportion'] = '';
			}
		}
                
                if($attributes['readmore']=='0')
                    $attributes['readmore'] = '';
                elseif($attributes['readmore']=='2')
                    $attributes['readmore'] = _x( 'Keep reading', 'atheme', 'azzu'.LANG_DN );
                elseif($attributes['readmore']=='3')
                    $attributes['readmore'] = _x( 'Continue reading', 'atheme', 'azzu'.LANG_DN );
                elseif($attributes['readmore']=='4')
                    $attributes['readmore'] = _x( 'Details', 'atheme', 'azzu'.LANG_DN );
                elseif($attributes['readmore']=='5')
                    $attributes['readmore'] = _x( 'More', 'atheme', 'azzu'.LANG_DN );
                else
                    $attributes['readmore'] = _x( 'Read more', 'atheme', 'azzu'.LANG_DN );
                
		$output = '';
                
                switch ( $attributes['type'] ) {
			case 'slider' : $output .= $this->blog_slider($attributes); break;
			default : $output .= $this->blog_grid($attributes);
		}

		return $output; 
	}
        
       
	/**
	 * Blog.
	 *
	 */
	public function blog_grid( $attributes = array() ) {
		global $post;

		$post_backup = $post;

		$azu_query = $this->get_posts_by_terms( $attributes );

		$output = '';

		if ( $azu_query->have_posts() ) {

			

			// backup and reset config
			$config_backup = azum()->get();

			azum()->set('layout', $attributes['type']);
                        $attributes['template'] = 'blog';
			azum()->set('template', $attributes['template']);
                        
                        $attributes['description'] = 'under_image';
                        
                        
                        azum()->set('attr', $attributes);
                        $before_output = ''; 
                        $after_output='';
                        
                        // loop
                        $output .= azuf()->azu_get_posttype_content_loop($azu_query, $attributes['template']);

                        //show pagination
                        $guid=str_replace( array('{','}','-'),'', uniqid());
                        $after_output = azuh()->azu_get_pagination_type($attributes['loading_mode'], $azu_query->max_num_pages, $guid);

			// restore original $post
			$post = $post_backup;
			setup_postdata( $post );

			// restore config
			azum()->reset($config_backup);

			// masonry layout classes
			$masonry_container_classes = array( 'isotope', 'with-ajax', 'shortcode-blog-posts', 'description-under-image' );
			switch ( $attributes['type'] ) {
				case 'grid':
					$masonry_container_classes[] = 'iso-grid';
					break;
				case 'masonry':
					$masonry_container_classes[] = 'iso-container';
			}
                        
                        if($attributes['columns']== 1 && $attributes['image_size'] =='12' && azuf()->azu_get_option('sidebar_position') != 'dual'){
                                $masonry_container_classes[] = 'azu-post-full';
                        }
                        
                        if($attributes['align']=="1"){
                            $masonry_container_classes[] = 'azu-post-align-center';
                        }
                        elseif($attributes['align']=="2"){
                            $masonry_container_classes[] = 'azu-post-align-right';
                        }
                        elseif($attributes['align']=="3"){
                            $masonry_container_classes[] = 'azu-post-align-justify';
                        }
                        
			$masonry_container_classes = implode(' ', $masonry_container_classes);

                        wp_localize_script( 'azu-main', 'ajax_'.$guid, $attributes );
			$masonry_container_data_attr = array(
                                'data-guid="'.$guid.'"',
                                'data-min-width="260"',
				'data-padding="' . intval($attributes['padding']) . 'px"',
				'data-columns="' . intval($attributes['columns']) . '"',
                                'data-ratio="' . $attributes['proportion'] . '"'
			);

			// attribute
			$masonry_container_data_attr = implode(' ', $masonry_container_data_attr);

			// wrap output
			$output = sprintf( '<div class="%s" %s>%s</div>',
				esc_attr($masonry_container_classes),
				$masonry_container_data_attr,
				$output
			);

                        $output = $before_output.$output.$after_output;
                        
		} // if have posts

		if ( function_exists('vc_is_inline') && vc_is_inline() ) {

			$terms_list = azuh()->azzu_get_terms_list_by_id( array( 'term_id' => $attributes['category'], 'taxonomy' => 'category' ) );
	
			$output = '
				<div class="azu_vc-shortcode_dummy azu_vc-blog" style="height: 250px;">
					<h5>Blog</h5>
					<p class="text-small"><strong>Display categories:</strong> ' . $terms_list . '</p>
				</div>
			';
		}

		return $output;
	}

         /**
	 * post slider.
	 *
	 */
	public function blog_slider( $attributes = array() ) {
		$attributes['template'] = 'blog';
                $slides = $attributes['slides'];
                $attributes['image_size'] = '12';
                
                if(!array_key_exists("class",$attributes)){
                    $attributes['class'] = 'azu-swiper-post';
                }
                
                if($attributes['align']=="1"){
                    $attributes['class'] .= ' azu-post-align-center';
                }
                elseif($attributes['align']=="2"){
                    $attributes['class'] .= ' azu-post-align-right';
                }
                elseif($attributes['align']=="3"){
                    $attributes['class'] .= ' azu-post-align-justify';
                }
                
                if($slides > 1){
                        $attributes['class'] .= ' azu-swiper-col-'.$slides;
                }
                
                $swiper_options = array(
                                'slidesPerView'=> $slides > 1 ? 'auto' : '1',
                                'slidesPerGroup' => $slides,
				//'loopedSlides' => $slides > 1 ? $slides : 1,
                                'enable_arrow' => $attributes['azu_arrow'],
                                'calculateHeight' => true,
                                'speed' => 500,
                                'loop'=> false
                            );
                return $this->azu_posttype_slider($attributes, $swiper_options);
	}

        // VC map function
        public function azu_vc_map() {
                if(!function_exists('vc_map')){ return; }
                // ! Blog
                vc_map( array(
                        "name" => __("Blog", 'azzu'.LANG_DN),
                        "base" => "azu_blog_posts",
                        "icon" => "azu_vc_ico_blog_posts",
                        "class" => "azu_vc_sc_blog_posts",
                        "description" => __("Loop",'azzu'.LANG_DN),
                        "category" => __('by Theme', 'azzu'.LANG_DN),
                        "params" => array(

                                // Taxonomy
                                array(
                                        "type" => "azu_taxonomy",
                                        "taxonomy" => "category",
                                        "class" => "",
                                        "heading" => __("Categories", 'azzu'.LANG_DN),
                                        "param_name" => "category",
                                        "description" => __("Note: By default, all your posts will be displayed. <br>If you want to narrow output, select category(s) above. Only selected categories will be displayed.", 'azzu'.LANG_DN)
                                ),

                                // Appearance
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Appearance", 'azzu'.LANG_DN),
                                        "param_name" => "type",
                                        "admin_label" => true,
                                        "value" => array(
                                                __("Masonry", 'azzu'.LANG_DN) => "masonry",
                                                __("Grid", 'azzu'.LANG_DN) => "grid",
                                                __("Slider", 'azzu'.LANG_DN) => "slider"
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
                                        "dependency" => array(
                                                "element" => "type",
                                                "value" => array(
                                                        "masonry",
                                                        "grid"
                                                )
                                        ),
                                        "description" => __("How many columns?", 'azzu'.LANG_DN),
                                ),
                                // slides number
                                array(
                                        "type" => "azu_range",
                                        "class" => "",
                                        "heading" => __("Slides number", 'azzu'.LANG_DN),
                                        "param_name" => "slides",
                                        "value" => 1,
                                        "min" => 1,
                                        "max" => 5,
                                        "dependency" => array(
                                                "element" => "type",
                                                "value" => array(
                                                        "slider"
                                                )
                                        ),
                                        "description" => __("Visible Items at Once", 'azzu'.LANG_DN)
                                ),
                                // arrow
                                array(
                                       "type" => "azu_toggle",
                                       "class" => "",
                                       "heading" => __("Show arrow", 'azzu'.LANG_DN),
                                       "param_name" => "azu_arrow",
                                       "std" => "no",
                                       "value" => array(
                                               "" => "yes"
                                       ),
                                       "dependency" => array(
                                                "element" => "type",
                                                "value" => array(
                                                        "slider"
                                                )
                                       ),
                                       "description" => __("left and right arrow", 'azzu'.LANG_DN),
                                ),
                                // Gap
                                array(
                                        "type" => "azu_number",
                                        "class" => "",
                                        "heading" => __("Gap between posts (px)", 'azzu'.LANG_DN),
                                        "param_name" => "padding",
                                        "value" => 15,
                                        "min" => 0,
                                        "max" => 30,
                                        "description" => __("Post paddings (e.g. 5 pixel padding will give you 10 pixel gaps between images)", 'azzu'.LANG_DN),
                                ),

                                //Image size
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Image width percentage", 'azzu'.LANG_DN),
                                        "param_name" => "image_size",
                                        "value" => array(
                                                __("100% full", 'azzu'.LANG_DN) => "12",
                                                __("66% large", 'azzu'.LANG_DN) => "8",
                                                __("50% normal", 'azzu'.LANG_DN) => "6",
                                                __("33% small", 'azzu'.LANG_DN) => "4",
                                                __("25% extra small", 'azzu'.LANG_DN) => "3"
                                        ),
                                        "dependency" => array(
                                                "element" => "type",
                                                "value" => array(
                                                        "masonry",
                                                        "grid"
                                                )
                                        ),
                                        "description" => ""
                                ),

                                // Proportions
                                array(
                                        "type" => "textfield",
                                        "class" => "",
                                        "heading" => __("Image proportions", 'azzu'.LANG_DN),
                                        "param_name" => "proportion",
                                        "value" => "",
                                        "description" => __("Width:height (e.g. 4:3). Leave this field empty to preserve original image proportions.", 'azzu'.LANG_DN)
                                ),

                                // Post width
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Make posts same width", 'azzu'.LANG_DN),
                                        "param_name" => "same_width",
                                        "std" => "no",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),

                                //Align
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Text align", 'azzu'.LANG_DN),
                                        "param_name" => "align",
                                        "value" => array(
                                                __("Left", 'azzu'.LANG_DN) => "0",
                                                __("Center", 'azzu'.LANG_DN) => "1",
                                                __("Right", 'azzu'.LANG_DN) => "2",
                                                __("Justify", 'azzu'.LANG_DN) => "3",
                                        ),
                                        "description" => ""
                                ),
                                //Read more
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Read more link", 'azzu'.LANG_DN),
                                        "param_name" => "readmore",
                                        "value" => array(
                                                __("Read more", 'azzu'.LANG_DN) => "1",
                                                __("None", 'azzu'.LANG_DN) => "0",
                                                __("Keep reading", 'azzu'.LANG_DN) => "2",
                                                __("Continue reading", 'azzu'.LANG_DN) => "3",
                                                __("Details", 'azzu'.LANG_DN) => "4",
                                                __("More", 'azzu'.LANG_DN) => "5",
                                        ),
                                        "description" => ""
                                ),
                            
                                //Loading mode
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Loading mode", 'azzu'.LANG_DN),
                                        "param_name" => "loading_mode",
                                        "dependency" => array(
                                                "element" => "type",
                                                "value" => array(
                                                        "masonry",
                                                        "grid"
                                                )
                                        ),
                                        "value" => array(
                                                __("None", 'azzu'.LANG_DN) => "0",
                                                __("pagination", 'azzu'.LANG_DN) => "1",
                                                __("Load more", 'azzu'.LANG_DN) => "2",
                                                __("Auto load", 'azzu'.LANG_DN) => "3"
                                        ),
                                        "description" => ""
                                ),

                                // Number of posts
                                array(
                                        "type" => "azu_number",
                                        "class" => "",
                                        "heading" => __("Number of posts to show", 'azzu'.LANG_DN),
                                        "param_name" => "number",
                                        "value" => 12,
                                        "min" => 1,
                                        "max" => 100,
                                        "description" => __("(Integer)", 'azzu'.LANG_DN)
                                ),

                                // Order by
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Order by", 'azzu'.LANG_DN),
                                        "param_name" => "orderby",
                                        "value" => array(
                                                __("Date", 'azzu'.LANG_DN) => "date",
                                                __("Author", 'azzu'.LANG_DN) => "author",
                                                __("Title", 'azzu'.LANG_DN) => "title",
                                                __("Slug", 'azzu'.LANG_DN) => "name",
                                                __("Date modified", 'azzu'.LANG_DN) => "modified",
                                                __("ID", 'azzu'.LANG_DN) => "id",
                                                __("Random", 'azzu'.LANG_DN) => "rand"
                                        ),
                                        "description" => __("Select how to sort retrieved posts.", 'azzu'.LANG_DN)
                                ),

                                // Order
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Order way", 'azzu'.LANG_DN),
                                        "param_name" => "order",
                                        "value" => array(
                                                __("Descending", 'azzu'.LANG_DN) => "desc",
                                                __("Ascending", 'azzu'.LANG_DN) => "asc"
                                        ),
                                        "description" => __("Designates the ascending or descending order.", 'azzu'.LANG_DN)
                                )
                        )
                ) );
        }
        
        
}

// create shortcode
AZU_Shortcode_BlogPosts::get_instance();
