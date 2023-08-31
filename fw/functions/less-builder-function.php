<?php
/**
 * Description here.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Themeoptions data.
 *
 */
function azzu_themeoptions_to_less( $options_inteface = array() ) {

	if ( !is_array($options_inteface) ) {
		$options_inteface = array();
	}

	$font_family_falloff = ',Helvetica,Arial,verdana,arial,sans-serif';

        // Base
        require_once( AZZU_LIBRARY_DIR . '/theme-options/options-framework.php' );
        $optionsframework_settings = get_option( 'optionsframework' );

	// Gets the unique option id
	if ( isset( $optionsframework_settings['id'] ) ) {
		$option_name = $optionsframework_settings['id'];
	}
	else {
		$option_name = 'optionsframework';
	};

	$settings = get_option($option_name);
        $settings = apply_filters( 'azzu_before_builder_option', $settings );
        
        if ( current_user_can( 'edit_theme_options' ) ) 
            {
                // Gets the default options data from the array in options.php
                $f_options =& Options_Framework::_optionsframework_options();
                $iarray = array();
                $options_without = array();
                foreach ( $f_options as $i => $value ) {
                    $type ='';
                    $wrap = '';
                    $interface = '';
                    if(!isset($value['id']))
                        continue;
                    if ( !in_array( $value['id'], $options_without ) )
                    switch ( $value['type'] ) {
                        // Basic inputs
//                        case 'upload':
//                        case 'textarea':
                        case 'square_size':
                            if(isset($value['less_builder']) && $value['less_builder']){
                                $type = 'array';
                                $less_var = array($value['id']);
                                $php_var = array( $type => array($value['id'],$value['std']) ); 
                            }
                        break;
                        case 'select':
                            $type = 'keyword';
                            $less_var = array($value['id']);
                            $php_var = array( $type => array($value['id'],$value['std']) );   
                        case 'radio':
                            if(isset($value['less_builder']) && $value['less_builder']){
                                $type = 'keyword';
                                $less_var = array($value['id']);
                                $php_var = array( $type => array($value['id'],$value['std']) );
                            }
                            break;
                        case 'images':
                            $type = 'keyword';
                            if(isset($value['interface']))
                                $interface = $value['interface'];
                            $less_var = array($value['id']);
                            $php_var = array( $type => array($value['id'],$value['std']) );                
                            break;
                        case 'checkbox':
                            if(isset($value['interface']) || (isset($value['less_builder']) && $value['less_builder']) ){
                                $type = 'keyword';
                                $interface = $value['interface'];
                                $less_var = array($value['id']);
                                $php_var = array( $type => array($value['id'],$value['std']) );
                            }
                            break;
                        case 'background_img':
                            $type = 'image';
                            $less_var = array($value['id'],$value['id'].'-repeat',$value['id'].'-position-x',$value['id'].'-position-y');
                            $php_var = array( $type => array($value['id'],$value['std']) );
                            break;
                        case 'color':
                            $type = 'hex_color';
                            $less_var = array($value['id']);
                            $php_var = array( 'color' => array($value['id'],$value['std']));
                            break;
                        case 'custom_fonts':
                            $type = 'custom_fonts';
                            $less_var = array($value['id']);
                            $php_var = array( $type => array($value['id'], $value['std']) ); 
                                break;         
                        case 'text':
                            // same option with slider
                            if(!isset($value['wrap']))
                                break;             
                        case 'slider':
                            $type = 'number';
                            if(isset($value['wrap']))
                                $wrap = $value['wrap'];
                            $less_var = array($value['id']);
                            $php_var = array( $type => array($value['id'], $value['std']) );                           
                            break;
                        case 'web_fonts':
                            $type = 'font';
                            $wrap = array( '"', '"' . $font_family_falloff );
                            $less_var = array($value['id'], $value['id'].'-weight', $value['id'].'-style');
                            $php_var = array( $type => array($value['id'],array('family' => $value['std'])) );
                            break;
                        default :
                            break;
                    }
                    if(!empty($type)){
                        $iarray = array(
                                    'type' 	=> $type,
                                    'less_vars' => $less_var,
                                    'php_vars'	=> $php_var,
                                );
                        if(!empty($wrap))
                            $iarray['wrap'] = $wrap;
                        if(!empty($interface))
                            $iarray['interface'] = $interface;
                        
                        // color with rgb, next element
                        $lid = $i+1;
                        
                        if(isset($f_options[$lid]) && $f_options[$lid]['type']=='listbox'){
                            if ( isset( $f_options[$lid]['id'], $settings[($f_options[$lid]['id'])] ) ) {
				$val = $settings[($f_options[$lid]['id'])];
                                
                                $less_array = $iarray['less_vars'];
                                if(!empty($val)) {
                                    if(!is_array($val))
                                        $val = json_decode($val, true);
                                    
                                    //array font weight
                                    $font_weight = array();
                                    
                                    //array value of listbox
                                    foreach ($val as $nval => $array_data) {
                                        $copy = $iarray;
                                        $rep_str='';
                                        // show opacity slider
                                        if(isset($f_options[$lid]['mode']))  {
                                            $child_val = array('Size' => 'normal','Weight' => '400', 'ls' => 0, 'uc' => 'none');
                                            if($f_options[$lid]['mode']=='color')
                                                $child_val = array( 'option' => 100 );
                                            
                                            if(is_array($array_data) && count($array_data)>0)
                                                    $child_val = array_merge($child_val, $array_data);
                                            if(isset($value['listbox_default']) && $value['listbox_default']){
                                                $default_std = $f_options[$lid]['options'][$nval]['std'];
                                                $copy['php_vars'][$f_options[$lid]['mode']] = array($nval , $default_std);
                                            }
                                            if($f_options[$lid]['mode']=='color'){
                                                $copy['php_vars']['opacity'] = array($nval , $child_val['option']);
                                                $copy['type'] = 'rgba_color';
                                            }
                                            else if($f_options[$lid]['mode']=='font') {
                                                foreach($child_val as $n => $n_value){
                                                    $ifont = array(
                                                        'type' 	=> 'keyword',
                                                        'less_vars' => array($nval.'_'.strtolower($n)),
                                                        'php_vars'	=>  array( 'keyword' => array($nval.'_'.strtolower($n),$n_value) ),
                                                    );
                                                    if($n == 'Weight')
                                                        $font_weight[] = $n_value;
                                                    $options_inteface[] = $ifont;
                                                }
                                            }
                                        }
                                        
                                        //get font weight
                                        if(isset($f_options[$lid]['mode']) && $f_options[$lid]['mode'] == 'font')
                                        {
                                            $font_weight = array_unique($font_weight);
                                            $lisbox_font_weight = get_option( 'azu_lisbox_font_weight');
                                            if($lisbox_font_weight === false)
                                            {    
                                                $lisbox_font_weight = array();
                                                $lisbox_font_weight[$f_options[$lid]['id']] = $font_weight;
                                                add_option( 'azu_lisbox_font_weight',$lisbox_font_weight);
                                            }
                                            else {
                                                $lisbox_font_weight[$f_options[$lid]['id']] = $font_weight;
                                                update_option( 'azu_lisbox_font_weight',$lisbox_font_weight);
                                            }
                                        }
                                                
                                        // change the id
                                        foreach ($less_array as $j => $lval){
                                            if(empty($rep_str))
                                                $rep_str = $less_array[$j];
                                            $copy['less_vars'][$j] = str_replace($rep_str,$nval,$less_array[$j]);
                                        }
                                        $options_inteface[] = $copy;
                                    }
                                }
                            }
                        }
                        else
                            $options_inteface[] = $iarray;
                    }
                }
        }
	return $options_inteface;
}
add_filter( 'azzu_less_options_interface', 'azzu_themeoptions_to_less', 15 );


/**
 * Compilled less special cases.
 *
 */
function azzu_compilled_less_special_cases( $options = array() ) {

	$top_level_img_sizes = of_get_option( 'header-icons_size', array('width' => 20, 'height' => 20) );
	$sub_level_img_sizes = of_get_option( 'header-submenu_icons_size', array('width' => 16, 'height' => 16) );

	// menu image sizes
	$options['main-menu-icon-width'] = $top_level_img_sizes['width'] . 'px';
	$options['main-menu-icon-height'] = $top_level_img_sizes['height'] . 'px';

	// sub menu image sizes
	$options['sub-menu-icon-width'] = $sub_level_img_sizes['width'] . 'px';
	$options['sub-menu-icon-height'] = $sub_level_img_sizes['height'] . 'px';
        
        if(!array_key_exists('general-gutter-width' ,$options)){
            $options['general-gutter-width'] = AZZU_THEME_GUTTER.'px';
        }
        if(!array_key_exists('azu-layout-mobile-width' ,$options)){
            $options['azu-layout-mobile-width'] = AZZU_THEME_MOBILE_WIDTH.'px';
        }
        if(!array_key_exists('vc-bottom-margin' ,$options)){
            $options['vc-bottom-margin'] = '35px';
        }
        
        if(!array_key_exists('wc-archive-columns' ,$options)){
            $options['wc-archive-columns'] = 3;
        }
        if(!array_key_exists('wc-star-rating' ,$options)){
            $options['wc-star-rating'] = 'off';
        }
        if(!array_key_exists('wc-single-title' ,$options)){
            $options['wc-single-title'] = '';
        }
        if(!array_key_exists('wc-single-subtitle' ,$options)){
            $options['wc-single-subtitle'] = '';
        }
        

	return $options;
}
add_filter( 'azzu_compiled_less_vars', 'azzu_compilled_less_special_cases', 15 );