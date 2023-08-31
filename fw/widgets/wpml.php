<?php
/**
 * WPML widget.
 *
 * @package azzu.
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Load the widget */
add_action( 'widgets_init', array( 'Azzu_Widgets_WPML', 'azzu_register_widget' ) );

class Azzu_Widgets_WPML extends WP_Widget {
    
    /* Widget defaults */
    public static $widget_defaults = array( 
                'title'     	=> '',
		'hide_active'     => 0,
                'wpml_flag' => 1,
                'wpml_name' => 0,
                'wpml_translated_name' => 0
    );

	/* Widget setup  */
	function __construct() {  
            /* Widget settings. */
            $widget_ops = array( 'description' => _x( 'Language Selector', 'widget', 'azzu'.LANG_DN ) );

            /* Create the widget. */
            parent::__construct(
                'azzu-wpml',
                AZU_WIDGET_PREFIX . _x( 'WPML', 'widget', 'azzu'.LANG_DN ),
                $widget_ops
            );
	}

	/* Display the widget  */
	function widget( $args, $instance ) {

		extract( $args );

                $instance = wp_parse_args( (array) $instance, self::$widget_defaults );
		/* Our variables from the widget settings. */
		$title = apply_filters( 'widget_title', $instance['title'] );
                
                if ( in_array( 'sitepress-multilingual-cms/sitepress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )  :
                    echo $before_widget;

                    // title
                    if ( $title ) echo $before_title . $title . $after_title;
                
                    /////////////////////////////
                    // WPML                    //
                    /////////////////////////////

                    if ( defined('ICL_SITEPRESS_VERSION') ) {
                        ?><div class="<?php azus()->_class('col-sm-auto-right'); ?>"><?php
                            azuh()->azzu_language_selector_flags($instance);
                        ?></div><?php
                    } // wwpml languages flags

                    echo $after_widget;
                endif;
	}

	/* Update the widget settings  */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
                $instance['title'] = strip_tags($new_instance['title']);
                $instance['hide_active'] = $new_instance['hide_active'] ? 1 : 0;
                $instance['wpml_flag'] = $new_instance['wpml_flag'] ? 1 : 0;
                $instance['wpml_name'] = $new_instance['wpml_name'] ? 1 : 0;
                $instance['wpml_translated_name'] = $new_instance['wpml_translated_name'] ? 1 : 0;
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
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<?php _ex( 'Title:', 'widget', 'azzu'.LANG_DN ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'hide_active' ); ?>" <?php checked($instance['hide_active']); ?> />
                        <label for="<?php echo $this->get_field_id( 'hide_active' ); ?>"><?php _ex('Hide active language', 'widget', 'azzu'.LANG_DN); ?>
		</p>
                <p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'wpml_flag' ); ?>"  <?php checked($instance['wpml_flag']); ?> />
                        <label for="<?php echo $this->get_field_id( 'wpml_flag' ); ?>"><?php _ex('Flag', 'widget', 'azzu'.LANG_DN); ?>
		</p>
                <p>
                        <input type="checkbox" name="<?php echo $this->get_field_name( 'wpml_name' ); ?>"  <?php checked($instance['wpml_name']); ?> />
			<label for="<?php echo $this->get_field_id( 'wpml_name' ); ?>"><?php _ex('Native language name', 'widget', 'azzu'.LANG_DN); ?>
		</p>
                <p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'wpml_translated_name' ); ?>" <?php checked($instance['wpml_translated_name']); ?> />
                        <label for="<?php echo $this->get_field_id( 'wpml_translated_name' ); ?>"><?php _ex('Language name in display language', 'widget', 'azzu'.LANG_DN); ?>
		</p>

	<?php
	}

	public static function azzu_register_widget() {
		register_widget( get_class() );
	}
}