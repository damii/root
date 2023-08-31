<?php
/**
 * Flickr widget.
 *
 * @package azzu.
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/* Load the widget */
add_action( 'widgets_init', array( 'Azzu_Widgets_Flickr', 'azzu_register_widget' ) );

class Azzu_Widgets_Flickr extends WP_Widget {

	/* Widget defaults */
	public static $widget_defaults = array(
            'title'                 => '',
            'columns'               => 3,
            'items'                 => 12,
            'view'                  => '_q', //_m , _s, _t
            'defore_item'           => '',
            'after_item'            => '',
            'before_flickr_widget'  => '',
            'after_flickr_widget'   => '',
            'more_title'            => '',
            'target'                => '',
            'show_titles'           => '',
            'user_id'               => '',
            'error'                 => '',
            'thickbox'              => 'checked',
            'tags'                  => '',
            'random'                => '',
            'showfollow'            => false,
            'javascript'            => ''
        );  

	/* Widget setup  */
	function __construct() {  
		/* Widget settings. */
		$widget_ops = array( 'description' => _x( 'Flickr', 'widget', 'azzu'.LANG_DN ) );

		/* Create the widget. */
		parent::__construct(
			'azzu-flickr-widget',
			AZU_WIDGET_PREFIX . _x( 'Flickr', 'widget', 'azzu'.LANG_DN ),
			$widget_ops
		);
	}

	/* Display the widget  */
        function widget( $args, $instance ) {
            
	extract($args);

        $instance = wp_parse_args( (array) $instance, self::$widget_defaults );
	
	$title = apply_filters( 'widget_title', $instance["title"] );
        $user_link = '';
	$items = $instance["items"];
	$view = $instance["view"];
	$before_item = '';
	$after_item = '';
	$before_flickr_widget = $instance["before_flickr_widget"];
	$after_flickr_widget = $instance["after_flickr_widget"];
	$more_title = $instance["more_title"];
	$target = $instance["target"];
	$show_titles = $instance["show_titles"];
	$user_id = isset($instance["user_id"])?$instance["user_id"]:'';
	$error = $instance["error"];
	$thickbox = $instance["thickbox"];
	$tags = isset($instance["tags"])?$instance["tags"]:'';
	$random = isset($instance["random"])?$instance["random"]:false;
	$javascript = $instance["javascript"];
	if (empty($error) || 1)
	{	
		$target = "";
		$show_titles = ($show_titles == "checked") ? true : false;
		$thickbox = ($thickbox == "checked") ? true : false;
		$tags = (strlen($tags) > 0) ? "&tags=" . urlencode($tags) : "";
		$random = ($random == "checked") ? true : false;
		$javascript = ($javascript == "checked") ? true : false;
		
		if ($javascript) $flickrformat = "json"; else $flickrformat = "php";
		
		$flickrformat = "json"; 
		
		if (empty($items) || $items < 1 || $items > 20) $items = 3;
		
		// user id?
		$url = "http://api.flickr.com/services/feeds/photos_public.gne?id=".urlencode($user_id)."&format=".$flickrformat."&lang=en-us".$tags;
		
		$url = preg_replace('/(format=)[^$\&]+/', '\\1'.$flickrformat.'', $url);

		//echo $url;
		
		if (!function_exists("json_decode"))
		{
		   $out =  "This widget is unfortunately not supported by your host. You can use JS widget provided by flickr.com. Please refer to flickr.com documentation or install a flickr widget.";
		}
		// Output via php or javascript?
		elseif (!$javascript)
		{
		   $flickr_data = wp_remote_fopen($url);

		   $flickr_data = str_replace('<?php', '', $flickr_data);
		   $flickr_data = str_replace('?>', '', $flickr_data);
			$flickr_data = str_replace("jsonFlickrFeed(", "", $flickr_data);
			$flickr_data = preg_replace("/\)[\n\r\t ]*$/", "", $flickr_data);
			$flickr_data = preg_replace('/"(description|title)":.*?\n/', '', $flickr_data);
			$flickr_data = json_decode($flickr_data, TRUE);
						
			$photos = $flickr_data;
			
			if($random && isset($photos["items"]) ) shuffle($photos["items"]);
			
			if ($photos)
			{	
			   $out="";
			   $counter=1;
                           $thumb_width = ($thickbox) ? 150 : 75;
                           $user_link = $photos["link"];
				foreach($photos["items"] as $key => $value)
				{
					if (--$items < 0) break;
					$photo_title = '';
										
					$photo_url = $value["media"]["m"];
                                        
                                        $photo_medium_url = str_replace("_m.jpg", "$view.jpg", $photo_url);
					$photo_url = str_replace("_m.jpg", "_s.jpg", $photo_url);
					
					$photo_url = ($thickbox) ? $photo_medium_url : $photo_url;
					$href = $value["link"];
					
					$out .= $before_item . "<a target=\"_blank\" href=\"$href\" class=\"azu-rollover rollover-small\"><img alt=\"\" title=\"\" src=\"$photo_url\" width=\"$thumb_width\" height=\"$thumb_width\" /></a>$photo_title" . "" .$after_item;
					
					$counter++;
				}
				$flickr_home = $photos["link"];
			}
			else
			{
				$out = "Something went wrong with the Flickr feed! Please check your configuration and make sure that the Flickr id exists";
			}
		}
		else // via javascript
		{
			$out = "<script type=\"text/javascript\" src=\"$url\"></script>";
		}

		$before_widget = str_replace('class="widget', 'class="widget instagram-photos', $before_widget);
		?>
<!-- Quick Flickr start -->
	<?php 
        $widget_class = 'instagram-photos clearfix';
        $widget_class .= ' azu-col-'.$instance['columns'];
        echo $before_widget . $before_flickr_widget; ?>
	<?php if(!empty($title)) { $title = apply_filters('localization', $title); echo $before_title . $title . $after_title; } ?>
	<?php echo '<div class="'.$widget_class.'">'; ?>
		<?php echo $out ?>
		<?php if (!empty($more_title) && !$javascript) echo "<a href=\"" . strip_tags($flickr_home) . "\">$more_title</a>"; ?>
	<?php 
                //Follow button HTML
                if($instance['showfollow'])
                    echo '<div class="azzu_follow_btn"><a href="'.$user_link.'"  target="_blank"><i class="icon-flickr"></i>'._x('Visit On Flickr','widget','azzu'.LANG_DN).'</a></div>';
              echo '</div>' . $after_flickr_widget . $after_widget; ?>
<!-- Quick Flickr end -->
	<?php
	}
	else // error
	{
		$out = $error;
	}
}

	/* Update the widget settings  */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
                $instance['columns'] = absint($new_instance['columns']);
                $instance['user_id'] = strip_tags($new_instance['user_id']);
                $instance['items'] = absint($new_instance['items']);
                $instance['tags'] = strip_tags($new_instance['tags']);
                $instance['random'] = $new_instance['random'];
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
                $flickr_id = esc_html($instance["user_id"]);

                $tags = esc_html($instance["tags"]);
                $random = esc_html($instance["random"]);
                $showfollow = esc_html($instance["showfollow"]);
                ?>
                <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _ex('Title:', 'widget', 'azzu'.LANG_DN); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
                <p><label for="<?php echo $this->get_field_id( 'user_id' ); ?>"><?php _ex('Flickr User id :', 'widget', 'azzu'.LANG_DN); ?> &nbsp;(<a href="<?php echo 'http://idgettr.com/'; ?>" target="_blank">Get Your ID</a>) <input class="widefat" id="<?php echo $this->get_field_id( 'user_id' ); ?>" name="<?php echo $this->get_field_name( 'user_id' ); ?>" type="text" value="<?php echo $flickr_id; ?>" /></label></p>
		<p>
			<label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php _ex('Columns:', 'widget', 'azzu'.LANG_DN); ?></label>
			<select id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>">
				<?php foreach( $col_list as $value=>$name ): ?>
				<option value="<?php echo $value; ?>" <?php selected( $instance['columns'], $value ); ?>><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
                <p><label for="<?php echo $this->get_field_id( 'items' ); ?>"><?php _ex('How many items?', 'widget', 'azzu'.LANG_DN); ?> <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'items' ); ?>" name="<?php echo $this->get_field_name( 'items' ); ?>" value="<?php echo $items; ?>" /></label></p>

                <p><label for="<?php echo $this->get_field_id( 'tags' ); ?>"><?php _ex('Filter by tags (comma seperated):', 'widget', 'azzu'.LANG_DN); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'tags' ); ?>" name="<?php echo $this->get_field_name( 'tags' ); ?>" type="text" value="<?php echo $tags; ?>" /></label></p>

                <p><label for="<?php echo $this->get_field_id( 'random' ); ?>"><input id="<?php echo $this->get_field_id( 'random' ); ?>" name="<?php echo $this->get_field_name( 'random' ); ?>" type="checkbox" value="checked" <?php echo $random; ?> /> <?php _ex('Random pick', 'widget', 'azzu'.LANG_DN); ?></label></p>
                <p><label for="<?php echo $this->get_field_id( 'showfollow' ); ?>"><input id="<?php echo $this->get_field_id( 'showfollow' ); ?>" name="<?php echo $this->get_field_name( 'showfollow' ); ?>" type="checkbox" value="checked" <?php echo $showfollow; ?> /> <?php _ex('Show visit link', 'widget', 'azzu'.LANG_DN); ?></label></p>
                <div style="clear: both;"></div>
                <?php
        }
        
        public static function azzu_register_widget() {
		register_widget( get_class() );
	}
}
