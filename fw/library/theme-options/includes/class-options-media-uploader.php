<?php
/**
 * @package   Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 */

class Options_Framework_Media_Uploader {

	/**
	 * Initialize the media uploader class
	 *
	 * @since 1.7.0
	 */
	public function init() {
		//add_action( 'admin_enqueue_scripts', array( $this, 'optionsframework_media_scripts' ) );
	}

	/**
	 * Media Uploader Using the WordPress Media Library.
	 *
	 * Parameters:
	 *
	 * string $_id - A token to identify this field (the name).
	 * string $_value - The value of the field, if present.
	 * string $_desc - An optional description of the field.
	 *
	 */
        static function optionsframework_uploader( $_id, $_value, $_desc = '', $_name = '' ) {

                $optionsframework_settings = get_option( 'optionsframework' );

                // Gets the unique option id
                $option_name = $optionsframework_settings['id'];

                $output = '';
                $id = '';
                $class = '';
                $int = '';
                $value = '';
                $name = '';
                $att_id = 0;
                $_mode = false;
                $id = strip_tags( strtolower( $_id ) );

                // If a value is passed and we don't have a stored value, use the value that's passed through.
                if ( !empty( $_value ) ) {
                        $value = $_value;

                        // In case it's array
                        if ( is_array($value) ) {
                            $_mode = true;
                            if(array_key_exists(0,$value)){
                                $att_id = !empty( $value[1] ) ? absint($value[1]) : 0;
                                $value = !empty( $value[0] ) ? $value[0] : '';
                            }
                            else if(array_key_exists('id',$value)){
                                $att_id = !empty( $value['id'] ) ? absint($value['id']) : 0;
                                $value = !empty( $value['uri'] ) ? $value['uri'] : '';
                            }
                        }
                }

                if ($_name != '') { $name = $_name;
                } else { $name = $option_name.'['.$id.']'; }

                if ( $value ) { $class = ' has-file'; }

                $uploader_name = $name;

                if ( $_mode ) {
                        $uploader_name .= '[uri]';
                        $output .= '<input type="hidden" class="upload-id" name="'.$name.'[id]" value="' . $att_id . '" />' . "\n";
                }

                $output .= '<input id="' . $id . '" class="upload' . $class . '" type="text" name="'.$uploader_name.'" value="' . $value . '" placeholder="' . __('No file chosen', 'azzu'.LANG_DN) .'" readonly="readonly"/>' . "\n";

                if ( function_exists( 'wp_enqueue_media' ) ) {
                        if ( ( $value == '' ) ) {
                                $output .= '<input id="upload-' . $id . '" class="upload-button uploader-button button" type="button" value="' . __( 'Upload', 'azzu'.LANG_DN ) . '" />' . "\n";
                        } else {
                                $output .= '<input id="remove-' . $id . '" class="remove-file uploader-button button" type="button" value="' . __( 'Remove', 'azzu'.LANG_DN ) . '" />' . "\n";
                        }
                } else {
                        $output .= '<p><i>' . __( 'Upgrade your version of WordPress for full media support.', 'azzu'.LANG_DN ) . '</i></p>';
                }

                if ( $_desc != '' ) {
                        $output .= '<span class="of-metabox-desc">' . $_desc . '</span>' . "\n";
                }

                $output .= '<div class="screenshot" id="' . $id . '-image">' . "\n";

                if ( $value != '' ) { 
                        $remove = '<a class="remove-image">Remove</a>';

                        $image = preg_match( '/(^.*\.jpg|jpeg|png|gif|ico*)/i', $value );
                        if ( $image ) {
                                $output .= '<img src="' . azuf()->azu_get_of_uploaded_image($value) . '" alt="" />' . $remove;
                        } else {
                                $parts = explode( "/", $value );
                                for( $i = 0; $i < sizeof( $parts ); ++$i ) {
                                        $title = $parts[$i];
                                }

                                // No output preview if it's not an image.			
                                $output .= '';

                                // Standard generic output if it's not an image.	
                                $title = __( 'View File', 'azzu'.LANG_DN );
                                $output .= '<div class="no-image"><span class="file_link"><a href="' . $value . '" target="_blank" rel="external">'.$title.'</a></span></div>';
                        }	
                }
                $output .= '</div>' . "\n";
                return $output;
        }



    /**
     * Enqueue scripts for file uploader
     */
    static function optionsframework_media_scripts( $hook ) {
//        $menu = Options_Framework_Admin::menu_settings();
//
//        if ( substr( $hook, -strlen( $menu[0]['menu_slug'] ) ) !== $menu[0]['menu_slug'] )
//	        return;
	if ( function_exists( 'wp_enqueue_media' ) )
		wp_enqueue_media();
	wp_register_script( 'of-media-uploader', OPTIONS_FRAMEWORK_URL .'js/media-uploader.js', array( 'jquery' ), Options_Framework::VERSION, true );
	wp_enqueue_script( 'of-media-uploader' );
	wp_localize_script( 'of-media-uploader', 'optionsframework_l10n', array(
		'upload' => __( 'Upload', 'azzu'.LANG_DN ),
		'remove' => __( 'Remove', 'azzu'.LANG_DN )
	) );
    }

}


