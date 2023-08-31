<?php
/**
 * Social icon widget.
 *
 * @package azzu.
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Load the widget */
add_action( 'widgets_init', array( 'Azzu_Widgets_SocialIcons', 'azzu_register_widget' ) );

class Azzu_Widgets_SocialIcons extends WP_Widget {
    
    private $size = array();
    
    /* Widget defaults */
    public static $widget_defaults = array( 
		'title'     	=> '',
                'text'     	=> '',
		'icons'     	=> '',
                'border'        => 0,
                'reverse'       => 0,
                'size'          => 'default'
    );

	/* Widget setup  */
	function __construct($widget_id = null,$widget_name= null,$widget_ops = null) {  
            /* Widget settings. */
            if($widget_id == null)
                $widget_ops = array( 'description' => _x( 'Social icons', 'widget', 'azzu'.LANG_DN ) );

            $this->size = array(
                'default' => _x( 'Default', 'widget', 'azzu'.LANG_DN ),
                'large' => _x( 'Large', 'widget', 'azzu'.LANG_DN ),
                'normal' => _x( 'Normal', 'widget', 'azzu'.LANG_DN ),
                'small' => _x( 'Small', 'widget', 'azzu'.LANG_DN ),
            );
            if($widget_name == null)
                $widget_name = AZU_WIDGET_PREFIX . _x( 'Social icons', 'widget', 'azzu'.LANG_DN );
            if($widget_id == null)
                $widget_id = 'azzu-social-icons';
            
            /* Create the widget. */
            parent::__construct(
                $widget_id,
                $widget_name,
                $widget_ops
            );
        
            if ( 'widgets.php' == basename( $_SERVER['PHP_SELF'] ) ) {
                    add_action( 'admin_print_scripts', array( &$this, 'add_admin_script' ) );
            }
	}
        
        function add_admin_script() {
		wp_enqueue_script( 'social-icon-widget', AZZU_OPTIONS_URI . '/assets/js/social-icon-widget.js', array( 'jquery' ) );
	}

	/* Display the widget  */
	function widget( $args, $instance ) {

		extract( $args );

                $instance = wp_parse_args( (array) $instance, self::$widget_defaults );

		/* Our variables from the widget settings. */
                if($instance['title'] === null)
                    $title = false;
                else
                    $title = apply_filters( 'widget_title', $instance['title'] );
		
                
                
		$output = azuh()->azzu_get_topbar_social_icons($instance);
                $class = 'azu-social-icons';
                if($instance['size']=='large')
                    $class .= ' azu-soc-large';
                else if($instance['size']=='normal')
                    $class .= ' azu-soc-normal';
                else if($instance['size']=='small')
                    $class .= ' azu-soc-small';
                
                if($instance['border'])
                    $class .= ' azu-soc-border';
                
                if($instance['reverse'])
                    $class .= ' azu-social-reverse';
                
                if($instance['title'] !== null){
                    echo $before_widget;
                }

		// title
		if ( $title ) echo $before_title . $title . $after_title;
                if(!empty($instance['text'])){
                    $class .= ' azu-widget-text';
                    echo '<div class="textwidget">'.$instance['text'].'</div>';
                }
                
		echo '<div class="'.esc_attr($class).'">' . $output . '</div>';
                if($instance['title'] !== null){
                    echo $after_widget;
                }
	}

	/* Update the widget settings  */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
        
		$instance['title'] = strip_tags($new_instance['title']);
                $instance['size'] = $new_instance['size'];
                $instance['icons'] = array();
                $instance['border'] = absint($new_instance['border']);
                $instance['reverse'] = absint($new_instance['reverse']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) );
                if(is_array($new_instance['icons']))
                {
                    foreach ($new_instance['icons'] as $arr) {
                        if(is_array($arr))
                            $instance['icons'][] = $arr;
                    }
                }

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
        $title = strip_tags( $instance['title'] );
        $text = esc_textarea($instance['text']);
        $default_soc = array();
        ?>
                <?php if($instance['title'] === null): ?>
		<p>
			<h3>
				<?php _ex( 'Social Icons', 'widget', 'azzu'.LANG_DN ); ?>
			</h3>
		</p>
                <?php else: $default_soc[] = array('icon' => 'facebook', 'url' => '#'); ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<?php _ex( 'Title:', 'widget', 'azzu'.LANG_DN ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _ex( 'Content:', 'widget', 'azzu'.LANG_DN ); ?></label>
		<textarea class="widefat" rows="10" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea></p>
                <?php endif; ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e('Size:', 'azzu'.LANG_DN); ?></label>
			<select name="<?php echo $this->get_field_name( 'size' ); ?>" id="<?php echo $this->get_field_id( 'size' ); ?>" class="widefat">
				<?php foreach ( $this->size as $name => $value ):?>
				<option value="<?php echo $name;?>"<?php selected( $instance['size'], $name );?>><?php echo $value;?></option>
				<?php endforeach;?>
			</select>
		</p>
                <p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'border' ); ?>" value="1" <?php checked($instance['border']); ?> />
                        <label for="<?php echo $this->get_field_id( 'border' ); ?>"><?php _ex('With border', 'widget', 'azzu'.LANG_DN); ?>
		</p>
                <p>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'reverse' ); ?>" value="1" <?php checked($instance['reverse']); ?> />
                        <label for="<?php echo $this->get_field_id( 'reverse' ); ?>"><?php _ex('Reverse', 'widget', 'azzu'.LANG_DN); ?>
		</p>
                <hr/>
	<?php
            
            // fields_generator
            $options = array(
                    'id'        => $this->get_field_name( 'icons' ),
                    'std'       => $default_soc,
                    'options'   => array(
                            'fields' => array(
                                    'icon'   => array(
                                            'type'          => 'select',
                                            'class'         => 'of_fields_gen_title',
                                            'description'   => _x( 'Icon: ', 'atheme', 'azzu'.LANG_DN ),
                                            'wrap'          => '<label>%2$s%1$s</label>',
                                            'desc_wrap'     => '%2$s',
                                            'options'		=> azuf()->azzu_get_social_icons_data()
                                    ),
                                    'url'   => array(
                                            'type'          => 'text',
                                            'description'   => _x( 'Url: ', 'atheme', 'azzu'.LANG_DN ),
                                            'wrap'          => '<label>%2$s%1$s</label>',
                                            'desc_wrap'     => '%2$s'
                                    )
                            )
                    )
            );
           
            echo azuf()->azu_fields_generator($options, $instance['icons']);
	}
        


	public static function azzu_register_widget() {
		register_widget( get_class() );
	}
}
