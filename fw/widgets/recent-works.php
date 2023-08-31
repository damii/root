<?php
/**
 * Recent works widget.
 *
 * @package azzu.
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Load the widget */
add_action( 'widgets_init', array( 'Azzu_Widgets_RecentWorks', 'azzu_register_widget' ) );

class Azzu_Widgets_RecentWorks extends WP_Widget {
    
    /* Widget defaults */
    public static $widget_defaults = array( 
		'title'     	=> '',
		'order'     	=> 'DESC',
		'orderby'   	=> 'date',
                'columns'       => 3,
		'select'	=> 'all',
		'show'      	=> 6,
		'cats'      	=> array(),
    );

	/* Widget setup  */
	function __construct() {  
            /* Widget settings. */
                    $widget_ops = array( 'description' => _x( 'Recent works', 'widget', 'azzu'.LANG_DN ) );

            /* Create the widget. */
            parent::__construct(
                'azzu-recent-works',
                AZU_WIDGET_PREFIX . _x( 'Recent works', 'widget', 'azzu'.LANG_DN ),
                $widget_ops
            );
	}

	/* Display the widget  */
	function widget( $args, $instance ) {

		extract( $args );

        $instance = wp_parse_args( (array) $instance, self::$widget_defaults );

		/* Our variables from the widget settings. */
		$title = apply_filters( 'widget_title', $instance['title'] );
		$terms = empty($instance['cats']) ? array(0) : (array) $instance['cats'];

        $html = '';
        if ( $terms ) {

        	$attachments_data = azuh()->azzu_get_related_posts( array(
                                'exclude_current'	=> false,
                                'cats'			=> $terms,
                                'select'		=> $instance['select'],
                                'post_type' 		=> 'azu_portfolio',
                                'taxonomy'		=> 'azu_portfolio_category',
                                'field'			=> 'term_id',
                                'args'			=> array(
        			'posts_per_page' 	=> $instance['show'],
        			'orderby'		=> $instance['orderby'],
        			'order'                 => $instance['order'],
        		)
        	) );

                $widget_class = 'instagram-photos recent-works clearfix';
                $widget_class .= ' azu-col-'.$instance['columns'];
		$list_args = array('show_title' => false, 'class' => 'azu-rollover rollover-small','image_size' => 150);
                
        	$posts_list = azuh()->azzu_get_posts_small_list( $attachments_data, $list_args );
        	if ( $posts_list ) {

        		foreach ( $posts_list as $p ) {
        			$html .= $p;
        		}

        		$html = '<div class="'.$widget_class.'">' . $html . '</div>';
        	}
        }

		echo $before_widget ;

		// title
		if ( $title ) echo $before_title . $title . $after_title;

		echo $html;

		echo $after_widget;
	}

	/* Update the widget settings  */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
        
		$instance['title'] 	= strip_tags($new_instance['title']);
                $instance['order']    	= esc_attr($new_instance['order']);
		$instance['orderby']   	= esc_attr($new_instance['orderby']);
		$instance['show']     	= intval($new_instance['show']);
		$instance['columns']    = absint($new_instance['columns']);
		$instance['select']   	= in_array( $new_instance['select'], array('all', 'only', 'except') ) ? $new_instance['select'] : 'all';
		$instance['cats']    	= (array) $new_instance['cats'];
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

	$terms = get_terms( 'azu_portfolio_category', array(
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
        
        $col_list = array( 
                3 => '3',
                2 => '2',
                4 => '4',
                5 => '5',
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
			<label for="<?php echo $this->get_field_id( 'show' ); ?>"><?php _ex('Number of posts:', 'widget', 'azzu'.LANG_DN); ?></label>
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
			<label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php _ex('Columns:', 'widget', 'azzu'.LANG_DN); ?></label>
			<select id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>">
				<?php foreach( $col_list as $value=>$name ): ?>
				<option value="<?php echo $value; ?>" <?php selected( $instance['columns'], $value ); ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<div style="clear: both;"></div>
	<?php
	}

	public static function azzu_register_widget() {
		register_widget( get_class() );
	}
}
