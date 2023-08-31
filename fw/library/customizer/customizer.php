<?php

/**
 * Theme Customizer Boilerplate
 *
 * @package		Theme_Customizer_Boilerplate
 * @copyright	Copyright (c) 2012, Slobodan Manic
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License, v2 (or newer)
 * @author		Slobodan Manic
 *
 * @since		Theme_Customizer_Boilerplate 1.0
 *
 * License:
 *	
 * Copyright 2013 Slobodan Manic (slobodan.manic@gmail.com)
 *	
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 *	
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *	
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */


/**
 * Arrays of options
 */	
require_once( dirname(__FILE__) . '/options.php' );

/**
 * Helper functions
 */	
require_once( dirname(__FILE__) . '/helpers.php' );


/**
 * Adds Customizer Sections, Settings and Controls
 *
 * - Require Custom Customizer Controls
 * - Add Customizer Sections
 *   -- Add Customizer Settings
 *   -- Add Customizer Controls
 *
 * @uses	thsp_get_theme_customizer_sections()	Defined in helpers.php
 * @uses	thsp_settings_page_capability()			Defined in helpers.php
 * @uses	thsp_get_theme_customizer_fields()		Defined in options.php
 *
 * @link	$wp_customize->add_section				http://codex.wordpress.org/Class_Reference/WP_Customize_Manager/add_section
 * @link	$wp_customize->add_setting				http://codex.wordpress.org/Class_Reference/WP_Customize_Manager/add_setting
 * @link	$wp_customize->add_control				http://codex.wordpress.org/Class_Reference/WP_Customize_Manager/add_control
 */
function thsp_cbp_customize_register( $wp_customize ) {
    
        global $wp_version;

	/**
	 * Custom controls
	 */	
	require_once( dirname(__FILE__) . '/custom-controls.php' );


	/*
	 * Get all the fields using a helper function
	 */
	$thsp_sections = AZU_Customize_Control::$azu_sections = thsp_cbp_get_fields();


	/*
	 * Get name of DB entry under which options will be stored
	 */
	$thsp_cbp_option = thsp_cbp_option();


	/**
	 * Loop through the array and add Customizer sections
	 */
	foreach( $thsp_sections as $thsp_section_key => $thsp_section_value ) {
		/**
		 * Adds Customizer panel for greater than wordpress 4.0
		 */
                if(isset($thsp_section_value['panel'])){
                        /**
                        * Adds Customizer panel
                        */
                        if(version_compare( $wp_version, '4.0' ) >= 0){
                                $wp_customize->add_panel(
                                    $thsp_section_value['panel']['id'],
                                    $thsp_section_value['panel']['panel_args']
                                );
                        }
                        continue;
                } // end if
                
		/**
		 * Adds Customizer section, if needed
		 */
		if( ! $thsp_section_value['existing_section'] ) {
			
			$thsp_section_args = $thsp_section_value['args'];
			
			// Add section
			$wp_customize->add_section(
				$thsp_section_key,
				$thsp_section_args
			);
			
		} // end if
		
		/*
		 * Loop through 'fields' array in each section
		 * and add settings and controls
		 */
		$thsp_section_fields = $thsp_section_value['fields'];
		foreach( $thsp_section_fields as $thsp_field_key => $thsp_field_value ) {
			/*
			 * Check if 'option' or 'theme_mod' is used to store option
			 *
			 * If nothing is set, $wp_customize->add_setting method will default to 'theme_mod'
			 * If 'option' is used as setting type its value will be stored in an entry in
			 * {prefix}_options table. Option name is defined by thsp_cbp_option() function
			 */
			if ( isset( $thsp_field_value['setting_args']['type'] ) && 'option' == $thsp_field_value['setting_args']['type'] ) {
                                $setting_control_id = $thsp_cbp_option . '[' . $thsp_field_key . ']';
			} else {
				$setting_control_id = $thsp_field_key;
			}
			
			/*
			 * Add default callback function, if none is defined
			 */
			if ( ! isset( $thsp_field_value['setting_args']['sanitize_cb'] ) ) {
				$thsp_field_value['setting_args']['sanitize_cb'] = 'thsp_cbp_sanitize_cb';
			}

			/**
			 * Adds Customizer settings
			 */
                        if ( is_array($thsp_field_value['setting_args']['default']) && in_array($thsp_field_value['control_args']['type'], array('background_img','square_size','azu_image')) ) {
                                $array_controls = array('width','height');
                                if('background_img' == $thsp_field_value['control_args']['type'])
                                    $array_controls = array('image','repeat','position_x','position_y');
                                else if('azu_image' == $thsp_field_value['control_args']['type'])
                                    $array_controls = array('uri','id');
                                foreach ( $array_controls as $index => $v ) {
                                    $copy_args = $thsp_field_value['setting_args'];
                                    if('azu_image' == $thsp_field_value['control_args']['type'])
                                        $copy_args['default'] = $thsp_field_value['setting_args']['default'][$index];
                                    else
                                        $copy_args['default'] = $thsp_field_value['setting_args']['default'][$v];
                                    $wp_customize->add_setting (
                                            new WP_Customize_Setting(
                                                    $wp_customize,
                                                    $setting_control_id.'['.$v.']',
                                                    $copy_args
                                            )
                                    );
                                }
                        }
                        else if ( in_array($thsp_field_value['control_args']['type'], array('azu_listbox','custom_fonts','social_buttons') )) {
                                $wp_customize->add_setting (
					new AZU_Customize_Array_Setting(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['setting_args']
					)
				);
                        }
                        else if ( 'text' == $thsp_field_value['control_args']['type'] ) {
				$wp_customize->add_setting (
					new WP_Customize_Setting(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['setting_args']
					)
				);
                        }
                        else
                            $wp_customize->add_setting (
                                    $setting_control_id,
                                    $thsp_field_value['setting_args']
                            );

			/**
			 * Adds Customizer control
			 *
			 * 'section' value must be added to 'control_args' array
			 * so control can get added to current section
			 */
			$thsp_field_value['control_args']['section'] = $thsp_section_key;
			
			/*
			 * $wp_customize->add_control method requires 'choices' to be a simple key => value pair
			 */
			if ( isset( $thsp_field_value['control_args']['choices'] ) ) {
				$thsp_cbp_choices = array();
				foreach( $thsp_field_value['control_args']['choices'] as $thsp_cbp_choice_key => $thsp_cbp_choice_value ) {
					$thsp_cbp_choices[$thsp_cbp_choice_key] = $thsp_cbp_choice_value['label'];
				}
				$thsp_field_value['control_args']['choices'] = $thsp_cbp_choices;		
			}
			
			
			// Check 
			if ( 'color' == $thsp_field_value['control_args']['type'] ) {
				$wp_customize->add_control(
					new WP_Customize_Color_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
				);
                        }
                        // Azu Checkbox 
			else if ( 'azu_checkbox' == $thsp_field_value['control_args']['type'] ) {
				$wp_customize->add_control(
					new AZU_Customize_Check_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
				);
                                
			}
                        else if ( 'social_buttons' == $thsp_field_value['control_args']['type'] ) {
				$wp_customize->add_control(
					new AZU_Customizer_Social_buttons_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
                                );
                        }
                       elseif ( 'image' == $thsp_field_value['control_args']['type'] ) { 
				$wp_customize->add_control(
					new WP_Customize_Image_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
				);
			} elseif ( 'upload' == $thsp_field_value['control_args']['type'] ) { 
				$wp_customize->add_control(
					new WP_Customize_Upload_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
				);
			} elseif ( 'square_size' == $thsp_field_value['control_args']['type'] ) { 
				$wp_customize->add_control(
					new AZU_Customize_Square_Size_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
				);      
			} elseif ( 'background_img' == $thsp_field_value['control_args']['type'] ) { 
				$wp_customize->add_control(
					new AZU_Customize_Background_Img_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
				);   
			} elseif ( 'azu_image' == $thsp_field_value['control_args']['type'] ) { 
				$wp_customize->add_control(
					new AZU_Customize_Image_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
				);                                  
			} elseif ( 'azu_listbox' == $thsp_field_value['control_args']['type'] ) { 
				$wp_customize->add_control(
					new AZU_Customizer_Listbox_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
				);
			} elseif ( 'custom_fonts' == $thsp_field_value['control_args']['type'] ) { 
				$wp_customize->add_control(
					new AZU_Customizer_Custom_fonts_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
				);
			} elseif ( 'range' == $thsp_field_value['control_args']['type'] ) { 
				$wp_customize->add_control(
					new AZU_Customizer_Range_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
				);                                
                                
			} elseif ( 'number' == $thsp_field_value['control_args']['type'] ) { 
				$wp_customize->add_control(
					new CBP_Customizer_Number_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
				);
			} elseif ( 'textarea' == $thsp_field_value['control_args']['type'] ) { 
				$wp_customize->add_control(
					new CBP_Customizer_Textarea_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
				);
			} elseif ( 'images_radio' == $thsp_field_value['control_args']['type'] ) {
				$wp_customize->add_control(
					new CBP_Customizer_Images_Radio_Control(
						$wp_customize,
						$setting_control_id,
						$thsp_field_value['control_args']
					)
				);
			} else {
				$wp_customize->add_control(
					$setting_control_id,
					$thsp_field_value['control_args']
				);
			}
				
		} // end foreach
		
	} // end foreach
	
	
	// Remove built-in Customizer sections
	$thsp_cbp_remove_sections = apply_filters( 'tshp_cbp_remove_sections', array() );
	if ( is_array( $thsp_cbp_remove_sections) ) {
		foreach( $thsp_cbp_remove_sections as $thsp_cbp_remove_section ) {
			$wp_customize->remove_section( $thsp_cbp_remove_section );
		}
	}

	// Remove built-in Customizer settings
	$thsp_cbp_remove_settings = apply_filters( 'tshp_cbp_remove_settings', array() );
	if ( is_array( $thsp_cbp_remove_settings) ) {
		foreach( $thsp_cbp_remove_settings as $thsp_cbp_remove_setting ) {
			$wp_customize->remove_setting( $thsp_cbp_remove_setting );
		}
	}	

	// Remove built-in Customizer controls
	$thsp_cbp_remove_controls = apply_filters( 'tshp_cbp_remove_controls', array() );
	if ( is_array( $thsp_cbp_remove_controls) ) {
		foreach( $thsp_cbp_remove_controls as $thsp_cbp_remove_control ) {
			$wp_customize->remove_control( $thsp_cbp_remove_control );
		}
	}	
        
        $wp_customize->get_setting( 'blogname' )->transport        = DEFAULT_TRANSPORT_MODE;
	$wp_customize->get_setting( 'blogdescription' )->transport = DEFAULT_TRANSPORT_MODE;
}
add_action( 'customize_register', 'thsp_cbp_customize_register', 11 );


/**
 * Theme Customizer sanitization callback function
 */
function thsp_cbp_sanitize_cb( $input ) {
	
	return wp_kses_post( $input );
	
}


/**
 * Used by hook: 'customize_preview_init'
 * 
 * @see add_action('customize_preview_init',$func)
 */
function azu_customizer_live_preview()
{
	wp_enqueue_script( 
		  'azu-themecustomizer',			//Give the script an ID
		  thsp_cbp_directory_uri() . '/theme-customizer.js',//Point to file
		  array( 'customize-preview' ),	//Define dependencies //,'jquery' 
		  '',						//Define a version (optional) 
		  true						//Put script in footer?
	);
        global $azuMenu;
        //add_filter( 'wp_nav_menu_args' , array( $azuMenu , 'megaMenuFilter' ), 2000 );
        
}
add_action( 'customize_preview_init', 'azu_customizer_live_preview', 11 );

//Fires after Customize settings have been saved.
add_action( 'customize_save_after', array( 'azu_functions','azzu_generate_less_css_file_after_customizer_save') );

