<?php
/**
 * Config class.
 *
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Singleton.
 *
 */
class Azzu_Config extends azu_base {

        protected function add_actions(){
            $this->set_default_vars();
        }
	protected $options = array();

	protected function __construct() {
            parent::__construct();
        }

	public function set( $name, $value = null ) {
		$this->options[ $name ] = $value;
	}

	public function reset( $options = array() ) {
		$this->options = $options;
	}

	public function get( $name = '',$default = null ) {
		if ( '' == $name ) {
			return $this->options;
		}
		if ( array_key_exists( $name, $this->options ) && ($this->options[ $name ] !== '' || $default === null) ) {
                        return $this->options[ $name ];
		}
		return $default;
	}

	public function base_init( $new_post_id = null ) {
		global $post;
		$post_id = $this->get('post_id');

		if ( null == $post_id ) {

			if ( $new_post_id ) {
				$post_id = $new_post_id;
			} else if ( !empty($post) ) {
				$post_id = $post->ID;
			}

			$this->set( 'post_id', $post_id );
		}

		if ( empty( $post_id ) ) {
			return;
		}

		$cur_post_type = get_post_type( $post_id );
		switch ( $cur_post_type ) {
			case 'page': $this->set_page_options(); break;
			case 'post': break;
			case 'azu_portfolio': break;
		}

		// common options
		$this->set_header_options();
		$this->set_sidebar_and_footer_options();
	}
        
        private function set_default_vars(){
            if ( empty($this->options['attr']) ) {
                $sm_col = absint(of_get_option('general-blog-image-size',12));
                $this->set( 'attr', array( 
                    'same_width' => true,
                    'columns' => 1,
                    'align'   => '0',
                    'readmore' => _x( 'Read more', 'atheme', 'azzu'.LANG_DN ),
                    'column_width' => azuf()->azu_calculate_width_size($sm_col==12 ? 1 : 2),
                    'show_title' => true,
                    'hover_effect'	=> '',
                    'descriptions'	=> true,
                    'show_like'         => true,
                    'meta_info'	=> true,
                    'show_zoom'	=> true,
                    'show_excerpt' => true,
                    'show_link' => true,
                    'show_details' => true,
                    'hover' => true,
                    'border' => false,
                    'round' => false,
                    'image_size' => $sm_col,
                    'border_padding' => -1
                    ));
            }
            if ( empty($this->options['order']) )
                $this->set('order' ,'asc');
            if ( empty($this->options['orderby']) )
                $this->set('orderby' ,'name');
            if ( empty($this->options['posts_per_page']) )
                $this->set('posts_per_page' ,12);
            if ( empty($this->options['display']) )
                $this->set('display' ,array());
            if ( empty($this->options['load_style']) )
                $this->set('load_style' ,'ajax_more');
        }
        
	private function set_page_options() {
		global $post;

		$prefix = '_azu_page_';
                $this->set( 'page_override', get_post_meta( $this->options['post_id'], "{$prefix}override", true ) );
		// populate options
		$this->set( 'general-layout', get_post_meta( $this->options['post_id'], "{$prefix}page_layout", true ) );
		
                $hidden_parts = get_post_meta( $this->options['post_id'], "{$prefix}hidden_parts", false );
                if ( is_array( $hidden_parts ) ) {
                    $this->set( 'top_bar-show',  in_array('top_bar', $hidden_parts));
                    $this->set( 'page_header',  in_array('header', $hidden_parts));
                    $this->set( 'page_bottom_bar',  in_array('bottom_bar', $hidden_parts));
                    $this->set( 'header-show_floating_menu',  in_array('floating_menu', $hidden_parts));
                    $this->set( 'page_menu',  in_array('menu', $hidden_parts));
                    $this->set( 'page_page_title',  in_array('page_title', $hidden_parts));
                }
                $this->set( 'page_header_logo', get_post_meta( $this->options['post_id'], "{$prefix}header_logo", true ) );
		$this->set( 'page_bottom_logo', get_post_meta( $this->options['post_id'], "{$prefix}bottom_logo", true ) );
                $this->set( 'page_bg_color', get_post_meta( $this->options['post_id'], "{$prefix}bg_color", true ) );
		$this->set( 'page_bg_image', get_post_meta( $this->options['post_id'], "{$prefix}bg_image", true ) );
		$this->set( 'page_bg_repeat', get_post_meta( $this->options['post_id'], "{$prefix}bg_repeat", true ) );
		$this->set( 'page_bg_position_x', get_post_meta( $this->options['post_id'], "{$prefix}bg_position_x", true ) );
		$this->set( 'page_bg_position_y', get_post_meta( $this->options['post_id'], "{$prefix}bg_position_y", true ) );
		$this->set( 'page_bg_fullscreen', get_post_meta( $this->options['post_id'], "{$prefix}bg_fullscreen", true ) );
	}

	private function set_header_options() {
		global $post;

		// Header options
		$prefix = '_azu_header_';

		$this->set( 'header_title', get_post_meta( $this->options['post_id'], "{$prefix}title", true ) );
                
                $header_slideshow = get_post_meta( $this->options['post_id'], "{$prefix}slideshow", true );
                $this->set( 'slideshow_slider', $header_slideshow );
                
		if ( $header_slideshow ) 
			$slideshow_type = get_post_meta( $this->options['post_id'], "{$prefix}type", true );
		else 
			$slideshow_type = 'full';
		
		$this->set( 'slideshow_type', $slideshow_type );
                $this->set( 'general_padding', get_post_meta( $this->options['post_id'], "{$prefix}padding", true ) );
                
		$this->set( 'slideshow_mode', get_post_meta( $this->options['post_id'], "{$prefix}mode", true ) );

		$this->set( 'slideshow_revolution_slider', get_post_meta( $this->options['post_id'], "{$prefix}revolution_slider", true ) );
                $this->set( 'slideshow_royal_slider', get_post_meta( $this->options['post_id'], "{$prefix}royal_slider", true ) );
                $this->set( 'slideshow_master_slider', get_post_meta( $this->options['post_id'], "{$prefix}master_slider", true ) );
		$this->set( 'slideshow_layer_slider', get_post_meta( $this->options['post_id'], "{$prefix}layer_slider", true ) );
	
                
                $bp = get_post_meta( $this->options['post_id'], "{$prefix}border_padding", true );
                if( $bp !== null )
                    $this->set( 'border_padding', $bp );
                
        }

	private function set_sidebar_and_footer_options() {
		global $post;

		// Sidebar options
		$prefix = '_azu_sidebar_';
		$this->set( 'sidebar_position', get_post_meta( $this->options['post_id'], "{$prefix}position", true ) );
                $this->set( 'sidebar_wide', get_post_meta( $this->options['post_id'], "{$prefix}wide", true ) );
		$this->set( 'sidebar_widgetarea_id', get_post_meta( $this->options['post_id'], "{$prefix}widgetarea_id", true ) );
                $this->set( 'sidebar_sticky', get_post_meta( $this->options['post_id'], "{$prefix}sticky", true ) );
		// Footer options
		$prefix = '_azu_footer_';
		$this->set( 'footer_show', get_post_meta( $this->options['post_id'], "{$prefix}show", true ) );
		$this->set( 'footer_widgetarea_id', get_post_meta( $this->options['post_id'], "{$prefix}widgetarea_id", true ) );
                $this->set( 'footer_widgetarea_id2', get_post_meta( $this->options['post_id'], "{$prefix}widgetarea_id2", true ) );
	}

}
