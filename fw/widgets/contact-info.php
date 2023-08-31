<?php
/**
 * Contact info widget.
 *
 * @package azzu.
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Load the widget */
add_action( 'widgets_init', array( 'Azzu_Widgets_ContactInfo', 'azzu_register_widget' ) );

class Azzu_Widgets_ContactInfo extends Azzu_Widgets_SocialIcons {

	/* Widget defaults */
	public static $widget_defaults = array( 
                'title'     => '',
		'text'     => '',
		'address'      => '',
                'phone'      => '',
                'email'      => '',
                'skype'      => '',
                'clock'      => '',
                'info'      => '',
                'image'      => '',
	);

	/* Widget setup  */
	function __construct() {  
		/* Widget settings. */
		$widget_ops = array( 'description' => _x( 'Contact info', 'widget', 'azzu'.LANG_DN ) );

		/* Create the widget. */
		parent::__construct(
			'azzu-contact-info-widget',
			AZU_WIDGET_PREFIX . _x( 'Contact info', 'widget', 'azzu'.LANG_DN ),
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

		/* Our variables from the widget settings. */
		$title = apply_filters( 'widget_title', $instance['title'] );

                $fields = array(
                        'text',
			'address',
			'phone',
			'email',
			'skype',
			'clock',
			'info'
		);
                
		echo $before_widget ;

		// title
		if ( $title ) echo $before_title . $title . $after_title;
                
                $image_bg = wp_get_attachment_image_src( $instance['image'], 'full' );
                $image_bg = is_array($image_bg) ? 'background-image: url('.$image_bg[0].');' : '';
                echo '<div class="'.azus()->get('azu-widget-contact-wrap').'" style="'.$image_bg.'">';
		// fields
		if ( !empty($fields) ) {

			echo '<div class="'.azus()->get('azu-widget-contact').'"><ul>';

			foreach ( $fields as $field ) {
                                if ( !empty($instance[$field]) ) 
                                    echo '<li class="'.$field.'">'.$instance[$field].'</li>';
			}

			echo '</ul></div>';

		}

                $args['before_widget'] = $args['after_widget'] = '';
                $instance['title'] = null;
                $instance['text'] = '';
                parent::widget( $args, $instance );
                echo '</div>';
		echo $after_widget;
	}

	/* Update the widget settings  */
	function update( $new_instance, $old_instance ) {
		$instance = parent::update( $new_instance, $old_instance );
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) );
                $instance['address'] = strip_tags($new_instance['address']);
                $instance['phone'] = strip_tags($new_instance['phone']);
                $instance['email'] = strip_tags($new_instance['email']);
                $instance['skype'] = strip_tags($new_instance['skype']);
                $instance['clock'] = strip_tags($new_instance['clock']);
                $instance['info'] = strip_tags($new_instance['info']);
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
                // contact fields
                $contact_fields = array(
                        array(
                                'prefix'    => 'text',
                                'desc'      => _x('Text', 'widget', 'azzu'.LANG_DN) 
                        ),
                        array(
                                'prefix'    => 'address',
                                'desc'      => _x('Address', 'widget', 'azzu'.LANG_DN) 
                        ),
                        array(
                                'prefix'    => 'phone',
                                'desc'      => _x('Phone', 'widget', 'azzu'.LANG_DN) 
                        ),
                        array(
                                'prefix'    => 'email',
                                'desc'      => _x('Email', 'widget', 'azzu'.LANG_DN) 
                        ),
                        array(
                                'prefix'    => 'skype',
                                'desc'      => _x('Skype', 'widget', 'azzu'.LANG_DN) 
                        ),
                        array(
                                'prefix'    => 'clock',
                                'desc'      => _x('Working hours', 'widget', 'azzu'.LANG_DN) 
                        ),
                        array(
                                'prefix'    => 'info',
                                'desc'      => _x('Additional info', 'widget', 'azzu'.LANG_DN) 
                        )
                );
                
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _ex('Title:', 'widget',  'azzu'.LANG_DN); ?></label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>
                <?php 
                // contact fields
                foreach( $contact_fields as $field ) {
                ?>
                    <p>
                            <label for="<?php echo $this->get_field_id( $field['prefix'] ); ?>"><?php echo $field['desc']; ?></label>
                            <input type="text" id="<?php echo $this->get_field_id( $field['prefix'] ); ?>" class="widefat" name="<?php echo $this->get_field_name( $field['prefix'] ); ?>" value="<?php echo esc_attr($instance[$field['prefix']]); ?>" />
                    </p>
                <?php 
                }
                ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'image' ); ?>"><b><?php _ex( 'Background:', 'widget', 'azzu'.LANG_DN ); ?></b>
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
		<div style="clear: both;"></div>
	<?php
                $instance['title'] = null;
                parent::form( $instance );
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
