<?php
/**
 * Portfolio shortcode.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode testimonials class.
 *
 */
class AZU_Shortcode_Portfolio extends AZU_Shortcode {

	static protected $instance;

	protected $shortcode_name = 'azu_portfolio';
	protected $post_type = 'azu_portfolio';
	protected $taxonomy = 'azu_portfolio_category';
	protected $atts;

	public static function get_instance() {
		if ( !self::$instance ) {
			self::$instance = new AZU_Shortcode_Portfolio();
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
			'category'              => '',
                        'show_filter'           => '1',
                        'filter_align'          => '1',
			'order'                 => 'desc',
			'orderby'               => 'date',
			'number'                => '12',
			'show_title'            => '1',
			'show_excerpt'          => '1',
			'show_details'          => '1',
                        'show_like'             => '0',
                        'show_zoom'             => '0',
                        'show_link'             => '1',
                        'override_link'         => '0',
			'meta_info'             => '1',
                        'azu_arrow'          => '0',
			// masonry/grid
                        'descriptions'          => '0',
			'hover_effect'          => '',
			'proportion'            => '',
                        'loading_mode'          => '0',
			'same_width'            => '0',
			'padding' 		=> '5',
			'columns' 		=> '1',
                        'slides'                => '1',
                        'full_width' 		=> '0'

		), $atts );
		
		// sanitize attributes
		$attributes['type'] = in_array($attributes['type'], array('masonry', 'grid', 'slider') ) ? $attributes['type'] : 'masonry';
		$attributes['order'] = apply_filters('azu_sanitize_order', $attributes['order']);
		$attributes['orderby'] = apply_filters('azu_sanitize_orderby', $attributes['orderby']);
		$attributes['number'] = apply_filters('azu_sanitize_posts_per_page', $attributes['number']);

		if ( $attributes['category']) {
                        $attributes['category'] = $this->azu_category($attributes['category']);
			$attributes['select'] = 'only';
		} else {
			$attributes['select'] = 'all';
		}

                $attributes['show_filter'] = apply_filters('azu_sanitize_flag', $attributes['show_filter']);
                $attributes['filter_align'] = in_array($attributes['filter_align'], array('1', '2')) ? absint($attributes['filter_align']) : '0';
		$attributes['azu_arrow'] = apply_filters('azu_sanitize_flag', $attributes['azu_arrow']);
                $attributes['show_title'] = apply_filters('azu_sanitize_flag', $attributes['show_title']);
		$attributes['show_excerpt'] = apply_filters('azu_sanitize_flag', $attributes['show_excerpt']);
		$attributes['show_details'] = apply_filters('azu_sanitize_flag', $attributes['show_details']);
                $attributes['override_link'] = apply_filters('azu_sanitize_flag', $attributes['override_link']);
                $attributes['show_link'] = apply_filters('azu_sanitize_flag', $attributes['show_link']);
                $attributes['show_like'] = apply_filters('azu_sanitize_flag', $attributes['show_like']);
		$attributes['show_zoom'] = apply_filters('azu_sanitize_flag', $attributes['show_zoom']);
		$attributes['meta_info'] = apply_filters('azu_sanitize_flag', $attributes['meta_info']);

		// masonry/grid
                $attributes['full_width'] = apply_filters('azu_sanitize_flag', $attributes['full_width']);
                $attributes['slides'] = in_array($attributes['slides'], array('2', '3', '4','5')) ? absint($attributes['slides']) : 1;
                $attributes['columns'] = in_array($attributes['columns'], array('2', '3', '4', '5', '6')) ? absint($attributes['columns']) : 1;
                $attributes['column_width'] = azuf()->azu_calculate_width_size($attributes['columns'],$attributes['full_width']);
                
		$attributes['descriptions'] = apply_filters('azu_sanitize_flag', $attributes['descriptions']);

                $attributes['hover_effect'] = in_array($attributes['hover_effect'], array('lily','sadie','layla','oscar','marley','ruby','roxy','bubba','sarah','chico','zoe','julia','selena','apollo','steve','jazz', 'ming')) ? $attributes['hover_effect'] : '';

		$attributes['same_width'] = apply_filters('azu_sanitize_flag', $attributes['same_width']);
                $attributes['loading_mode'] = in_array($attributes['loading_mode'], array('1', '2', '3')) ? absint($attributes['loading_mode']) : '0';
		
                // grid
		$attributes['padding'] = intval($attributes['padding']);

		if ( $attributes['proportion'] ) {
			$wh = array_map( 'absint', explode(':', $attributes['proportion']) );
			if ( 2 == count($wh) && !empty($wh[0]) && !empty($wh[1]) ) {
				$attributes['proportion'] = $wh[0]/$wh[1];
			} else {
				$attributes['proportion'] = '';
			}
		}

		$output = '';
		
                switch ( $attributes['type'] ) {
			case 'slider' : $output .= $this->portfolio_slider($attributes); break;
			default : $output .= $this->portfolio_grid($attributes);
		}

                if ( $attributes['full_width'] ) {
                        $output = '<div class="azu-full-width">' . $output . '</div>';
                }
		return $output;
	}

	/**
	 * Portfolio.
	 *
	 */
	public function portfolio_grid( $attributes = array() ) {
		global $post;

		$post_backup = $post;

		$azu_query = $this->get_posts_by_terms( $attributes );
                
		$output = '';

		if ( $azu_query->have_posts() ) {

                    	// backup and reset config
			$config_backup = azum()->get();
                    
                        azum()->set('layout', $attributes['type']);
                        $attributes['template'] = 'portfolio';
			azum()->set('template', $attributes['template']);
                        
                        azum()->set('attr', $attributes);
                        
			$details_already_hidden = false;
			if ( !$attributes['show_details'] && !has_filter('azzu_post_readmore_link', 'azzu_return_empty_string') ) {
				add_filter('azzu_post_readmore_link', 'azzu_return_empty_string');
				$details_already_hidden = true;
			}

                        
			$before_output = ''; 
                        $after_output='';
                        // categorizer
                        if ( $attributes['show_filter'] ) 
                        {
                                $filter_class = 'filter-ajax with-ajax';
                                
                                if($attributes['filter_align'] == '1')
                                        $filter_class .= ' azu-filter-align-center';
                                elseif($attributes['filter_align'] == '2')
                                        $filter_class .= ' azu-filter-align-right';
                                // categorizer args
                                $filter_args = array(
                                        'taxonomy'	=> $this->taxonomy,
                                        'post_type'	=> $this->post_type,
                                        'select'	=> $attributes['select'],
                                        'field'         => 'term_id',
                                        'terms'		=> is_array($attributes['category']) ? $attributes['category'] : array(),
                                );
                                // display categorizer
                                $before_output = '<div class="'.$filter_class.'" >'.azuh()->azzu_get_category_list( array(
                                        'data'	=> azuf()->azu_prepare_categorizer_data( $filter_args ),
                                        'class'	=> 'filter' 
                                ) ).'</div>';
                        }
                        

                        // loop
                        $output .= azuf()->azu_get_posttype_content_loop($azu_query, $attributes['template']);
                        
                        //show pagination
                        $guid=str_replace( array('{','}','-'),'', uniqid());
                        $after_output = azuh()->azu_get_pagination_type($attributes['loading_mode'], $azu_query->max_num_pages, $guid);
                        
			if ( $details_already_hidden ) {
				// remove details filter
				remove_filter('azzu_post_readmore_link', 'azzu_return_empty_string');
			}

			// restore original $post
			$post = $post_backup;
			setup_postdata( $post );

                        // restore config
			azum()->reset($config_backup);
 
			// isotope layout classes
			$masonry_container_classes = array( 'isotope', 'with-ajax', 'img-grid' );

                        switch ( $attributes['type'] ) {
				case 'grid':
					$masonry_container_classes[] = 'iso-grid';
					break;
				case 'masonry':
					$masonry_container_classes[] = 'iso-container';
                                        break;
			}
                        
			$masonry_container_classes = implode(' ', $masonry_container_classes);
                        
                        wp_localize_script( 'azu-main', 'ajax_'.$guid, $attributes );
			$container_data_attr = array(
                                'data-guid="'.$guid.'"',
                                'data-min-width="220"',
				'data-padding="' . intval($attributes['padding']) . 'px"',
				'data-columns="' . intval($attributes['columns']) . '"',
                                'data-ratio="' . $attributes['proportion'] . '"'
			);

			// data attribute
			$container_data_attr = implode(' ', $container_data_attr);

			// wrap output
			$output = sprintf( '<div class="%s" %s>%s</div>',
				esc_attr($masonry_container_classes),
				$container_data_attr,
				$output
			);
                        
                        $output = $before_output.$output.$after_output;

		} // if have posts

		if ( function_exists('vc_is_inline') && vc_is_inline() ) {
			$terms_list = azuh()->azzu_get_terms_list_by_id( array( 'term_id' => $attributes['category'], 'taxonomy' => 'azu_portfolio_category' ) );

			$output = '
				<div class="azu_vc-shortcode_dummy azu_vc-portfolio" style="height: 250px;">
					<h5>Portfolio</h5>
					<p class="text-small"><strong>Display categories:</strong> ' . $terms_list . '</p>
				</div>
			';
		}
                
		return $output;
	}
        
        /**
	 * Portfolio slider.
	 *
	 */
	public function portfolio_slider( $attributes = array() ) {
		$attributes['template'] = 'portfolio';
                $slides = $attributes['slides'];
                //$attributes['class'] = 'azu-carousel-mode';
                $swiper_options = array(
                                'slidesPerView'=> $slides > 1 ? 'auto' : '1',
                                'slidesPerGroup' => $slides,
                                'enable_arrow' => $attributes['azu_arrow'],
                                //'initialSlide' => 0,
                                //'freeModeFluid' => true,
                                //'resistance' => '100%',
                                //'centeredSlides' => true,
                                //'resizeReInit' => true,
                                //'autoResize' => true,
                                'calculateHeight' => true,
                                'speed' => 500,
                                'loop'=> false
                            );
                return $this->azu_posttype_slider($attributes, $swiper_options);
	}
        // VC map function
        public function azu_vc_map() {
                if(!function_exists('vc_map')){ return; }
                // ! Portfolio
                vc_map( array(
                        "name" => __("Portfolio", 'azzu'.LANG_DN),
                        "base" => "azu_portfolio",
                        "icon" => "azu_vc_ico_portfolio",
                        "class" => "azu_vc_sc_portfolio",
                        "description" => __("Loop",'azzu'.LANG_DN),
                        "category" => __('by Theme', 'azzu'.LANG_DN),
                        "params" => array(

                                // Terms
                                array(
                                        "type" => "azu_taxonomy",
                                        "taxonomy" => "azu_portfolio_category",
                                        "class" => "",
                                        "heading" => __("Categories", 'azzu'.LANG_DN),
                                        "param_name" => "category",
                                        "description" => __("Note: By default, all your projects will be displayed. <br>If you want to narrow output, select category(s) above. Only selected categories will be displayed.", 'azzu'.LANG_DN)
                                ),

                                // Appearance
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Appearance", 'azzu'.LANG_DN),
                                        "admin_label" => true,
                                        "param_name" => "type",
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
                                        "description" => __("How many columns?", 'azzu'.LANG_DN),
                                        "dependency" => array(
                                                "element" => "type",
                                                "value" => array(
                                                        "masonry",
                                                        "grid"
                                                )
                                        )
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
                                        "heading" => __("Gap between images (px)", 'azzu'.LANG_DN),
                                        "param_name" => "padding",
                                        "value" => 5,
                                        "min" => 0,
                                        "max" => 300,
                                        "description" => __("Image paddings (e.g. 5 pixel padding will give you 10 pixel gaps between images)", 'azzu'.LANG_DN),
                                ),

                                // Projects width
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Make projects same width", 'azzu'.LANG_DN),
                                        "param_name" => "same_width",
                                        "std" => "no",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),

                                // Proportions
                                array(
                                        "type" => "textfield",
                                        "class" => "",
                                        "heading" => __("Thumbnails proportions", 'azzu'.LANG_DN),
                                        "param_name" => "proportion",
                                        "value" => "",
                                        "description" => __("Width:height (e.g. 16:9). Leave this field empty to preserve original image proportions.", 'azzu'.LANG_DN)
                                ),

                                // Show filter
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "std" => "yes",
                                        "heading" => __("Show category filter", 'azzu'.LANG_DN),
                                        "param_name" => "show_filter",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),
                            
                                //Filter Align
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Filter alignment", 'azzu'.LANG_DN),
                                        "param_name" => "filter_align",
                                        "dependency" => array(
                                                "element" => "show_filter",
                                                //"not_empty" => false,
                                                "value" => array( "yes" )
                                        ),
                                        "value" => array(
                                                __("Center", 'azzu'.LANG_DN) => "1",
                                                __("Left", 'azzu'.LANG_DN) => "0",
                                                __("Right", 'azzu'.LANG_DN) => "2"
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
                                        )
                                ),

                                // Description
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Descriptions under image", 'azzu'.LANG_DN),
                                        "param_name" => "descriptions",
                                        "std" => "no",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),

                                // Hover effect
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Hover effect", 'azzu'.LANG_DN),
                                            "param_name" => "hover_effect",
                                            "value" => array(
                                                "Default hover" => "",
                                                "Lily" => "lily",
                                                "Sadie" => "sadie",
                                                "Layla" => "layla",
                                                "Oscar" => "oscar",
                                                "Marley" => "marley",
                                                "Ruby" => "ruby",
                                                "Roxy" => "roxy",
                                                "Bubba" => "bubba",
                                                "Sarah" => "sarah",
                                                "Chico" => "chico",
                                                "Zoe" => "zoe",
                                                "Julia" => "julia",
                                                "Selena" => "selena",
                                                "Apollo" => "apollo",
                                                "Steve" => "steve",
                                                "Jazz" => "jazz",
                                                "Ming" => "ming"
                                        ),
                                        "description" => ""
                                ),


                                // Show title
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Show title", 'azzu'.LANG_DN),
                                        "param_name" => "show_title",
                                        "std" => "yes",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),

                                // Show excerpt
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Show excerpt", 'azzu'.LANG_DN),
                                        "param_name" => "show_excerpt",
                                        "std" => "yes",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),
                            
                                // Show meta info
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Show meta info", 'azzu'.LANG_DN),
                                        "param_name" => "meta_info",
                                        "std" => "yes",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "dependency" => array(
                                                "element" => "hover_effect",
                                                "not_empty" => true,
                                        ),
                                        "description" => ""
                                ),
                            
                                // Show readmore button
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Show readmore button", 'azzu'.LANG_DN),
                                        "param_name" => "show_details",
                                        "std" => "yes",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),
                            
                                // Show like
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Show like", 'azzu'.LANG_DN),
                                        "param_name" => "show_like",
                                        "std" => "no",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => ""
                                ),                    

                                // Show zoom
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Show zoom", 'azzu'.LANG_DN),
                                        "param_name" => "show_zoom",
                                        "std" => "no",
                                        'value' => array( "" => "yes" ),
                                        "description" => ""
                                ),
                            
                                // Show link
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Show link", 'azzu'.LANG_DN),
                                        "param_name" => "show_link",
                                        "std" => "yes",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => __("Custom link", 'azzu'.LANG_DN),
                                ),
                            
                                // Override link by custom
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Replace link", 'azzu'.LANG_DN),
                                        "param_name" => "override_link",
                                        "std" => "no",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "dependency" => array(
                                                "element" => "show_link",
                                                "value" => array( "yes" )
                                        ),
                                        "description" => "Change link of portfolio by custom link"
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
                                        "heading" => __("Number of projects to show", 'azzu'.LANG_DN),
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
AZU_Shortcode_Portfolio::get_instance();