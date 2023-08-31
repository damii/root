<?php
/**
 * Post categories widget.
 *
 * @package azzu.
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Load the widget */
add_action( 'widgets_init', array( 'Azzu_Widgets_PostCategories', 'azzu_register_widget' ) );

class Azzu_Widgets_PostCategories extends WP_Widget {
    
    /* Widget defaults */
    public static $widget_defaults = array( 
		'title'     	=> '',
		'order'     	=> 'DESC',
		'orderby'   	=> 'date',
                'posttype'      => '',
                'show_count'      => 0,
		'select'	=> 'all',
		'show'      	=> 6,
		'cats'      	=> array(),
		'thumbnails'	=> true,
    );

	/* Widget setup  */
	function __construct() {  
            /* Widget settings. */
                    $widget_ops = array( 'description' => _x( 'Post categories', 'widget', 'azzu'.LANG_DN ) );

            /* Create the widget. */
            parent::__construct(
                'azzu-post-categories',
                AZU_WIDGET_PREFIX . _x( 'Post categories', 'widget', 'azzu'.LANG_DN ),
                $widget_ops
            );
	}

	/* Display the widget  */
	function widget( $args, $instance ) {

		extract( $args );

        $instance = wp_parse_args( (array) $instance, self::$widget_defaults );

		/* Our variables from the widget settings. */
		$title = apply_filters( 'widget_title', $instance['title'] );
		
                $posttype = ($instance['posttype'] == 'post' || empty($instance['posttype'])) ? 'category' : 'azu_'.$instance['posttype'].'_category';
                
		$cats_args = array(
			'show_count'    => false,
			'hierarchical'  => false,
			'title_li'      => '',
			'echo'          => false,
                        'taxonomy'      => $posttype,
			'walker'        => new Walker_Category_AZU()
		);
                if($instance['show_count'])
                    $cats_args['show_count'] = true;
                
		switch ( $instance['select'] ) {
			case 'except' :
				$cats_args['exclude'] = implode( ',', $instance['cats'] );
				break;
			case 'only' :
				$cats_args['include'] = implode( ',', $instance['cats'] );
		}

		$cats = wp_list_categories( $cats_args );

		echo $before_widget ;

		// title
		if ( $title ) echo $before_title . $title . $after_title;

		echo '<ul class="custom-categories">' . $cats . '</ul>';

		echo $after_widget;
	}

	/* Update the widget settings  */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
        
		$instance['title'] = strip_tags($new_instance['title']);
                $instance['show_count'] = absint($new_instance['show_count']);
                $instance['posttype'] = strip_tags($new_instance['posttype']);
		$instance['select'] = in_array( $new_instance['select'], array('all', 'only', 'except') ) ? $new_instance['select'] : 'all';
		$instance['cats'] = (array) $new_instance['cats'];
		if ( empty($instance['cats']) ) { $instance['select'] = 'all'; }

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

        $posttype = ($instance['posttype'] == 'post' || empty($instance['posttype'])) ? 'category' : 'azu_'.$instance['posttype'].'_category';
        
        $title = strip_tags( $instance['title'] );
	$terms = get_terms( $posttype, array(
            'hide_empty'    => 1,
            'hierarchical'  => false 
        ) );
                
        $cat_list = array( 'post' => _x( 'Post', 'widget', 'azzu'.LANG_DN ) );

        if ( azu_check_custom_posttype('portfolio' ) ) 
                $cat_list['portfolio'] = _x( 'Portfolio', 'widget', 'azzu'.LANG_DN );
		if ( azu_check_custom_posttype('team') ) 	
                $cat_list['team'] = _x( 'Team', 'widget', 'azzu'.LANG_DN );
		if ( azu_check_custom_posttype('testimonials') ) 
                $cat_list['testimonials'] = _x( 'Testimonials', 'widget', 'azzu'.LANG_DN );
				
        
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<?php _ex( 'Title:', 'widget', 'azzu'.LANG_DN ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
                <p>
			<label for="<?php echo $this->get_field_id( 'show_count' ); ?>"><?php _ex('Show post counts', 'widget', 'azzu'.LANG_DN); ?>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'show_count' ); ?>" value="1" <?php checked($instance['show_count']); ?> />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'posttype' ); ?>"><?php _ex('PostType:', 'widget', 'azzu'.LANG_DN); ?></label>
			<select id="<?php echo $this->get_field_id( 'posttype' ); ?>" name="<?php echo $this->get_field_name( 'posttype' ); ?>">
				<?php foreach( $cat_list as $value=>$name ): ?>
				<option value="<?php echo $value; ?>" <?php selected( $instance['posttype'], $value ); ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<strong><?php _ex('Category:', 'widget', 'azzu'.LANG_DN); ?></strong><br />
            <?php if( !is_wp_error($terms) ): ?>

	            <div class="azu-widget-switcher">

	            	<label><input type="radio" name="<?php echo $this->get_field_name( 'select' ); ?>" value="all" <?php checked($instance['select'], 'all'); ?> /><?php _ex('All', 'widget', 'azzu'.LANG_DN); ?></label>
	            	<label><input type="radio" name="<?php echo $this->get_field_name( 'select' ); ?>" value="only" <?php checked($instance['select'], 'only'); ?> /><?php _ex('Only', 'widget', 'azzu'.LANG_DN); ?></label>
	            	<label><input type="radio" name="<?php echo $this->get_field_name( 'select' ); ?>" value="except" <?php checked($instance['select'], 'except'); ?> /><?php _ex('Except', 'widget', 'azzu'.LANG_DN); ?></label>

				</div>

				<div class="hide-if-js">

					<?php foreach( $terms as $term ): ?>

					<input id="<?php echo $this->get_field_id($term->term_id); ?>" type="checkbox" name="<?php echo $this->get_field_name('cats'); ?>[]" value="<?php echo $term->term_id; ?>" <?php checked( in_array($term->term_id, $instance['cats']) ); ?> />
					<label for="<?php echo $this->get_field_id($term->term_id); ?>"><?php echo $term->name; ?></label><br />

					<?php endforeach; ?>

				</div>

			<?php endif; ?>

		</p>

		<div style="clear: both;"></div>
	<?php
	}

	public static function azzu_register_widget() {
		register_widget( get_class() );
	}
}
