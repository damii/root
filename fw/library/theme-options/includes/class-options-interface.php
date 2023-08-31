<?php
/**
 * @package   Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 */

class Options_Framework_Interface {

    /**
     * Generates the tabs that are used in the options menu
     */
    static function optionsframework_tabs() {
            $counter = 0;
            $options = Options_Framework::_optionsframework_options();
            $options = array_filter( $options, 'optionsframework_options_for_page_filter' );
            $menu = '';
            foreach ( $options as $value ) {
                    // Heading for Navigation
                    if ( $value['type'] == "heading" ) {
                            $counter++;
                            $class = '';
                            $class = ! empty( $value['id'] ) ? $value['id'] : $value['name'];
                            $class = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower($class) ) . '-tab';
                            $menu .= '<a id="options-group-'.  $counter . '-tab" class="nav-tab ' . $class .'" title="' . esc_attr( $value['name'] ) . '" href="' . esc_attr( '#options-group-'.  $counter ) . '">' . esc_html( $value['name'] ) . '</a>';
                    }
            }

            return $menu;
    }

    /**
     * Generates the options fields that are used in the form.
     */
    static function optionsframework_fields() {

	global $allowedtags;
        
        $options_framework = new Options_Framework;
	//$option_name = $options_framework->get_option_name();
        
	$optionsframework_settings = get_option( 'optionsframework' );

	// Gets the unique option id
	if ( isset( $optionsframework_settings['id'] ) ) {
		$option_name = $optionsframework_settings['id'];
	}
	else {
		$option_name = 'optionsframework';
	};

	$settings = get_option($option_name);
	$options =& Options_Framework::_optionsframework_options();
        
	// Clear function static variables
	optionsframework_options_for_page_filter( 0 );
        
	// Filter options for current page
	$options = array_filter( $options, 'optionsframework_options_for_page_filter' );

	$optionsframework_debug = (defined('OPTIONS_FRAMEWORK_DEBUG') && OPTIONS_FRAMEWORK_DEBUG) ? true: false;

	$counter = 0;
	$menu = '';
	$elements_without_wrap = array(
		'block_begin',
		'block_end',
		'heading',
		'info',
		'page',
		'js_hide_begin',
		'js_hide_end',
		'title',
		'divider'
	);

	foreach ( $options as $value ) {

		$val = '';
		$select_value = '';
		$checked = '';
		$output = '';

		if ( !empty($value['before']) ) {
			$output .= $value['before'];
		}

		// Wrap all options
		if ( !in_array( $value['type'], $elements_without_wrap ) ) {

			// Keep all ids lowercase with no spaces
			$value['id'] = preg_replace('/(\W!-)/', '', strtolower($value['id']) );

			$id = 'section-' . $value['id'];

			$class = 'section';
			if ( isset( $value['type'] ) ) {
				$class .= ' section-' . $value['type'];
			}
			if ( isset( $value['class'] ) ) {
				$class .= ' ' . $value['class'];
			}

			$output .= '<div id="' . esc_attr( $id ) .'" class="' . esc_attr( $class ) . '">'."\n";

			$output .= '<div class="option">' . "\n";

			if ( !empty( $value['name'] ) || $optionsframework_debug ) {

				$output .= '<div class="name">' . ( !empty( $value['name'] ) ? esc_html( $value['name'] ): '' ) . "\n";

				$explain_value = '';
				if ( isset( $value['desc'] ) ) {
					$explain_value = $value['desc'];
				}
				$output .= '<div class="explain"><small>' . wp_kses( $explain_value, $allowedtags) . ( $optionsframework_debug ? '<br /><code>' . $value['id'] . '</code>' : '' ) . '</small></div>'."\n";

				$output .= '</div>' . "\n";
			}

			if ( $value['type'] != 'editor' ) {

				if ( empty( $value['name'] ) ) {
					$output .= '<div class="controls controls-fullwidth">' . "\n";
				} else {
					$output .= '<div class="controls">' . "\n";
				}
			}
			else {
				$output .= '<div>' . "\n";
			}
		}

		// Set default value to $val
		if ( isset( $value['std'] ) ) {
			$val = $value['std'];
		}

		// If the option is already saved, override $val
		if ( !in_array( $value['type'], array( 'page', 'info', 'heading' ) ) ) {
			if ( isset( $value['id'], $settings[($value['id'])] ) ) {
				$val = $settings[($value['id'])];
				// Striping slashes of non-array options
				if ( !is_array($val) ) {
					$val = stripslashes( $val );
				}
			}
		}

		switch ( $value['type'] ) {

		// Basic text input
		case 'text':
			$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="text" value="' . esc_attr( $val ) . '" />';
			break;

		// Password input
		case 'password':
			$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="password" value="' . esc_attr( $val ) . '" />';
			break;

		// Textarea
		case 'textarea':
			$rows = '8';

			if ( isset( $value['settings']['rows'] ) ) {
				$custom_rows = $value['settings']['rows'];
				if ( is_numeric( $custom_rows ) ) {
					$rows = $custom_rows;
				}
			}

			$val = stripslashes( $val );
			$output .= '<textarea id="' . esc_attr( $value['id'] ) . '" class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" rows="' . $rows . '">' . esc_textarea( $val ) . '</textarea>';
			break;

		// Select Box
		case 'select':
			$output .= '<select class="of-input" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '">';

			foreach ($value['options'] as $key => $option ) {
				$selected = '';
				if ( $val != '' ) {
					if ( $val == $key) { $selected = ' selected="selected"';}
				}
				$output .= '<option'. $selected .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
			}
			$output .= '</select>';
			break;


		// Radio Box
		case "radio":
			$name = $option_name .'['. $value['id'] .']';
			
			$show_hide = empty($value['show_hide']) ? array() : (array) $value['show_hide'];
			$classes = array( 'of-input', 'of-radio' );

			if ( !empty($show_hide) ) {
				$classes[] = 'of-js-hider';
			}

			foreach ($value['options'] as $key => $option) {
				$id = $option_name . '-' . $value['id'] .'-'. $key;
				$input_classes = $classes;
				$attr = '';

				if ( !empty($show_hide[ $key ]) ) {
					$input_classes[] = 'js-hider-show';

					if ( true !== $show_hide[ $key ] ) {
						$attr = ' data-js-target="' . $show_hide[ $key ] . '"';
					}
				}

				$output .= '<input class="' . esc_attr(implode(' ', $input_classes)) . '"' . $attr . ' type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="'. esc_attr( $key ) . '" '. checked( $val, $key, false) .' /><label for="' . esc_attr( $id ) . '">' . esc_html( $option ) . '</label>';
			}
			break;

		// Image Selectors
		case "images":
			$name = $option_name .'['. $value['id'] .']';
			$show_hide = empty($value['show_hide']) ? array() : (array) $value['show_hide'];
			$classes = array('of-radio-img-radio');

			if ( !empty($show_hide) ) {
				$classes[] = 'of-js-hider';
			}

			if ( empty($value['base_dir']) ) {
				$dir = get_template_directory_uri();
			} else {
				$dir = $value['base_dir'];
			}

			foreach ( $value['options'] as $key => $option ) {
				$input_classes = $classes;
				$selected = '';
				$checked = '';
				$attr = '';
				if ( $val != '' ) {
					if ( $val == $key ) {
						$selected = ' of-radio-img-selected';
						$checked = ' checked="checked"';
					}
				}

				if ( !empty($show_hide[ $key ]) ) {
					$input_classes[] = 'js-hider-show';

					if ( true !== $show_hide[ $key ] ) {
						$attr = ' data-js-target="' . $show_hide[ $key ] . '"';
					}
				}

				$output .= '<div class="of-radio-img-inner-container">';

				$output .= '<input type="radio" id="' . esc_attr( $value['id'] .'_'. $key) . '" class="' . esc_attr(implode(' ', $input_classes)) . '"' . $attr . ' value="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" '. $checked .' />';

				$img_info = '';
				if ( is_array( $option ) && isset( $option['src'], $option['title'] ) ) {

					$img = $dir . $option['src'];
					$title = $option['title'];

					if ( $title ) {
						$img_info = '<div class="of-radio-img-label">' . esc_html($title) . '</div>';
					}
				} else {

					$img = $dir . $option;
					$title = $key;
				}
				
				$output .= '<img src="' . esc_url( $img ) . '" title="'.esc_attr($title).'" alt="' . esc_attr($img) .'" class="of-radio-img-img' . $selected .'" onclick="azuRadioImagesSetCheckbox(\''. esc_attr($value['id'] .'_'. $key) .'\');" />';

				$output .= $img_info;

				$output .= '</div>';
			}
			break;

		// Checkbox
		case "checkbox":
			
			$classes = array();
			$classes[] = 'checkbox';
			$classes[] = 'of-input';
			if( isset($value['options']['java_hide']) && $value['options']['java_hide'] ) {
				$classes[] = 'of-js-hider';
			}else if( isset($value['options']['java_hide_global']) && $value['options']['java_hide_global'] ) {
				$classes[] = 'of-js-hider-global';
			}
			$classes = implode(' ', $classes);
			
			$output .= '<input id="' . esc_attr( $value['id'] ) . '" class="' . $classes . ' azu-switch-toggle azu-switch-toggle-round" type="checkbox" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" '. checked( $val, 1, false) .' />';
                        $output .= '<label for="'.esc_attr( $value['id'] ).'"></label>';
			break;

		// Multicheck
		case "multicheck":
			foreach ($value['options'] as $key => $option) {
				$checked = '';
				$label = $option;
				$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($key));

				$id = $option_name . '-' . $value['id'] . '-'. $option;
				$name = $option_name . '[' . $value['id'] . '][' . $option .']';

				if ( isset($val[$option]) ) {
					$checked = checked($val[$option], 1, false);
				}

				$output .= '<input id="' . esc_attr( $id ) . '" class="checkbox of-input" type="checkbox" name="' . esc_attr( $name ) . '" ' . $checked . ' /><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
			}
			break;

		// Color picker
		case "color":
			$default_color = '';
			if ( isset($value['std']) ) {
				if ( $val !=  $value['std'] )
					$default_color = ' data-default-color="' .$value['std'] . '" ';
			}
			$output .= '<input name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . esc_attr( $value['id'] ) . '" class="of-color"  type="text" value="' . esc_attr( $val ) . '"' . $default_color .' />';
	
			break;

		// Uploader
		case "upload":
			$output .= Options_Framework_Media_Uploader::optionsframework_uploader( $value['id'], $val, null );
			
			break;

		// Typography
		case 'typography':
		
			unset( $font_size, $font_style, $font_face, $font_color );
		
			$typography_defaults = array(
				'size' => '',
				'face' => '',
				'style' => '',
				'color' => ''
			);
			
			$typography_stored = wp_parse_args( $val, $typography_defaults );
			
			$typography_options = array(
				'sizes' => of_recognized_font_sizes(),
				'faces' => of_recognized_font_faces(),
				'styles' => of_recognized_font_styles(),
				'color' => true
			);
			
			if ( isset( $value['options'] ) ) {
				$typography_options = wp_parse_args( $value['options'], $typography_options );
			}

			// Font Size
			if ( $typography_options['sizes'] ) {
				$font_size = '<select class="of-typography of-typography-size" name="' . esc_attr( $option_name . '[' . $value['id'] . '][size]' ) . '" id="' . esc_attr( $value['id'] . '_size' ) . '">';
				$sizes = $typography_options['sizes'];
				foreach ( $sizes as $i ) {
					$size = $i . 'px';
					$font_size .= '<option value="' . esc_attr( $size ) . '" ' . selected( $typography_stored['size'], $size, false ) . '>' . esc_html( $size ) . '</option>';
				}
				$font_size .= '</select>';
			}

			// Font Face
			if ( $typography_options['faces'] ) {
				$font_face = '<select class="of-typography of-typography-face" name="' . esc_attr( $option_name . '[' . $value['id'] . '][face]' ) . '" id="' . esc_attr( $value['id'] . '_face' ) . '">';
				$faces = $typography_options['faces'];
				foreach ( $faces as $key => $face ) {
					$font_face .= '<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['face'], $key, false ) . '>' . esc_html( $face ) . '</option>';
				}
				$font_face .= '</select>';
			}

			// Font Styles
			if ( $typography_options['styles'] ) {
				$font_style = '<select class="of-typography of-typography-style" name="'.$option_name.'['.$value['id'].'][style]" id="'. $value['id'].'_style">';
				$styles = $typography_options['styles'];
				foreach ( $styles as $key => $style ) {
					$font_style .= '<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['style'], $key, false ) . '>'. $style .'</option>';
				}
				$font_style .= '</select>';
			}

			// Font Color
			if ( $typography_options['color'] ) {
				$default_color = '';
				if ( isset($value['std']['color']) ) {
					if ( $val !=  $value['std']['color'] )
						$default_color = ' data-default-color="' .$value['std']['color'] . '" ';
				}
				$font_color = '<input name="' . esc_attr( $option_name . '[' . $value['id'] . '][color]' ) . '" id="' . esc_attr( $value['id'] . '_color' ) . '" class="of-color of-typography-color  type="text" value="' . esc_attr( $typography_stored['color'] ) . '"' . $default_color .' />';
			}
	
			// Allow modification/injection of typography fields
			$typography_fields = compact( 'font_size', 'font_face', 'font_style', 'font_color' );
			$typography_fields = apply_filters( 'of_typography_fields', $typography_fields, $typography_stored, $option_name, $value );
			$output .= implode( '', $typography_fields );
			
			break;

		// Background
		case 'background':

			$background = $val;

			// Background Color
			$default_color = '';
			if ( isset( $value['std']['color'] ) ) {
				if ( $val !=  $value['std']['color'] )
					$default_color = ' data-default-color="' .$value['std']['color'] . '" ';
			}
			$output .= '<input name="' . esc_attr( $option_name . '[' . $value['id'] . '][color]' ) . '" id="' . esc_attr( $value['id'] . '_color' ) . '" class="of-color of-background-color"  type="text" value="' . esc_attr( $background['color'] ) . '"' . $default_color .' />';

			// Background Image
			if ( !isset($background['image']) ) {
				$background['image'] = '';
			}
			
			$output .= Options_Framework_Media_Uploader::optionsframework_uploader( $value['id'], $background['image'], null, esc_attr( $option_name . '[' . $value['id'] . '][image]' ) );
			
			$class = 'of-background-properties';
			if ( '' == $background['image'] ) {
				$class .= ' hide';
			}
			$output .= '<div class="' . esc_attr( $class ) . '">';

			// Background Repeat
			$output .= '<select class="of-background of-background-repeat" name="' . esc_attr( $option_name . '[' . $value['id'] . '][repeat]'  ) . '" id="' . esc_attr( $value['id'] . '_repeat' ) . '">';
			$repeats = of_recognized_background_repeat();

			foreach ($repeats as $key => $repeat) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['repeat'], $key, false ) . '>'. esc_html( $repeat ) . '</option>';
			}
			$output .= '</select>';

			// Background Position
			$output .= '<select class="of-background of-background-position" name="' . esc_attr( $option_name . '[' . $value['id'] . '][position]' ) . '" id="' . esc_attr( $value['id'] . '_position' ) . '">';
			$positions = of_recognized_background_position();

			foreach ($positions as $key=>$position) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['position'], $key, false ) . '>'. esc_html( $position ) . '</option>';
			}
			$output .= '</select>';

			// Background Attachment
			$output .= '<select class="of-background of-background-attachment" name="' . esc_attr( $option_name . '[' . $value['id'] . '][attachment]' ) . '" id="' . esc_attr( $value['id'] . '_attachment' ) . '">';
			$attachments = of_recognized_background_attachment();

			foreach ($attachments as $key => $attachment) {
				$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['attachment'], $key, false ) . '>' . esc_html( $attachment ) . '</option>';
			}
			$output .= '</select>';
			$output .= '</div>';

			break;
			
		// Editor
		case 'editor':
			$output .= '<div class="explain">' . wp_kses( $explain_value, $allowedtags) . '</div>'."\n";
			echo $output;
			$textarea_name = esc_attr( $option_name . '[' . $value['id'] . ']' );
			$default_editor_settings = array(
				'textarea_name' => $textarea_name,
				'media_buttons' => false,
				'tinymce' => array( 'plugins' => 'wordpress' )
			);
			$editor_settings = array();
			if ( isset( $value['settings'] ) ) {
				$editor_settings = $value['settings'];
			}
			$editor_settings = array_merge($editor_settings, $default_editor_settings);
			wp_editor( $val, $value['id'], $editor_settings );
			$output = '';
			break;

		// Info
		case "info":
			$id = '';
			$class = 'section';
			if ( isset( $value['id'] ) ) {
				$id = 'id="' . esc_attr( $value['id'] ) . '" ';
			}
			if ( isset( $value['type'] ) ) {
				$class .= ' section-' . $value['type'];
			}
			if ( isset( $value['class'] ) ) {
				$class .= ' ' . $value['class'];
			}

			$output .= '<div ' . $id . 'class="' . esc_attr( $class ) . '">' . "\n";

			if ( isset($value['name']) ) {
				$output .= '<h4 class="heading">' . esc_html( $value['name'] ) . '</h4>' . "\n";
			}

			if ( $value['desc'] ) {
				$output .= apply_filters('of_sanitize_info', $value['desc'] ) . "\n";
			}

			if ( !empty($value['image']) ) {
				$output .= '<div class="info-image-holder"><img src="' . esc_url($value['image']) . '" /></div>';
			}

			$output .= '</div>' . "\n";
			break;

		// Heading for Navigation
		case "heading":
			$counter++;
			if ( $counter >= 2 ) {
				$output .= '</div>'."\n";
			}
			$class = '';
			$class = ! empty( $value['id'] ) ? $value['id'] : $value['name'];
			$class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class) );
			$output .= '<div id="options-group-' . $counter . '" class="group ' . $class . '">';
			break;

		/* Custom fields */

		// Background
		case 'background_img':

			$background = $val;

			// Background Image
			if ( !isset($background['image']) ) {
				$background['image'] = '';
			}

			$output .= Options_Framework_Media_Uploader::optionsframework_uploader( $value['id'], $background['image'],null , esc_attr( $option_name . '[' . $value['id'] . '][image]' ) );
			
			$class = 'of-background-properties';
			
			if ( '' == $background['image'] ) {
				$class .= ' hide';
			}
			
			$output .= '<div class="' . esc_attr( $class ) . '">';

			if ( !isset($value['fields']) || in_array('repeat', (array) $value['fields']) ) {

				// Background Repeat
				$output .= '<select class="of-background of-background-repeat" name="' . esc_attr( $option_name . '[' . $value['id'] . '][repeat]'  ) . '" id="' . esc_attr( $value['id'] . '_repeat' ) . '">';
				$repeats = of_recognized_background_repeat();

				foreach ($repeats as $key => $repeat) {
					$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['repeat'], $key, false ) . '>'. esc_html( $repeat ) . '</option>';
				}
				$output .= '</select>';

			}

			if ( !isset($value['fields']) || in_array('position_x', (array) $value['fields']) ) {

				// Background Position x
				$output .= '<select class="of-background of-background-position" name="' . esc_attr( $option_name . '[' . $value['id'] . '][position_x]' ) . '" id="' . esc_attr( $value['id'] . '_position_x' ) . '">';
				$positions = of_recognized_background_horizontal_position();

				foreach ($positions as $key=>$position) {
					$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['position_x'], $key, false ) . '>'. esc_html( $position ) . '</option>';
				}
				$output .= '</select>';

			}

			if ( !isset($value['fields']) || in_array('position_y', (array) $value['fields']) ) {

				// Background Position y
				$output .= '<select class="of-background of-background-position" name="' . esc_attr( $option_name . '[' . $value['id'] . '][position_y]' ) . '" id="' . esc_attr( $value['id'] . '_position_y' ) . '">';
				$positions = of_recognized_background_vertical_position();

				foreach ($positions as $key=>$position) {
					$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $background['position_y'], $key, false ) . '>'. esc_html( $position ) . '</option>';
				}
				$output .= '</select>';

			}

			// Background Attachment

			$output .= '</div>';

			break;

		// Block begin
		case "block_begin":
			$class = 'section';
			$id = '';
			if ( isset( $value['type'] ) ) {
				$class .= ' section-' . $value['type'];
			}
			if ( isset( $value['class'] ) ) {
				$class .= ' ' . $value['class'];
			}
			if( isset( $value['id'] ) ){
				$id .= ' id="' . esc_attr($value['id']) . '"'; 
			}
			$output .= '<div' .$id. ' class="postbox ' . esc_attr( $class ) . '">'."\n";
			if( isset($value['name']) && !empty($value['name']) ){
				$output .= '<h3>' . esc_html( $value['name'] ) . '</h3>' . "\n";
			}
		break;

		// Block End
		case "block_end":
			$output .= '</div>'."\n".'<!-- block_end -->'; 
		break;

		// Page
		case "page": break;

		// Custom fonts upload
		case "custom_fonts":
                        wp_enqueue_media();
                        wp_enqueue_script( 'azu-image-upload', AZZU_OPTIONS_URI . '/assets/js/image-upload.js', array( 'jquery' ) );
                        $output .='<div class="manual-fonts">
                            <input type="hidden" id="' . esc_attr( $value['id'] ) . '" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" value="'.esc_attr(wp_unslash(json_encode($val))).'" />
                            <input class="upload-uri upload" type="text" placeholder="'.__('No file chosen', 'azzu'.LANG_DN).'" value="" readonly="readonly"/>
                            <a href="#" class="button-secondary azu-font-upload">
                                '._x('Upload','theme_option','azzu'.LANG_DN).'
                            </a>';
                        $output .='<p>'._x('Accepted Font Format : ttf, otf, eot, svg, woff, woff2','theme_option','azzu'.LANG_DN).' <input type="button" class="button-primary azu_add_font" value="'.__('Add Font', 'azzu'.LANG_DN).'"><br></p>';
                        $row_data = '';
                        foreach ( $val as $key => $array_data ){
                            $row_data .= '<tr id="azu-mf-id-'.$key.'" data-id="'.$key.'">';
                                $row_data .= '<td>'.$key.'</td>';
                                $row_data .= '<td>'.$array_data.'</td>';
                                $row_data .= '<td><a class="azu-mf-delete" href="javascript:void(0)">delete</a></td>';
                            $row_data .= '</tr>';
                        }
                        if(empty($row_data))
                            $row_data = '<tr class="azu-mf-no-item"><td colspan="3">'.__('No font found. Please click on Add Font button to add fonts', 'azzu'.LANG_DN).'</td></tr>';
                        $output .='<p></p><table cellspacing="0" class="azu-font-table wp-list-table widefat fixed bookmarks"><thead><tr><th width="20">'.__('Id', 'azzu'.LANG_DN).'</th><th>'.__('Font', 'azzu'.LANG_DN).'</th><th width="60">'.__('Delete', 'azzu'.LANG_DN).'</th></tr></thead><tbody>'.$row_data.'</tbody></table>';

                        $output .='</div>';
			break;
		// Listbox
		case 'listbox':
                        $output .= '<input class="of-listbox" id="' . esc_attr( $value['id'] ) . '" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" type="hidden" value="'.esc_attr(json_encode($val)).'" />';
			$output .= '<ul id="fc_'.esc_attr( $value['id'] ).'" class="azu-drag-and-drop" >';
                        foreach ( $val as $_value => $array_data ){
                                $azu_desc = '';
                                $azu_std ='';
                                $azu_label = $_value;
                                $opacity_control='';
                                $azu_listbox_toggle = "";
                                $op_id = $_value;
                                // show opacity slider
                                if(isset($value['mode'])){ 
                                        $azu_listbox_toggle = "azu-listbox-toggle"; 
                                        $child_val = array('Size' => 'normal','Weight' => '400', 'ls' => 0, 'uc' => 'none');
                                        if($value['mode']=='color')
                                            $child_val = array( 'option' => 100 );
                                            
                                        $range_id = $_value;
                                        if(is_array($array_data) && count($array_data)>0)
                                                $child_val = array_merge($child_val, $array_data);
                                        
                                        $range_id = esc_attr(preg_replace("/\W/", "", strtolower($range_id) )). '_range';
                                        if($value['mode']=='color') 
                                            $opacity_control='<div class="azu-listbox-body azu-customize-hide">'._x('Opacity','theme_option','azzu'.LANG_DN).' &nbsp;&nbsp;<input style="max-width: 200px;" type="range" data-mode="'.esc_attr($value['mode']).'" id="'.$range_id.'" class="azu-listbox-child" min="0" max="100" step="1" value="'.esc_attr($child_val['option']).'" oninput="'.$range_id.'_output.value=this.value;" /><output class="" name="'.$range_id.'_output" for="'.$range_id.'">'. $child_val['option'] .'</output></div>';
                                        else if($value['mode']=='font') {
                                            $opacity_control='<div class="azu-listbox-body azu-customize-hide">';
                                            $font_array = array( 
                                                'Weight' => azuf()->azzu_get_font_weight_list()
                                            );
                                            if(strlen ($azu_label) > 2 && !in_array(strtolower(substr($azu_label,0,2)),array('h1','h2','h3','h4','h5','h6')))
                                            {
                                                $font_array['Size'] = azuf()->azzu_themeoptions_get_font_size_defaults(false);
                                            }
                                            foreach ( $font_array as $n => $font_array_val ){
                                                    $range_id = esc_attr(preg_replace("/\W/", "", strtolower($_value) )). '_'.$n;
                                                    $opacity_control .= $n.': &nbsp;&nbsp;<select class="azu-listbox-child" style="width: 180px;" id="'.$range_id.'" data-azu-select="'.$n.'" data-mode="'.esc_attr($value['mode']).'" value="'.esc_attr($child_val[$n]).'" >';
                                                    foreach ( $font_array_val as $m => $_font )
                                                        $opacity_control .='<option value="'.$m.'" '.selected($child_val[$n], $m,false)  .'>'.$_font.'</option>';
                                                    $opacity_control .='</select>';
                                            }
                                            $ls_id = esc_attr(preg_replace("/\W/", "", strtolower($range_id) )). '_ls';
                                            $uc_id = esc_attr(preg_replace("/\W/", "", strtolower($range_id) )). '_uc';
                                            $opacity_control .= '<br />'._x('Letter-spacing','theme_option','azzu'.LANG_DN).' &nbsp;&nbsp;<input class="azu-listbox-child of-slider-value" type="range" data-mode="'.esc_attr($value['mode']).'" name="'.$ls_id.'" oninput="'.$ls_id.'_output.value=this.value/10" max="20" min="-20" step="1" value="'.esc_attr($child_val['ls']*10).'" style="width: 100px;"><output class="" name="'.$ls_id.'_output" for="'.$ls_id.'">'.esc_attr($child_val['ls']).'</output>px';
                                            $opacity_control .= '<br />'._x( 'Uppercase', 'theme-option', 'azzu'.LANG_DN ).'<input type="checkbox" class="azu-listbox-child azu-switch-toggle azu-switch-toggle-round" name="' . $uc_id . '" id="' . $uc_id . '" data-mode="'.esc_attr($value['mode']).'" value="'.esc_attr($child_val['uc']).'" '.checked( $child_val['uc'], 'uppercase', false).' /><label for="'.$uc_id.'" style="margin-left: 30px; display: inline-block;"></label>';
                                            $opacity_control .='</div>';
                                        }
                                        else
                                            $azu_listbox_toggle = "";
                                }
                                        
                                if(isset($value['options']) && is_array($value['options'])){
                                    $azu_fields = $value['options'];
                                    if (!array_key_exists($op_id,$azu_fields)){
                                        continue;
                                    }
                                    $azu_label = $azu_fields[$op_id]['label'];
                                    $azu_desc = $azu_fields[$op_id]['desc'];
                                    $azu_std = $azu_fields[$op_id]['std'];
                                }
                            $output .= '<li class="azu-listbox-item" title="'. esc_attr($azu_desc).'" data-azu-listbox="'.esc_attr($_value).'" data-azu-listbox-option="'.esc_attr(json_encode($child_val)).'" data-std="'.esc_attr($azu_std).'"><div class="azu-listbox-title"><div class="azu-color-background"><div class="azu-color-window"></div></div>'.esc_attr($azu_label).'<div class="'.esc_attr($azu_listbox_toggle).'"></div></div>'.$opacity_control.'</li>';
                        }
                        $output .= '</ul>';
			break;
		// Slider
		case 'slider':

			$classes = array( 'of-slider' );

			if ( !empty( $value['options']['java_hide_if_not_max'] ) ) {
				$classes[] = 'of-js-hider';
				$classes[] = 'js-hide-if-not-max';
			} else if( !empty( $value['options']['java_hide_global_not_max'] ) ) {
				$classes[] = 'of-js-hider-global';
				$classes[] = 'js-hide-if-not-max';
			}
			$classes = implode( ' ', $classes );

			$output .= '<div class="' . $classes . '">';

			$slider_opts = array(
				'max'   => isset( $value['options']['max'] ) ? $value['options']['max'] : 100,
				'min'   => isset( $value['options']['min'] ) ? $value['options']['min'] : 0,
				'step'  => isset( $value['options']['step'] ) ? $value['options']['step'] : 1,
				'value' => isset( $val ) ? $val : 100
			);
			$str = '';
			foreach( $slider_opts as $name=>$val ) {
				$str .= ' ' . $name . '="' . esc_attr($val) . '"';
			}
			$range_id = esc_attr(preg_replace("/\W/", "", strtolower($value['id']) ));
			$output .=	'<input class="of-slider-value" id="' . esc_attr($value['id']) . '" type="range" name="'.esc_attr($option_name . '[' . $value['id'] . ']').'" oninput="'.$range_id.'_output.value=this.value" '. $str .' style="width: 210px;" />';
                        $output .= isset($value['wrap']) && is_array($value['wrap']) ? $value['wrap'][0] : '';
                        $output .= '<output class="" name="'.$range_id.'_output" for="'.$range_id.'">'.esc_attr( $val ).'</output>';
			$output .= isset($value['wrap']) && is_array($value['wrap']) ? $value['wrap'][1] : '';
                        $output .= '</div>';
			break;

		// Hidden area begin
		case 'js_hide_begin':
			$class = 'of-js-hide hide-if-js';
			if ( !empty( $value['class'] ) ) {
				$class .= ' ' . $value['class'];
			}
			$output .= '<div class="' . esc_attr($class) . '">';
			break;

		// Hidden area end
		case 'js_hide_end':
			$output .= '</div>';
			break;

		// Social buttons
		case 'social_buttons':
			$social_buttons = (array)apply_filters('optionsframework_interface-social_buttons', array());

			if ( empty($social_buttons) ) {
				$output .= '<p>Use "optionsframework_interface-social_buttons" filter to add some buttons. It needs array( id1 => name1, id2 => name2 ).</p>';
				break;
			}

			$saved_buttons = isset($val) ? (array) $val : array();
                        if(empty($saved_buttons)) {
                            foreach ( $social_buttons as $v => $social_value )
                                $saved_buttons[$v] = 0;
                        }
			$output .= '<ul class="connectedSortable">';
                        foreach ( $saved_buttons as $v=> $social_value ) {
                                if ( !isset($social_buttons[$v]) ) 
                                    continue;
                                $field = $social_buttons[$v];
                                $id = $option_name . '-' . $value['id'] . '-'. $field;
                                    
                                $checked = checked($social_value, 1, false);
				$output .= '<li class="ui-state-default"><input type="hidden" value="'.$social_value.'" name="' . esc_attr($option_name . '[' . $value['id'] . ']['.$v.']') . '"/><input type="checkbox" value="'.$social_value.'" id="' . esc_attr( $id ) . '" ' . $checked . '  /><label for="' . esc_attr( $id ) . '">' . esc_html( $field ) . '</label></li>';
			}

			$output .= '</ul>';
			
			break;

		// Web fonts
		case 'web_fonts':
			$id = esc_attr( $value['id'] );
			
			$output .= '<select class="of-input azu-web-fonts" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" id="' . $id . '">';

			foreach ( $value['options'] as $key => $option ) {
				$selected = '';
				if ( $val != '' && $val == $key ) {
					$selected = ' selected="selected"'; 
				}
				$output .= '<option'. $selected .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
			}

			$output .= '</select>';

			$output .= '<div class="azu-web-fonts-preview"><span>It\'s better to be a pirate than to join the Navy.</span></div>';

			break;

		case 'square_size':
			$id = esc_attr( $value['id'] );

			$output .= '<input type="text" class="of-input azu-square-size" name="' . esc_attr($option_name . '[' . $value['id'] . '][width]') . '" value="' . absint($val['width']) . '" />';
			$output .= '<span>&times;</span>';
			$output .= '<input type="text" class="of-input azu-square-size" name="' . esc_attr($option_name . '[' . $value['id'] . '][height]') . '" value="' . absint($val['height']) . '" />';

			break;

		// import/export theme options
		case 'import_export_options':
			$rows = '8';

			if ( isset( $value['settings']['rows'] ) ) {
				$custom_rows = $value['settings']['rows'];
				if ( is_numeric( $custom_rows ) ) {
					$rows = $custom_rows;
				}
			}

			$valid_settings = $settings;
			$fields_black_list = apply_filters( 'optionsframework_fields_black_list', array() );

			// do not export preserved settings
			foreach ( $fields_black_list as $black_setting ) {
				if ( array_key_exists($black_setting, $valid_settings) ) {
					unset( $valid_settings[ $black_setting ] );
				}
			}

			$val = azu_b64_encode( serialize( $valid_settings ) );

			$output .= '<textarea id="' . esc_attr( $value['id'] ) . '" class="of-input of-import-export" name="' . esc_attr( $option_name . '[' . $value['id'] . ']' ) . '" rows="' . $rows . '" onclick="this.focus();this.select()">' . esc_textarea( $val ) . '</textarea>';
			break;

		case 'title':
			$output .= '<div class="of-title"><h4>' . esc_html($value['name']) . '</h4></div>';
			break;

		case 'divider':
			$output .= '<div class="divider"></div>';
			break;


                // Select Box
                case 'pages_list':
                        $html = wp_dropdown_pages( array(
                                'name' => esc_attr( $option_name . '[' . $value['id'] . ']' ),
                                'id' => esc_attr( $value['id'] ),
                                'echo' => 0,
                                'show_option_none' => __( '&mdash; Select &mdash;', 'azzu'.LANG_DN ),
                                'option_none_value' => '0',
                                'selected' => $val
                        ) );

                        $html = str_replace( '<select', '<select class="of-input"', $html );

                        $output .= $html;
                        break;

		}

		if ( !in_array( $value['type'], $elements_without_wrap ) ) {

			if ( $value['type'] != "checkbox" ) {
				$output .= '<br/>';
			}

			$output .= '</div>';
			$output .= '<div class="clear"></div></div></div>'."\n";
		}

		if ( !empty($value['after']) ) {
			$output .= $value['after'];
		}

		do_action( 'options-interface-before-output', $output, $value, $val );

		echo apply_filters( 'options-interface-output', $output, $value, $val );
	}
	echo '</div>';
    }

}