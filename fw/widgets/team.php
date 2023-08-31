<?php
/**
 * Team widget.
 *
 * @package azzu.
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Load the widget */
add_action( 'widgets_init', array( 'Azzu_Widgets_Team', 'azzu_register_widget' ) );

class Azzu_Widgets_Team extends WP_Widget {

	/* Widget defaults */
	public static $widget_defaults = array( 
		'title'         => '',
		'order'     	=> 'DESC',
		'orderby'   	=> 'date',
		'select'        => 'all',
		'show'          => 6,
		'cats'          => array(),
		'autoslide'		=> 0,
	);

	/* Widget setup  */
	function __construct() {  
		/* Widget settings. */
		$widget_ops = array( 'description' => _x( 'Team', 'widget', 'azzu'.LANG_DN ) );

		/* Create the widget. */
		parent::__construct(
			'azzu-team',
			AZU_WIDGET_PREFIX . _x( 'Team', 'widget', 'azzu'.LANG_DN ),
			$widget_ops
		);
	}

	/* Display the widget  */
	function widget( $args, $instance ) {

		extract( $args );

		$instance = wp_parse_args( (array) $instance, self::$widget_defaults );

		/* Our variables from the widget settings. */
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget . "\n";

		// title
		if ( $title ) {
			echo $before_title . $title . $after_title . "\n";
		}

                $attr = array(
                    'type' => 'slider',
                    'padding' => 0,
                    'category' => implode(",",$instance['cats']),
                    'order' => $instance['order'],
                    'orderby' => $instance['orderby'],
                    'number' => $instance['show'],
                    'autoslide' => $instance['autoslide'],
                    'select' => $instance['select'],
                    'proportion' => '1:1'
                );
                
                echo azut()->azzu_call_shortcode('azu_team',$attr);
                
		echo $after_widget . "\n";
	}

	/* Update the widget settings  */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']      = strip_tags($new_instance['title']);
		$instance['order']      = apply_filters('azu_sanitize_order', $new_instance['order']);
		$instance['orderby']    = apply_filters('azu_sanitize_orderby', $new_instance['orderby']);
		$instance['select']     = in_array( $new_instance['select'], array('all', 'only', 'except') ) ? $new_instance['select'] : 'all';
		$instance['show']       = intval($new_instance['show']);

		$instance['cats']       = (array) $new_instance['cats'];
		if ( empty($instance['cats']) ) {
			$instance['select'] = 'all';
		}

		$instance['autoslide']  = absint($new_instance['autoslide']);

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

		$terms = get_terms( 'azu_team_category', array(
			'hide_empty'    => 1,
			'hierarchical'  => false 
		) );

		$orderby_list = array(
			'ID'        => _x( 'Order by ID', 'widget', 'azzu'.LANG_DN ),
			'author'    => _x( 'Order by author', 'widget', 'azzu'.LANG_DN ),
			'title'     => _x( 'Order by title', 'widget', 'azzu'.LANG_DN ),
			'date'      => _x( 'Order by date', 'widget', 'azzu'.LANG_DN ),
			'modified'  => _x( 'Order by modified', 'widget', 'azzu'.LANG_DN ),
			'rand'      => _x( 'Order by rand', 'widget', 'azzu'.LANG_DN ),
			'menu_order'=> _x( 'Order by menu', 'widget', 'azzu'.LANG_DN )
		);

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _ex('Title:', 'widget',  'azzu'.LANG_DN); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
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

		<p>
			<label for="<?php echo $this->get_field_id( 'show' ); ?>"><?php _ex('Number of team members:', 'widget', 'azzu'.LANG_DN); ?></label>
			<input id="<?php echo $this->get_field_id( 'show' ); ?>" name="<?php echo $this->get_field_name( 'show' ); ?>" value="<?php echo esc_attr($instance['show']); ?>" size="2" maxlength="2" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _ex('Sort by:', 'widget', 'azzu'.LANG_DN); ?></label>
			<select id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
				<?php foreach( $orderby_list as $value=>$name ): ?>
				<option value="<?php echo $value; ?>" <?php selected( $instance['orderby'], $value ); ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		</p>
			<label>
			<input name="<?php echo $this->get_field_name( 'order' ); ?>" value="ASC" type="radio" <?php checked( $instance['order'], 'ASC' ); ?> /><?php _ex('Ascending', 'widget', 'azzu'.LANG_DN); ?>
			</label>
			<label>
			<input name="<?php echo $this->get_field_name( 'order' ); ?>" value="DESC" type="radio" <?php checked( $instance['order'], 'DESC' ); ?> /><?php _ex('Descending', 'widget', 'azzu'.LANG_DN); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'autoslide' ); ?>"><?php _ex('Autoslide: 1000ms = 1 second', 'widget',  'azzu'.LANG_DN); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'autoslide' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'autoslide' ); ?>" value="<?php echo esc_attr($instance['autoslide']); ?>" />
		</p>

		<div style="clear: both;"></div>
	<?php
	}

	public static function azzu_register_widget() {
		register_widget( get_class() );
	}
}