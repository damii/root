<?php
/**
 * Include framework
 *
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since azzu 1.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 1200; /* pixels */
}

if ( !class_exists('AzuTheme') ) :
class AzuTheme
{
	function init($options)
	{
		$this->constants($options);
		$this->functions();
                $this->init_detect();
		$this->plugins();
		$this->admin();
		add_action('after_setup_theme', array( &$this, 'supports'), 15);
		add_action('widgets_init', array( &$this, 'widgets' ));
	}

        function init_detect(){
            //Mobile detection library
            require_once( AZZU_FUNCTION_DIR . '/mobile-detect-function.php' );
            azu_mobile_detect();
            //retina & screen size detect
            if ( !isset($_COOKIE['devicePixelRatio']) && !azu_is_login_page()) :
                // Add hook for front-end <head></head>
                add_action('wp_head','retina_detection_js');
            endif;
        }
	function constants($options)
	{
		/**
		 * Theme init file.
		 *
		 */
		require_once( get_template_directory() . '/fw/constants.php' );
	}
        
        function functions()
	{
		/**
		 * Include core functions.
		 *
		 */
		require_once( AZZU_FUNCTION_DIR . '/core-functions.php' );
		
		 /**
		 * Include options framework if it is not installed like plugin.
		 *
		 */
		if ( !defined('OPTIONS_FRAMEWORK_VERSION') ) {

			// Base
			require_once( AZZU_LIBRARY_DIR . '/theme-options/options-framework.php' );

			if ( current_user_can( 'edit_theme_options' ) ) {

				// add theme options
				add_filter( 'options_framework_location', 'azzu_add_theme_options' );
			}
		}
	}
	function widgets()
	{
                $widget_areas = apply_filters( 'azu_widget_areas', array() );
                foreach ($widget_areas as $value) {
                    $azu_widget='azu-widget';
                    if($value=='Topbar')
                            $azu_widget.= azuf()->azu_get_alignment_class('top_bar-content_alignment');
                    else if($value=='Bottombar')
                        $azu_widget.= azuf()->azu_get_alignment_class('bottom_bar-content_alignment');
                    
                    register_sidebar( array(
                    'name'          => $value,
                    'id'            => strtolower(AZU_WIDGET_PREFIX . sanitize_key($value)),
                    'description'   => _x( 'Widget area created by theme', 'atheme', 'azzu'.LANG_DN ),
                    'before_widget' => '<div class="'.$azu_widget.' %2$s">', //id="%1$s"
                    'after_widget'  => '</div>',
                    'before_title'  => '<div class="widget-title"><'.AZU_WIDGET_TITLE_H.'>',
                    'after_title'   => '</'.AZU_WIDGET_TITLE_H.'></div>',
                    ) );
                }
                
                // widget adder
                require_once(AZZU_LIBRARY_DIR.'/widget_areas/class.redux_widget_areas.php');
                global $azu_widget_areas;
                if ( class_exists( 'Redux_Widget_Areas' ) && $azu_widget_areas === null) 
                {
                    $azu_widget_areas = new Redux_Widget_Areas();
                }
	}
	function plugins()
	{
            //////////////////////////////////
            // WPBakery Visual Composer     //
            //////////////////////////////////

            if ( class_exists( 'Vc_Manager', false ) ) {

                    if ( function_exists( 'vc_set_as_theme' ) ) {
                            vc_set_as_theme(true);
                    }

                    if ( function_exists( 'vc_set_default_editor_post_types' ) ) {
                            vc_set_default_editor_post_types( array( 'page', 'post', 'azu_portfolio' ) );
                    }
                    
                    require_once locate_template('/fw/vc_plugins/vc_addons.php');

                    if ( !function_exists('azu_js_composer_load_bridge') ) {
                            function azu_js_composer_load_bridge() {
                                    require_once locate_template('/fw/vc_plugins/vc_bridge.php');
                            }
                    }

                    if ( ! function_exists( 'js_composer_bridge_admin' ) ) {

                            function js_composer_bridge_admin( $hook ) {
                                    // azu stuff
                                    wp_enqueue_style( '', AZZU_URI . '/vc_plugins/assets/css/vc_bridge.css' );
                            }
                    }
                    
                    add_action( 'init', 'azu_js_composer_load_bridge', 20 );
                    add_action( 'admin_enqueue_scripts', 'js_composer_bridge_admin', 15 );

                    if ( function_exists( 'vc_set_shortcodes_templates_dir' ) ) {
                            vc_set_shortcodes_templates_dir( get_template_directory() . '/fw/vc_plugins/templates' );
                    }
            }

		
	}
	function supports()
	{
		/**
		 *  This theme styles the visual editor to resemble the theme style.
		 */
                add_editor_style( array( 'ui/'. AZZU_DESIGN.'/css/editor-style.css', azuh()->azu_font_url() ) );
		/**
		 * Add default posts and comments RSS feed links to head
		*/
		add_theme_support( 'automatic-feed-links' );
                /*
                 * Let WordPress manage the document title.
                 * By adding theme support, we declare that this theme does not use a
                 * hard-coded <title> tag in the document head, and expect WordPress to
                 * provide it for us.
                 */
                add_theme_support( 'title-tag' ); //since wordpress 4.1
		/**
		 * Enable support for Post Thumbnails
		*/
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 350 , 350, true);
		/**
		 * This theme uses wp_nav_menu() in one location.
		 */
  		register_nav_menus( apply_filters( 'azu_register_nav_menus', array() ) ); 

		/**
		 * Enable support for Post Formats
		 */
		add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'audio', 'quote', 'link', 'gallery', 'status', 'chat' ) );
	
		/**
		 * Allow shortcodes in widgets.
		 *
		 */
		add_filter( 'widget_text', 'do_shortcode' );
		
		// create upload dir
		wp_upload_dir();
                 /**
                 * Make theme available for translation
                 * Translations can be filed in the /languages/ directory
                 * If you're building a theme based on azzu, use a find and replace
                 * to change 'azzu'.LANG_DN to the name of your theme in all the template files
                 */
                load_theme_textdomain( 'azzu'.LANG_DN, get_stylesheet_directory() . '/languages' );
	}
	function admin()
	{
        $current_page_name = azuf()->azu_get_current_page_name();
                
		$is_backend = is_admin() || azu_is_login_page();
		
		if ( function_exists('vc_is_inline') && vc_is_inline() ) {
			$is_backend = false;
		}
		
		/**
		 * Include custom menu.
		 *
		 */
		require_once( AZZU_LIBRARY_DIR . '/megamenu/azumenu.php' );
		
                 /**
                 * customizer.
                 */
                require_once( AZZU_LIBRARY_DIR . '/customizer/customizer.php' );
                
		/**
		 * Include admin functions.
		 */
		if ( $is_backend && is_admin() ) {
                    	/**
			 * Include the TGM_Plugin_Activation class.
			 */
			require_once( AZZU_LIBRARY_DIR . '/class-tgm-plugin-activation.php' );
                        
                        
                        /**
			 * Attach metaboxes.
			 *
			 */
			require_once( AZZU_OPTIONS_DIR . '/page-option/metaboxes.php' );
			if ( $located_file = locate_template( 'fw/options/page-option/page-option.php' ) ) {
				include_once( $located_file );
			}

			require_once( AZZU_FUNCTION_DIR . '/admin-functions.php' );
                        
                         /**
			 * One click demo install.
			 *
			 */
			require_once( AZZU_LIBRARY_DIR . '/one-click-demo/init.php' );

		} else if ( !$is_backend ) {
			/**
			 * Include AQResizer.
			 *
			 */
			require_once( AZZU_LIBRARY_DIR . '/aq_resizer.php' );			
		}
                
		/**
		 * Include widgets.
		 *
		 */
                $azzu_widgets = apply_filters( 'azzu_widgets', array() );
		// include widgets only for frontend and widgets admin page
		if ( $azzu_widgets && ( in_array($current_page_name, array('widgets.php', 'admin-ajax.php', 'themes.php')) || !$is_backend ) ) {
			foreach ( $azzu_widgets as $azzu_widget ) {
                                if(strpos($azzu_widget, '/') !== FALSE)
                                    $file_path = $azzu_widget;
				else 
                                    $file_path = locate_template( 'fw/widgets/' . $azzu_widget );
                                require_once( $file_path );
			}
		}
                

                $azzu_shortcodes = apply_filters( 'azzu_shortcodes', array() );
		// include shortcodes only for frontend and post admin pages
		if ( $azzu_shortcodes && ( in_array( $current_page_name, array('post.php', 'post-new.php', 'admin-ajax.php') ) || !$is_backend ) ) {
			/**
			 * Setup shortcodes.
			 *
			 */
			require_once( AZZU_SHORTCODES_DIR . '/shortcode.class.php' );
			foreach ( $azzu_shortcodes as $shortcode_name ) {
                                if(strpos($shortcode_name, '/') !== FALSE)
                                    $file_path = $shortcode_name. '.php';
				else 
                                    $file_path = AZZU_SHORTCODES_DIR . '/' . $shortcode_name . '.php';
				include_once( $file_path );
			}
		}
                
            	// if Layer and Revolution sliders both active
		if ( defined('LS_PLUGIN_VERSION') && class_exists('UniteBaseClassRev') ) {
			/**
			 * Layer slider compatibility settings.
			 *
			 */
			add_action( 'admin_init', 'azzu_layerslider_set_properties',9 );
		}
	}
}
endif; //theme

//Create theme
$itheme = new AzuTheme();
$itheme->init(array(
		"theme_name" => preg_replace("/\W /", "", strtolower(wp_get_theme()->get( 'Name' )) ),
		"theme_slug" => "AZU"
));