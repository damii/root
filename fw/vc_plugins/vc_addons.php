<?php
/**
 * Visual Composer extensions.
 *
 */

// Initialising Shortcodes
if (class_exists('WPBakeryVisualComposerAbstract')) {
    
    
        /**
	 * Range field.
	 *
	 */
        function azzu_vc_range_settings_field($settings, $value)
        {
                $param_name = isset($settings['param_name']) ? $settings['param_name'] : '';
                $type = isset($settings['type']) ? $settings['type'] : '';
                $unit = isset($settings['unit']) ? $settings['unit'] : '';
                $min = isset($settings['min']) ? $settings['min'] : '';
                $max = isset($settings['max']) ? $settings['max'] : '';
                $step = isset($settings['step']) ? $settings['step'] : '';
                $suffix = isset($settings['suffix']) ? $settings['suffix'] : '';
                $class = isset($settings['class']) ? $settings['class'] : '';
                $output = '<input type="range" min="'.$min.'" max="'.$max.'" step="'.$step.'" class="wpb_vc_param_value azu-customize-range ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="'.$value.'" style="max-width:250px; margin-right: 10px;" oninput="'.$param_name.'_output.value=this.value" /><output class="" id="'.$param_name.'_output" for="'.$param_name.'">'.$value.'</output><span>'.$unit.'</span> '.$suffix;
                return $output;
        }
        
        /**
	 * Toggle field.
	 *
	 */
        function azzu_vc_toggle_settings_field($settings, $value)
        {
                $param_name = isset($settings['param_name']) ? $settings['param_name'] : '';
                $type = isset($settings['type']) ? $settings['type'] : '';
                $suffix = isset($settings['suffix']) ? $settings['suffix'] : '';
                $class = isset($settings['class']) ? $settings['class'] : '';
                $checked = apply_filters('azu_sanitize_flag', $value);
                $output = '<input onchange="azu_toggle_change(this)" type="checkbox" class="wpb_vc_param_value azu-switch-toggle azu-switch-toggle-round ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" id="' . $param_name . '" value="'.$value.'" '.checked( $checked, true, false).' style="margin-right: 10px;" /><label for="'.$param_name.'"></label>'.$suffix;
                return $output;
        }
        
        
	/**
	 * Taxonomy checkbox list field.
	 *
	 */
	function azzu_vc_taxonomy_settings_field($settings, $value) {

		$terms_fields = array();

		$value_arr = $value;
		if ( !is_array($value_arr) ) {
			$value_arr = array_map( 'trim', explode(',', $value_arr) );
		}

		if ( !empty($settings['taxonomy']) ) {

			$terms = get_terms( $settings['taxonomy'] );
			if ( $terms && !is_wp_error($terms) ) {

				foreach( $terms as $term ) {

					$terms_fields[] = sprintf(
						'<label><input id="%s" class="%s" type="checkbox" name="%s" value="%s" %s/>%s</label>',
						$settings['param_name'] . '-' . $term->slug,
						$settings['param_name'].' '.$settings['type'],
						$settings['param_name'],
						$term->slug,
						checked( in_array( $term->slug, $value_arr ), true, false ),
						$term->name
					);
				}
			}
		}

		return '<div class="azu_taxonomy_block">'
				.'<input type="hidden" name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-checkboxes '.$settings['param_name'].' '.$settings['type'].'_field" value="'.$value.'" />'
				 .'<div class="azu_taxonomy_terms">'
				 .implode( $terms_fields )
				 .'</div>'
			 .'</div>';
	}

	/**
	 * Icon picker field.
	 *
	 */
	function azzu_vc_iconpicker_settings_field($settings, $value) {

		return '<div class="azu_iconpicker_block">'
				.'<input type="text" name="'.$settings['param_name'].'" class="azuIconPicker wpb_vc_param_value wpb-textinput '.$settings['param_name'].' '.$settings['type'].'_field" value="'.$value.'" />'
			 .'</div>';
	}

        /**
	 * Number field.
	 *
	 */
        function azzu_vc_number_settings_field($settings, $value)
        {
                $param_name = isset($settings['param_name']) ? $settings['param_name'] : '';
                $type = isset($settings['type']) ? $settings['type'] : '';
                $min = isset($settings['min']) ? $settings['min'] : '';
                $max = isset($settings['max']) ? $settings['max'] : '';
                $step = isset($settings['step']) ? $settings['step'] : '';
                $suffix = isset($settings['suffix']) ? $settings['suffix'] : '';
                $class = isset($settings['class']) ? $settings['class'] : '';
                $output = '<input type="number" min="'.$min.'" max="'.$max.'" step="'.$step.'" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="'.$value.'" style="max-width:150px; margin-right: 10px;" />'.$suffix;
                return $output;
        }
        
        function azu_addshortcode_param($name, $form_field_callback, $script_url = null){
            return call_user_func('vc_add_'.'shortcode_param', $name, $form_field_callback, $script_url );
        }
        
	function azzu_vc_add_custom_fields() {

		$dir = get_template_directory_uri();
		// fonticonpicker js
		wp_enqueue_script('azu-fonticonpicker-js', AZZU_THEME_URI.'/js/jquery.fonticonpicker.min.js', array(), AZZU_VERSION, true);
                // add icon data
        	wp_localize_script( 'azu-fonticonpicker-js', 'azuIconPicker', array('icons' => azuf()->azuIconPicker()) );
		//wp_enqueue_style( 'azu-fonticonpicker', $dir . '/css/iconpicker.css', array() );
                wp_enqueue_style( 'azu-fontello', AZZU_UI_URI.'/'.AZZU_DESIGN . '/css/fontello.min.css', array() );
                // fonticonpicker css
		wp_enqueue_style('azu-fonticonpicker-css', 	AZZU_UI_URI.'/'.AZZU_DESIGN.'/css/jquery.fonticonpicker.css', 	array(), AZZU_VERSION);
                
                if(function_exists('vc_add_'.'shortcode_param'))
		{
                    azu_addshortcode_param('azu_taxonomy', 'azzu_vc_taxonomy_settings_field', $dir . '/fw/vc_plugins/vc_extend/azu-taxonomy.js' );
                    azu_addshortcode_param('azu_number', 'azzu_vc_number_settings_field' );
                    azu_addshortcode_param('azu_toggle', 'azzu_vc_toggle_settings_field', $dir . '/fw/vc_plugins/vc_extend/azu-toggle.js' );
                    azu_addshortcode_param('azu_range', 'azzu_vc_range_settings_field' );
                    azu_addshortcode_param('azu_iconpicker', 'azzu_vc_iconpicker_settings_field', $dir . '/js/jquery.fonticonpicker.min.js' );
                }
	}
	add_action( 'admin_init', 'azzu_vc_add_custom_fields', 15 );

}
