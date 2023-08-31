<?php
/**
 * Login widget.
 *
 * @package azzu.
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Load the widget */
add_action( 'widgets_init', array( 'Azzu_Widgets_Login', 'azzu_register_widget' ) );

class Azzu_Widgets_Login extends WP_Widget {
    
    /* Widget defaults */
    public static $widget_defaults = array( 
                'show_register' => 0,
                'title' => '',
                'icon' => 0,
		'login' => ''
    );

	/* Widget setup  */
	function __construct() {  
            /* Widget settings. */
            $widget_ops = array( 'description' => _x( 'Login link', 'widget', 'azzu'.LANG_DN ) );

            /* Create the widget. */
            parent::__construct(
                'azzu-login',
                AZU_WIDGET_PREFIX . _x( 'Login', 'widget', 'azzu'.LANG_DN ),
                $widget_ops
            );
	}

	/* Display the widget  */
	function widget( $args, $instance ) {

		extract( $args );

                $instance = wp_parse_args( (array) $instance, self::$widget_defaults );

                echo $before_widget;
                if ( !empty( $instance['title'] ) ) {
                        echo $before_title . $instance['title'] . $after_title;
                }
                $output ='';
                $icon='';
                $redirect = '';
                if ( !is_user_logged_in() ) {
                    /////////////////////////////
                    // Login                   //
                    /////////////////////////////
                    //wp-login.php?action=register  ?redirect_to=http%3A%2F%2Fsupport.aquagraphite.com
                    if(empty($instance['login'])){
                        $instance['login'] = site_url('', is_ssl() ? 'https' : 'http').'/wp-login.php';
                        $redirect = '?redirect_to='.urlencode(site_url('', is_ssl() ? 'https' : 'http').'/wp-admin/').'&amp;reauth=1';
                    }
                    
                    $output .= '<div class="azu-login-widget">';
                    
                    if($instance['icon'])
                        $icon='<i class="azu-icon-user"></i>';
                    
                    $output .= '<a href="'.esc_url($instance['login']).$redirect.'">'.$icon._x('Log In','widget','azzu'.LANG_DN).'</a>';

                    if($instance['show_register'])
                        $output .= ' '._x('or','widget','azzu'.LANG_DN).' <a href="'.esc_url($instance['login']).'?action=register">'._x('Sign Up','widget','azzu'.LANG_DN).'</a>';

                    $output .= '</div>';
                    echo $output;
                }
                echo $after_widget;
	}

	/* Update the widget settings  */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
       		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['login'] = esc_url_raw( $new_instance['login'] );
                $instance['show_register'] = $new_instance['show_register'] ? 1 : 0;
                $instance['icon'] = $new_instance['icon'] ? 1 : 0;
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

	/* Set up some default widget settings. */
        $instance = wp_parse_args( (array) $instance, self::$widget_defaults );
        ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _ex( 'Title:', 'widget', 'azzu'.LANG_DN ); ?>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" /></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'login' ); ?>"><?php _ex( 'Login URI:', 'widget', 'azzu'.LANG_DN ); ?>
			<input class="widefat" id="<?php echo $this->get_field_id( 'login' ); ?>" name="<?php echo $this->get_field_name( 'login' ); ?>" type="text" value="<?php echo esc_url( $instance['login'] ); ?>" /></label>
		</p>

                <p>
                        <input type="checkbox" name="<?php echo $this->get_field_name( 'show_register' ); ?>"  <?php checked($instance['show_register']); ?> />
			<label for="<?php echo $this->get_field_id( 'show_register' ); ?>"><?php _ex('with register link', 'widget', 'azzu'.LANG_DN); ?>
		</p>
                <p>
                        <input type="checkbox" name="<?php echo $this->get_field_name( 'icon' ); ?>"  <?php checked($instance['icon']); ?> />
			<label for="<?php echo $this->get_field_id( 'icon' ); ?>"><?php _ex('Show icon', 'widget', 'azzu'.LANG_DN); ?>
		</p>
	<?php
	}

	public static function azzu_register_widget() {
		register_widget( get_class() );
	}
}