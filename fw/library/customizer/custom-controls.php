<?php

/**
 * Creates Customizer control for listbox
 *
 * @since	azu 1.0
 */
class AZU_Customizer_Custom_fonts_Control extends AZU_Customize_Control {

        function __construct( $manager, $id, $args = array() ) {
            parent::__construct($manager, $id, $args);
        }

	public $type = 'custom_fonts';
	
	public function render_content() {
            
            $ctrl_id = preg_replace("/\W/", "", strtolower($this->id) );
            $_value = $this->value();
            // If a value is passed and we don't have a stored value, use the value that's passed through.
            if ( !empty( $_value ) ) {
                // In case it's array
                if ( !is_array($_value) )
                    $_value = array($_value);
            }
            else
                $_value = array();
            
            ?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                </label>
            <div class="manual-fonts">
                <input type="hidden" id="<?php echo esc_attr( $ctrl_id ); ?>" <?php $this->link(); ?> value="<?php echo esc_attr(wp_unslash(json_encode($_value))); ?>" />
            <?php
            $output ='<input class="upload-uri upload" type="text" placeholder="'.__('No file chosen', 'azzu'.LANG_DN).'" value="" readonly="readonly"/>
                <a href="#" class="button-secondary azu-font-upload">
                    '._x('Upload','theme_option','azzu'.LANG_DN).'
                </a>';

            $output .= '<p>'._x('ttf, otf, eot, svg, woff, woff2','theme_option','azzu'.LANG_DN).' &nbsp;&nbsp;<input type="button" class="button-primary azu_add_font" value="'.__('Add Font', 'azzu'.LANG_DN).'"><br></p>';
            $row_data = '';
            foreach ( $_value as $key => $array_data ){
                $row_data .= '<tr id="azu-mf-id-'.$key.'" data-id="'.$key.'">';
                    $row_data .= '<td>'.$key.'</td>';
                    $row_data .= '<td>'.$array_data.'</td>';
                    $row_data .= '<td><a class="azu-mf-delete" href="javascript:void(0)">delete</a></td>';
                $row_data .= '</tr>';
            }
            if(empty($row_data))
                $row_data = '<tr class="azu-mf-no-item"><td colspan="3">'.__('No font found. Please click on Add Font button to add fonts', 'azzu'.LANG_DN).'</td></tr>';
            $output .='<p></p><table cellspacing="0" class="azu-font-table wp-list-table widefat fixed bookmarks"><thead><tr><th width="15">'.__('Id', 'azzu'.LANG_DN).'</th><th>'.__('Font', 'azzu'.LANG_DN).'</th><th width="50">'.__('Delete', 'azzu'.LANG_DN).'</th></tr></thead><tbody>'.$row_data.'</tbody></table>';

            $output .='</div><br /><p>'._x('Reload the customizer after a new custom font is added and saved.','customizer','azzu'.LANG_DN).'</p>';
            echo $output;
	}
        
        public function enqueue() {
                        wp_enqueue_media();
                        wp_enqueue_script( 'azu-image-upload', AZZU_OPTIONS_URI . '/assets/js/image-upload.js', array( 'jquery' ) );
	}
	
}

/**
 * Creates Customizer control for listbox
 *
 * @since	azu 1.0
 */
class AZU_Customizer_Listbox_Control extends AZU_Customize_Control {
    
        //Server-side sanitization callback for the setting's value.
        public $mode    = '';

        function __construct( $manager, $id, $args = array() ) {
            $key = 'mode';
            if ( isset( $args[ $key ] ) ) 
                $this->$key = $args[ $key ];
            parent::__construct($manager, $id, $args);
        }

	public $type = 'azu_listbox';
	
	public function render_content() {
            //set empty array
            $azu_choices = array();
            if ( !empty( $this->choices ) ){
                /*
                 * Get value of 'choices' array from $options array
                 * This contains paths to images for each option
                 */
                
                if(self::$azu_sections == null)
                    self::$azu_sections = thsp_cbp_get_fields();
                $azu_current_section = self::$azu_sections[ $this->section ];
                $azu_current_section_fields = $azu_current_section['fields'];
                /* 
                 * Going through all the fields in this section
                 * and getting the correct one so we could grab its 'choices'
                 */
                foreach ( $azu_current_section_fields as $azu_current_section_field_key => $azu_current_section_field_value ) {
                        /*
                         * Not the most sophisiticated way to do it
                         * There could be issues if one field has 'something' as ID
                         * and next one has 'somethi'
                         */
                        if ( strpos( $this->id, $azu_current_section_field_key ) ) {
                                $azu_choices = (isset($azu_current_section_fields[ $azu_current_section_field_key ]['control_args']['choices']) ? $azu_current_section_fields[ $azu_current_section_field_key ]['control_args']['choices'] : '');
                        }
                }
            }
            
            $listbox_id = preg_replace("/\W/", "", strtolower($this->id) );
            $_value = $this->value();
            // If a value is passed and we don't have a stored value, use the value that's passed through.
            if ( !empty( $_value ) ) {
                // In case it's array
                if ( !is_array($_value) )
                    $_value = array($_value);
            }
            else
                $_value = array();
             
	?>
        <input type="hidden" value="<?php echo esc_attr(json_encode($_value)); ?>" <?php $this->link(); ?>/>
            <ul id="<?php echo $listbox_id; ?>" class="azu-drag-and-drop" >
                <?php
                $desc ='';
                $azu_std ='';
                $label ='';
		foreach ( $_value as $value => $array_data ) {
                    $desc = '';
                    $label = $op_id = $value;
                    $opacity_control='';
                    $azu_listbox_toggle = "";
                    // show opacity slider
                    if(!empty($this->mode)){ 
                            $azu_listbox_toggle = "azu-listbox-toggle"; 
                            $child_val = array('Size' => 'normal','Weight' => '400', 'ls' => 0, 'uc' => 'none');
                            if($this->mode=='color') 
                                $child_val = array( 'option' => 100 );

                            $range_id = $value;
                            if(is_array($array_data) && count($array_data)>0)
                                    $child_val = array_merge($child_val, $array_data);
                            $range_id = esc_attr(preg_replace("/\W/", "", strtolower($range_id) )). '_range';
                            if($this->mode=='color') 
                                $opacity_control='<div class="azu-listbox-body azu-customize-hide">'._x('Opacity','theme_option','azzu'.LANG_DN).' &nbsp;&nbsp;<input style="max-width: 200px;" type="range" id="'.$range_id.'" data-mode="'.esc_attr($this->mode).'" class="azu-listbox-child" min="0" max="100" step="1" value="'.esc_attr($child_val['option']).'" oninput="'.$range_id.'_output.value=this.value;" /><output class="" name="'.$range_id.'_output" for="'.$range_id.'">'. $child_val['option'].'</output></div>';
                            else if($this->mode=='font' ) {
                                $opacity_control='<div class="azu-listbox-body azu-customize-hide">';
                                $font_array = array( 
                                    'Weight' => azuf()->azzu_get_font_weight_list()
                                );
                                if( strlen ($label) > 2 && !in_array(strtolower(substr($label,0,2)),array('h1','h2','h3','h4','h5','h6')))
                                {
                                    $font_array['Size'] = azuf()->azzu_themeoptions_get_font_size_defaults(false);
                                }
                                foreach ( $font_array as $n => $font_array_val ){
                                        $range_id = esc_attr(preg_replace("/\W/", "", strtolower($value) )). '_'.$n;
                                        $opacity_control .= $n.': &nbsp;&nbsp;<select class="azu-listbox-child" data-mode="'.esc_attr($this->mode).'" style="min-width: 160px;" id="'.$range_id.'" data-azu-select="'.$n.'" value="'.$child_val[$n].'" >';
                                        foreach ( $font_array_val as $m => $_font )
                                            $opacity_control .='<option value="'.$m.'" '.selected($child_val[$n], $m,false)  .'>'.$_font.'</option>';
                                        $opacity_control .='</select>';
                                }
                                $ls_id = esc_attr(preg_replace("/\W/", "", strtolower($range_id) )). '_ls';
                                $uc_id = esc_attr(preg_replace("/\W/", "", strtolower($range_id) )). '_uc';
                                $opacity_control .= '<br />'._x('Letter-spacing','theme_option','azzu'.LANG_DN).' &nbsp;&nbsp;<input class="of-slider-value azu-listbox-child" type="range" data-mode="'.esc_attr($this->mode).'" name="'.$ls_id.'" oninput="'.$ls_id.'_output.value=this.value/10" max="20" min="-20" step="1" value="'.esc_attr($child_val['ls']*10).'" style="width: 100px;"><output class="" name="'.$ls_id.'_output" for="'.$ls_id.'">'.esc_attr($child_val['ls']).'</output>px';
                                $opacity_control .= '<br />'._x( 'Uppercase', 'theme-option', 'azzu'.LANG_DN ).'<input type="checkbox" class="azu-listbox-child azu-switch-toggle azu-switch-toggle-round" name="' . $uc_id . '" id="' . $uc_id . '" data-mode="'.esc_attr($this->mode).'" value="'.esc_attr($child_val['uc']).'" '.checked( $child_val['uc'], 'uppercase', false).' /><label for="'.$uc_id.'" style="margin-left: 30px; display: inline-block;"></label>';
                                $opacity_control .='</div>';
                            }
                            else
                                $azu_listbox_toggle = "";
                    }

                    if(!empty($azu_choices)){
                        if (!array_key_exists($op_id,$azu_choices)){
                             continue;
                        }
                        $desc = $azu_choices[$op_id]['desc'];
                        $label = $azu_choices[$op_id]['label'];
                        $azu_std = $azu_choices[$op_id]['std'];
                    }
		?>
                    <li class="azu-listbox-item" title="<?php echo esc_attr($desc); ?>" data-azu-listbox="<?php echo esc_attr($value); ?>" data-azu-listbox-option="<?php echo esc_attr(json_encode($child_val)); ?>" data-std="<?php echo esc_attr($azu_std); ?>" ><div class="azu-listbox-title"><div class="azu-color-background"><div class="azu-color-window"></div></div><?php echo $label; ?><div class="<?php echo esc_attr($azu_listbox_toggle); ?>"></div></div><?php echo $opacity_control; ?></li>
                <?php 
                } 
                ?>
            </ul>
	<?php
	}
        
        public function enqueue() {
                global $wp_version;
                wp_enqueue_script(
			'field_chooser_script',
			AZZU_LIBRARY_URI . '/theme-options/js/field-chooser.js' , array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ), $wp_version, true
		);
		$azuCustomFonts = apply_filters('azuCustomFonts',array('font_face' => ''));
		wp_localize_script( 'field_chooser_script', 'azuCustomFonts', $azuCustomFonts);
                wp_enqueue_script(
			'azu_customizer_script',
			thsp_cbp_directory_uri() . '/azucustomizer.js' , array( 'field_chooser_script' ), $wp_version, true
		);
                wp_enqueue_script(  'less-js',thsp_cbp_directory_uri() . '/less.min.js','', '', false );
	}
	
}

/**
 * Creates Customizer control for textarea field
 *
 * @link	http://ottopress.com/2012/making-a-custom-control-for-the-theme-customizer/
 * @since	Theme_Customizer_Boilerplate 1.0
 */
class CBP_Customizer_Textarea_Control extends WP_Customize_Control {

	public $type = 'textarea';
	
	public function render_content() {
	?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<textarea rows="8" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
		</label>
	<?php
	}
	
}

/**
 * Creates Customizer control for social_buttons
 *
 */
class AZU_Customizer_Social_buttons_Control extends WP_Customize_Control {

	public $type = 'social_buttons';
	
	public function render_content() {
                $social_buttons = (array)apply_filters('optionsframework_interface-social_buttons', array());

                if ( empty($social_buttons) ) {
                        ?>
                        <p>Use "optionsframework_interface-social_buttons" filter to add some buttons. It needs array( id1 => name1, id2 => name2 ).</p>
                        <?php
                        return;
                }
                
                $saved_buttons = is_array($this->value()) ? (array) $this->value() : array();
                
                if(empty($saved_buttons)) {
                    foreach ( $social_buttons as $v => $social_value )
                        $saved_buttons[$v] = '';
                }
                ?>
                <label><span class="customize-control-title"><?php echo esc_html( $this->label );?></span>
                <input type="hidden" <?php $this->link(); ?>  value="<?php echo esc_attr(json_encode($this->value())); ?>"/>
                <ul class="connectedSortable">
                    <?php
                foreach ( $saved_buttons as $v => $social_value ) {
                        if ( !isset($social_buttons[$v]) ) 
                            continue;
                        $field = $social_buttons[$v];
                        $id = preg_replace("/\W/", "", strtolower($this->id) ). '-'. $field;
                        $checked = checked($social_value, 1, false);
                        ?>
                        <li class="ui-state-default"><input type="checkbox" data-name="<?php echo $v; ?>" value="<?php echo $social_value; ?>" id="<?php echo esc_attr( $id ); ?>" <?php echo $checked; ?>  /><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field ); ?></label></li>
                <?php
                }
                ?>
                </ul></label>
                <?php
	}
	
}


/**
 * Creates Customizer control for input[type=number] field
 *
 * @since	Theme_Customizer_Boilerplate 1.0
 */
class CBP_Customizer_Number_Control extends WP_Customize_Control {

	public $type = 'number';
	
	public function render_content() {
	?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<input type="number" <?php $this->link(); ?> value="<?php echo intval( $this->value() ); ?>" />
		</label>
	<?php
	}
	
}

/**
 * Creates Customizer control for input[type=azu_image] field
 *
 * @since	azu 1.0
 */
class AZU_Customize_Image_Control extends WP_Customize_Control {
    
    public $type = 'azu_image';
    
    public function enqueue()
    {
        wp_enqueue_media();
        wp_enqueue_script( 'azu-image-upload', AZZU_OPTIONS_URI . '/assets/js/image-upload.js', array( 'jquery' ) );
    }
    
    function __construct( $manager, $id, $args = array() ) {
        if(!(is_array($args['settings']) && count($args['settings'])==4) && !array_key_exists($id,$manager->settings()) )
            $args['settings'] = array(
                                        'default' => $id.'[uri]',
                                        'id'  => $id.'[id]',
                               );
        parent::__construct($manager, $id, $args);
    }
    
    public function render_content()
    {
        $value='';
        $_value = $this->value();
        // If a value is passed and we don't have a stored value, use the value that's passed through.
	if ( !empty( $_value ) ) {
		$value = $_value;
	}
        
        $this->theTitle();
        $this->theButtons();
        echo $this->theUploadedImage(azuf()->azu_get_of_uploaded_image($value));
    }
    protected function theTitle()
    {
        ?>
        <label>
            <span class="customize-control-title">
                <?php echo esc_html($this->label); ?> 
            </span> 
        </label>
        <?php
    }
    
    public function theButtons()
    {
       ?>
       <div>
            <input class="upload-uri" type="hidden" value="<?php echo esc_attr($this->value()); ?>" <?php $this->link(); ?>/>
            <?php if ( array_key_exists('id',$this->settings)): ?>
                <input class="upload-id" type="hidden" value="<?php echo absint($this->value('id')); ?>" <?php $this->link('id'); ?> />
            <?php endif;  ?>
            <a href="#" class="button-secondary azu-images-upload">
                <?php echo _x('Upload','customizer','azzu'.LANG_DN); ?>
            </a>
            <a href="#" class="button-secondary azu-images-remove">
            <?php echo _x('Remove','customizer','azzu'.LANG_DN); ?>
            </a>
       </div>
       <?php
    }
    
    public function theUploadedImage($src = '')
    {
        $img_hide='';
       if(empty($src))
           $img_hide = 'azu-customize-hide';
       
       $img_tag = '<div class="customize-control-content"><div class="thumbnails"><img src="'.esc_url($src).'" class="azu-customizer-image azu-upload-img '.esc_attr($img_hide).'" alt="the image"> </div> </div>';
       return $img_tag;
    }

}

/**
 * Creates Customizer control for input[type=azu_image] field
 *
 * @since	azu 1.0
 */
class AZU_Customize_Background_Img_Control extends AZU_Customize_Image_Control {
    	public $type = 'background_img';
        
        function __construct( $manager, $id, $args = array() ) {
            $args['settings'] = array(
	                                'default' => $id.'[image]',
	                                'repeat'  => $id.'[repeat]',
                                        'position_x'  => $id.'[position_x]',
                                        'position_y'  => $id.'[position_y]'
	                       );
            parent::__construct($manager, $id, $args);   
        }
        
        public function render_content()
        {
            parent::render_content();
                $image = $this->value('default');
                $repeat =  $this->value('repeat');
                $position_x =  $this->value('position_x');
                $position_y =  $this->value('position_y');

            ?>
                    <label>
                        <div class="azu-customize-background azu-image-upload-bg <?php echo empty( $image ) ? 'azu-customize-hide' : ''; ?>">
                            <select class="azu-customize-background-img-size" <?php $this->link('repeat'); ?>>
                                    <option value="no-repeat" <?php selected( $repeat, 'no-repeat' ); ?>>no repeat</option>
                                    <option value="repeat-x" <?php selected( $repeat, 'repeat-x' ); ?>>repeat x</option>
                                    <option value="repeat-y" <?php selected( $repeat, 'repeat-y' ); ?>>repeat y</option>
                                    <option value="repeat" <?php selected( $repeat, 'repeat' ); ?>>repeat</option>
                            </select>
                            <select class="azu-customize-background-img-size" <?php $this->link('position_x'); ?>>
                                    <option value="left" <?php selected( $position_x, 'left' ); ?>>left</option>
                                    <option value="right" <?php selected( $position_x, 'right' ); ?>>right</option>
                                    <option value="center" <?php selected( $position_x, 'center' ); ?>>center</option>
                            </select>
                            <select class="azu-customize-background-img-size" <?php $this->link('position_y'); ?>>
                                    <option value="top" <?php selected( $position_y, 'top' ); ?>>top</option>
                                    <option value="bottom" <?php selected( $position_y, 'bottom' ); ?>>bottom</option>
                                    <option value="center" <?php selected( $position_y, 'center' ); ?>>center</option>
                            </select>
                        </div>
                    </label>
	    <?php
            
        }
}

/**
 * Creates Customizer control for input[type=square_size] field
 *
 * @since	azu 1.0
 */
class AZU_Customize_Square_Size_Control extends WP_Customize_Control {

	public $type = 'square_size';
        
        function __construct( $manager, $id, $args = array() ) {
            $args['settings'] = array(
	                                'width' => $id.'[width]',
	                                'height'  => $id.'[height]'
	                       );
            parent::__construct($manager, $id, $args);   
        }
        
	public function render_content() {
            ?>
		<label>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <div style="float:left;">
                        <input type="text" class="azu-customize-square-size " <?php $this->link('width'); ?> value="<?php echo absint($this->value('width')); ?>" />
                        <span>&times;</span>
                        <input type="text" class="azu-customize-square-size" <?php $this->link('height'); ?> value="<?php echo absint($this->value('height')); ?>" />
                    </div>
                </label>
            <?php    
       }
}

/**
 * Creates Customizer control for input[type=range] field
 *
 * @since	azu 1.0
 */
class AZU_Customizer_Range_Control extends WP_Customize_Control {

	public $type = 'range';
	public $azu_options = array();
        
        function __construct( $manager, $id, $args = array() ) {
            parent::__construct($manager, $id, $args);
            $key = 'azu_options';
            if ( isset( $args[ $key ] ) )
                $this->$key = $args[ $key ];
        }
	public function render_content() {
            $range_id = preg_replace("/\W/", "", strtolower($this->id) );
            $min = 0; $max=100; $step=1; $wrap=''; $wrap0='';
            if ( !empty( $this->azu_options ) ){
                foreach($this->azu_options as $key => $val){
                    if($key=='min')
                        $min = $val;
                    else if($key=='max')
                        $max = $val;
                    else if($key=='step')
                        $step = $val;
                    else if($key=='wrap' && count($val)>1){
                        $wrap0 = $val[0];
                        $wrap = $val[1];
                    }
                }
            }
	?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<input class="azu-customize-range" type="range" <?php $this->link(); ?> value="<?php echo intval( $this->value() ); ?>" name="<?php echo $range_id; ?>" oninput="<?php echo $range_id; ?>_output.value=this.value" min="<?php echo intval($min); ?>" max="<?php echo intval($max); ?>" step="<?php echo intval($step); ?>"  />
                        <span style="font-size: 14px;"><?php echo $wrap0; ?></span>
                        <output class="azu-customize-range-output" name="<?php echo $range_id; ?>_output" for="<?php echo $range_id; ?>"><?php echo intval( $this->value() ); ?></output>
                        <span><?php echo $wrap; ?></span>
		</label>
	<?php
	}
}

/**
 * Creates Customizer control for input[type=azu_checkbox] field
 *
 * @since	azu 1.0
 */
class AZU_Customize_Check_Control extends WP_Customize_Control {

	public $type = 'azu_checkbox';

	public function render_content() {
                $checkbox_id = preg_replace("/\W/", "", strtolower($this->id) );
                    ?>
	            <span class="customize-control-title">
	                    <input id="<?php echo $checkbox_id; ?>" class="azu-switch-toggle azu-switch-toggle-round" type="checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?> />
	                    <?php echo esc_html( $this->label ); ?>
                            <label for="<?php echo $checkbox_id; ?>"></label>
	            </span>
	            <?php
	}
}

class AZU_Customize_Control extends WP_Customize_Control {
    static $azu_sections = null;
}

/**
 * Creates Customizer control for radio replacement images fields
 */
class CBP_Customizer_Images_Radio_Control extends AZU_Customize_Control {

	public $type = 'images_radio';
	
	public function render_content() {
		if ( empty( $this->choices ) ){
			return;
		}
		$name = '_customize-image-radios-' . $this->id;
		
		/*
		 * Get value of 'choices' array from $options array
		 * This contains paths to images for each option
		 */
                if(self::$azu_sections == null)
                    self::$azu_sections = thsp_cbp_get_fields();
		$thsp_cbp_current_section = self::$azu_sections[ $this->section ];
		$thsp_cbp_current_section_fields = $thsp_cbp_current_section['fields'];
		
		/* 
		 * Going through all the fields in this section
		 * and getting the correct one so we could grab its 'choices'
		 */
		foreach ( $thsp_cbp_current_section_fields as $thsp_cbp_current_section_field_key => $thsp_cbp_current_section_field_value ) {
			
			/*
			 * Not the most sophisiticated way to do it
			 * There could be issues if one field has 'something' as ID
			 * and next one has 'somethi'
			 */
			if ( strpos( $this->id, $thsp_cbp_current_section_field_key ) ) {
				$thsp_cbp_current_control_choices = $thsp_cbp_current_section_fields[ $thsp_cbp_current_section_field_key ]['control_args']['choices'];
			}
		}
		?>
		
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php
		foreach ( $this->choices as $value => $label ) {
			?>
			<input id="<?php echo esc_attr( $name ); ?>_<?php echo esc_attr( $value ); ?>" class="image-radio" type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
			
			<label for="<?php echo esc_attr( $name ); ?>_<?php echo esc_attr( $value ); ?>">
				<img class="customize-radio-img" src="<?php echo $thsp_cbp_current_control_choices[ $value ]['image_src']; ?>" alt="<?php echo $label; ?>" />
			</label>
			<?php
		} // end foreach
	}
	
	public function enqueue() {
		wp_enqueue_style(
			'thsp_customizer_style',
			thsp_cbp_directory_uri() . '/customizer-controls.css'
		);
	}
	
}



        /**
	 * A setting that is used to filter a value, but will not save the results.
	 *
	 * Results should be properly handled using another setting or callback.
	 *
	 * @since 1.0
	 */
	class AZU_Customize_Array_Setting extends WP_Customize_Setting {
	        /**
	         *
	         * @param $value
	         */
	        protected function update( $value ) {
                    if(!is_array($value))
                        $value = json_decode(wp_unslash($value), true);
                    parent::update($value);
	        }
                
                // override js value
                public function js_value() {
                    $_value = parent::js_value();
                    return json_encode($_value);
                }
	}
        
/**
 * Action hook that allows you to create your own controls
 */
do_action( 'thsp_cbp_custom_controls' );