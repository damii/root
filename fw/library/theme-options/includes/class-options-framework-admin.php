<?php
/**
 * @package   Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 */

class Options_Framework_Admin {

    /**
     * Page hook for the options screen
     *
     * @since 1.7.0
     * @type string
     */
    protected $options_screen = null;

    /**
     * Hook in the scripts and styles
     *
     * @since 1.7.0
     */
    public function init() {

            // Gets options to load
            $options = & Options_Framework::_optionsframework_options();

            // Checks if options are available
            if ( $options ) {

                            // Add the options page and menu item.
                            add_action( 'admin_menu', array( $this, 'add_custom_options_page' ) );

                            // Add the required scripts and styles
                            //add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
                            //add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

                            // Settings need to be registered after admin_init
                            add_action( 'admin_init', array( $this, 'settings_init' ) );

                            // Adds options menu to the admin bar
                            add_action( 'wp_before_admin_bar_render', array( $this, 'optionsframework_admin_bar' ) );

            }

    }

	/**
     * Registers the settings
     *
     * @since 1.7.0
     */
    function settings_init() {

        // Get the option name
        $options_framework = new Options_Framework;

        // Displays notice after options save
        add_action( 'optionsframework_after_validate', array( $this, 'save_options_notice' ) );

	// Optionally Loads the options file from the theme
	$location = apply_filters( 'options_framework_location', array('options.php') );
	$optionsfile = locate_template( $location );

	// Load settings
	$optionsframework_settings = $options_framework->get_option_name();

	// Updates the unique option id in the database if it has changed
	if ( function_exists( 'optionsframework_option_name' ) ) {
		optionsframework_option_name();
	}
	elseif ( has_action( 'optionsframework_option_name' ) ) {
		do_action( 'optionsframework_option_name' );
	}
	// If the developer hasn't explicitly set an option id, we'll use a default
	else {
		$default_themename = get_option( 'stylesheet' );
		$default_themename = preg_replace("/\W/", "_", strtolower($default_themename) );
		$default_themename = 'optionsframework_' . $default_themename;
		if ( isset( $optionsframework_settings['id'] ) ) {
			if ( $optionsframework_settings['id'] == $default_themename ) {
				// All good, using default theme id
			} else {
				$optionsframework_settings['id'] = $default_themename;
				update_option( 'optionsframework', $optionsframework_settings );
			}
		}
		else {
			$optionsframework_settings['id'] = $default_themename;
			update_option( 'optionsframework', $optionsframework_settings );
		}
	}

	$optionsframework_settings = get_option( 'optionsframework' );

	$saved_settings = get_option( $optionsframework_settings['id'] );

	// If the option has no saved data, load the defaults
	if ( ! $saved_settings ) {
		$this->optionsframework_setdefaults();
	}

	// Registers the settings fields and callback
	register_setting( 'optionsframework', $optionsframework_settings['id'], array ( $this, 'validate_options' ) );
	// Change the capability required to save the 'optionsframework' options group.
	add_filter( 'option_page_capability_optionsframework', array ( $this,'optionsframework_page_capability') );

    }

	/*
	 * Define menu options
	 *
	 * Examples usage:
	 *
	 * add_filter( 'optionsframework_menu', function( $menu ) {
	 *     $menu['page_title'] = 'The Options';
	 *	   $menu['menu_title'] = 'The Options';
	 *     return $menu;
	 * });
	 *
	 * @since 1.7.0
	 *
	 */
	static function menu_settings() {
                // Get options
                $options_arr =& Options_Framework::_optionsframework_options();
                
                // Filter options for subpages
                $menu_items = array_filter( $options_arr, 'optionsframework_options_page_filter' );
                
                array_unshift( $menu_items, array( 'page_title' => _x( 'General', 'backend', 'azzu'.LANG_DN ), 'menu_title' => _x( 'General', 'backend', 'azzu'.LANG_DN ), 'main_title' => _x( 'Theme Options', 'backend', 'azzu'.LANG_DN ) , 'menu_slug' => 'options-framework') );

                return $menu_items;
	}

	/**
     * Add a subpage called "Theme Options" to the appearance menu.
     *
     * @since 1.7.0
     */
	function add_custom_options_page() {
		$subpages = $this->menu_settings();
		$main_menu_item = $subpages[0];
		unset( $subpages[0] );

		// Add main page
		$main_page = call_user_func('add_me'.'nu_page', $main_menu_item['menu_title'], $main_menu_item['main_title'], 'edit_theme_options', $main_menu_item['menu_slug'], array( $this, 'options_page' ),'dashicons-menu',89);

		// Adds actions to hook in the required css and javascript
		add_action( 'admin_print_styles-' . $main_page, array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_print_scripts-' . $main_page, array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_print_scripts-' . $main_page, array( 'Options_Framework_Media_Uploader','optionsframework_media_scripts') );

		// Add subpages
		foreach ( $subpages as $subpage_data ) {
			$subpage = call_user_func( 'add_subm'.'enu_page',
				'options-framework',
				$subpage_data['page_title'],
				$subpage_data['menu_title'],
				'edit_theme_options',
				$subpage_data['menu_slug'],
				array( $this, 'options_page' )
			);

			// Adds actions to hook in the required css and javascript
			add_action( 'admin_print_styles-' . $subpage, array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_print_scripts-' . $subpage, array( $this, 'enqueue_admin_scripts' ) );
			add_action( 'admin_print_scripts-' . $subpage, array( 'Options_Framework_Media_Uploader','optionsframework_media_scripts') );
		}

		// Change menu name for main page
		global $submenu;
		if ( isset( $submenu[ $main_menu_item['menu_slug'] ] ) ) {
			$submenu[ $main_menu_item['menu_slug'] ][0][0] = $main_menu_item['menu_title'];
		}

	}

	/**
     * Loads the required stylesheets
     *
     * @since 1.7.0
     */

	function enqueue_admin_styles( $hook ) {

		if ( $this->options_screen != $hook )
	        return;

                wp_enqueue_style( 'optionsframework', OPTIONS_FRAMEWORK_URL.'css/optionsframework.css',array(),  Options_Framework::VERSION );
                wp_enqueue_style( 'optionsframework-global', AZZU_LIBRARY_URI . '/customizer/customizer-controls.css');
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
     * Loads the required javascript
     *
     * @since 1.7.0
     */
	function enqueue_admin_scripts( $hook ) {

		if ( $this->options_screen != $hook )
	        return;
 
                global $wp_version;
                // Enqueued some jQuery ui plugins
                wp_enqueue_script( 'jquery-ui-core' );
                wp_enqueue_script( 'jquery-ui-sortable' );

                // azu field chooser
                wp_enqueue_script( 'field_chooser_script', OPTIONS_FRAMEWORK_URL . 'js/field-chooser.js' , array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ), $wp_version, true );
				$azuCustomFonts = apply_filters('azuCustomFonts',array('font_face' => ''));
				wp_localize_script( 'field_chooser_script', 'azuCustomFonts', $azuCustomFonts);
                // Enqueue custom option panel JS
                wp_enqueue_script( 'options-custom', OPTIONS_FRAMEWORK_URL . 'js/options-custom.js', array( 'jquery','wp-color-picker', 'field_chooser_script' ), Options_Framework::VERSION, true );

                // Inline scripts from options-interface.php
                add_action( 'admin_head', array( $this, 'of_admin_head' ) );

                // Useful variables
                wp_localize_script( 'options-custom', 'optionsframework', array(
                        'ajaxurl'		=> admin_url( 'admin-ajax.php' ),
                        'optionsNonce'	=> wp_create_nonce( 'options-framework-nonce' )
                        )
                );
                
                

	}

	function of_admin_head() {
		// Hook to add custom scripts
		do_action( 'optionsframework_custom_scripts' );
	}

	/**
     * Builds out the options panel.
     *
	 * If we were using the Settings API as it was intended we would use
	 * do_settings_sections here.  But as we don't want the settings wrapped in a table,
	 * we'll call our own custom optionsframework_fields.  See options-interface.php
	 * for specifics on how each individual field is generated.
	 *
	 * Nonces are provided using the settings_fields()
	 *
     * @since 1.7.0
     */
	 function options_page() { 	settings_errors(); ?>

                <div id="optionsframework-wrap" class="wrap">

                <h2>Theme Options</h2>

                <?php 
                //screen_icon( 'themes' ); 
                ?>
                <h2 class="nav-tab-wrapper">
                        <?php echo Options_Framework_Interface::optionsframework_tabs(); ?>
                </h2>

                <div id="optionsframework-metabox" class="metabox-holder">
                        <div id="optionsframework">
                                <form action="options.php" method="post">
                                <?php settings_fields( 'optionsframework' ); ?>
                                <?php Options_Framework_Interface::optionsframework_fields(); /* Settings */ ?>

                                <div id="submit-wrap">
                                        <div id="optionsframework-submit">
                                                <input type="submit" class="button-primary" name="update" value="<?php esc_attr_e( 'Save Options', 'azzu'.LANG_DN ); ?>" />
                                                <input type="submit" class="reset-button button-secondary" name="reset" value="<?php esc_attr_e( 'Restore Defaults', 'azzu'.LANG_DN ); ?>" onclick="return confirm( '<?php print esc_js( __( 'Click OK to restore default settings on this page!', 'azzu'.LANG_DN ) ); ?>' );" />
                                                <div class="clear"></div>
                                        </div>
                                </div>

                                </form>
                        </div> <!-- / #container -->
                </div>
                <?php do_action( 'optionsframework_after' ); ?>
                </div> <!-- / .wrap -->

        <?php
	}

	/**
	 * Validate Options.
	 *
	 * This runs after the submit/reset button has been clicked and
	 * validates the inputs.
	 *
	 * @uses $_POST['reset'] to restore default options
	 */
	function validate_options( $input ) {

            /*
             * Restore Defaults.
             *
             * In the event that the user clicked the "Restore Defaults"
             * button, the options defined in the theme's options.php
             * file will be added to the option for the active theme.
             */

            if ( isset( $_POST['reset'] ) ) {
                    add_settings_error( 'options-framework', 'restore_defaults', __( 'Default options restored.', 'azzu'.LANG_DN ), 'updated fade' );
                    $current = null;
                    if ( isset( $_POST['_wp_http_referer'] ) ) {
                            $arr = array();
                            wp_parse_str( $_POST['_wp_http_referer'], $arr );
                            $current = current($arr);
                    }
                    return $this->get_default_values( $current );
            }

            /*
             * Update Settings
             *
             * This used to check for $_POST['update'], but has been updated
             * to be compatible with the theme customizer introduced in WordPress 3.4
             */

            // Get all defined options
            $options_orig =& Options_Framework::_optionsframework_options();

            // Get all saved options
            $known_options = get_option( 'optionsframework', array() );
            $saved_options = $used_options = get_option( $known_options['id'], array() );

            if ( !empty( $input['import_export'] ) ) {

                    // Use all options for sanitazing
                    $options = $options_orig;

                    $import_options = @unserialize(@azu_b64_decode($input['import_export']));

                    if ( is_array( $import_options ) ) {
                            $used_options = array_merge( (array) $saved_options, $import_options );
                    }

            // If regular page
            } else {

                    // Options only for current page
                    $options = array_filter( $options_orig, 'optionsframework_options_for_page_filter' );

                    // Define options data with which we will work
                    $used_options = $input;

            }

            $clean = array();

            // Sanitize options
            foreach ( $options as $option ) {

                    if ( ! isset( $option['id'] ) ) {
                            continue;
                    }

                    if ( ! isset( $option['type'] ) ) {
                            continue;
                    }

                    $id = preg_replace( '/(\W!-)/', '', strtolower( $option['id'] ) );

                    // Set checkbox to false if it wasn't sent in the $_POST
                    if ( 'checkbox' == $option['type'] && ! isset( $used_options[ $id ] ) ) {
                            $used_options[ $id ] = false;
                    }

                    // Set each item in the multicheck to false if it wasn't sent in the $_POST
                    if ( 'multicheck' == $option['type'] && ! isset( $used_options[ $id ] ) ) {
                            foreach ( $option['options'] as $key => $value ) {
                                    $used_options[ $id ][ $key ] = false;
                            }
                    }

                    // For a value to be submitted to database it must pass through a sanitization filter
                    if ( !empty( $option['sanitize'] ) && has_filter( 'of_sanitize_' . $option['sanitize'] ) ) {
                            $clean[ $id ] = apply_filters( 'of_sanitize_' . $option['sanitize'], $used_options[ $id ], $option );
                    } elseif ( has_filter( 'of_sanitize_' . $option['type'] ) ) {
                            $clean[ $id ] = apply_filters( 'of_sanitize_' . $option['type'], $used_options[ $id ], $option );
                    }
            }

            // Merge current options and saved ones
            $clean = array_merge( $saved_options, $clean );

            // Hook to run after validation
            do_action( 'optionsframework_after_validate', $clean, $input );

            return $clean;
	}

	/**
	 * Display message when options have been saved
	 */

	static function save_options_notice() {
                add_settings_error( 'options-framework', 'save_options', _x( 'Options saved.', 'backend', 'azzu'.LANG_DN ), 'updated fade' );
	}
        
        /**
         * Ensures that a user with the 'edit_theme_options' capability can actually set the options
         * See: http://core.trac.wordpress.org/ticket/14365
         *
         * @param string $capability The capability used for the page, which is manage_options by default.
         * @return string The capability to actually use.
         */

        function optionsframework_page_capability( $capability ) {
                return 'edit_theme_options';
        }

        /*
         * Adds default options to the database if they aren't already present.
         * May update this later to load only on plugin activation, or theme
         * activation since most people won't be editing the options.php
         * on a regular basis.
         *
         * http://codex.wordpress.org/Function_Reference/add_option
         *
         */

        function optionsframework_setdefaults() {

                $optionsframework_settings = get_option( 'optionsframework' );

                // Gets the unique option id
                $option_name = $optionsframework_settings['id'];

                /*
                 * Each theme will hopefully have a unique id, and all of its options saved
                 * as a separate option set.  We need to track all of these option sets so
                 * it can be easily deleted if someone wishes to remove the plugin and
                 * its associated data.  No need to clutter the database.
                 *
                 */

                if ( isset( $optionsframework_settings['knownoptions'] ) ) {
                        $knownoptions =  $optionsframework_settings['knownoptions'];
                        if ( !in_array($option_name, $knownoptions) ) {
                                array_push( $knownoptions, $option_name );
                                $optionsframework_settings['knownoptions'] = $knownoptions;
                                update_option( 'optionsframework', $optionsframework_settings);
                        }
                } else {
                        $newoptionname = array($option_name);
                        $optionsframework_settings['knownoptions'] = $newoptionname;
                        update_option('optionsframework', $optionsframework_settings);
                }

                // Gets the default options data from the array in options.php
                $options =& Options_Framework::_optionsframework_options();

                // If the options haven't been added to the database yet, they are added now
                $values = $this->get_default_values();

                if ( isset($values) ) {
                        add_option( $option_name, $values ); // Add option with default settings
                }
        }

	/**
	 * Get the default values for all the theme options
	 *
	 * Get an array of all default values as set in
	 * options.php. The 'id','std' and 'type' keys need
	 * to be defined in the configuration array. In the
	 * event that these keys are not present the option
	 * will not be included in this function's output.
	 *
	 * @return array Re-keyed options configuration array.
	 *
	 */
	function get_default_values($page = null) {
                $output = $saved_options = array();
                $config =& Options_Framework::_optionsframework_options();
                $known_options = get_option( 'optionsframework', array() );
                $tmp_options = get_option( $known_options['id'], array() );

                // Current page defaults
                if ( $page ) {

                        $arr = array();
                        $found = null;

                        // Find Page options
                        foreach( $config as $option ) {
                                if ( 'options-framework' == $page && ( null === $found ) ) {
                                        $found = true;
                                } elseif( isset( $option['type'] ) && 'page' == $option['type'] && $option['menu_slug'] == $page ) {
                                        $found = true;
                                        continue;
                                } elseif( isset( $option['type'] ) && 'page' == $option['type'] ) {
                                        $found = false;
                                }

                                if ( $found ) {
                                        $arr[] = $option;
                                }
                        }
                        $config = $arr;

                        $saved_options = $tmp_options;
                }

                foreach ( (array) $config as $option ) {
                        if ( ! isset( $option['id'] ) ) {
                                continue;
                        }
                        if ( ! isset( $option['std'] ) ) {
                                continue;
                        }
                        if ( ! isset( $option['type'] ) ) {
                                continue;
                        }
                        if ( has_filter( 'of_sanitize_' . $option['type'] ) ) {
                                $value = $option['std'];

                                $output[ $option['id'] ] = apply_filters( 'of_sanitize_' . $option['type'], $value, $option );
                        }
                }
                $output = array_merge($saved_options, $output);

                return $output;
	}

	/**
	 * Add options menu item to admin bar
	 */

	function optionsframework_admin_bar() {
                global $wp_admin_bar;

                $menu_items = $this->menu_settings();
                $parent_menu_item = $menu_items[0];
                $parent_menu_id = $parent_menu_item['menu_slug'] . '-parent';

                $wp_admin_bar->add_menu( array(
                        'id' => $parent_menu_id,
                        'title' => $parent_menu_item['main_title'],
                        'href' => admin_url( 'admin.php?page=' . urlencode($parent_menu_item['menu_slug']) )
                ));

                foreach( $menu_items as $menu_item ) {

                        $wp_admin_bar->add_menu( array(
                                'parent' => $parent_menu_id,
                                'id' => $menu_item['menu_slug'],
                                'title' => $menu_item['menu_title'],
                                'href' => admin_url( 'admin.php?page=' . urlencode($menu_item['menu_slug']) )
                        ));
                }
	}

}