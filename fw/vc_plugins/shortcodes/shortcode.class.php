<?php
/**
 * Shortcodes class.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( class_exists('WPBakeryVisualComposerAbstract') ) :
class AZU_Shortcode_Base extends WPBakeryVisualComposerAbstract {}
else:
class AZU_Shortcode_Base {
        /* Shortcode methods */
        /**
         * @param $tag
         * @param $func
         */
        public function addShortCode( $tag, $func ) {
                call_user_func('add_'.'shortcode', $tag, $func );
        }
}
endif;


class AZU_Shortcode extends AZU_Shortcode_Base {

    // parent map function
    public function azu_vc_map() {
        //empty parent
    }
    
    protected function azu_category($category=''){
            $category = explode(',', $category);
            $value_arr = array_map('trim', $category);
            $terms = get_terms( $this->taxonomy );
            if ( $terms && !is_wp_error($terms) ) {
                $category = array();
                foreach( $terms as $term ) {
                    if(in_array( $term->slug, $value_arr ) || in_array( $term->term_id, $value_arr ))
                           $category[] =  $term->term_id;
                }
            }
            
            return $category;
    }
    
    public function get_posts_by_terms( $instance = array() ) {
        if ( empty($this->post_type) || empty($this->taxonomy) ) {
            return false;
        }

        $args = array(
            //'no_found_rows'         => 1,
            'ignore_sticky_posts'   => '1',
            'posts_per_page'        => isset( $instance['number'] ) ? $instance['number'] : -1,
            'post_type'             => $this->post_type,
            'post_status'           => 'publish',
            'orderby'               => isset( $instance['orderby'] ) ? $instance['orderby'] : 'date',
            'order'                 => isset( $instance['order'] ) ? $instance['order'] : 'DESC',
            'tax_query'             => array( array(
                'taxonomy'          => $this->taxonomy,
                'field'             => 'term_id',
                'terms'             => $instance['category']
            ) ),
        );

        switch( $instance['select'] ) {
            case 'only': $args['tax_query'][0]['operator'] = 'IN'; break;
            case 'except': $args['tax_query'][0]['operator'] = 'NOT IN'; break;
            default: unset( $args['tax_query'] );
        }

        return new WP_Query( $args );
    }
    
    protected function azu_posttype_slider($attributes = array(),$options=array()){
		global $post;
                $indicators ='';
                $post_backup = $post;
		$azu_query = $this->get_posts_by_terms( $attributes );

                $default_options = array(
                        'speed'	=> 300
		);
		$options = wp_parse_args( $options, $default_options );
                $class = array();
                if(isset($attributes['class'])){
                    $class[] = $attributes['class'];
                }
                $slider_args = array(
                        'class'     => $class,
                        'swiper'    => $options
                    );
                if(isset($attributes['padding']))
                    $slider_args['padding'] = $attributes['padding'];
                if(isset($attributes['min-width']))
                    $slider_args['min-width'] = $attributes['min-width'];
                if(isset($attributes['proportion']))
                    $slider_args['proportion'] = $attributes['proportion'];
                if(isset($options['pagination']))
                    $indicators .='<span class="swiper-pagination-switch"></span>'; 
                
                // backup and reset config
                $config_backup = azum()->get();

                azum()->set('layout', $attributes['type']);
                azum()->set('template', $attributes['template']);
                azum()->set('attr', $attributes);
		$output = '';
		if ( $azu_query->have_posts() ) {

			$post_backup = $post;
                        
			while ( $azu_query->have_posts() ) { $azu_query->the_post();
                                $output .= '<div class="swiper-slide">';
                                    ob_start();
                                    if($attributes['template'] == 'blog')
                                        $content_type = get_post_format();
                                    else
                                        $content_type = $attributes['template'];
                                    get_template_part( 'content', $content_type );

                                    $output .= ob_get_contents();
                                    ob_end_clean();
				$output .= '</div>';
			}

			$post = $post_backup;
                        if($post)
                            setup_postdata( $post );

		} // if have posts
                
                // restore config
                azum()->reset($config_backup);
                        
                $output = azuh()->azzu_generate_carousel_slider( $output, $indicators, $slider_args );

		return $output;
    }

}
