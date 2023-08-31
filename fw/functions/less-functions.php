<?php
/**
 * Description here.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Set azzu_less_css_is_writable option to 0.
 *
 */
function azzu_stylesheet_is_not_writable() {

	if ( get_option( 'azzu_less_css_is_writable' ) ) {

		update_option( 'azzu_less_css_is_writable', 0 );
	}
}
add_action( 'wp-less_save_stylesheet_error', 'azzu_stylesheet_is_not_writable' );

/**
 * Set azzu_less_css_is_writable option to 1.
 *
 */
function azzu_stylesheet_is_writable() {

	update_option( 'azzu_less_css_is_writable', 1 );
}
add_action( 'wp-less_stylesheet_save_post', 'azzu_stylesheet_is_writable' );

/**
 * Compile less vars from theme options.
 *
 */
function azzu_compile_less_vars() {
	if ( !class_exists('WPLessPlugin') ) {
		return array();
	}

	// $less = WPLessPlugin::getInstance();

	$image_defaults = array(
		'image'			=> '',
		'repeat'		=> 'repeat',
		'position_x'	=> 'center',
		'position_y'	=> 'center'
	);

	$font_family_falloff = ', Helvetica, Arial, Verdana, sans-serif';
	$font_family_defaults = array('family' => AZZU_THEME_DEFAULT_FONT);


	do_action( 'azzu_before_compile_less_vars' );

	// main array
	$options = array();

	$options_inteface = apply_filters( 'azzu_less_options_interface', array() );

	//----------------------------------------------------------------------------------------------------------------
	// Process options
	//----------------------------------------------------------------------------------------------------------------

	if ( $options_inteface ) {

		foreach( $options_inteface as $data ) {

			if ( empty($data) || empty($data['type']) || empty($data['less_vars']) || empty($data['php_vars']) ) continue;

			$type = $data['type'];
			$less_vars = $data['less_vars'];
			$php_vars = $data['php_vars'];
			$wrap = isset($data['wrap']) ? $data['wrap'] : false;
			$interface = isset($data['interface']) ? $data['interface'] : false;

			extract($php_vars);

			switch( $type ) {

				case 'rgba_color':

					if ( isset($ie_color, $less_vars[1]) ) {

						$ie_color = of_get_option($ie_color[0], $ie_color[1]);
					} else {

						$ie_color = false;
					}

					$color_option = of_get_option( $color[0], $color[1] );
					$opacity_option = of_get_option( $opacity[0], $opacity[1] );

					if ( !$color_option ) {
						$color_option = $color[1];
					}

					$computed_color = azu_stylesheet_make_ie_compat_rgba(
						$color_option,
						$ie_color,
						$opacity_option
					);

					$options[ current($less_vars) ] = $computed_color['rgba'];
                                        
                                        // save accent color to option
                                        $option_name = 'base-brand-color';
                                        if(current($less_vars) == $option_name)
                                        {
                                            if ( get_option( $option_name ) !== false ) {
                                                // The option already exists, so we just update it.
                                                update_option( $option_name, $computed_color['rgba'] );
                                            } else {
                                                // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
                                                $deprecated = null;
                                                $autoload = 'no';
                                                add_option( $option_name, $computed_color['rgba'], $deprecated, $autoload );
                                            }
                                        }
                                        
					if ( $ie_color ) {

						if ( !empty($ie_color[2]) && function_exists($ie_color[2]) ) {
							$computed_color['ie_color'] = call_user_func( $ie_color[2], $computed_color['ie_color'] );
						}

						if ( empty($computed_color['ie_color']) ) {
							$computed_color['ie_color'] = '~"transparent"';
						}
						$options[ next($less_vars) ] = $computed_color['ie_color'];
					}

					break;

				case 'rgb_color':
					$color_option = of_get_option( $color[0], $color[1] );
					$computed_color = azu_stylesheet_color_hex2rgb( $color_option ? $color_option : $color[1] );

					if ( $computed_color && false !== $wrap ) {

						if ( is_array($wrap) ) {

							$computed_color = current($wrap) . $computed_color . next($wrap);
						} else {

							$computed_color = $wrap . $computed_color . $wrap;
						}
					}

					$options[ current($less_vars) ] = $computed_color;
					break;

				case 'hex_color':
					$computed_color = of_get_option( $color[0], $color[1] );

					if ( !$computed_color ) {
						$computed_color = $color[1];
					}

					$options[ current($less_vars) ] = $computed_color;
					break;

				case 'image':

					if ( !isset($image) ) {
						break;
					}

					$computed_image = of_get_option($image[0], $image[1]);

                                        $image_id = $computed_image['image'];
                                        if( $image_id && 'none' != $image_id )
                                            $image_id = azuf()->azu_get_attachment_id_by_url(azuf()->azu_get_of_uploaded_image($computed_image['image']));
                                        
                                        if($image_id!==null && 'none' != $image_id ){
                                            require_once( AZZU_LIBRARY_DIR . '/aq_resizer.php' );
                                            $image_id = wp_get_attachment_image_src( $image_id, 'full' );
                                            $image_id = azuf()->azu_get_resized_img( $image_id, array( 'w' => of_get_option('azu-layout-width',$image_id[1]), 'z' => 0 ) );
                                            $image_id = azu_stylesheet_get_image($image_id[0]);
                                        }
                                        
                                        
					$computed_image['image'] = azu_stylesheet_get_image($computed_image['image']);
                                        
					if ( false !== $wrap ) {

						if ( isset($wrap['image']) ) {

							$computed_image['image'] = current($wrap['image']) . $computed_image['image'] . next($wrap['image']);
                                                        reset($wrap['image']);
                                                        $image_id = current($wrap['image']) . $image_id . next($wrap['image']);
						}

						if ( isset($wrap['repeat']) ) {

							$computed_image['repeat'] = current($wrap['repeat']) . $computed_image['repeat'] . next($wrap['repeat']);
						}

						if ( isset($wrap['position_x']) ) {

							$computed_image['position_x'] = current($wrap['position_x']) . $computed_image['position_x'] . next($wrap['position_x']);
						}

						if ( isset($wrap['position_y']) ) {

							$computed_image['position_y'] = current($wrap['position_y']) . $computed_image['position_y'] . next($wrap['position_y']);
						}

					}
                                        
					// image
					$options[ current($less_vars) ] = $computed_image['image'];
                                        if(!($image_id != $computed_image['image'] && 'none' != $image_id))
                                            $image_id = $computed_image['image'];
                                          
                                        $options[ current($less_vars).'_2' ] = $image_id;

					// repeat
					if ( false != next($less_vars) && current($less_vars) ) {

						$options[ current($less_vars) ] = $computed_image['repeat'];
					}

					// position x
					if ( false != next($less_vars) && current($less_vars) ) {

						$options[ current($less_vars) ] = $computed_image['position_x'];
					}

					// position y
					if ( false != next($less_vars) && current($less_vars) ) {

						$options[ current($less_vars) ] = $computed_image['position_y'];
					}

					break;

				case 'number':

					if ( !isset($number) ) {
						break;
					}

					$computed_number = intval( of_get_option($number[0], $number[1]) );

					if ( false !== $wrap ) {

						if ( is_array($wrap) ) {

							$computed_number = current($wrap) . $computed_number . next($wrap);
						} else {

							$computed_number = $wrap . $computed_number . $wrap;
						}
					}

					$options[ current($less_vars) ] = $computed_number;

					break;
				case 'array':
					if ( !isset($array) ) {
						break;
					}
                                        
					$computed_array = (array) of_get_option($array[0], $array[1]);
                                        if(is_array($computed_array)){
                                            $pre_key = current($less_vars);
                                            foreach ($computed_array as $k => $arr_val) {
                                                $options[ $pre_key.'_'.$k ] = $arr_val;
                                            }
                                        }
					break;
				case 'custom_fonts':
					if ( !isset($custom_fonts) ) {
						break;
					}
                                        
					$computed_array = (array) of_get_option($custom_fonts[0], $custom_fonts[1]);
                                        
                                        if(is_array($computed_array)){
                                                $font_list = azuf()->azu_custom_font_face_regroup(azuf()->azu_custom_font_face($computed_array));
                                                //print_r($font_list);
                                                $pre_key = current($less_vars);
                                                $index = 0;
                                                $src_arr = array('ttf', 'otf', 'eot', 'svg', 'woff', 'woff2');
                                                foreach ($font_list as $arr_val) {
                                                    $options[ $pre_key.'_'.$index ] = $arr_val['family'];
                                                    $options[ $pre_key.'_'.$index.'_weight' ] = $arr_val['weight'];
                                                    $options[ $pre_key.'_'.$index.'_style' ] = $arr_val['style'];
                                                    foreach ($src_arr as $src) {
                                                        if(array_key_exists($src, $arr_val['src'])){
                                                            if($src == 'svg')
                                                                $options[ $pre_key.'_'.$index.'_'.$src ] = azu_stylesheet_get_image($arr_val['src'][$src].'#'.$arr_val['family']);
                                                            else
                                                                $options[ $pre_key.'_'.$index.'_'.$src ] = azu_stylesheet_get_image($arr_val['src'][$src]);
                                                        }
                                                        else
                                                            $options[ $pre_key.'_'.$index.'_'.$src ] = '';
                                                    }
                                                    $index++;
                                                }
                                                $options[ $pre_key.'_count' ] = $index;
                                        }
					break;
				case 'keyword':

					if ( !isset($keyword) ) {
						break;
					}

					$computed_keyword = (string) of_get_option($keyword[0], $keyword[1]);

					if ( false !== $interface ) {

						if ( isset( $interface[ $computed_keyword ] ) ) {

							$computed_keyword = $interface[ $computed_keyword ];
						} else {

							$computed_keyword = current($interface);
						}
					}

					$options[ current($less_vars) ] = $computed_keyword;

					break;

				case 'font':

					if ( !isset($font) ) {
						break;
					}

					$computed_font = azu_stylesheet_make_web_font_object( of_get_option($font[0]), $font[1] );

					if ( !$computed_font ) {
						break;
					}

					// TODO: refactor this
					if ( false !== $wrap ) {

						if ( is_array($wrap) ) {

							$computed_font->family = current($wrap) . $computed_font->family . next($wrap);
						} else {

							$computed_font->family = $wrap . $computed_font->family . $wrap;
						}
					}

					// font family
					$options[ current($less_vars) ] = $computed_font->family;

					// weight
					if ( false != next($less_vars) ) {
						$options[ current($less_vars) ] = $computed_font->weight;
					}

					// style
					if ( false != next($less_vars) ) {
						$options[ current($less_vars) ] = $computed_font->style;
					}

					break;
			}
		}
	}

	return apply_filters( 'azzu_compiled_less_vars', $options );
}

/**
 * Escape color for svg objects.
 *
 */
function azzu_less_escape_color( $color = '' ) {
	return '~"' . implode( ',%20', array_map( 'urlencode', explode( ',', $color ) ) ) . '"';
}

/**
 * Escape function for lessphp.
 *
 */
function azzu_lessphp_escape( $value ) {
	$v = &$value[2][1][1];
	$v = rawurlencode( $v );

	return $value;
}

/**
 * Register escape function in lessphp.
 *
 */
function azzu_register_escape_function_for_lessphp() {
	if ( !class_exists('WPLessPlugin') || !function_exists('azzu_lessphp_escape') ) {
		return;
	}

	$less = WPLessPlugin::getInstance();
	$less->registerFunction('escape', 'azzu_lessphp_escape');
}
add_action( 'azzu_before_compile_less_vars', 'azzu_register_escape_function_for_lessphp', 15 );