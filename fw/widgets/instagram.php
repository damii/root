<?php
/**
 * Instagram widget.
 *
 * @package azzu.
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/* Load the widget */
add_action( 'widgets_init', array( 'Azzu_Widgets_Instagram', 'azzu_register_widget' ) );

class Azzu_Widgets_Instagram extends WP_Widget {

	/* Widget defaults */
	public static $widget_defaults = array(
            'title'                 => '',
            'user_id'               => '',
            'columns'               => 3,
            'showfollow'            => false,
            'items'                => 12
        );  

	/* Widget setup  */
	function __construct() {  
		/* Widget settings. */
		$widget_ops = array( 'description' => _x( 'Instagram', 'widget', 'azzu'.LANG_DN ) );

		/* Create the widget. */
		parent::__construct(
			'azzu-instagram-widget',
			AZU_WIDGET_PREFIX . _x( 'Instagram', 'widget', 'azzu'.LANG_DN ),
			$widget_ops
		);
	}

	/* Display the widget  */
        function widget( $args, $instance ) {
            
            extract($args);

            $instance = wp_parse_args( (array) $instance, self::$widget_defaults );

            /* Our variables from the widget settings. */
            $title = apply_filters( 'widget_title', $instance['title'] );
            $count = $instance['items'];
            $thumb_width = 150;
            $user_id = 'self';
            $access_token = '1988767744.cf0499d.7498a7405fff4b6d9d3a13d85def176d';
            if(empty($instance['user_id']) || strlen($instance['user_id']) > 10)
                $access_token = $instance['user_id'];
            else
                $user_id = $instance['user_id'];
            
            $apiurl = "https://api.instagram.com/v1/users/" . $user_id . "/media/recent/?access_token=" . $access_token;
            
            $response = wp_remote_get( $apiurl, array('timeout' => 20 ) );
            $widget_class = 'instagram-photos clearfix';
            $widget_class .= ' azu-col-'.$instance['columns'];
            
            $azu_instagram_content = '<div class="'.$widget_class.'">';
            
            if(!is_wp_error($response)) {
                    $data = json_decode( $response['body'], true );

                    $username = '';
                    if( isset($data['data']) && $data && count($data['data']) > 0 ){
                        $username = $data['data'][0]['user']['username'];
                        if( count( $data['data'] ) > $count )
                                $query = $count;
                        else 
                                $query = count( $data['data'] );
                    }
                    else {
                        $query = 0;
                        $azu_instagram_content .= "Something went wrong with the Instagram feed! Please check your configuration and make sure that the Instagram id exists";
                    }


                    for( $i = 0; $i < $query; $i++ ) {
                            $output = '<a href="' . $data['data'][$i]['link'] . '" title="' . htmlspecialchars( $data['data'][$i]['caption']['text'], ENT_QUOTES ). '" class="azu-rollover rollover-small" target="_blank">';
                                    $output .= '<img src="' . $data['data'][$i]['images']['thumbnail']['url'] . '" width="' . $thumb_width .'" height="' . $thumb_width . '" title="' . $data['data'][$i]['caption']['text'] . '">';
                                    $output .= "</a>";
                                    $azu_instagram_content .= $output;		
                    }

                    //Follow button HTML
                    if($instance['showfollow'])
                        $azu_instagram_content .= '<div class="azzu_follow_btn"><a href="http://instagram.com/'.$username.'"  target="_blank"><i class="icon-instagram"></i>'._x('Follow On Instagram','widget','azzu'.LANG_DN).'</a></div>';
                    $azu_instagram_content .= '</div>'; 
            }
            
            echo $before_widget;

            // title
            if ( $title ) echo $before_title . $title . $after_title;

            echo $azu_instagram_content;

            echo $after_widget;
        }

	/* Update the widget settings  */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
                $instance['user_id'] = trim(strip_tags($new_instance['user_id']));
                $instance['items'] = absint($new_instance['items']);
                $instance['columns'] = absint($new_instance['columns']);
                $instance['showfollow'] = $new_instance['showfollow'];
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

                $title = esc_html($instance["title"]);
                $items = esc_html($instance["items"]);
                if( empty($items) || $items < 1 ) $items = 3;
                $col_list = array( 
                        3 => '3',
                        2 => '2',
                        4 => '4',
                        5 => '5',
                );
                $instagram_id = esc_html($instance["user_id"]);
                $showfollow = esc_html($instance["showfollow"]);

                ?>
                <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _ex('Title:', 'widget', 'azzu'.LANG_DN); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
                <p><label for="<?php echo $this->get_field_id( 'user_id' ); ?>"><?php _ex('User id | Access Token:', 'widget', 'azzu'.LANG_DN); ?> &nbsp;(<a href="<?php echo 'http://otzberg.net/iguserid/'; ?>" target="_blank">Get Your ID</a>) <input class="widefat" id="<?php echo $this->get_field_id( 'user_id' ); ?>" name="<?php echo $this->get_field_name( 'user_id' ); ?>" type="text" value="<?php echo $instagram_id; ?>" /></label></p>
		<p>
			<label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php _ex('Columns:', 'widget', 'azzu'.LANG_DN); ?></label>
			<select id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>">
				<?php foreach( $col_list as $value=>$name ): ?>
				<option value="<?php echo $value; ?>" <?php selected( $instance['columns'], $value ); ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
                <p><label for="<?php echo $this->get_field_id( 'items' ); ?>"><?php _ex('How many items?', 'widget', 'azzu'.LANG_DN); ?> <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'items' ); ?>" name="<?php echo $this->get_field_name( 'items' ); ?>" value="<?php echo $items; ?>" /></label></p>
                <p><label for="<?php echo $this->get_field_id( 'showfollow' ); ?>"><input id="<?php echo $this->get_field_id( 'showfollow' ); ?>" name="<?php echo $this->get_field_name( 'showfollow' ); ?>" type="checkbox" value="checked" <?php echo $showfollow; ?> /> <?php _ex('Show follow link', 'widget', 'azzu'.LANG_DN); ?></label></p>
                <div style="clear: both;"></div>
                <?php
        }
        
        
        public static function azzu_register_widget() {
		register_widget( get_class() );
	}
}
