<?php
/**
 * Image widget.
 *
 * @package azzu.
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Load the widget */
add_action( 'widgets_init', array( 'Azzu_Widgets_Image', 'azzu_register_widget' ) );

class Azzu_Widgets_Image extends WP_Widget {
    
    /* Widget defaults */
    public static $widget_defaults = array( 
                'title' => '',
                'width' => 400,
                'link'      => '',
                'target'      => '',
		'image' => ''
    );

	/* Widget setup  */
	function __construct() {  
            /* Widget settings. */
            $widget_ops = array( 'description' => _x( 'Image', 'widget', 'azzu'.LANG_DN ) );

            /* Create the widget. */
            parent::__construct(
                'azzu-image',
                AZU_WIDGET_PREFIX . _x( 'Image', 'widget', 'azzu'.LANG_DN ),
                $widget_ops
            );
            if ( 'widgets.php' == basename( $_SERVER['PHP_SELF'] ) ) {
                    add_action( 'admin_print_scripts', array( &$this, 'add_admin_script' ) );
            }
	}
        
        function add_admin_script() {
                wp_enqueue_media();
		wp_enqueue_script( 'azu-image-upload', AZZU_OPTIONS_URI . '/assets/js/image-upload.js', array( 'jquery' ) );
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
                $wrap = '<img %IMG_CLASS% %SRC% %SIZE% %IMG_TITLE% %ALT% />';
                if ( !empty($instance['link'])){
                    $wrap = '<a %HREF% %CLASS% %CUSTOM% title="%RAW_ALT%" ><img %IMG_CLASS% %SRC% %SIZE% %IMG_TITLE% %ALT% /></a>';
                }
                $target = '';
                if ($instance['target'] === '_blank'){
                   $target = 'target="_blank"';
                }
                
                    /////////////////////////////
                    // Image                   //
                    /////////////////////////////
                    if ( !empty($instance['image'])){
			$media_img = azuf()->azu_get_thumb_img( array(
				'img_meta'      => wp_get_attachment_image_src( $instance['image'], 'full' ),
				'img_id'		=> $instance['image'],
                                'class'		=> '',
                                'href'		=> $instance['link'],
                                'custom'        => $target,
                                'options'       => array( 'w' => $instance['width'] * azuf()->azu_device_pixel_ratio() ),
				'echo'			=> false,
				'wrap'			=> $wrap,
			) );
                        $output .= '<div class="azu-widget-image" style="max-width: '.$instance['width'].'px;">'.$media_img.'</div>';
                    }
                    echo $output;
                echo $after_widget;
	}

	/* Update the widget settings  */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
       		$instance['title']    = strip_tags( $new_instance['title'] );
                $instance['width'] = absint( $new_instance['width'] );
                $instance['link'] = esc_url( $new_instance['link']);
                $instance['target'] = esc_attr( $new_instance['target'] );
		$instance['image'] = absint( $new_instance['image'] );
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
			<label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _ex( 'Link:', 'widget', 'azzu'.LANG_DN ); ?>
			<input class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="text" value="<?php echo esc_attr( $instance['link'] ); ?>" /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'target' ); ?>"><?php _ex('Target:', 'widget', 'azzu'.LANG_DN); ?></label>
			<select id="<?php echo $this->get_field_id( 'target' ); ?>" name="<?php echo $this->get_field_name( 'target' ); ?>">
				<option value="_self" <?php selected( $instance['target'], '_self' ); ?>>self</option>
                                <option value="_blank" <?php selected( $instance['target'], '_blank' ); ?>>blank</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _ex( 'Max width:', 'widget', 'azzu'.LANG_DN ); ?>
			<input class="" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="number" min="1" max="9999" maxlength="4" value="<?php echo esc_attr( $instance['width'] ); ?>" /> px</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'image' ); ?>"><b><?php _ex( 'Image Upload:', 'widget', 'azzu'.LANG_DN ); ?></b>
			</label>
                        <?php $image_url = wp_get_attachment_image_src( $instance['image'], 'full' );
                            $image_url = is_array($image_url) ? $image_url[0] : '';
                        ?>
                        <div class="widefat">
                            <input class="upload-uri" type="hidden" value="<?php echo esc_url($image_url); ?>" />
                            <input class="upload-id" type="hidden" id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" value="<?php echo esc_attr($instance['image']); ?>" />
                            <a href="#" class="button-secondary azu-images-upload">
                                <?php echo _x('Upload','widget','azzu'.LANG_DN); ?>
                            </a>
                            <a href="#" class="button-secondary azu-images-remove">
                            <?php echo _x('Remove','widget','azzu'.LANG_DN); ?>
                            </a>
                        </div>
                        <?php echo $this->theUploadedImage(azuf()->azu_get_of_uploaded_image($image_url)); ?>
                </p>
	<?php
	}
        
        public function theUploadedImage($src = '')
        {
                $img_hide='';
                if(empty($src))
                    $img_hide = 'azu-widget-hide';

                $img_tag = '<div class="widget-control-content"><div class="thumbnails"><img src="'.esc_url($src).'" class="azu-widget-image azu-upload-img '.esc_attr($img_hide).'" alt="the image"></div></div>';
                return $img_tag;
        }

	public static function azzu_register_widget() {
		register_widget( get_class() );
	}
}