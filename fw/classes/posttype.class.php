<?php
/**
 * Declare custom post types.
 *
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/*******************************************************************/
// Custom post type
/*******************************************************************/

if ( !class_exists('Azzu_Custom_Post_Type') ):

class Azzu_Custom_Post_Type {
    
        public static function get_ajax_query($attr = array()) {
                    $ppp = $attr['number'];

                    $query_args = array(
                            'ignore_sticky_posts'   => '1',
                            'post_type'	=> $attr['post_type'],
                            'post_status'	=> 'publish' ,
                            'orderby'               => isset( $attr['orderby'] ) ? $attr['orderby'] : 'date',
                            'order'                 => isset( $attr['order'] ) ? $attr['order'] : 'DESC',
                            'paged'		=> azuf()->azu_get_paged_var(),
                    );

                    if ( $ppp ) {
                            $query_args['posts_per_page'] = intval($ppp);
                    }

                    if ( 'all' != $attr['select'] && is_array( $attr['category'] ) ) {

                            $query_args['tax_query'] = array( array(
                                    'taxonomy'	=> $attr['taxonomy'],
                                    'field'		=> 'term_id',
                                    'terms'		=> array_values($attr['category']),
                            ) );

                            switch( $attr['select'] ) {
                                    case 'only':
                                            $query_args['tax_query'][0]['operator'] = 'IN';
                                            break;

                                    case 'except':
                                            $query_args['tax_query'][0]['operator'] = 'NOT IN';
                            }

                    }

                    return new WP_Query( $query_args );
        }
    
    	/**
	 * Get posttype content layout.
	 *
	 */
	public static function get_ajax_content( $ajax_data = array() ) {
		global $post, $wp_query, $paged, $page;

		extract($ajax_data);

		if ( !$nonce || !$post_id  || !wp_verify_nonce( $nonce, 'azzu-posts-ajax' ) || !$target_page 
                        ) {
			$responce = array( 'success' => false, 'reason' => 'corrupted data' );

		} else {

			/**
			 * Include AQResizer.
			 *
			 */
			require_once( AZZU_LIBRARY_DIR . '/aq_resizer.php' );

			/**
			 * Include core functions.
			 *
			 */
                        $azu_is_ajax_admin = false;
			require_once( AZZU_FUNCTION_DIR . '/core-functions.php' );

			if ( !class_exists('azu_mobile_detect') ) {
				/**
				 * Mobile detection library.
				 *
				 */
                                require_once( AZZU_FUNCTION_DIR . '/mobile-detect-function.php' );
                                azu_mobile_detect();
			}
			// get page
			query_posts( array(
				'post_type' => 'page',
				'page_id' => $post_id,
				'post_status' => 'publish',
				'page' => $target_page
			) );
                        
                        
			azum()->set('layout', $ajaxarray['type']);
			azum()->set('template', $contentType);
                        azum()->base_init( $post_id );
                        azum()->set('attr', $ajaxarray);
                        if(!empty($term) && $term!=='none'){
                            $ajaxarray['select'] = 'only';
                            $ajaxarray['category'] = array($term);
                        }
                        $ajaxarray['post_type'] = $post_type;
                        $ajaxarray['taxonomy'] = $taxonomy;
                        
			$html = '';
			$responce = array( 'success' => true );
                        $load_style = absint($ajaxarray['loading_mode']);
                        $deleted_items = array();
                        if( $load_style == 1 || $sender == 'filter' ){
                            $deleted_items = $loaded_items;
                            $loaded_items = array();
                        }
                        
                        if($contentType=='portfolio')
                            azut()->azzu_portfolio_meta_new_controller();
                        else 
                            azut()->azzu_post_meta_new_controller();
                        
			if ( have_posts() && !post_password_required() ) : while ( have_posts() ) : the_post(); // main loop

				ob_start();

                                $page_query = self::get_ajax_query($ajaxarray);
                                
				if ( $page_query->have_posts() ) {
                                        // loop
                                        echo azuf()->azu_get_posttype_content_loop($page_query, $contentType, $loaded_items);
				}
                                

			$html .= ob_get_clean();
			endwhile;

			$next_page_link = azuf()->azu_get_next_posts_url( $page_query->max_num_pages );

			if ( $next_page_link ) {
				$responce['nextPage'] = azuf()->azu_get_paged_var() + 1;
			} else {
				$responce['nextPage'] = 0;
			}

			// pagination style
			if ( absint($load_style) > 1 || 1 == $load_style) {
                                $class_paginator = 'paginator with-ajax';
                                if( $load_style > 1) 
                                     $class_paginator.= ' paginator-more-button';
                                
                                $responce['paginationType'] = $load_style;
				
                                if ( $responce['nextPage'] > 0 || 1 == $load_style) {
					$responce['currentPage'] = azuf()->azu_get_paged_var();
					$responce['paginationHtml'] = azuh()->azu_get_pagination_type($load_style, $page_query->max_num_pages, '',$responce['currentPage']);
				} else 
					$responce['currentPage'] = $post_paged;
			} 

			$responce['itemsToDelete'] = array_values($deleted_items);
			$responce['order'] = $page_query->query['order'];
			$responce['orderby'] = $page_query->query['orderby'];

			endif; // main loop

			$responce['html'] = $html;

		}

		return $responce;
	}
    
}

endif;






