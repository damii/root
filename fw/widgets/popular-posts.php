<?php
/**
 * Popular post widget.
 *
 * @package azzu.
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Load the widget */
add_action( 'widgets_init', array( 'Azzu_Widgets_PopularPosts', 'azzu_register_widget' ) );

class Azzu_Widgets_PopularPosts extends WP_Widget {
    
    /* Widget defaults */
    public static $widget_defaults = array( 
		'title'     	=> '',
		'order'     	=> 'DESC',
		'orderby'   	=> 'date',
                'display'   	=> 'date',
		'select'	=> 'all',
		'show'      	=> 6,
		'cats'      	=> array(),
                'recent'        => 0,
                'daily_range'   => 30,
		'thumbnails'	=> true,
    );

	/* Widget setup  */
	function __construct() {  
        /* Widget settings. */
		$widget_ops = array( 'description' => _x( 'Popular posts', 'widget', 'azzu'.LANG_DN ) );

	/* Create the widget. */
        parent::__construct(
            'azzu-popular-posts',
            AZU_WIDGET_PREFIX . _x( 'Popular posts', 'widget', 'azzu'.LANG_DN ),
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
        $recent_list ='';
        $output = '<div class="azu-tabbable">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#popular-post" data-toggle="tab">'._x( 'Popular', 'widget', 'azzu'.LANG_DN ).'</a></li>
            <li><a href="#recent-post" data-toggle="tab">'._x( 'Recent', 'widget', 'azzu'.LANG_DN ).'</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="popular-post">%1$s</div>
            <div class="tab-pane" id="recent-post">%2$s</div>
          </div>
        </div>';
        
        if ( $terms ) {
                $list_args = array( 'show_images' => (boolean) $instance['thumbnails'] );
                
                if($instance['display'] == 'date')
                    $list_args['is_date']= true;
                elseif($instance['display'] == 'comment')      
                        $list_args['is_comment']= true;
                
                if($instance['recent']){
                     $attachments_data = azuh()->azzu_get_related_posts( array(
                            'exclude_current'	=> false,
                            'cats'				=> $terms,
                            'select'			=> $instance['select'],
                            'post_type'                     => 'post',
                            'taxonomy'			=> 'category',
                            'field'				=> 'term_id',
                            'args'				=> array(
                                    'posts_per_page'    => $instance['show'],
                                    'orderby'           => 'date',
                                    'order'             => 'DESC',
                            )
                    ) );
                    $posts_list = azuh()->azzu_get_posts_small_list( $attachments_data, $list_args );
                    if ( $posts_list ) {

        		foreach ( $posts_list as $p ) {
        			$recent_list .= sprintf( '<li>%s</li>', $p );
        		}

        		$recent_list = '<ul class="recent-posts">' . $recent_list . '</ul>';
                    }
                }
                    
            	
        	$attachments_data = azuh()->azzu_get_related_posts( array(
        		'exclude_current'	=> false,
        		'cats'				=> $terms,
        		'select'			=> $instance['select'],
        		'post_type'                     => 'post',
        		'taxonomy'			=> 'category',
        		'field'				=> 'term_id',
        		'args'				=> array(
        			'posts_per_page'    => $instance['show'],
                                'meta_key'          => '_azu-popular-posts-' . $instance['orderby'],
        			'orderby'           => 'meta_value_num',
        			'order'             => $instance['order'],
                                'date_query'    => array(
                                    'column'  => 'post_date',
                                    'after'   => '-'.$instance['daily_range'].' days'
                                ),
        		)
        	) );

			

        	$posts_list = azuh()->azzu_get_posts_small_list( $attachments_data, $list_args );
        	if ( $posts_list ) {

        		foreach ( $posts_list as $p ) {
        			$html .= sprintf( '<li>%s</li>', $p );
        		}

        		$html = '<ul class="recent-posts">' . $html . '</ul>';
        	}
        }

		echo $before_widget ;

		// title
		if ( $title ) echo $before_title . $title . $after_title;

                if($instance['recent'])
                    echo sprintf($output,$html,$recent_list);
                else      
                    echo $html; 
                

		echo $after_widget;
	}

	/* Update the widget settings  */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
        
		$instance['title'] 	= strip_tags($new_instance['title']);
                $instance['order']    	= esc_attr($new_instance['order']);
		$instance['orderby']   	= esc_attr($new_instance['orderby']);
                $instance['display']    = esc_attr($new_instance['display']);
		$instance['show']     	= intval($new_instance['show']);
		
		$instance['select']   	= in_array( $new_instance['select'], array('all', 'only', 'except') ) ? $new_instance['select'] : 'all';
		$instance['cats']    	= (array) $new_instance['cats'];
		if ( empty($instance['cats']) ) { $instance['select'] = 'all'; }

                $instance['recent'] = absint($new_instance['recent']);
		$instance['thumbnails'] = absint($new_instance['thumbnails']);
                $instance['daily_range'] = absint($new_instance['daily_range']);
                
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

	$terms = get_terms( 'category', array(
            'hide_empty'    => 1,
            'hierarchical'  => false 
        ) );

	$orderby_list = array(
            'pageviews'        => _x( 'Pageviews', 'widget', 'azzu'.LANG_DN ),
            'comments'        => _x( 'Comments', 'widget', 'azzu'.LANG_DN ),
        );
        
        $display_list = array(
            'date'        => _x( 'Post date', 'widget', 'azzu'.LANG_DN ),
            'comment'    => _x( 'Comments', 'widget', 'azzu'.LANG_DN ),
            'none'     => _x( 'None', 'widget', 'azzu'.LANG_DN )
        );

        ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _ex('Title:', 'widget',  'azzu'.LANG_DN); ?></label>
            <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'recent' ); ?>"><?php _ex('with tabbable recent posts', 'widget', 'azzu'.LANG_DN); ?>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'recent' ); ?>" value="1" <?php checked($instance['recent']); ?> />
		</p>
                <p>
                	<label for="<?php echo $this->get_field_id( 'daily_range' ); ?>">
				 <?php _ex( 'before days: ', 'widget', 'azzu'.LANG_DN ); ?>
                                 <input  id="<?php echo $this->get_field_id( 'daily_range' ); ?>" name="<?php echo $this->get_field_name( 'daily_range' ); ?>" type="text" value="<?php echo esc_attr( $instance['daily_range']); ?>" size="3" maxlength="3" />
			</label>
                        
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
			<label for="<?php echo $this->get_field_id( 'thumbnails' ); ?>"><?php _ex('Show featured images', 'widget', 'azzu'.LANG_DN); ?>
			<input type="checkbox" name="<?php echo $this->get_field_name( 'thumbnails' ); ?>" value="1" <?php checked($instance['thumbnails']); ?> />
		</p>
                <p>
			<label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _ex('Display:', 'widget', 'azzu'.LANG_DN); ?></label>
			<select id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>">
				<?php foreach( $display_list as $value=>$name ): ?>
				<option value="<?php echo $value; ?>" <?php selected( $instance['display'], $value ); ?>><?php echo $name; ?></option>
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
