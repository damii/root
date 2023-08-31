<?php
/**
 * love_this
 * Like on a post.
 *
 * @since azzu 1.0
 */
if ( !class_exists('love_this') ) :
class love_this extends azu_base {

	function __construct() {
		parent::__construct();
	}

	// add actions inside
        protected function add_actions(){
		add_action( 'wp_ajax_azu_love_post', array( &$this, 'azu_love_post' ) );
		add_action( 'wp_ajax_nopriv_azu_love_post', array( &$this, 'azu_love_post' ) );
	}


	public function azu_love_post( $post_id ) {
		if ( isset( $_POST['post_id'] ) ) {
			$post_id = str_replace( 'azu-love-', '', $_POST['post_id'] );
			echo $this->love_post( $post_id, 'update' );
		}
		else {
			$post_id = str_replace( 'azu-love-', '', $_POST['post_id'] );
			echo $this->love_post( $post_id, 'get' );
		}
		exit;
	}


	function love_post( $post_id, $action = 'get' ) {
		if ( !is_numeric( $post_id ) ) return;

		switch ( $action ) {

		case 'get':
			$love_count = get_post_meta( $post_id, '_azu_post_love', true );
			if ( !$love_count ) {
				$love_count = 0;
				add_post_meta( $post_id, '_azu_post_love', $love_count, true );
			}

			return '<span class="azu-love-count">'. $love_count .'</span>';
			break;

		case 'update':
			$love_count = get_post_meta( $post_id, '_azu_post_love', true );
			if ( isset( $_COOKIE['azu_'.AZZU_DESIGN.'_love_'. $post_id] ) ) return $love_count;

			$love_count++;
			update_post_meta( $post_id, '_azu_post_love', $love_count );
			setcookie( 'azu_'.AZZU_DESIGN.'_love_'. $post_id, $post_id, time()*20, '/' );

			return '<span class="azu-love-count">'. $love_count .'</span>';
			break;

		}
	}


	function send_love($with_tooltip = true) {
		global $post;

		$output = $this->love_post( $post->ID );
		$class = 'azu-love-this';
                $custom_attr = '';
                if($with_tooltip){
                    $class .=' azu-tooltip';
                    $custom_attr .= 'data-toggle="tooltip" data-placement="top"';
                }
		if ( isset( $_COOKIE['azu_'.AZZU_DESIGN.'_love_'. $post->ID] ) ) {
			$class .= ' item-loved';
		}

		return '<a href="#" class="'. $class .'" id="azu-love-'. $post->ID .'" '.$custom_attr.' title="'._x('Like','atheme','azzu'.LANG_DN).'"><i class="azu-icon-like"></i> '. $output .'</a>';
	}

} endif; // love this


?>
