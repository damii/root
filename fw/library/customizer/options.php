<?php

/**
 * Get Theme Customizer Fields
 *
 * @package		Theme_Customizer_Boilerplate
 * @copyright	Copyright (c) 2013, Slobodan Manic
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License, v2 (or newer)
 * @author		Slobodan Manic
 *
 * @since		Theme_Customizer_Boilerplate 1.0
 */


/**
 * Helper function that holds array of theme options.
 *
 * @return	array	$options	Array of theme options
 * @uses	thsp_get_theme_customizer_fields()	defined in customizer/helpers.php
 */
function thsp_cbp_get_fields() {

	/*
	 * Using helper function to get default required capability
	 */
	$thsp_cbp_capability = thsp_cbp_capability();
	
	$options = array();
        
        
        /*
         * load options framework
         */
        if ( defined('OPTIONS_FRAMEWORK_VERSION') ) {
                // Base
		require_once( AZZU_LIBRARY_DIR . '/theme-options/options-framework.php' );

		if ( current_user_can( 'edit_theme_options' ) ) {
                        // Gets the default options data from the array in options.php
                        $f_options =& Options_Framework::_optionsframework_options();
                        //customizer fields
                        $c_options=array();
                        $section_name='';
                        $block_name='';
                        $panel_id_after='';
                        $panel_id = 'of-general-menu';
                        //first panel
                        $options[$panel_id] = customizer_option_panel_maker($panel_id,'General');
                        $customizer_settings_option = optionsframework_get_options_id();
                        foreach ( $f_options as $value ) {
                            
                            switch ( $value['type'] ) {
                                // Basic inputs
                                case 'text':
                                case 'checkbox':
                                case 'select':
                                case 'radio':
                                case 'upload':
                                case 'background_img':
                                case 'textarea':
                                case 'images':
                                case 'color':
                                case 'slider':
                                case 'square_size':
                                case 'listbox':
                                case 'custom_fonts':
                                case 'web_fonts':
                                case 'social_buttons':
                                    if(isset($value['theme_customizer']) && !$value['theme_customizer'])
                                            break;
                                    // create setting & control options
                                    $c_options[$value['id']]=array(
					'setting_args' => customizer_option_setting_maker($value,$thsp_cbp_capability),			
					'control_args' => customizer_option_control_maker($value,$customizer_settings_option,$block_name)
                                    );
                                    $block_name = '';
                                    break;
                                case 'page':
                                        // panel maker in wordpress 4.0
                                        $panel_id ='';
                                        if(!in_array($value['menu_slug'], array('of-color-menu','of-typography-menu','of-image-menu') ))
                                        {
                                            $panel_id = preg_replace("/\W/", "", strtolower($value['menu_slug']) );
                                            $options[$panel_id] = customizer_option_panel_maker($panel_id,$value['page_title']);
                                        }
                                    break;
                                case 'heading':
                                    // section creator
                                    if(!empty($section_name) && is_array($c_options) && !(count($c_options) === 0)) {
                                            $options[preg_replace("/\W/", "", strtolower($section_name) )] = customizer_option_section_maker($c_options, $section_name,'',$panel_id_after);
                                            $c_options=array();
                                    }
                                    $panel_id_after = $panel_id;
                                    $section_name = $value['name'];
                                    break;
                                case 'block_begin':
                                    //print divider of begin block
                                    $divider_len = intval((31-strlen($value['name']))/2);
                                    $divider_str = $divider_len <= 0 ? '' : str_repeat("=", $divider_len);
                                    $block_name = sprintf('%1$s%2$s%1$s ',$divider_str, str_replace(' ', '-', $value["name"]) );
                                    break;
                                default :
                                    break;
                            }
                        }
                        // section creator
                        if(is_array($c_options) && !(count($c_options) === 0))
                            $options[preg_replace("/\W/", "", strtolower($section_name) )] = customizer_option_section_maker($c_options, $section_name,'', $panel_id_after);
		}
	}
	
	/* 
	 * 'thsp_cbp_options_array' filter hook will allow you to 
	 * add/remove some of these options from a child theme
	 */
	return apply_filters( 'thsp_cbp_options_array', $options );
	
}



/**
 * Creates Customizer panel for Customizer options
 *
 * @since	azu 1.0
 */
function customizer_option_panel_maker($panel_id,$panel_title='',$panel_desc=''){
    static $count=100;
    $count++;
    return array(
        'panel' => array(
            'id' => $panel_id,
            'panel_args' => array(
               'title' => $panel_title,
               'description' => $panel_desc,
               'priority' => $count,
            ),   
         ),
    );
}

/**
 * Creates Customizer Section for Customizer options
 *
 * @since	azu 1.0
 */

function customizer_option_section_maker($c_options = null,$section_name ='',$section_desc='',$panel_id=''){
    static $count=100;
    $count++;
    return array(
        'existing_section' => false,
        'args' => array(
               'title' => $section_name,
               'description' => $section_desc,
               'priority' => $count,
               'panel'  => $panel_id
         ),
         'fields' => $c_options,                
    );
}

/**
 * Creates Customizer Setting for Customizer options
 *
 * @since	azu 1.0
 */
function customizer_option_setting_maker($value = null,$capability =''){
    static $count=100;
    $count++;
    $default_std = '';
    $std_val = $value['std'];
    switch ( $value['type'] ) {
           case 'checkbox':
               $default_std = false;
           break;
           case 'background_img':
               $std_val = $value['std'];
               $default_std = '';
           break;
    }
    
    $std = ($std_val!=null && !empty($std_val)) ? $std_val : $default_std;
    $azu_setting = array(
						'default' => $std,
						'type' => 'option',
						'capability' => $capability,
						'transport' => DEFAULT_TRANSPORT_MODE, //refresh OR postMessage
                                                'priority' => $count
					);
    if(isset($value['transport']) && in_array( $value['transport'], array('refresh','postMessage')))
        $azu_setting['transport'] = $value['transport'];
    
    //sanitize
    if(isset($value['sanitize'])){
            $function_name='';
            switch ($value['sanitize']) {
                case 'dimensions':
                    $function_name = array('azu_functions','azu_sanitize_dimensions');
                    break;
                case 'ppp':
                    $function_name = 'absint';
                case 'slider':
                    $function_name = 'azuint';
                    break;
                case 'email':
                    $function_name = 'sanitize_email';
                    break;
                case 'without_sanitize':
                    $function_name = array('azu_functions','azu_without_sanitize');
                    break;
                default:
                    break;
            }
            if(!empty($function_name)){
                $azu_setting['sanitize_callback'] = $function_name;
            }
    }
    
    return $azu_setting;
}

/**
 * Creates Customizer Control for Customizer options
 *
 * @since	azu 1.0
 */
function customizer_option_control_maker($value = array(),$customizer_settings_option = '',$block_name=''){
    static $count=10;
    $extra_key = 'choices';
    $count++;
    $type = $value['type'];
    // convert type of controls
    if($value['type']=='images')
        $type = 'images_radio';
    else if($value['type']=='slider'){
        $extra_key = 'azu_options';
        $type = 'range';
    }
    else if($value['type']=='web_fonts')
        $type = 'select';   
    else if($value['type']=='upload')
        $type = 'azu_image';
    else if($value['type']=='checkbox')
        $type = 'azu_checkbox';
    else if($value['type']=='listbox')
        $type = 'azu_listbox';
    $azu_control = array(
						'label' => $block_name. (isset($value['name']) ? $value['name'] : ''),
						'type' => $type, // type of customizer field control
                                                'settings' => $customizer_settings_option.'['.$value['id'].']',
						'priority' => $count
					);
    if(isset($value['options']) && is_array($value['options']) && !(count($value['options']) === 0)){
        $choices_list = array();
        foreach ($value['options'] as $key => $_value){
            if($type == 'images_radio')
                $choices_list[$key] = array(
			'label' => $key,
                        'image_src' => AZZU_THEME_URI.$_value
                );
            else if($type == 'range')
                $choices_list[$key] = $_value;
            else if($type == 'azu_listbox')
                $choices_list[$key] = $_value;
            else
                $choices_list[$key] = array(
			'label' => $_value,
                );
        }
        
        // wrap array transfer to Range control
        if($type == 'range' && isset($value['wrap']))
            $choices_list['wrap'] = $value['wrap'];
        else if($type == 'azu_listbox' && isset($value['mode']))
            $azu_control['mode'] = $value['mode'];
        $azu_control[$extra_key] = $choices_list;
    }
    return $azu_control;
}