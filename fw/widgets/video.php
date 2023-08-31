<?php
/**
 * Blog categories widget.
 *
 * @package azzu.
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Load the widget */
add_action( 'widgets_init', array( 'Azzu_Widgets_video', 'azzu_register_widget' ) );

class Azzu_Widgets_video extends WP_Widget {

        /* Widget setup  */
	function __construct() {  
            /* Widget settings. */
            $widget_ops = array( 'description' => _x( 'You can add youtube and Vimeo', 'widget', 'azzu'.LANG_DN ) );

            /* Create the widget. */
            parent::__construct(
                'azzu-blog-video',
                AZU_WIDGET_PREFIX . _x( 'Video', 'widget', 'azzu'.LANG_DN ),
                $widget_ops
            );
	}

        /* Display the widget  */
	function widget( $args, $instance ) {
		extract( $args );
		$title = $instance['title'];
		$type= $instance['type'];
		$clip_id= $instance['clip_id'];
		$width= $instance['width'];
                //if(empty($width) || $width==0)
                    $width = 1080;
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;


                $class='embed-responsive embed-responsive-16by9';
		if ( !empty( $clip_id ) ) {

			$height = intval( $width * 9 / 16 );
                        $if_tag = 'iframe';
			// Vimeo Video post type
			if ( $type =='vimeo' ) {
				echo '<div class="'.$class.'"><'.$if_tag.' src="http'.((is_ssl())? 's' : '').'://player.vimeo.com/video/'.$clip_id.'?title=0&amp;byline=0&amp;portrait=0&amp;color=01a8ff" width="'.$width.'" height="'.$height.'" allowFullScreen></'.$if_tag.'></div>';
			}

			// Youtube Video post type
			if ( $type =='youtube' ) {
				echo '<div class="'.$class.'"><'.$if_tag.' src="http'.((is_ssl())? 's' : '').'://www.youtube.com/embed/'.$clip_id.'?showinfo=0&amp;theme=light&amp;color=white&amp;autohide=1" width="'.$width.'" height="'.$height.'" allowFullScreen></'.$if_tag.'></div>';
			}

			// dailymotion Video post type
			if ( $type =='dailymotion' ) {

				echo '<div class="'.$class.'"><'.$if_tag.' width="'.$width.'" height="'.$height.'" src="http'.((is_ssl())? 's' : '').'://www.dailymotion.com/embed/video/'.$clip_id.'?foreground=%2300c65d&amp;highlight=%23ffffff&amp;background=%23000000&amp;logo=0"></'.$if_tag.'></div>';
			}

			// bliptv Video post type
			if ( $type =='bliptv' ) {
				echo '<div class="'.$class.'"><'.$if_tag.' src="http'.((is_ssl())? 's' : '').'://blip.tv/play/'.$clip_id.'.x?p=1" width="'.$width.'" height="'.$height.'" allowfullscreen></'.$if_tag.'><embed type="application/x-shockwave-flash" src="http://a.blip.tv/api.swf#'.$clip_id.'" style="display:none"></embed></div>';
			}


			// viddler Video post type
			if ( $type =='viddler' ) {
				echo '<div class="'.$class.'"><'.$if_tag.' id="viddler-bdce8c7" src="//www.viddler.com/embed/'.$clip_id.'/?f=1&amp;offset=0&amp;autoplay=0&amp;secret=18897048&amp;disablebranding=0&amp;view_secret=18897048" width="'.$width.'" height="'.$height.'" mozallowfullscreen="true" webkitallowfullscreen="true" scrolling="no" style="overflow:hidden !important;"></'.$if_tag.'></div>';
			}
		}




		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['type'] = strip_tags( $new_instance['type'] );
		$instance['clip_id'] = $new_instance['clip_id'];
		$instance['width'] = absint($new_instance['width']);

		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$type = isset( $instance['type'] ) ? $instance['type'] : 'youtube';
		$clip_id = isset( $instance['clip_id'] ) ? $instance['clip_id'] : '';
		$width = isset( $instance['width'] ) ? absint( $instance['width'] ) : 300;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'azzu'.LANG_DN); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

     	<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e('Type:', 'azzu'.LANG_DN); ?></label>
			<select name="<?php echo $this->get_field_name( 'type' ); ?>" id="<?php echo $this->get_field_id( 'type' ); ?>" class="widefat">
            	<option value="youtube"<?php selected( $type, 'youtube' );?>><?php _e('Youtube', 'azzu'.LANG_DN); ?></option>
				<option value="vimeo"<?php selected( $type, 'vimeo' );?>><?php _e('Vimeo', 'azzu'.LANG_DN); ?></option>
				<option value="dailymotion"<?php selected( $type, 'dailymotion' );?>><?php _e('Dailymotion', 'azzu'.LANG_DN); ?></option>
				<option value="bliptv"<?php selected( $type, 'bliptv' );?>><?php _e('bliptv', 'azzu'.LANG_DN); ?></option>
				<option value="viddler"<?php selected( $type, 'viddler' );?>><?php _e('viddler', 'azzu'.LANG_DN); ?></option>

			</select>
		</p>

		<p><label for="<?php echo $this->get_field_id( 'clip_id' ); ?>"><?php _e('Clip Id:', 'azzu'.LANG_DN); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'clip_id' ); ?>" name="<?php echo $this->get_field_name( 'clip_id' ); ?>" type="text" value="<?php echo $clip_id; ?>" /></p>

		<p style="display: none;"><label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Max width', 'azzu'.LANG_DN); ?></label>
		<input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo $width; ?>" size="3" /></p>

<?php

	}
        
        public static function azzu_register_widget() {
		register_widget( get_class() );
	}
}
/***************************************************/
