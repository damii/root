<?php
/**
 * Theme functions.
 *
 * @package Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists('theme_functions') ) :
abstract class theme_functions extends azu_base {
public function __construct()
{
        parent::__construct();
}

function init(){}
    
protected function add_actions(){
        // auto update hide bundled sliders
        add_action('admin_init', array( &$this,'azzu_revslider_autoupdate_hide'));
        // Config Layer slider
        add_action('layerslider_ready', array( &$this,'azzu_layerslider_overrides'));
        // azzu_generate_less_css_file_after_options_save
        add_action( 'admin_init', array( &$this, 'azzu_generate_less_css_file_after_options_save'), 11 );
         // azzu_enqueue_scripts
        add_action( 'wp_enqueue_scripts', array( &$this, 'azzu_enqueue_scripts'), 15 );
        // azzu_admin_scripts
        add_action( 'admin_enqueue_scripts', array( &$this, 'azzu_admin_scripts'), 11 );
        //ajax pagination
        add_action( 'wp_ajax_nopriv_azzu_posttype_ajax', array( &$this,'azzu_ajax_pagination_controller') );
        add_action( 'wp_ajax_azzu_posttype_ajax', array( &$this,'azzu_ajax_pagination_controller') );
        add_action( 'wp-less_stylesheet_save_post', array( &$this,'azu_vc_buildCustomColorCss' ) );
        //change wp login logo
        add_action( 'login_enqueue_scripts', array( &$this,'azu_login_logo') );
        
        add_filter( 'embed_oembed_html', array( &$this,'azu_embed_oembed_html'), 99, 4);
        add_filter( 'azu_sanitize_order', array( &$this,'azu_sanitize_order'), 15 );
        add_filter( 'azu_sanitize_orderby', array( &$this,'azu_sanitize_orderby'), 15 );
        add_filter( 'azu_sanitize_posts_per_page', array( &$this,'azu_sanitize_posts_per_page'), 15);
        add_filter( 'azu_sanitize_flag', array( &$this,'azu_sanitize_flag'), 15 );
        add_filter( 'optionsframework_interface-social_buttons', array( &$this,'azzu_themeoptions_add_share_buttons'), 15 );
        add_filter( 'comment_id_fields', array( &$this,'azzu_comment_id_fields_filter') );
        add_filter( 'azzu_widgets', array( &$this,'azu_widgets'), 15 );
        add_filter( 'azzu_shortcodes', array( &$this,'azu_shortcodes'), 15 );
        add_filter( 'azzu_post_type_azu_portfolio_args', array( &$this,'azzu_change_azu_potfolio_post_type_args') ); 
        add_filter( 'optionsframework_fields_black_list', array( &$this,'azzu_options_black_list' ) );
        //body class
        add_filter( 'body_class', array( &$this,'azzu_body_class') );      
        //change wp login logo
        add_filter( 'login_headerurl', array( &$this,'azu_login_logo_url') );
        add_filter( 'login_headertitle', array( &$this,'azu_login_logo_url_title') );
        add_filter( 'azu_widget_areas', array( &$this,'azu_widget_areas'), 15 );
        add_filter( 'azu_register_nav_menus', array( &$this,'azu_register_nav_menus'), 15 );
        add_filter( 'upload_mimes', array( &$this,'azu_custom_upload_mimes'), 99 );
        add_filter( 'azu_stylesheet_get_websafe_fonts', array( &$this,'azu_add_custom_fonts'), 15 );
        add_filter( 'azuCustomFonts', array( &$this,'azu_get_custom_fonts_face'), 15 );
        
}

/**
 * abstract functions
 */
abstract public function azzu_themeoptions_get_headers_defaults();
abstract public function azzu_themeoptions_get_font_size_defaults();
abstract public function azzu_themeoptions_get_color_defaults();
abstract public function azzu_themeoptions_get_color_group();
abstract public function azzu_themeoptions_get_typography_defaults();
abstract public function azzu_themeoptions_get_typography_group();



    // theme widget areas
    function azu_widget_areas($areas){
        $areas = array('Sidebar','Topbar','Header','Menu-right','404','Footer','Bottombar');
        if( class_exists( 'Woocommerce' )){
            $areas[] = 'Woocommerce';
        }
        return $areas;
    }
    
    // register navigation menus
    function azu_register_nav_menus($menus){
        $menus = array(
			'primary' 	=> __( 'Primary Menu', 'azzu'.LANG_DN ),
			'top'		=> __( 'Top Menu', 'azzu'.LANG_DN ),
			'bottom'	=> __( 'Bottom Menu', 'azzu'.LANG_DN ),
		);
        return $menus;
    }

/* change wordpress login logo */
function azu_login_logo() { 
        $logo_height = absint(of_get_option('header-bg-height',100));
        $logo_url = azuh()->azzu_get_logo_image ( 'header-logo', $logo_height, true );
        if ($logo_url) : ?>
            <style type="text/css">
                body.login div#login h1 a {
                    background-image: url(<?php echo esc_url($logo_url); ?>);
                    background-size: contain;
                    width: 80%;
                    height: auto;
                    min-height: 100px;
                }
            </style>
        <?php endif; 
    
}
  

/**
 * Font weight list.
 *
 * return array.
 */
public function azzu_get_font_weight_list() {
    $weight = array(
                                        "100" => '100 - Thin',
                                        "300" => '300 - Light',
                                        "400" => '400 - Normal',
                                        "400italic" => '400 - Italic',
                                        "500" => '500 - Medium',
                                        "600" => '600 - Semi Bold',
                                        "700" => '700 - Bold',
                                        "900" => '900 - Large',
                                    );
    return $weight;
}
    
/* change wordpress logo url */
function azu_login_logo_url() {
        return home_url();
}

/* change wordpress logo title */
function azu_login_logo_url_title() {
        return get_bloginfo('description');
}

    
/* Widgets list */
function azu_widgets($azzu_widgets) {
        $array_widgets = array(
                	'post-categories.php',
             		'social-icons.php',
			'contact-info.php',
			'recent-posts.php',
                        'popular-posts.php',
			'flickr.php',
                        'instagram.php',
                        'wpml.php',
                        'login.php',
                        'image.php',
                        'video.php'
		);
        if ( azu_check_custom_posttype('portfolio' ) ) 
            $array_widgets[] = 'recent-works.php';
        if ( azu_check_custom_posttype('team' ) ) 
            $array_widgets[] = 'team.php';
	if ( azu_check_custom_posttype('testimonials' ) ) 
            $array_widgets[] = 'testimonials.php';
        
        $azzu_widgets = array_merge($azzu_widgets, $array_widgets);
        return array_unique($azzu_widgets);
}

// List of shortcodes folders to include
// All folders located in /include
function azu_shortcodes($azzu_shortcodes) {
        $array_shortcodes = array(
                        'social-icons',
			'separator',
			'button',
			'blog-posts',
                        'icon',
                        'carousel',
                        'gallery',
                        'page-title',
                        'fancyblock',
                        'dropcaps',
                        'subscriber',
                        'price',
                        'single-meta'
		);
        // check plug-in active
        if ( azu_check_custom_posttype('portfolio' ) ) 
            $array_shortcodes[] = 'portfolio';
		if ( azu_check_custom_posttype('team' ) ) 
            $array_shortcodes[] = 'team';
		if ( azu_check_custom_posttype( 'testimonials' ) ) 
            $array_shortcodes[] = 'testimonials';
        
        
        $azzu_shortcodes = array_merge($azzu_shortcodes, $array_shortcodes);
        return array_unique($azzu_shortcodes);
}
    

// Config Layer slider
function azzu_layerslider_overrides() {
        // Disable auto-updates
        $GLOBALS['lsAutoUpdateBox'] = false;
}

// auto update hide bundled sliders
function azzu_revslider_autoupdate_hide() {
        //set the RevSlider Plugin as a Theme. This hides the activation notice and the activation area in the Slider Overview
        if(function_exists('set_revslider_as_theme'))
            set_revslider_as_theme();
        if(class_exists('RevSlider'))
            update_option('revSliderAsTheme', 'true');

        //Ultimate add-on's hide activation
        //if(get_option('ultimate_updater') === 'enabled') 
        //      update_option('ultimate_updater','disabled');

}


function azzu_comment_id_fields_filter( $result ) {
        $comment_buttons = '<input class="btn" name="azu-submit" type="submit" id="azu-submit" value="'.__( 'Submit', 'azzu'.LANG_DN ).'"/>';
        return $comment_buttons . $result;
}

/**
 * Change portfolio custom post type slug
 *
 * @since azzu 1.0
 * @param  array  $args Custom post type registration arguments
 * @return array        Changed arguments
 */
function azzu_change_azu_potfolio_post_type_args( $args = array() ) {

        if ( array_key_exists('rewrite', $args) && array_key_exists('slug', $args['rewrite']) ) {

                $new_slug = of_get_option( 'general-post_type_portfolio_slug', '' );
                if ( $new_slug && is_string($new_slug) ) {
                        $args['rewrite']['slug'] = trim( strtolower( $new_slug ) );
                }
        }

        return $args;
}
// does not import same options
function azzu_options_black_list( $fields = array() ) {

        $fields_black_list = array(
                // general
                'general-post_type_portfolio_slug',
                'general-custom_css',
                'general-tracking_code',
                'posttype-portfolio',
                'posttype-team',
                'posttype-testimonials',
                
                //header
                'top-bar-text',
            
                //image
                'general-favicon',
                'header-logo',
                'bottom-bar-logo',
                'title-bg-image',
                'general-boxed_bg_image',
                'top_bar-bg_image',
                'header-bg_image',
                'general-bg_image',
                'sidebar-bg_image',
                'footer-bg_image',
                'bottom_bar-bg_image',

                // bottom bar
                'bottom_bar-copyrights',

                //blog 
                'general-single-title',
                'general-single-subtitle',
                // Share buttons
                'social_buttons-post',
                'social_buttons-portfolio',
                'social_buttons-photo',
                'social_buttons-page',

                // export & import
                'import_export'
        );
        return array_unique( array_merge( $fields, $fields_black_list ) );
}

 /**
 * Check global option.
 */
function azu_get_option($name='',$default_value=''){
    if(empty($name))
        return '';
    //global $post;
    $val = azum()->get($name, $default_value); //get_post_meta( $post->ID, '_azu_'.$name,  true );
    if(empty($val) || $val=='default' || ('sidebar_wide'==$name && azum()->get('sidebar_position', 'default') == 'default'))
        $val = of_get_option($name,$default_value);
    return $val;
}


/**
 * Social buttons.
 */
function azzu_themeoptions_get_social_buttons_list() {
        return array(
                'facebook' 	=> __('Facebook', 'azzu'.LANG_DN),
                'twitter' 	=> __('Twitter', 'azzu'.LANG_DN),
                'google+' 	=> __('Google+', 'azzu'.LANG_DN),
                'pinterest' => __('Pinterest', 'azzu'.LANG_DN),
                'linkedin' => __('LinkedIn', 'azzu'.LANG_DN),
        );
}

 /**
 * compute bootsrap columns.
 */
function azzu_compute_col($col='',$options = array()){
        $default_options = array(
            'invert' => false,
            'media_empty' => true,
            'media_size' => 'col-sm-',
            'offset' => false,
            'class' => ''
        );
        
        $options = wp_parse_args( $options, $default_options );
        
        if(!empty($col)){
            if($options['invert'])
                $col = ''.(12-absint($col));
            else
                $col = ''.absint($col);
        }

        if(($options['invert'] && $options['media_empty'])){
            $options['class'] .= ' azu-nomedia';
            $col = '12';
        }
        if($col == '' || $col=='0')
            $col = '12';

        if($options['offset'] && $col == '12'){
            $options['class'] .= ' col-sm-offset-1';
            $col = '10';
        }
        
        $col = $options['media_size'].$col;
        if(!empty($options['class']))
            $col .= ' '.trim($options['class']);
        
        return $col;
}

/**
 * Add some share buttons to theme options.
 */
function azzu_themeoptions_add_share_buttons( $buttons ) {
        $theme_soc_buttons = $this->azzu_themeoptions_get_social_buttons_list();
        if ( $theme_soc_buttons && is_array( $theme_soc_buttons ) ) {
                $buttons = array_merge( $buttons, $theme_soc_buttons );
        }
        return $buttons;
}

// detect device pixel ratio
function azu_device_pixel_ratio($fit = 1.5){
        $ratio = 1; //1.5
        if ( isset($_COOKIE['devicePixelRatio']) )
            $ratio = floatval($_COOKIE['devicePixelRatio']);
        if($ratio >= 2){
            if(AZZU_MOBILE_DETECT=='1')
                $ratio = $fit;
            else
                $ratio = 2;
        }
        else if($ratio <= 1)
            $ratio = 1;
        return $ratio;
}

// screen max size
function azu_get_screen_max_size($full=false){
        if(AZZU_MOBILE_DETECT){
            $size = $defaultSize = of_get_option('azu-layout-mobile-width',AZZU_THEME_MOBILE_WIDTH);
            if(AZZU_MOBILE_DETECT=='1')
                $size = floor($defaultSize/2);
        }
        else
            $size = $defaultSize = of_get_option('azu-layout-width',1240);
        if ( !isset($_COOKIE['deviceHeight']) || !isset($_COOKIE['deviceWidth']) )
            return $size;
        $deviceHeight = absint($_COOKIE['deviceHeight']);
        $deviceWidth = absint($_COOKIE['deviceWidth']);

        if(AZZU_MOBILE_DETECT){
                $size = floor(($deviceHeight + $deviceWidth - 50)/2 );
        }
        else if($full)
            return $deviceWidth;

        if($size>$defaultSize)
            $size = $defaultSize;
        return $size;
}

// calculate image width size
function azu_calculate_image_size($column_width){
        $column_width = absint($column_width);
        $maxWidth = $this->azu_get_screen_max_size();
        if($column_width>$maxWidth)
            $column_width = $maxWidth;

        $column_width = $column_width * $this->azu_device_pixel_ratio();

        if($column_width>300)
        {
            $width_step = 50;
            if($column_width>1000)
                $width_step = 200;
            else if($column_width>550)
                $width_step = 100;

            $column_width = absint(($column_width + ($width_step/2) )/$width_step);
            $column_width = $column_width * $width_step;
        }
        return $column_width;
}

// calculate image width by columns
function azu_calculate_width_size($columns, $full=false){
        $standard_size = 300;
        $multiplier = (AZZU_MOBILE_DETECT=='0' && $full) ? 1.5 : 1.2;
        $sm_size = floor($standard_size * $multiplier);
        $column_width = $max_size = $this->azu_get_screen_max_size($full);

        if(AZZU_MOBILE_DETECT=='1' && $columns > 2)
            $columns = 2;

        if($columns > 1){
            if($max_size > $sm_size)
                $column_width = round($column_width/$columns);

            if($max_size<$sm_size && $max_size>$column_width)
                $column_width = $max_size;
            else if($max_size > $sm_size && $sm_size > $column_width)
                $column_width = $sm_size;
        }

        if($column_width < $standard_size)
            $column_width = $standard_size;

        return $column_width;
}

 /**
 * dynamic stylesheets list.
 *
 * @return array
 */
function azzu_get_dynamic_stylesheets_list() {

        static $dynamic_stylesheets = null;

        if ( null === $dynamic_stylesheets ) {

                $template_uri = get_template_directory_uri();
                $theme_version = wp_get_theme()->get( 'Version' );

                $dynamic_stylesheets = array();
                if(defined('AZU_ALWAYS_REGENERATE_DYNAMIC_CSS') && AZU_ALWAYS_REGENERATE_DYNAMIC_CSS)
                        $dynamic_stylesheets['bootstrap-theme'] = array(
                                                'path' => AZZU_UI_DIR . '/'.AZZU_DESIGN.'/less/only_one.less',
                                                'src' => AZZU_UI_URI . '/'.AZZU_DESIGN.'/less/only_one.less',
                                                'fallback_src' => $template_uri . '/css/theme.css',
                                                'deps' => array(),
                                                'ver' => $theme_version,
                                                'media' => 'all'
                        );
                $dynamic_stylesheets['azu-custom.less'] = array(
                                                'path' => AZZU_UI_DIR . '/'.AZZU_DESIGN.'/less/core.less',
                                                'src' => AZZU_UI_URI . '/'.AZZU_DESIGN.'/less/core.less',
                                                'fallback_src' => $template_uri . '/css/design-compiled.css',
                                                'deps' => array(),
                                                'ver' => $theme_version,
                                                'media' => 'all'
                        );

        }

        return $dynamic_stylesheets;
}

 /**
 * Visual composer css build.
 *
 * @return n/a
 */
 function azu_vc_buildCustomColorCss( ) {
     if(defined('AZU_ALWAYS_REGENERATE_DYNAMIC_CSS') && AZU_ALWAYS_REGENERATE_DYNAMIC_CSS){
            /** WordPress Template Administration API */
            require_once( ABSPATH . 'wp-admin/includes/template.php');
            /** WordPress Administration File API */
            require_once( ABSPATH . 'wp-admin/includes/file.php');

            if(class_exists('Vc_Settings')){
                /** Visual composer settings class */
                require_once( WP_PLUGIN_DIR . '/js_composer/include/classes/settings/class-vc-settings.php');
                $defaults = array(
                    'margin' => intval(get_option(AZZU_VC.'margin')),
                    'gutter' => get_option(AZZU_VC.'gutter'),
                    'responsive_max' => get_option(AZZU_VC.'responsive_max')
                );
                $options = array(
                    'margin' => of_get_option('vc-bottom-margin',35),
                    'gutter' => of_get_option('general-gutter-width',AZZU_THEME_GUTTER),
                    'responsive_max' => of_get_option('azu-layout-mobile-width',AZZU_THEME_MOBILE_WIDTH)
                );

                if($defaults != $options){
                    $this->azu_vc_settings($options);
                    $vc = new Vc_Settings();
                    $vc->initAdmin();
                    $vc->buildCustomColorCss();
                }
            }
     }
 } 

 /**
 * Visual composer settings.
 *
 * @return n/a
 */
 function azu_vc_settings( $options = array(),$isdelete = false) {
         $defaults = array(
                'use_custom' => '1',
                'vc_color' => '#f7f7f7',
                'vc_color_hover' => '#F0F0F0',
                'vc_color_call_to_action_bg' => '#ffffff',
                'vc_color_google_maps_bg' => '#ffffff',
                'vc_color_post_slider_caption_bg' => '#ffffff',
                'vc_color_progress_bar_bg' => '#ffffff',
                'vc_color_separator_border' => '#dadada',
                'vc_color_tab_bg' => '#ffffff',
                'vc_color_tab_bg_active' => '#ffffff',
                'margin' => '35',
                'gutter' => AZZU_THEME_GUTTER,
                'responsive_max' => AZZU_THEME_MOBILE_WIDTH
         );
         $options = wp_parse_args( $options, $defaults );

         foreach($options as $id=>$value) {
             if($isdelete)
                 delete_option(AZZU_VC.$id);
             else
                 update_option(AZZU_VC.$id, $value);
         }
 }    


/**
 * Update custom.less stylesheet.
*
*/
function azzu_generate_less_css_file_after_options_save() {
	$css_is_writable = get_option( 'azzu_less_css_is_writable' );

//	if ( isset($_GET['page']) && 'options-framework' == $_GET['page'] && !$css_is_writable ) {
//		return;
//	}

	$set = get_settings_errors('options-framework');
	if ( !empty( $set ) ) {
                $this->azzu_generate_less_css_list(false);

		if ( $css_is_writable ) 
                {
			add_settings_error( 'azzu-wp-less', 'save_stylesheet', _x( 'Stylesheet saved.', 'backend', 'azzu'.LANG_DN ), 'updated fade' );
		}
	}

}

// filter of of_get_option
public static function azzu_options_filter($f_options=array(), $name = ''){
        $atts = azum()->get('azzu_builder_array',array());
        if ( array_key_exists($name, $atts) )
                $f_options[$name] = $atts[$name];
        return $f_options;
}

// filter of less builder option
public static function azzu_builder_filter($f_options=array()){
        $atts = azum()->get('azzu_builder_array',array());
        $out = array();
        foreach($f_options as $name => $default) {
                    if ( array_key_exists($name, $atts) )
                            $out[$name] = $atts[$name];
                    else
                            $out[$name] = $default;
            }
        return $out;
}

 /**
 * Customizer refresh options
 *
 * @return n/a
 */
function azzu_customizer_refresh_options(){
        if(isset($_POST['wp_customize']) && $_POST['wp_customize']=='on')
        {
                $options = isset($_POST['customized']) ? json_decode(stripslashes($_POST['customized']), true) : false;
                //echo "Stage 1: Mem usage is: ", memory_get_usage(), "\n";
                if(is_array($options))
                {
                        $option_array = array();
                        foreach($options as $i => $val){
                            $key = $i;

                            if(is_string($i) && strlen($i) > 0 && substr($i, -1) === ']') 
                            {
                                $option_id = str_replace("]", "", $key);
                                $option_id = preg_split( '/\[/', $option_id );
                                if(count($option_id)>1)
                                    $key = $option_id[1];
                            }
                            $option_array[$key] = $val;

                            if(!empty($val) && is_string($val)) 
                            {
                                $val = trim($val);
                                if(strlen($val) > 0 && substr($val, 0, 1) === '{' && substr($val, -1) === '}'){
                                    $option_array[$key] = json_decode($val, true);
                                    if($option_array[$key] === null)
                                        $option_array[$key] = $val;
                                }
                            }
                        }
                        unset($options);
                        azum()->set('azzu_builder_array',$option_array);

                        // add filter for theme options here
                        add_filter( 'azu_of_get_option', array( 'azu_functions','azzu_options_filter'), 15, 2 );
                        add_filter( 'azzu_before_builder_option', array( 'azu_functions', 'azzu_builder_filter') );
                        // dirty hack of same defaults
                        $re_init = azum()->get('attr');
                        $re_init['image_size'] = absint(of_get_option('general-blog-image-size',12));
                        $re_init['column_width'] = azuf()->azu_calculate_width_size($re_init['image_size']==12 ? 1 : 2);
                        azum()->set('attr',$re_init);
                }   
        }
}


//generate new css file by new option after customizer saved
public static function azzu_generate_less_css_file_after_customizer_save() {
        $css_is_writable = get_option( 'azzu_less_css_is_writable' );
        if( $GLOBALS['pagenow']=='admin-ajax.php' && $css_is_writable) //
        {
            of_get_option(null);
            azuf()->azzu_generate_less_css_list(false);
        }
}


/**
 * Update custom.less stylesheet.
*/
function azzu_generate_less_css_file( $handler = 'azu-custom.less', $src = '' ) {

	/**
	 * Include WP-Less.
	 *
	 */
	require_once( AZZU_LIBRARY_DIR . '/wp-less/bootstrap-for-theme.php' );

	// WP-Less init
	if ( class_exists('WPLessPlugin') ) {
		$less = WPLessPlugin::getInstance();
		$less->dispatch();
	}

	/**
	 * Less helpers.
	 *
	 * @since azzu 1.0
	 */
	require_once( AZZU_FUNCTION_DIR . '/less-functions.php' );

	/**
	 * Less variables.
	 *
	 * @since azzu 1.0
	*/
	require_once( AZZU_FUNCTION_DIR . '/less-builder-function.php' );

	// $less = WPLessPlugin::getInstance();
	$config = $less->getConfiguration();

	if ( !wp_style_is($handler, 'registered') ) {

		if ( !$src ) {
			$src = AZZU_UI_URI . '/'.AZZU_DESIGN.'/less/core.less';
		}

		wp_register_style( $handler, $src );
	}

	// save options
	$options = azzu_compile_less_vars();

	if ( $options ) {
		$less->setVariables( $options );
	}

	WPLessStylesheet::$upload_dir = $config->getUploadDir();
	WPLessStylesheet::$upload_uri = $config->getUploadUrl();
        
	return $less->processStylesheet( $handler, true );
}


/**
 * azzu_generate_less_css_list.
*/
function azzu_generate_less_css_list($print=true, $must_compile=true){

	$dynamic_stylesheets = $this->azzu_get_dynamic_stylesheets_list();
        
	foreach ( $dynamic_stylesheets as $stylesheet_handle=>$stylesheet ) {

		$stylesheet_path_hash = md5( $stylesheet['path'] );
		$stylesheet_cache_name = 'wp_less_stylesheet_data_' . $stylesheet_path_hash;
		$stylesheet_cache = get_option( $stylesheet_cache_name );
                
		// regenerate less files if needed
		if ( $must_compile || ( !$stylesheet['fallback_src'] && !$stylesheet_cache ) ) 
                {
			$this->azzu_generate_less_css_file( $stylesheet_handle, $stylesheet['src'] )->getTargetUri();
			$azu_ajax_only_css = (isset($_POST['wp_customize']) && $_POST['wp_customize']=='on' && !(isset($_POST['action']) && $_POST['action'] =='customize_save'));
			if($azu_ajax_only_css)
                            $stylesheet_cache_name .= '_ajax';
                        $stylesheet_cache = get_option( $stylesheet_cache_name );
		}
		// enqueue stylesheets
                if($print)
                    $this->azzu_enqueue_dynamic_style( array( 'handle' => $stylesheet_handle, 'cache' => $stylesheet_cache, 'stylesheet' => $stylesheet ) );
	}
}

/**
 * Enqueue *.less files
*/
function azzu_enqueue_dynamic_stylesheets(){
        $must_compile=false;
        if( defined('AZU_ALWAYS_REGENERATE_DYNAMIC_CSS') && AZU_ALWAYS_REGENERATE_DYNAMIC_CSS && current_user_can( 'edit_theme_options' ) )
            $must_compile=true;
        else if(isset($_POST['wp_customize']) && $_POST['wp_customize']=='on' && isset($_POST['customized']) && $_POST['customized'] !='{}')
            $must_compile=true;
        $this->azzu_generate_less_css_list(true, $must_compile);
	do_action( 'azzu_enqueue_dynamic_stylesheets' );
}



/**
 * Return string CSS or Create compiled CSS file.
*/
function azzu_enqueue_dynamic_style( $args = array() ) {
        $out ='';
	$stylesheet = empty( $args['stylesheet'] ) ? array() : $args['stylesheet'];
	$handle = empty( $args['handle'] ) ? '' : $args['handle'];
        
	if ( empty( $stylesheet ) || empty( $handle )) {
		return $out;
	}

	$stylesheet_cache = empty( $args['cache'] ) ? array() : $args['cache'];

	// less stylesheet
        if ( !empty($stylesheet_cache['compiled']) ) {
                // print custom css inline
		$out = $stylesheet_cache['compiled'];
		wp_add_inline_style( 'bootstrap-theme', $out );
	}
	elseif ( get_option( 'azzu_less_css_is_writable' ) && isset($stylesheet_cache['target_uri']) ) {

		$out = set_url_scheme( $stylesheet_cache['target_uri'], is_ssl() ? 'https' : 'http' );
                wp_enqueue_style( $handle, $out, $stylesheet['deps'], $stylesheet['ver'], $stylesheet['media'] );

	}  elseif ( !empty($stylesheet['fallback_src']) ) {
                $out = $stylesheet['fallback_src'];
		// load skin precompiled css
                wp_enqueue_style( $handle, $out, $stylesheet['deps'], $stylesheet['ver'], $stylesheet['media'] );
	}
}


/**
 * Enqueue scripts and styles.
*/
function azzu_enqueue_scripts() {

	// enqueue web fonts if needed
        azuh()->azzu_enqueue_web_fonts();
        
	$theme_version = wp_get_theme()->get( 'Version' );
	$template_uri = get_template_directory_uri();
	
        //icon fonts
	wp_enqueue_style( 'azu-fontello', AZZU_UI_URI.'/'.AZZU_DESIGN . '/css/fontello.min.css', array(), $theme_version );
        //global $wp_styles;
        //wp_enqueue_style( 'azu-fontello-ie', $template_uri . '/css/fontello-ie7.css', array(), $theme_version );
        //$wp_styles->add_data( 'azu-fontello-ie', 'conditional', 'lt IE 8' );
        
        if(!defined('AZU_ALWAYS_REGENERATE_DYNAMIC_CSS') || !AZU_ALWAYS_REGENERATE_DYNAMIC_CSS)
            wp_enqueue_style( 'bootstrap-theme', $template_uri . '/css/theme.css', array(), $theme_version );
        
	$this->azzu_enqueue_dynamic_stylesheets();
        

        wp_enqueue_style( 'style', get_stylesheet_uri(), array(), $theme_version );

        // in header
        wp_enqueue_script( 'azu-modernizr', $template_uri . '/js/modernizr.js', array( 'jquery' ), $theme_version );
	wp_localize_script( 'azu-modernizr', 'azuGlobals', array(
            'theme' => wp_get_theme()->get( 'Name' ), 
            'IsMobile'  => AZZU_MOBILE_DETECT,
            'burger_menu_back' => __('back', 'azzu'.LANG_DN),
            'burger_width' => of_get_option('menu-side-width',300),
            'mobile_width' => of_get_option('azu-layout-mobile-width',AZZU_THEME_MOBILE_WIDTH),
            'menualign'	=> (of_get_option('header-menu_alignment','left')=='left' && of_get_option('header-layout','left') =='side') ? 'left' : 'right')
        );
        
        // before </body> tag
        if(WP_DEBUG) {
            wp_enqueue_script( 'azu-library', $template_uri . '/js/library.js', array( 'jquery' ), AZZU_VERSION, true );
            wp_enqueue_script( 'azu-main', $template_uri . '/js/theme.js', array( 'jquery','azu-library' ), AZZU_VERSION, true );
        }
        else {
            wp_enqueue_script( 'azu-main', $template_uri . '/js/theme.min.js', array( 'jquery' ), AZZU_VERSION, true );
        }
        // sub js each theme
        wp_enqueue_script( 'azu-sub', AZZU_UI_URI . '/' . AZZU_DESIGN .'/js/sub.js', array( 'jquery','azu-main' ), AZZU_VERSION, true );
        
        global $post;
        
        $azu_local = array(
                'passText'		=> __('To view this protected post, enter the password below:', 'azzu'.LANG_DN),
                'moreButtonAllLoadedText' => __('No more post', 'azzu'.LANG_DN),
                'postID'		=> empty( $post->ID ) ? null : $post->ID,
                'ajaxurl'		=> admin_url( 'admin-ajax.php' ),
                'gutter_width'          => of_get_option('general-gutter-width',AZZU_THEME_GUTTER),
                'contactNonce'	=> wp_create_nonce('azu_contact_form'),
                'menutype'	=> of_get_option('header-layout','left'),
                'floatingMenu' => apply_filters( 'azzu_floating_menu', false ) || of_get_option('header-layout','left') == 'side' ? 0 : of_get_option('header-show_floating_menu', 'off'),
                'ajaxNonce'		=> wp_create_nonce('azzu-posts-ajax')
        );

        // add some additional data
        wp_localize_script( 'azu-main', 'azuLocal', $azu_local );

        // wordpress comments reply script
        if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
                wp_enqueue_script( 'comment-reply' );
        }
        
        $custom_css = of_get_option( 'general-custom_css', '' );
        if ( $custom_css ) {
                wp_add_inline_style( 'style', $custom_css );
        }
        //blankpage custom background css
        if ( azum()->get('page_override') ) {
            $class_name = 'page';
            $style_background = '';
            $style_color = azum()->get('page_bg_color');
            $style_image = azum()->get('page_bg_image');
            $style_repeat = azum()->get('page_bg_repeat');
            if(azuf()->azu_get_option('general-layout')=='wide')
                $class_name = 'azu-content';
            if(!empty($style_color) && strpos($style_color,'#') !== false)
                $style_background .= 'background-color: '.esc_attr($style_color).';';
            if(absint($style_image) > 0){
                $style_image = azuf()->azu_get_uploaded_logo(array('',$style_image));
                $style_background .= sprintf( 'background-image: url("%s");
                    background-repeat: %s; 
                    background-position: %s %s;
                    background-size: %s;',
                        esc_url(azuf()->azu_get_of_uploaded_image($style_image[0])),
                        $style_repeat,
                        esc_attr(azum()->get('page_bg_position_x')),
                        esc_attr(azum()->get('page_bg_position_y')),
                        azum()->get('page_bg_fullscreen') ? 'cover' : ($style_repeat=='no-repeat' ? '100% auto' :'auto')
                        );
            }
            if(!empty($style_background))
                wp_add_inline_style( 'style', '.'.$class_name.'{'.$style_background.'}' );
        }
}


/****************************************************************
// AJAX PAGINATION
*****************************************************************

/**
 * Ajax pagination controller.
 *
 */
function azzu_ajax_pagination_controller() {

	$ajax_data = array(
		'nonce' => isset($_POST['nonce']) ? $_POST['nonce'] : false,
		'post_id' => isset($_POST['postID']) ? absint($_POST['postID']) : false,
		'post_paged' => isset($_POST['paged']) ? absint($_POST['paged']) : false,
		'target_page' => isset($_POST['targetPage']) ? absint($_POST['targetPage']) : false,
		'ajaxarray' => isset($_POST['ajaxarray']) ? $_POST['ajaxarray'] : false,
		'term' => isset($_POST['term']) ? $_POST['term'] : '',
		'orderby' => isset($_POST['orderby']) ? $_POST['orderby'] : '',
		'order' => isset($_POST['order']) ? $_POST['order'] : '',
		'loaded_items' => isset($_POST['visibleItems']) ? array_map('absint', $_POST['visibleItems']) : array(),
		'contentType' => isset($_POST['contentType']) ? $_POST['contentType'] : '',
                'post_type' => 'post',
                'taxonomy' => 'category',
                'sender' => isset($_POST['sender']) ? $_POST['sender'] : ''
	);

	$responce = array( 'success' => false, 'reason' => 'undefined posttype' );

	switch( $ajax_data['contentType'] ) {
                case "blog":
                    $responce = Azzu_Custom_Post_Type::get_ajax_content( $ajax_data ); break;
                    break;
                case "portfolio":
                    $ajax_data['post_type'] = 'azu_portfolio';
                    $ajax_data['taxonomy'] = 'azu_portfolio_category';
                    $responce = Azzu_Custom_Post_Type::get_ajax_content( $ajax_data ); break;
                    break;
                case "team":
                    $ajax_data['post_type'] = 'azu_team';
                    $ajax_data['taxonomy'] = 'azu_team_category';
                    $responce = Azzu_Custom_Post_Type::get_ajax_content( $ajax_data ); break;
                    break;
                case "testimonials":
                    $ajax_data['post_type'] = 'azu_testimonials';
                    $ajax_data['taxonomy'] = 'azu_testimonials_category';
                    $responce = Azzu_Custom_Post_Type::get_ajax_content( $ajax_data ); break;
                    break;
		default: break;
	}

	$responce = json_encode( $responce );

	// responce output
	header( "Content-Type: application/json" );
	echo $responce;

	// IMPORTANT: don't forget to "exit"
	exit;
}


/**
 * Get default image.
*
* Return array( 'url', 'width', 'height' );
*
* @return array.
*/
function azzu_get_default_image() {
	return array( AZZU_THEME_URI . '/images/noimage.jpg', 1000, 1000 );
}



/**
 * Prepare array with widgetareas options.
*
*/
function azzu_get_widgetareas_options() {
	$widgetareas_list = array();
        
        $widgetareas_stored = get_theme_mod('azu-widget-areas',array());
             
        foreach ( array('Sidebar','Footer') as $value ) {
            $widgetareas_list[strtolower(AZU_WIDGET_PREFIX . sanitize_key($value))] = $value;
        }
        
	if ( is_array($widgetareas_stored) ) {
		foreach ( $widgetareas_stored as $op ) {
			$widgetareas_list[ sanitize_key($op) ] = $op;
		}
	}

	return $widgetareas_list;
}

 // azzu_get_widgetareas_options


/**
 * Return social icons array( 'class', 'title' ).
*
*/
function azzu_get_social_icons_data() {
	return array(
			'facebook'		=> __('Facebook', 'azzu'.LANG_DN),
			'twitter'		=> __('Twitter', 'azzu'.LANG_DN),
			'google'		=> __('Google+', 'azzu'.LANG_DN),
                        'youtube'		=> __('YouTube', 'azzu'.LANG_DN),
                        'linkedin'		=> __('Linkedin', 'azzu'.LANG_DN),
                        'pinterest'		=> __('Pinterest', 'azzu'.LANG_DN),
                        'skype'			=> __('Skype', 'azzu'.LANG_DN),
                        'instagram'		=> __('Instagram', 'azzu'.LANG_DN),
                        'tumblr'		=> __('Tumblr', 'azzu'.LANG_DN),
			'dribbble'		=> __('Dribbble', 'azzu'.LANG_DN),
			'behance'		=> __('Behance', 'azzu'.LANG_DN),
                        'vimeo'			=> __('Vimeo', 'azzu'.LANG_DN),
			'rss'			=> __('Rss', 'azzu'.LANG_DN),
			'delicious'		=> __('Delicious', 'azzu'.LANG_DN),
			'flickr'		=> __('Flickr', 'azzu'.LANG_DN),
			'forrst'		=> __('Forrst', 'azzu'.LANG_DN),
			'lastfm'		=> __('Lastfm', 'azzu'.LANG_DN),
			'deviantart'		=> __('Deviantart', 'azzu'.LANG_DN),
			'github'		=> __('Github', 'azzu'.LANG_DN),
			'stumbleupon'	=> __('Stumbleupon', 'azzu'.LANG_DN),
			'mail'			=> __('Mail', 'azzu'.LANG_DN),
			'website'		=> __('Website', 'azzu'.LANG_DN),
			'500px'		=> __('500px', 'azzu'.LANG_DN),
			'vkontakte'			=> __('VK', 'azzu'.LANG_DN),
			'foursquare'	=> __('Foursquare', 'azzu'.LANG_DN),
			'xing'			=> __('XING', 'azzu'.LANG_DN),
			'weibo'			=> __('Weibo', 'azzu'.LANG_DN),
                        'swarm'			=> __('Swarm', 'azzu'.LANG_DN),
                        'phone'			=> __('Phone', 'azzu'.LANG_DN),
	);
}

 // azzu_get_social_icons_data




/**
 * Templates list.
*/
function azzu_themeoptions_get_template_list(){
        $social_list = array(
			'post' 				=> _x('Social buttons in blog posts', 'theme-options', 'azzu'.LANG_DN),
			'photo' 			=> _x('Social buttons in gallery & carousel', 'theme-options', 'azzu'.LANG_DN),
			'page' 				=> _x('Social buttons in page', 'theme-options', 'azzu'.LANG_DN),
	);
                
        if ( azu_check_custom_posttype('portfolio' ) ) {
                $social_list['portfolio'] = _x('Social buttons in portfolio', 'theme-options', 'azzu'.LANG_DN);
        }
        if ( class_exists( 'Woocommerce' ) ) {
                $social_list['woocommerce'] = _x('Social buttons in woocommerce', 'theme-options', 'azzu'.LANG_DN);
        }
	return $social_list;
}

 // azzu_themeoptions_get_template_list




/**
 * Add metaboxes scripts and styles.
*/
function azzu_admin_scripts( $hook ) {
        wp_enqueue_style( 'azu-admin-general', AZZU_OPTIONS_URI . '/assets/css/admin_general.css' );
        
	if ( !in_array( $hook, array( 'post-new.php', 'post.php' ) ) ) {
		return;
	}

	wp_enqueue_style( 'azu-metabox', AZZU_OPTIONS_URI . '/assets/css/admin_metabox.css' );

	wp_enqueue_script( 'azu-metabox', AZZU_OPTIONS_URI . '/assets/js/admin_metabox.js', array('jquery'), false, true );

}




/**
 * Add theme speciffik classes to body.
 *
 * @since azzu 1.0
 */
function azzu_body_class( $classes ) {
        global $post;


        $template = azum()->get('template');
        $layout = azum()->get('layout');

        // template classes
        switch ( $template ) {
                case 'blog':
                        $classes[] = 'blog';
                        break;
                default: 
                    $classes[] = 'one-page-row';
                    if ( !azum()->get('page_override') ) { $classes[] = 'is-scroll'; } 
                    break;
        }
        // check mobile to CSS
        if(AZZU_MOBILE_DETECT == '1')
            $classes[] = 'azu-mobile azu-phone';
        else if(AZZU_MOBILE_DETECT == '2')
            $classes[] = 'azu-mobile azu-tablet';

        if ( of_get_option( 'general-layout', 'wide' ) != 'wide' )
            $classes[] = 'azu-boxed';
        
        $classes[] = 'azu-content-style-'.of_get_option( 'general-layout-style' , 'none'  );
        $classes[] = 'azu-main-hover-'.of_get_option( 'hover-style' , 'none'  );
        $classes[] = 'azu-nav-hover-style-'.of_get_option( 'header-hover_style' , 'text'  );
        
        if ( of_get_option( 'general-hover_icon', 1 ) )
            $classes[] = 'general-hover-icon-on';
        
        $classes[] = 'azu-nav-style-'.of_get_option( 'menu-item-style' , 'none'  );
        $classes[] = 'azu-nav-icon-position-'.of_get_option( 'menu-image-position' , 'left'  );
        $classes[] = 'azu-nav-menu-alignment-'.of_get_option( 'header-menu_alignment' , 'left'  );
        $classes[] = 'azu-header-layout-'.of_get_option( 'header-layout' , 'left'  );
        $classes[] = 'azu-menu-ct-'.of_get_option( 'header-caret-style' , 'none'  );
        
        if ( of_get_option( 'header-submenu_next_level_indicator', 1 ) )
            $classes[] = 'azu-submenu-next-level-ind';
        
        if ( of_get_option( 'general-scrollbar', 1 ) )
            $classes[] = 'azu-scroll-bar-style-on';
        $classes[] = 'azu-page-header-'.of_get_option( 'general-title_align' , 'center'  );
        
        $classes[] = 'azu-divider-'.of_get_option( 'general-thin_divider_style' , 'style-1'  );
        $classes[] = 'azu-btn-'.of_get_option('general-button-style','default');
        
        // Sticky sidebar
        $page_sticky = azum()->get('sidebar_sticky', 'global');
        if( ( $page_sticky == 'global' && of_get_option('sidebar_sticky',1)) || $page_sticky == 'on'){
            $classes[] = 'azu-sticky-js';
        }
        
        // remove top & bottom padding of main content
        if ( !azum()->get('general_padding') )
            $classes[] = 'azu-general-padding';
        // layout classes
        switch ( $layout ) {
                case 'masonry':
                        $classes[] = 'layout-masonry';
                        break;
                case 'grid':
                        $classes[] = 'layout-grid';
                        break;
                case 'checkerboard':
                case 'list': $classes[] = 'layout-list'; break;
        }

        $classes = apply_filters( 'azzu_body_class', $classes );
        
        return array_values( array_unique( $classes ) );
}


/**
 * Constrain dimensions helper.
 *
 * @param $w0 int
 * @param $h0 int
 * @param $w1 int
 * @param $h1 int
 * @param $change boolena
 *
 * @return array
 */
function azu_constrain_dim( $w0, $h0, &$w1, &$h1, $change = false ) {
	$prop_sizes = wp_constrain_dimensions( $w0, $h0, $w1, $h1 );

	if ( $change ) {
		$w1 = $prop_sizes[0];
		$h1 = $prop_sizes[1];
	}
	return array( $w1, $h1 );
}

/**
 * Resize image to speciffic dimetions.
 *
 * Magick - do not touch!
 *
 * Evaluate new width and height.
 * $img - image meta array ($img[0] - image url, $img[1] - width, $img[2] - height).
 * $opts - options array, supports w, h, zc, a, q.
 *
 * @param array $img
 * @param 
 * @return array
 */
function azu_get_resized_img( $img, $opts ) {

	$opts = apply_filters( 'azu_get_resized_img-options', $opts, $img );

	if ( !is_array( $img ) || !$img || (!$img[1] && !$img[2]) ) {
		return false;
	}

	if ( !is_array( $opts ) || !$opts ) {

		if ( !isset( $img[3] ) ) {

			$img[3] = image_hwstring( $img[1], $img[2] );
		}

		return $img;
	}


	$defaults = array( 'w' => 0, 'h' => 0 , 'zc' => 1, 'z'	=> 1 );
	$opts = wp_parse_args( $opts, $defaults );

	$w = absint( $opts['w'] );
	$h = absint( $opts['h'] );

	// If zoomcropping off and image smaller then required square
	if ( 0 == $opts['zc'] && ( $img[1] <= $w  && $img[2] <= $h ) ) {

		return array( $img[0], $img[1], $img[2], image_hwstring( $img[1], $img[2] ) );

	} elseif ( 3 == $opts['zc'] || empty ( $w ) || empty ( $h ) ) {

		if ( 0 == $opts['z'] ) {
			$this->azu_constrain_dim( $img[1], $img[2], $w, $h, true );
		} else {
			$p = absint( $img[1] ) / absint( $img[2] );
			$hx = absint( floor( $w / $p ) ); 
			$wx = absint( floor( $h * $p ) );
			
			if ( empty( $w ) ) {
				$w = $wx;
			} else if ( empty( $h ) ) {
				$h = $hx;
			} else {
				if ( $hx < $h && $wx >= $w ) {
					$h = $hx;
				} elseif ( $wx < $w && $hx >= $h ) {
					$w = $wx;
				}
			}
		}

		if ( $img[1] == $w && $img[2] == $h ) {
			return array( $img[0], $img[1], $img[2], image_hwstring( $img[1], $img[2] ) );
		}

	}

	$img_h = $h;
	$img_w = $w;

	if ( 1 == $opts['zc'] ) {

		if ( $img[1] >= $img_w && $img[2] >= $img_h ) {

			// do nothing

		} else if ( $img[1] <= $img[2] && $img_w >= $img_h ) { // img=portrait; c=landscape

			$cw_new = $img[1];
			$k = $cw_new/$img_w;
			$ch_new = $k * $img_h;

		} else if ( $img[1] >= $img[2] && $img_w <= $img_h ) { // img=landscape; c=portrait

			$ch_new = $img[2];
			$k = $ch_new/$img_h;
			$cw_new = $k * $img_w;

		} else {

			$kh = $img_h/$img[2];
			$kw = $img_w/$img[1];
			$kres = max( $kh, $kw );
			$ch_new = $img_h/$kres;
			$cw_new = $img_w/$kres;

		}

		if ( isset($ch_new, $cw_new) ) {
			$img_h = absint(floor($ch_new));
			$img_w = absint(floor($cw_new));
		}

	}

	$file_url = aq_resize( $img[0], $img_w, $img_h, true, true, false );

	if ( !$file_url ) {
		$file_url = $img[0];
	}

	return array(
		$file_url,
		$w,
		$h,
		image_hwstring( $w, $h )
	);
}

/**
 * DT master get image function. 
 *
 * @param $opts array
 *
 * @return string
 */
function azu_get_thumb_img( $opts = array() ) {
	global $post;

	$default_image = $this->azzu_get_default_image();

	$defaults = array(
		'wrap'			=> '<a %HREF% %CLASS% %TITLE% %CUSTOM%><img %SRC% %IMG_CLASS% %SIZE% %ALT% %IMG_TITLE% /></a>',
		'class'         	=> '',
		'alt'			=> '',
		'title'         	=> '',
		'custom'        	=> '',
		'img_class'     	=> '',
		'img_title'			=> '',
		'img_description'	=> '',
		'img_caption'		=> '',
		'href'				=> '',
		'img_meta'      	=> array(),
		'img_id'			=> 0,
		'options'    		=> array(),
		'default_img'		=> $default_image,
		'prop'				=> false,
		'echo'				=> true
	);
	$opts = wp_parse_args( $opts, $defaults );
	$opts = apply_filters('azu_get_thumb_img-args', $opts);

        if(isset($opts['options']['w']) && !isset($opts['options']['h']))
            $opts['options']['w'] = $this->azu_calculate_image_size($opts['options']['w']);
        
	$original_image = null;
	if ( $opts['img_meta'] ) {
		$original_image = $opts['img_meta'];
	} elseif ( $opts['img_id'] ) {
		$original_image = wp_get_attachment_image_src( $opts['img_id'], 'full' );
	}

	if ( !$original_image ) {
		$original_image = $opts['default_img'];
	}

	// proportion
	if ( $original_image && !empty($opts['prop']) && ( empty($opts['options']['h']) || empty($opts['options']['w']) ) ) {
		$_prop = $opts['prop'];
		$_img_meta = $original_image;

		if ( $_prop > 1 ) {
			$h = intval(round($_img_meta[1] / $_prop));
			$w = intval(round($_prop * $h));
		} else if ( $_prop < 1 ) {
			$w = intval(round($_prop * $_img_meta[2]));
			$h = intval(round($w / $_prop));
		} else {
			$w = $h = min($_img_meta[1], $_img_meta[2]);
		}

		if ( !empty($opts['options']['w']) ) {
			$__prop = $h / $w;
			$w = intval($opts['options']['w']);
                        $h = intval(round($__prop * $w));
		} else if ( !empty($opts['options']['h']) ) {
			$__prop = $w / $h;
			$h = intval($opts['options']['h']);
			$w = intval(round($__prop * $h));
		}

		$opts['options']['w'] = $w;
		$opts['options']['h'] = $h;
	}

	if ( $opts['options'] ) {
		$resized_image = $this->azu_get_resized_img( $original_image, $opts['options'] );
	} else {
		$resized_image = $original_image;
	}

	if ( $img_id = absint( $opts['img_id'] ) ) {

		if ( '' === $opts['alt'] ) {
			$opts['alt'] = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
		}

		if ( '' === $opts['img_title'] && azuh()->azzu_image_title_is_hidden( $img_id ) ) {
			$opts['img_title'] = get_the_title( $img_id );
		}
	}

	$class = empty( $opts['class'] ) ? '' : 'class="' . esc_attr( trim($opts['class']) ) . '"';
	$title = empty( $opts['title'] ) ? '' : 'title="' . esc_attr( trim($opts['title']) ) . '"';
	$img_title = empty( $opts['img_title'] ) ? '' : 'title="' . esc_attr( trim($opts['img_title']) ) . '"';
	$img_class = empty( $opts['img_class'] ) ? '' : 'class="' . esc_attr( trim($opts['img_class']) ) . '"';

	$href = $opts['href'];
	if ( !$href ) {
		$href = $original_image[0];
	}

	$src = $resized_image[0];

	if ( empty($resized_image[3]) || !is_string($resized_image[3]) ) {
		$size = image_hwstring($resized_image[1], $resized_image[2]);
	} else {
		$size = $resized_image[3];
	}

	$src = str_replace( array(' '), array('%20'), $src );

	$output = str_replace(
		array(
			'%HREF%',
			'%CLASS%',
			'%TITLE%',
			'%CUSTOM%',
			'%SRC%',
			'%IMG_CLASS%',
			'%SIZE%',
			'%ALT%',
			'%IMG_TITLE%',
			'%RAW_TITLE%',
			'%RAW_ALT%',
			'%RAW_IMG_TITLE%',
			'%RAW_IMG_DESCRIPTION%',
			'%RAW_IMG_CAPTION'
		),
		array(
			'href="' . esc_url( $href ) . '"',
			$class,
			$title,
			strip_tags( $opts['custom'] ),
			'src="' . esc_url( $src ) . '"',
			$img_class,
			$size,
			'alt="' . esc_attr( $opts['alt'] ) . '"',
			$img_title,
			esc_attr( $opts['title'] ),
			esc_attr( $opts['alt'] ),
			esc_attr( $opts['img_title'] ),
			esc_attr( $opts['img_description'] ),
			esc_attr( $opts['img_caption'] )
		),
		$opts['wrap']
	);

	if ( $opts['echo'] ) {
		echo $output;
		return '';
	}

	return $output;
}


/**
 * Description here.
 *
 * @param $src string
 *
 * @return string
 *
 * @since azzu 1.0
 */
function azu_get_of_uploaded_image( $src ) {
	if ( ! $src ) {
		return '';
	}

	$uri = $src;
	if ( ! parse_url( $src, PHP_URL_SCHEME ) ) {

		$uploads = wp_upload_dir();
		$baseurl = str_replace( site_url(), '', $uploads['baseurl'] );

		$pattern = '/wp-content/';
		if (  strpos( $src, $baseurl ) !== false || strpos( $src, $pattern ) !== false ) {

			$uri = site_url( $src );
		} else {

			$uri = get_template_directory_uri() . $src;
		}
	}

	return $uri;
}

/**
 * Return an ID of an attachment by searching the database with the file URL.
 *
 * First checks to see if the $url is pointing to a file that exists in
 * the wp-content directory. If so, then we search the database for a
 * partial match consisting of the remaining path AFTER the wp-content
 * directory. Finally, if a match is found the attachment ID will be
 * returned.
 *
 * @param string $url The URL of the image (ex: http://mysite.com/wp-content/uploads/2013/05/test-image.jpg)
 * 
 * @return int|null $attachment Returns an attachment ID, or null if no attachment is found
 */
function azu_get_attachment_id_by_url( $url ) {
	// Split the $url into two parts with the wp-content directory as the separator
	$parsed_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

	// Get the host of the current site and the host of the $url, ignoring www
	$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
	$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

	// Return nothing if there aren't any $url parts or if the current host and $url host do not match
	if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
		return;
	}

	// Now we're going to quickly search the DB for any attachment GUID with a partial path match
	// Example: /uploads/2013/05/test-image.jpg
	global $wpdb;

	$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );
  
	// Returns null if no attachment is found
        if (array_key_exists(0,$attachment))
            return $attachment[0];
        else 
            return null;
}

/**
 * Return prepeared logo attributes array or null.
 *
 * @param $logo array array( 'href', 'id' )
 *
 * @return mixed
 *
 * @since azzu 1.0
 */
function azu_get_uploaded_logo( $logo) {
	if( empty( $logo ) ) { return null; }
        
        if( ! is_array( $logo ))
            $logo = array($logo,0);

        $logo_src = array();
	if ( next( $logo ) ) {
		$logo_src = wp_get_attachment_image_src( current( $logo ), 'full' );
	} else {
		reset( $logo );
                $logo = $this->azu_get_attachment_id_by_url($this->azu_get_of_uploaded_image( current( $logo ) ));
                if($logo != null)
                    $logo_src = wp_get_attachment_image_src($logo, 'full' );
	}
        
	return $logo_src;
}

/**
 * Get image based on devicePixelRatio coocie and theme options.
 *
 * @param $logo array Regular logo.
 * @param $custom string Custom img attributes.
 *
 * @return string.
 */
function azu_get_logo_image ( $img_meta,$logo_height, $custom = '', $class = '' ) {
	if ( empty( $img_meta ) ) { return ''; }
        
        $output = $this->azu_get_thumb_img( array(
		'wrap' 		=> '<img %IMG_CLASS% %SRC% %SIZE% %CUSTOM% />',
		'img_class'	=> $class,
		'img_meta' 	=> $img_meta,
                'options'	=> array( 'h' => round($logo_height), 'z' => 0 ),
		'custom'	=> $custom,
		'echo'		=> false,
		// TODO: add alt if it's possible
		'alt'		=> '',
	) );

	return $output;
}



/**
 * Description here.
 *
 * @since azzu 1.0
 */
function azu_make_web_font_uri( $font, $font_light = array(400,300,100) ) {
	if ( !$font ) {
		return false;
	}

        $font_light = ':'.implode(",",$font_light);
        // italic font hack
        $font_light = str_replace( '400italic', '400italic,700italic', $font_light );

	$protocol = is_ssl() ? "https" : "http";
        if(strpos($font,'&') !== false)
            $font = str_replace( '&', $font_light.'&', $font );
        else
            $font .= $font_light;
	$uri = $protocol . '://fonts.googleapis.com/css?family=' . str_replace( ' ', '+', $font );
	return $uri;
}

/**
 * Create html tag.
 *
 * @return object.
 *
 * @since azzu 1.0
 */
function azu_create_tag( $type, $options ) {
	switch( $type ) {
		case 'checkbox': return new AZU_Mcheckbox( $options );
		case 'radio': return new AZU_Mradio( $options );
		case 'select': return new AZU_Mselect( $options );
		case 'button': return new AZU_Mbutton( $options );
		case 'text': return new AZU_Mtext( $options );
		case 'textarea': return new AZU_Mtextarea( $options );
		case 'link': return new AZU_Mlink( $options );
	}
}

/**
 * Return favicon html.
 *
 * @param $icon string
 *
 * @return string.
 *
 * @since azzu 1.0
 */
function azu_get_favicon( $icon = '' ) {
	$output = '';
	if ( ! empty( $icon ) ) {

		if ( strpos( $icon, '/wp-content' ) === 0 || strpos( $icon, '/files' ) === 0 ) {
			$icon = get_site_url() . $icon;
		}
                
		$ext = explode( '.', $icon );
		if ( count( $ext ) > 1 ) {
			$ext = end( $ext );
		} else {
			return '';
		}

		switch ( $ext ) {
			case 'png':
				$icon_type = esc_attr( image_type_to_mime_type( IMAGETYPE_PNG ) );
				break;
			case 'gif':
				$icon_type = esc_attr( image_type_to_mime_type( IMAGETYPE_GIF ) );
				break;
			case 'jpg':
			case 'jpeg':
				$icon_type = esc_attr( image_type_to_mime_type( IMAGETYPE_JPEG ) );
				break;
			case 'ico':
				$icon_type = esc_attr( 'image/x-icon' );
				break;
			default:
				return '';
		}

		$output .= '<!-- icon -->' . "\n";
                $output .= '<link rel="icon" href="' . $icon . '" type="' . $icon_type . '" />' . "\n";
		$output .= '<link rel="shortcut icon" href="' . $icon . '" type="' . $icon_type . '" />' . "\n";
                //$output .= '<link rel="apple-touch-icon-precomposed" href="' . $icon . '" type="' . $icon_type . '" />' . "\n";
	}
	return  $output;
}

/**
 * Get page template name.
 *
 * Return template name based on current post ID or empty string if fail's.
 *
 * @return string.
 */
function azu_get_template_name( $post_id = 0, $force_in_loop = false ) {
	global $post;

	// work in admin
	if ( is_admin() && !$force_in_loop ) {

		if ( isset($_GET['post']) ) {

			$post_id = $_GET['post'];
		} elseif( isset($_POST['post_ID']) ) {

			$post_id = $_POST['post_ID'];
		}
	}

	// work in the loop
	if ( !$post_id && isset($post->ID) ) {
		$post_id = $post->ID;
	}

	return get_post_meta( absint($post_id), '_wp_page_template', true );
}



/**
 * Get symplyfied post mime type.
 *
 * @param $post_id int
 *
 * @return string Mime type
 */
function azu_get_short_post_myme_type( $post_id = '' ) {
	$mime_type = get_post_mime_type( $post_id );
	if ( $mime_type ) {
		$mime_type = current(explode('/', $mime_type));
	}
	return $mime_type;
}

/**
 * Returns oembed generated html based on $src or false.
 *
 * @param $src string
 * @param $width mixed
 * @param $height mixed
 *
 * @return mixed.
 */
function azu_get_embed( $src, $width = null, $height = null ) {
        if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'shortcodes' ) ){
            if($width == null || $height == null)
                $video_shotcode = wp_oembed_get($src);
            else
                $video_shotcode = wp_oembed_get($src, array('width'=>$width,'height'=>$height));
        }
        else {
            global $wp_embed;
            if ( empty( $wp_embed ) ) {
                    return false;
            }

            if(strpos($src,'youtube.com/watch?v=') !== false) {
                $src = str_replace("youtube.com/watch?v=","youtube.com/embed/",$src);
            }
            else if( strpos($src,'soundcloud.com/tracks') !== false){
                // sound cloud
                $video_shotcode = sprintf( '<%1$s width="%2$s" height="%3$s" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=%4$s&amp;auto_play=false&amp;hide_related=true&amp;show_comments=false&amp;show_user=false&amp;show_reposts=false&amp;visual=false%5$s"></%1$s>',
                    'iframe',
                    !empty($width)?' width="'.intval($width).'"':'100%',
                    !empty($height)?' height="'.intval($height).'"':'150',
                    esc_url($src),
                    ( strpos($src,'color=') !== false) ? '' : '&amp;color='.azu_stylesheet_color_rgb2hex(get_option( 'base-brand-color' ) )
                );
                return $video_shotcode;
            }

            $video_shotcode = sprintf( '[embed%s%s]%s[/embed]',
                    !empty($width)?' width="'.intval($width).'"':'',
                    !empty($height)?' height="'.intval($height).'"':'',
                    $src
            );
            
            $video_shotcode = $wp_embed->run_shortcode( $video_shotcode );
            
            if ($video_shotcode && 0 === strpos($video_shotcode, '[audio')) 
                $video_shotcode = do_shortcode($video_shotcode);
       }
       return $video_shotcode;
}

/* Adding a Wrapping Div to Video Embeds */
function azu_embed_oembed_html($html, $url, $attr, $post_id) {
  // enable vimeo or youtube autoplay & loop etc
  if (strpos($url, '?') !== false && (strpos($url, 'player.vimeo.com') !== false || strpos($url, 'youtube.com/embed') !== false)) {
      $url_sp = explode('?', $url, 2);
      if(strpos($html, $url_sp[0].'?') === false){
        $html = str_replace($url_sp[0],$url,$html);
      }
  }
  return '<div class="azu-video-container">' . $html . '</div>';
  
}



/**
 * Order sanitize filter.
 *
 * @param $order string
 *
 * @return string
 */
function azu_sanitize_order( $order = '' ) {
	return in_array($order, array('ASC', 'asc')) ? 'ASC' : 'DESC';
}

/**
 * alignment class name.
 *
 * @param $alignment string
 *
 * @return string
 */
function azu_get_alignment_class( $option_name = '' ) {
        $alignment = of_get_option( $option_name, 'right' );
        if($alignment == 'left')
            $alignment ='col-sm-auto';
        else if($alignment == 'center')
            $alignment ='col-middle';
        else
            $alignment ='col-sm-auto-right';
	return ' '.$alignment;
}

/**
 * Orderby sanitize filter.
 *
 * @param $orderby string
 *
 * @return string
 */
function azu_sanitize_orderby( $orderby = '' ) {
	$orderby_values = array(
		'none',
		'ID',
		'author',
		'title',
		'name',
		'date',
		'modified',
		'parent',
		'rand',
		'comment_count',
		'menu_order',
		'meta_value',
		'meta_value_num',
		'post__in',
	);

	return in_array($orderby, $orderby_values) ? $orderby : 'date';
}


/**
 * Posts per page sanitize.
 *
 * @param $ppp mixed (string/integer)
 *
 * @return int
 */
function  azu_sanitize_posts_per_page( $ppp = '' ) {
	$ppp = intval($ppp);
	return $ppp > 0 ? $ppp : -1;
}


/**
 * Flag sanitize.
 *
 * @param $flag string
 *
 * @return boolean
 */
function azu_sanitize_flag( $flag = '' ) {
	return in_array($flag, array( '1', 'true', 'y', 'on', 'yes'));
}

/**
 * Dimensions sanitize.
 *
 * @param $input array
 *
 * @return array
 */
static function  azu_sanitize_dimensions($input) {
	if ( is_array($input) ) {
		return array_map('absint', $input);
	}
	return $input;
}

/**
 * Without sanitize.
 */
static function azu_without_sanitize($input) {
	return $input;
}


/**
 * Get current admin page name.
 *
 * @return string
 */
function azu_get_current_page_name() {
	if ( isset($GLOBALS['pagenow']) && is_admin() ) {
		return $GLOBALS['pagenow'];
	} else {
		return false;
	}
}



/**
 * Return current paged/page query var or 1 if it's empty.
 *
 * @return ineger.
 *
 * @since azzu 1.0
 */
function azu_get_paged_var() {
	if ( !( $pg = get_query_var('page') ) ) {
		$pg = get_query_var('paged');
		$pg = $pg ? $pg : 1;
	}
	return absint($pg);
}



/**
 * Inner left join filter for query.
 *
 * @param $parts array
 *
 * @return array
 */
static function azu_core_join_left_filter( $parts ) {
	if( isset($parts['join']) && !empty($parts['join']) ) {
		$parts['join'] = str_replace( 'INNER', 'LEFT', $parts['join']);
	}
	return $parts;
}



/**
 * Count words based on wp_trim_words() function.
 *
 * @param $text string
 * @param $num_words int
 *
 * @return int
 */
function azu_count_words( $text, $num_words = 55 ) {
	$text = wp_strip_all_tags( $text );
	/* translators: If your word count is based on single characters (East Asian characters),
	   enter 'characters'. Otherwise, enter 'words'. Do not translate into your own language. */
	if ( 'characters' == _x( 'words', 'word count: words or characters?', 'azzu'.LANG_DN ) && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
		$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
		preg_match_all( '/./u', $text, $words_array );
		$words_array = array_slice( $words_array[0], 0, null );
	} else {
		$words_array = preg_split( "/[\n\r\t ]+/", $text, -1, PREG_SPLIT_NO_EMPTY );
	}

	return count( $words_array );
}


/**
 * Get next post url.
 *
 */
function azu_get_next_posts_url( $max_page = 0 ) {
	global $paged, $wp_query;

	if( !$paged = intval(get_query_var('page'))) {
		$paged = intval(get_query_var('paged'));
	}

	if ( !$max_page ) {
		$max_page = $wp_query->max_num_pages;
	}

	if ( !$paged ) {
		$paged = 1;
	}

	$nextpage = intval($paged) + 1;

	if ( !is_single() && ( $nextpage <= $max_page ) ) {
		return next_posts( $max_page, false );
	}

	return false;
}

/**
 * Prepare data for categorizer.
 * Returns array or false.
 *
 * @return mixed
 */
function azu_prepare_categorizer_data( array $opts ) {
	$defaults = array(
		'taxonomy'          => null,
		'post_type'         => null,
		'count_attachments' => false,
		'all_btn'           => true,
		'select'            => 'all',
		'terms'             => array(),
	);
	$opts = wp_parse_args( $opts, $defaults );

	if( !($opts['taxonomy'] && $opts['post_type'] && is_array($opts['terms'])) ) {
		return false;
	}

	$post_ids_str = '';
	$posts_terms_count = array();

	$args = array(
		'type' => $opts['post_type'],
		'hide_empty' => 1,
		'hierarchical' => 0,
		'orderby' => 'slug',
		'order' => 'ASC',
		'taxonomy' => $opts['taxonomy'],
		'pad_counts' => false
	);

	if ( isset( $opts['terms']['child_of'] ) ) {

		$args['child_of'] = $opts['terms']['child_of'];
		$args['hide_empty'] = 0;
		unset( $opts['terms']['child_of'] );
	}

	// get all or selected categories
	$terms = $terms_all = get_categories( $args );
	$terms_all_arr = array();

	if ( ! empty( $opts['terms'] ) ) {

		$terms_arr = array_map( 'intval', array_values( $opts['terms'] ) );
		$terms_str = implode( ',', $terms_arr );

		foreach ( $terms as $index=>$trm ) {
			$terms_all_arr[] = $trm->term_id;
			if ( 'except' == $opts['select'] && in_array( $trm->term_id, $terms_arr ) ) {
				unset( $terms[ $index ] );
			} else if ( 'only' == $opts['select'] && !in_array( $trm->term_id, $terms_arr ) ) {
				unset( $terms[ $index ] );
			}
			
		}
	}
	// asort( $terms );

	if ( empty( $terms ) ) {
		return false;
	}

	if ( ! isset( $terms_str ) ) {
		$terms_arr = array();
		foreach ( $terms as $term ) {
			$terms_all_arr[] = $term->term_id;
			$terms_arr[] = $term->term_id;
		}
		$terms_str = implode( ',', $terms_arr );
	}

	if ( !empty($posts_terms_count) ) {
		foreach( $terms as $term ) {
			if( isset($posts_terms_count[$term->term_id]) ) {
				if ( 'except' == $opts['select'] ) {
					$term->count -= $posts_terms_count[$term->term_id];
				} else if ( 'only' == $opts['select'] ) {
					$term->count = $posts_terms_count[$term->term_id];
				}
			}
		}
	}

	global $wpdb;

	$att_query = $all = $other = null;

	$attachments_query = "SELECT ID, post_parent
		FROM $wpdb->posts
		WHERE post_type = 'attachment'
		AND post_status = 'inherit'
		AND post_mime_type LIKE 'image/%%'
		AND post_parent IN(%s)";

	if ( $opts['all_btn'] ) {		
		
		$args = array(
			'no_found_rows'		=> true,
			'post_status'		=> 'publish',
			'post_type'			=> $opts['post_type'],
			'posts_per_page'	=> -1,
			'fields'			=> 'ids',
			'order'				=> 'ASC',
		);
		
		switch( $opts['select'] ) {
			case 'only':
				if ( !empty( $opts['post_ids'] ) ) {
					$args['post__in'] = $opts['post_ids'];
				} else {
					$args['tax_query'] = array( array(
						'taxonomy'	=> $opts['taxonomy'],
						'field'		=> 'term_id',
						'terms'		=> $terms_arr,
						'operator '	=> 'IN',
					) );
				}
				break;
			case 'except':
				if ( !empty( $opts['post_ids'] ) ) {
					$args['post__not_in'] = $opts['post_ids'];
				} else {
					$terms_diff_arr = array_values( array_diff( $terms_all_arr, $terms_arr ) );
					$args['tax_query'] = array(
						array(
							'taxonomy'	=> $opts['taxonomy'],
							'field'		=> 'term_id',
							'terms'		=> $terms_all_arr,
							'operator' 	=> 'NOT IN'
						)
					);
					if ( $terms_diff_arr ) {
						$args['tax_query']['relation']	= 'OR';
						$args['tax_query'][] = array(
							'taxonomy'	=> $opts['taxonomy'],
							'field'		=> 'term_id',
							'terms'		=> $terms_diff_arr,
							'operator' 	=> 'IN'
						);
					}
				}
				break;
		}

		$all_posts = new WP_Query( $args );
		
		$all = $all_ids = $all_posts->posts;

		if ( $opts['count_attachments'] ) {
			foreach( $all_ids as $index=>$id ) {
				if ( post_password_required( $id ) ) {
					unset( $all_ids[ $index ] );
				}
			}
			unset( $id );
			$all_ids = implode( ',', $all_ids );
			$all = $wpdb->get_results( str_replace( '%s', $all_ids, $attachments_query ) );
		}
		
	}

	if ( $opts['count_attachments'] && ! empty( $all_ids ) ) {
		$terms_count = $wpdb->get_results( "
			SELECT COUNT($wpdb->posts.ID) AS count, $wpdb->term_taxonomy.term_id AS term_id
			FROM $wpdb->posts
			JOIN $wpdb->term_relationships ON $wpdb->term_relationships.object_id = $wpdb->posts.post_parent
			JOIN $wpdb->term_taxonomy ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
			WHERE $wpdb->posts.post_type = 'attachment'
			AND $wpdb->posts.post_status = 'inherit'
			AND $wpdb->posts.post_mime_type LIKE 'image/%%'
			AND $wpdb->posts.post_parent IN($all_ids)
			GROUP BY $wpdb->term_taxonomy.term_id
		" );
		if ( $terms_count ) {
			$term_count_arr = array();
			foreach ( $terms_count as $t_count ) {
				$term_count_arr[$t_count->term_id] = $t_count->count;
			}
			foreach ( $terms as &$term ) {
				if ( isset($term_count_arr[$term->term_id]) ) {
					$term->count = $term_count_arr[$term->term_id];
				} 
			}
			unset($term);
		}
	}

	if ( empty( $opts['terms'] ) && 'all' != $opts['select'] ) {
		$terms = array();
	}

	return array(
		'terms'         => $terms,
		'all_count'     => count( $all ),
		'other_count'   => count( $other )
	);
}


/**
 * get content loop of posttype
 * 
 **/
function azu_get_posttype_content_loop($azu_query='',$template='',$loaded_items=array()){
        if(empty($azu_query))
            return '';
        global $post;
        $output = '';
        
        $before_post_hook_added = false;
	$after_post_hook_added = false;
        
        // add masonry wrap
        if ( ! has_filter( 'azzu_before_post', array('azu_tags','azzu_before_post_masonry') ) ) {
                add_action('azzu_before_post', array('azu_tags','azzu_before_post_masonry'), 15);
                $before_post_hook_added = true;
        }

        if ( ! has_filter( 'azzu_after_post', array('azu_tags','azzu_after_post_masonry') ) ) {
                add_action('azzu_after_post', array('azu_tags','azzu_after_post_masonry'), 15);
                $after_post_hook_added = true;
        }
        $attr = azum()->get('attr');
        while ( $azu_query->have_posts() ) { $azu_query->the_post();
                // wide width content
                if(in_array($template, array('blog', 'portfolio')) && !$attr['same_width']){
                    if($template=='portfolio')
                        $prefix = '_azu_project_options_';
                    else
                        $prefix = '_azu_post_options_';
                    $preview = get_post_meta( $post->ID, $prefix."preview", true ); //$azu_query->post->ID
                    if(!$preview)
                        $preview = 0;
					$long = get_post_meta( $post->ID, $prefix."long", true );
					if(!$long)
                        $long = 0;
                    azum()->set('preview', $preview);
					azum()->set('long', $long);
                }
                if(!empty($loaded_items)){
                    $key_in_loaded = array_search($post->ID, $loaded_items);
                    if ( false !== $key_in_loaded ) {
                            unset( $loaded_items[ $key_in_loaded ] );
                            continue;
                    }
                }
                ob_start();
                if($template == 'blog')
                    $content_type = get_post_format();
                else
                    $content_type = $template;
                get_template_part( 'content', $content_type );

                $output .= ob_get_contents();
                ob_end_clean();
        }
        
        // remove masonry wrap
        if ( $before_post_hook_added ) {
                remove_action('azzu_before_post', array('azu_tags','azzu_before_post_masonry'), 15);
        }

        if ( $after_post_hook_added ) {
                remove_action('azzu_after_post', array('azu_tags','azzu_after_post_masonry'), 15);
        }
        
        wp_reset_postdata();
        return $output;
}


/**
 * Return the post URL.
 *
 * Falls back to the post permalink if no URL is found in the post.
 *
 * @since 1.0
 *
 * @see get_url_in_content()
 *
 * @return string The Link format URL.
 */
function azu_get_link_url() {
	$has_url = get_url_in_content( get_the_content() );

	return $has_url ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}


function azu_fields_generator($values,$val){
        // Set default value to $val
        if ( isset( $values['std'] ) && empty($val) ) {
                $val = $values['std'];
        }
        $output ='';
        if ( ! isset( $values['options']['fields'] ) || ! is_array( $values['options']['fields'] ) ) {
                break;
        }

        $del_link = '<div class="submitbox"><a href="#" class="of_fields_gen_del submitdelete">'. _x('Delete', 'backend fields', 'azzu'.LANG_DN). '</a></div>';

        $output .= '<ul class="of_fields_gen_list">';

        // saved elements
        if ( is_array( $val ) && count($val) > 0) {

                $i = 0;
                // create elements
                foreach ( $val as $index=>$field ) {

                        $block = $b_title = '';
                        // use patterns
                        foreach ( $values['options']['fields'] as $name => $data ) {

                                // if only_for list isset and current index not in the list - skip this element
                                if ( isset( $data['only_for'] ) && is_array( $data['only_for'] ) && ! in_array( $index, $data['only_for'] ) ) {
                                        continue;
                                }

                                // Checked if the field is present in the record, if there is no value field in the template
                                // Or if there are equal to the value of the field in the record
                                $checked = false;
                                if ( isset( $field[$name] ) &&
                                        ( ! isset( $data['value'] ) || 
                                        ( isset( $data['value'] ) && $data['value'] == $field[$name] ) ) ) {
                                        $checked = true;
                                }

                                // get the title
                                if ( isset( $data['class'] ) && 'of_fields_gen_title' == $data['class'] ) {
                                        $b_title = $field[$name];
                                }

                                $el_args = array(
                                        'name'          => sprintf('%s[%d][%s]',
                                                $values['id'],
                                                $index,
                                                $name
                                        ),
                                        'description'   => isset($data['description']) ? $data['description'] : '',
                                        'class'         => isset($data['class']) ? $data['class'] : '',
                                        'value'         => ('checkbox' == $data['type']) ? '' : $field[$name],
                                        'checked'       => $checked
                                );

                                if ( 'select' == $data['type'] ) {
                                        $el_args['options'] = isset($data['options']) ? $data['options'] : array();
                                        $el_args['selected'] = $el_args['value'];
                                }

                                if( isset($data['desc_wrap']) ) {
                                        $el_args['desc_wrap'] = $data['desc_wrap'];
                                }

                                if( isset($data['wrap']) ) {
                                        $el_args['wrap'] = $data['wrap'];
                                }

                                if( isset($data['style']) ) {
                                        $el_args['style'] = $data['style'];
                                }

                                // create form elements
                                $element = azuf()->azu_create_tag( $data['type'], $el_args);

                                $block .= $element;
                        }
                        unset($data);

                        $output .= '<li class="nav-menus-php nav-menu-index-' . $index . '">';

                        $output .= '<div class="of_fields_gen_title menu-item-handle" data-index="' . $index . '"><span class="azu-menu-item-title">' . esc_attr($b_title). '</span>';
                        $output .= '<span class="item-controls"><a class="item-edit"></a></span></div>';
                        $output .= '<div class="of_fields_gen_data menu-item-settings description" style="display: none;">' . $block;

                        $output .= $del_link;

                        $output .= '</div>';
                        $output .= '</li>';

                        $i++;
                }
                unset($field);

        }

        $output .= '</ul>';

        // control panel
        $output .= '<div class="of_fields_gen_controls clearfix">';

        // use pattern
        foreach( $values['options']['fields'] as $name => $data ) {
                if( isset($data['only_for']) ) continue;

                $el_args = array(
                        'name'          => sprintf('%s[%s]',
                                $values['id'],
                                $name
                        ),
                        'description'   => isset($data['description']) ? $data['description'] : '',
                        'class'         => isset($data['class']) ? $data['class'] : '',
                        'checked'       => isset($data['checked']) ? $data['checked'] : false
                );

                if ( 'select' == $data['type'] ) {
                        $el_args['options'] = isset($data['options']) ? $data['options'] : array();
                        $el_args['selected'] = isset($data['selected']) ? $data['selected'] : false;
                }

                if( isset($data['desc_wrap']) ) {
                        $el_args['desc_wrap'] = $data['desc_wrap'];
                }

                if( isset($data['wrap']) ) {
                        $el_args['wrap'] = $data['wrap'];
                }

                if( isset($data['style']) ) {
                        $el_args['style'] = $data['style'];
                }

                if( isset($data['value']) ) {
                        $el_args['value'] = $data['value'];
                }

                // create form
                $element = azuf()->azu_create_tag( $data['type'], $el_args);

                $output .= $element;
        }
        unset($data);

        // add button
        $button = azuf()->azu_create_tag( 'button', array(
                'name'  => str_replace(array('[',']'),'',$values['id']) . '[add]',
                'title' => isset($values['options']['button']['title'])?$values['options']['button']['title']:_x('Add', 'backend fields button', 'azzu'.LANG_DN),
                'class' => 'of_fields_gen_add'
        ));

        $output .= $button;

        $output .= '</div><p></p>';

        return $output;
}

/**
 * Theme icons for VC
 *
 * @since 1.0
 * @return array - of icons for iconpicker, can be categorized, or not.
 */
public static function azu_iconpicker_for_vc($icons){
    return $icons;
}

function azu_custom_font_face ( $fonts = array() ) {
    $font_list = array();
    $font_style = 'italic';
    $font_weight = array(
        'thin' => 100, //hairline
        'extralight' => 200, 
        'light' => 300, 
        'regular' => 400, //Normal, Book
        'medium' => 500, 
        'semibold' => 600, //demibold
        'bold' => 700, 
        'extrabold' => 800, //Heavy
        'black' => 900, 
    );
    foreach($fonts as $font){
        $font_temp = array( 'url' => $font, 'weight' => 400, 'style' => 'normal', 'format' => 'ttf' );
        $family = $font;
        $pos = strrpos($font, "/");
        if ($pos !== false) { 
            $family = substr($font, $pos + 1);
        }
        $pos = strrpos($family, ".");
        if ($pos !== false) { 
            $font_temp['format'] = strtolower(substr($family, $pos + 1));
            $family = substr($family, 0, $pos );
        }
        foreach($font_weight as $key => $weight){
            $pos = stripos($family, "-".$key);
            
            if ($pos !== false) {
                $font_temp['weight'] = $weight;
                $pos = stripos($family, "-".$key.$font_style);
                if ($pos !== false) {
                    $key .= $font_style;
                    $font_temp['style'] = $font_style;
                }
                $family = str_ireplace("-".$key,"",$family);
                break;
            }
        }
        
        $pos = stripos($family, "-".$font_style);
        if ($pos !== false) {
            $font_temp['style'] = $font_style;
            $family = str_ireplace("-".$font_style,"",$family);
        }
        
        $font_list[ucfirst($family)][] = $font_temp;
    }
    
    return $font_list;
}

function azu_custom_font_face_regroup( $fonts = array() ) {
    $font_list = array();
    foreach($fonts as $family => $font_attr){
        foreach($font_attr as $font){
            $current_key = $family.$font['weight'].$font['style'];
            if(!array_key_exists($current_key, $font_list)){
                $font_list[$current_key] = array(
                    'family' => $family,
                    'src' => array(),
                    'weight' => $font['weight'],
                    'style' => $font['style']
                );
            }
            $font_list[$current_key]['src'][$font['format']] = $font['url'];
        }
    }
    return $font_list;
}

function azu_add_custom_fonts($fonts){
    $font_list = $this->azu_custom_font_face(of_get_option('manual-fonts',array()));
    foreach ($font_list as $key => $value) {
        if(!in_array($key,$fonts))
              $fonts[$key] = $key;
    }
    return $fonts;
}



function azu_get_custom_fonts_face($font_face){
    $font_list = $this->azu_custom_font_face(of_get_option('manual-fonts',array()));
    $css = '';
    $font_format = array('ttf' => 'truetype', 'otf' => 'opentype', 'eot' => 'embedded-opentype', 'svg' => 'svg', 'woff' => 'woff', 'woff2' => 'woff2');
    foreach ($font_list as $key => $fonts) {
        $url = $format = '';
        foreach ($fonts as $index => $font) {
            if($index == 0 || (in_array($font['format'], array('ttf','woff')) && $font['weight'] == 400) ){
                $url = $font['url'];
                $format = $font_format[$font['format']];
            }
        }
        
        $css .= '@font-face{ font-family: "'.$key.'"; src:  url("'.$url.'") format("'.$format.'");} ';
    }
    $font_face['font_face'] = $css;
    return $font_face;
}

function azu_custom_upload_mimes ( $existing_mimes=array() ) {
    // Add file extension 'extension' with mime type 'mime/type'
    $existing_mimes['eot'] = 'font/eot';
    $existing_mimes['svg'] = 'font/svg'; 
    $existing_mimes['ttf'] = 'font/ttf'; 
    $existing_mimes['otf'] = 'font/otf'; 
    $existing_mimes['woff'] = 'font/woff'; 
    $existing_mimes['woff2'] = 'font/woff2';

    // and return the new full result
    return $existing_mimes;
}

} endif;
