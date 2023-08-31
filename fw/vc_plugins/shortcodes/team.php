<?php
/**
 * Team shortcode.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Shortcode Team class.
 *
 */
class AZU_Shortcode_Team extends AZU_Shortcode {

	static protected $instance;
	protected $atts;

	protected $shortcode_name = 'azu_team';
	protected $post_type = 'azu_team';
	protected $taxonomy = 'azu_team_category';

	public static function get_instance() {
		if ( !self::$instance ) {
			self::$instance = new AZU_Shortcode_Team();
		}
		return self::$instance;
	}

	public function __construct() {

		$this->addShortCode( $this->shortcode_name, array($this, 'shortcode') );
                $this->azu_vc_map();
	}

	public function shortcode( $atts, $content = null ) {
		$attributes = shortcode_atts( array(
                        'select' => '',
			'type'                  => 'slider',
                        'azu_arrow'          => '1',
			'category'              => '',
                        'proportion'            => '',
                        'image_size'            => '12',
                        'hover'                 => '0',
                        'descriptions'          => '0',
                        'border'                => '0',
			'order'                 => 'desc',
			'orderby'               => 'date',
			'number'                => '6',
			'padding'               => '15',
                        'slides'                => '1',
			'columns'          => '3',
                        'loading_mode'  => '0',
                        'autoslide'     => 0
		), $atts );
		
		// sanitize attributes
		$attributes['type'] = in_array($attributes['type'], array('masonry', 'grid', 'slider') ) ? $attributes['type'] : 'masonry';

                $attributes['hover'] = apply_filters('azu_sanitize_flag', $attributes['hover']);
                $attributes['descriptions'] = apply_filters('azu_sanitize_flag', $attributes['descriptions']);
                $attributes['border'] = apply_filters('azu_sanitize_flag', $attributes['border']);
		$attributes['azu_arrow'] = apply_filters('azu_sanitize_flag', $attributes['azu_arrow']);
                $attributes['order'] = apply_filters('azu_sanitize_order', $attributes['order']);
		$attributes['orderby'] = apply_filters('azu_sanitize_orderby', $attributes['orderby']);
		$attributes['number'] = apply_filters('azu_sanitize_posts_per_page', $attributes['number']);
                $attributes['image_size'] = in_array($attributes['image_size'], array('6', '12') ) ? $attributes['image_size'] : '12';
		$attributes['padding'] = absint($attributes['padding']);
                $attributes['slides'] = in_array($attributes['slides'], array('2', '3', '4','5')) ? absint($attributes['slides']) : 1;
                $attributes['loading_mode'] = in_array($attributes['loading_mode'], array('1', '2', '3')) ? absint($attributes['loading_mode']) : '0';
                $attributes['columns'] = in_array($attributes['columns'], array('2', '3', '4', '5', '6')) ? absint($attributes['columns']) : 1;
		$attributes['column_width'] = azuf()->azu_calculate_width_size($attributes['columns']);

                if ( $attributes['proportion'] ) {
                    $wh = array_map( 'absint', explode(':', $attributes['proportion']) );
                    if ( 2 == count($wh) && !empty($wh[0]) && !empty($wh[1]) ) {
                            $attributes['proportion'] = $wh[0]/$wh[1];
                    } else {
                            $attributes['proportion'] = '';
                    }
                }
                $attributes['round'] = false;
                if(empty($attributes['proportion'])){
                        $attributes['round'] = true;
                        $attributes['proportion'] = 1;
                }
                
		if ( $attributes['category']) {
			$attributes['category'] = $this->azu_category($attributes['category']);
			$attributes['select'] = 'only';
		} else {
			$attributes['select'] = 'all';
		}

                $output = '';
		switch ( $attributes['type'] ) {
			case 'slider' : $output .= $this->team_slider($attributes); break;
			default : $output .= $this->team_grid($attributes);
		}


		if ( function_exists('vc_is_inline') && vc_is_inline() ) {
			$terms_list = azuh()->azzu_get_terms_list_by_id( array( 'term_id' => $attributes['category'], 'taxonomy' => 'azu_team_category' ) );

			$output = '
				<div class="azu_vc-shortcode_dummy azu_vc-team" style="height: 250px;">
					<h5>Team</h5>
					<p class="text-small"><strong>Display categories:</strong> ' . $terms_list . '</p>
				</div>
			';
		}

		return $output;
	}
        
	/**
	 * Team.
	 *
	 */
        public function team_grid( $attributes = array() ) {
                global $post;
		$post_backup = $post;

		$azu_query = $this->get_posts_by_terms( $attributes );

		$output = $before_output = $after_output = '';

		if ( $azu_query->have_posts() ) {

			// backup and reset config
			$config_backup = azum()->get();

			azum()->set('layout', $attributes['type']);
                        $attributes['template'] = 'team';
			azum()->set('template', $attributes['template']);
                        azum()->set('attr', $attributes);

                        // loop
                        $output .= azuf()->azu_get_posttype_content_loop($azu_query, $attributes['template']);
                        //show pagination
                        $guid=str_replace( array('{','}','-'),'', uniqid());
                        $after_output = azuh()->azu_get_pagination_type($attributes['loading_mode'], $azu_query->max_num_pages, $guid);
			// restore original $post
			$post = $post_backup;
                        if($post)
                            setup_postdata( $post );

			// restore config
			azum()->reset($config_backup);

			// masonry layout classes
			$masonry_container_classes = array( 'isotope', 'with-ajax' );
			switch ( $attributes['type'] ) {
				case 'grid':
					$masonry_container_classes[] = 'iso-grid';
					break;
				case 'masonry':
					$masonry_container_classes[] = 'iso-container';
			}
			$masonry_container_classes = implode(' ', $masonry_container_classes);
                        wp_localize_script( 'azu-main', 'ajax_'.$guid, $attributes );
			$masonry_container_data_attr = array(
                                'data-guid="'.$guid.'"',
				'data-padding="' . intval($attributes['padding']) . 'px"',
                                'data-min-width="200"',
				'data-columns="' . intval($attributes['columns']) . '"',
                                'data-ratio="' . $attributes['proportion'] . '"'
			);

			// data attribute
			$masonry_container_data_attr = ' ' . implode(' ', $masonry_container_data_attr);

			// wrap output
			$output = sprintf( '<div class="%s"%s>%s</div>',
				esc_attr($masonry_container_classes),
				$masonry_container_data_attr,
				$output
			);

                        $output = $before_output.$output.$after_output;

		} // if have posts
                
                return $output;
        }
        
        
        /**
	 * Team slider.
	 *
	 */
	public function team_slider( $attributes = array() ) {
                $attributes['template'] = 'team';
                $slides = $attributes['slides'];
                $swiper_options = array(
                            'autoplay' => absint($attributes['autoslide']), 
                            'calculateHeight' => true,
                            'enable_arrow' => $attributes['azu_arrow'],
                            'loop' => true, 
                            'slidesPerView'=> $slides > 1 ? 'auto' : 1,
                            'slidesPerGroup' => $slides,
                            'loopedSlides' => $slides > 1 ? $slides - 1 : 0,
                            'slidesPerViewFit' => true);
                return $this->azu_posttype_slider($attributes, $swiper_options);
	}
        
        // VC map function
        public function azu_vc_map() {
                if(!function_exists('vc_map')){ return; }
                // ! Team
                vc_map( array(
                        "name" => __("Team", 'azzu'.LANG_DN),
                        "base" => "azu_team",
                        "icon" => "azu_vc_ico_team",
                        "class" => "azu_vc_sc_team",
                        "description" => __("Loop",'azzu'.LANG_DN),
                        "category" => __('by Theme', 'azzu'.LANG_DN),
                        "params" => array(

                                // Terms
                                array(
                                        "type" => "azu_taxonomy",
                                        "taxonomy" => "azu_team_category",
                                        "class" => "",
                                        "heading" => __("Categories", 'azzu'.LANG_DN),
                                        "param_name" => "category",
                                        "description" => __("Note: By default, all your team will be displayed. <br>If you want to narrow output, select category(s) above. Only selected categories will be displayed.", 'azzu'.LANG_DN)
                                ),

                                // Appearance
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Appearance", 'azzu'.LANG_DN),
                                        "param_name" => "type",
                                        "value" => array(
                                                __("Slider", 'azzu'.LANG_DN) => "slider",
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
                                        "value" => 3,
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
                                       "std" => "yes",
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
                                // Autoslide
                                array(
                                        "type" => "azu_number",
                                        "class" => "",
                                        "heading" => __("Autoslide", 'azzu'.LANG_DN),
                                        "param_name" => "autoslide",
                                        "value" => 0,
                                        "description" => __('In milliseconds (e.g. 2 seconds = 2000 miliseconds). Leave this field zero to disable autoslide. This field works only when "Appearance: Slider" selected.', 'azzu'.LANG_DN),
                                        "dependency" => array(
                                                "element" => "type",
                                                "value" => array(
                                                        "slider"
                                                )
                                        )
                                ),

                                //Image size
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Image size", 'azzu'.LANG_DN),
                                        "param_name" => "image_size",
                                        "value" => array(
                                                __("100%", 'azzu'.LANG_DN) => "12",
                                                __("50%", 'azzu'.LANG_DN) => "6"
                                        ),
                                        "description" => "",
                                        "dependency" => array(
                                                "element" => "type",
                                                "value" => array(
                                                        "masonry",
                                                        "grid"
                                                )
                                        )
                                ),

                                //Image shape
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Image shape", 'azzu'.LANG_DN),
                                        "param_name" => "proportion",
                                        "value" => array(
                                                __("Round", 'azzu'.LANG_DN) => "",
                                                __("Square", 'azzu'.LANG_DN) => "1:1",
                                                __("4:3", 'azzu'.LANG_DN) => "4:3",
                                                __("3:4", 'azzu'.LANG_DN) => "3:4",
                                        ),
                                        "description" => "",
                                ),

                                // Hover
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Hover", 'azzu'.LANG_DN),
                                        "param_name" => "hover",
                                        "std" => "no",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => __("Social icons over image", 'azzu'.LANG_DN),
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

                                // border
                                array(
                                        "type" => "azu_toggle",
                                        "class" => "",
                                        "heading" => __("Border", 'azzu'.LANG_DN),
                                        "param_name" => "border",
                                        "std" => "no",
                                        "value" => array(
                                                "" => "yes"
                                        ),
                                        "description" => __("General border", 'azzu'.LANG_DN),
                                ),

                                // Gap
                                array(
                                        "type" => "azu_number",
                                        "class" => "",
                                        "heading" => __("Gap between team members (px)", 'azzu'.LANG_DN),
                                        "param_name" => "padding",
                                        "value" => 15,
                                        "min" => 0,
                                        "max" => 300,
                                        "description" => __("Team member paddings (e.g. 5 pixel padding will give you 10 pixel gaps between team members)", 'azzu'.LANG_DN),
                                ),
                                //Loading mode
                                array(
                                        "type" => "dropdown",
                                        "class" => "",
                                        "heading" => __("Loading mode", 'azzu'.LANG_DN),
                                        "param_name" => "loading_mode",
                                        "value" => array(
                                                __("None", 'azzu'.LANG_DN) => "0",
                                                __("standart pagination", 'azzu'.LANG_DN) => "1",
                                                __("Load more", 'azzu'.LANG_DN) => "2",
                                                __("Auto load", 'azzu'.LANG_DN) => "3"
                                        ),
                                        "description" => "",
                                                            "dependency" => array(
                                                "element" => "type",
                                                "value" => array(
                                                        "masonry",
                                                        "grid"
                                                )
                                        )
                                ),

                                // Number of posts
                                array(
                                        "type" => "azu_number",
                                        "class" => "",
                                        "heading" => __("Number of team members to show", 'azzu'.LANG_DN),
                                        "param_name" => "number",
                                        "value" => 6,
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
AZU_Shortcode_Team::get_instance();
