<?php

/**
 * Class Azu_Theme_Importer
 *
 * This class provides the capability to import demo content as well as import widgets and WordPress menus
 *
 * @since 1.0
 *
 * @category AzuFramework
 * @package  WP Theme
 * @author   Damii
 *
 */
class Azu_Theme_Importer {

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 1.0
	 *
	 * @var object
	 */
	public $theme_options_file;

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 1.0
	 *
	 * @var object
	 */
	public $widgets;

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 1.0
	 *
	 * @var object
	 */
	public $content_demo;

	/**
	 * Flag imported to prevent duplicates
	 *
	 * @since 1.0
	 *
	 * @var object
	 */
	public $flag_as_imported = array();

    /**
     * Holds a copy of the object for easy reference.
     *
     * @since 1.0
     *
     * @var object
     */
    private static $instance;

    /**
     * Constructor. Hooks all interactions to initialize the class.
     *
     * @since 1.0
     */
    public function __construct() {

        self::$instance = $this;
        
        $this->theme_options_file = $this->demo_files_path . $this->theme_options_file_name;
        $this->widgets = $this->demo_files_path . $this->widgets_file_name;
        $this->content_demo = $this->demo_files_path . $this->content_demo_file_name;
		 
        add_action( 'admin_menu', array($this, 'add_admin') );
        add_action( 'admin_enqueue_scripts', array( &$this, 'azu_import_load' ) );
    }

	/**
	 * Add Panel Page
	 *
	 * @since 1.0
	 */
    public function add_admin() {

        add_theme_page("Import Demo Data", "Import Demo Data", 'switch_themes', 'azu_demo_installer', array($this, 'demo_installer'));

    }
    
    function azu_import_load(){
        if ( isset( $_REQUEST['page']) && $_REQUEST['page'] == 'azu_demo_installer'  ) 
        {
            wp_enqueue_script( 'azu-import-js', AZZU_LIBRARY_URI.'/one-click-demo/assets/azu_import.js', array('jquery'), time(), true );  
            wp_enqueue_style( 'azu-import-css', AZZU_LIBRARY_URI.'/one-click-demo/assets/azu_import.css', time() );
        }
        
    }
    
    
    /**
     * [demo_downloader description]
     *
     * @since 1.0
     *
     * @return [bool] 
     */
    private function demo_downloader($download_url='') {
        if(empty($download_url))
            return '';
        $upload_dir = wp_upload_dir();
        $destination_path = $upload_dir['basedir'].'/demo_import/';
        if (function_exists('wp_mkdir_p') && wp_mkdir_p($destination_path)) {
            $tmpfname = download_url($download_url,60);
            if(!is_wp_error($tmpfname)){
                WP_Filesystem();
                $unzipfile = unzip_file( $tmpfname, $destination_path);
                unlink( $tmpfname );
                if(is_wp_error($unzipfile)) {
                   echo $unzipfile->get_error_message();
                   return '';
                }
            }
            else {
                   echo $tmpfname->get_error_message();
                   return '';
            }
        }
        else {
            echo 'Could not create dir';
            return '';
        }
        return $destination_path;
    }
    
    /**
     * Parse a WXR file
     *
     * @param string $file Path to WXR file for parsing
     * @return array Information gathered from the WXR file
     */
    function parse( $file ) {
            $tmpfname = download_url($file,60);
            if(is_wp_error($tmpfname))
                return $tmpfname;
            else 
                $xml=simplexml_load_file($tmpfname) or new WP_Error( 'parse_error', __( 'Cannot create object', 'azzu'.LANG_DN ) );
            unlink( $tmpfname );
            return $xml;
    }
    
    /**
     * [demo_installer description]
     *
     * @since 1.0
     *
     * @return [type] [description]
     */
    public function demo_installer() {
        $demo_nonce = wp_create_nonce('azu-demo-code');
        $demo_list = array();
        $import_data = $this->parse('http://wp-theme.us/demo_content/'.AZZU_DESIGN.'/demos.xml');
        echo $import_data;
        if ( is_wp_error( $import_data ) )
            echo $import_data->get_error_message();
        foreach ($import_data->demo as $curr_demo) {
            $demo_list[] = array( 
                    'name' => $curr_demo->name, 
                    'file' => $curr_demo->file,
                    'image' => $curr_demo->image, 
                    'preview' => $curr_demo->preview);
        }
        $output = '';
        if(!empty($demo_list)){
            foreach ($demo_list  as $i => $curr_demo) {
                $demo_name = preg_replace("/\W /", "", strtolower($curr_demo['name']) ).$i;
                $output .= '<div class="theme" tabindex="0" aria-describedby="'.$demo_name.'">
                    <div class="theme-screenshot">
                            <img src="'.$curr_demo['image'].'" alt="">
                    </div>
                    <a class="more-details" style="text-decoration: none;" href="'.$curr_demo['preview'].'" target="_blank">'._x( 'Live Preview', 'atheme' , 'azzu'.LANG_DN ).'</a>
                            <h3 class="theme-name" id="'.$demo_name.'">'.$curr_demo['name'].'</h3>
                    <div class="theme-actions">
                            <a class="azu-button-dialog button button-primary" href="'.get_admin_url().'themes.php?page=azu_demo_installer" data-zip="'.urlencode($curr_demo['file']).'">Import Demo</a>
                    </div>
                </div>';
            }
            $output = '<div class="theme-browser rendered">'.$output.'</div><br/>';
        }
        
        //print_r($import_data);
        ?>
        <div id="icon-tools" class="icon32"><br></div>
        <h2>Import Demo Data</h2>
        <div style="background-color: #F5FAFD; margin:10px 0;padding: 10px;color: #0C518F;border: 1px solid #CAE0F3; claer:both; width:90%; line-height:18px;">
            <p class="tie_message_hint">Importing demo data (post, pages, images, theme options, ...) is the easiest way to setup your theme. It will
            allow you to quickly edit everything instead of creating content from scratch. When you import the data following things will happen:</p>

              <ul style="padding-left: 20px;list-style-position: inside;list-style-type: square;}">
                  <li>No existing posts, pages, categories, images, custom post types or any other data will be deleted or modified .</li>
                  <li>No WordPress settings will be modified .</li>
                  <li>Posts, pages, some images, some widgets and menus will get imported .</li>
                  <li>Images will be downloaded from our server, these images are copyrighted, for demo use only.</li>
                  <li>It can take a couple of minutes</li>
              </ul>
         </div>

         <div style="background-color: #F5FAFD; margin:10px 0;padding: 10px;color: #0C518F;border: 1px solid #CAE0F3; claer:both; width:90%; line-height:18px;"><p class="tie_message_hint">Before you begin, make sure all the required plugins are activated.</p></div>
        <br />
        <h3>Options</h3>
        <form id="azu-importer-form" method="post">
        <?php 
            $action = isset($_POST['action']) ? $_POST['action'] : '';
            $azu_contents = 1;
            $azu_attachments = 0;
            $azu_widgets = 1;
            if($action) {
                $azu_contents = isset($_POST['azu_contents']) ? $_POST['azu_contents'] : 0;
                $azu_attachments = isset($_POST['azu_attachments']) ? $_POST['azu_attachments'] : 0;
                $azu_widgets = isset($_POST['azu_widgets']) ? $_POST['azu_widgets'] : 0;
            }
        ?>
        <p>
		<input type="checkbox" value="<?php echo $azu_contents; ?>" name="azu_contents" id="import-contents" <?php echo $azu_contents ? 'checked' : '';?> >
		<label for="import-contents"><?php _ex('Import with Content','atheme','azzu'.LANG_DN); ?></label>
	</p>   
        <p>
		<input disabled type="checkbox" value="<?php echo $azu_attachments; ?>" name="azu_attachments" id="import-attachments" <?php echo $azu_attachments ? 'checked' : '';?>>
		<label for="import-attachments"><?php _ex('Download and import file attachments','atheme','azzu'.LANG_DN); ?></label>
	</p>
        <p>
		<input type="checkbox" value="<?php echo $azu_widgets; ?>" name="azu_widgets" id="import-widgets" <?php echo $azu_widgets ? 'checked' : '';?> >
		<label for="import-widgets"><?php _ex('Import with widgets','atheme','azzu'.LANG_DN); ?></label>
	</p>
            <input type="hidden" name="demononce" value="<?php echo $demo_nonce; ?>" />
            <input type="hidden" name="zip" id="zip" value="" />
            <input name="reset" class="azu-button-dialog panel-save button-primary" type="submit" value="Import Main Demo" />
            <input type="hidden" name="action" value="azu-demo-data" />
        </form>
        <br />
        <br />
        <?php
		echo $output;
        if( 'azu-demo-data' == $action && check_admin_referer('azu-demo-code' , 'demononce')){
            
            $this->theme_options_file = $this->demo_files_path . $this->theme_options_file_name;
            $this->widgets = $this->demo_files_path . $this->widgets_file_name;
            $this->content_demo = $this->demo_files_path . $this->content_demo_file_name;
            
            if(isset($_REQUEST['zip']) && !empty($_REQUEST['zip']))
            {
                
                $destination_path = $this->demo_downloader($_REQUEST['zip']);
                
                if(!empty($destination_path))
                {
                    $this->theme_options_file = $destination_path . $this->theme_options_file_name;
                    $this->widgets = $destination_path . $this->widgets_file_name;
                    $this->content_demo = $destination_path . $this->content_demo_file_name;
                }
            }
            
            if ( isset( $_POST['azu_contents']) && $_POST['azu_contents'] )
                $this->set_demo_data( $this->content_demo );

            $this->set_demo_theme_options( $this->theme_options_file ); //import before widgets incase we need more sidebars
            $this->set_demo_menus();
            
            if ( isset( $_POST['azu_widgets']) && $_POST['azu_widgets'] ){
                $this->process_widget_import_file( $this->widgets );
            }
            
            echo _x('You can save','atheme','azzu'.LANG_DN).' <a href="'.get_admin_url().'admin.php?page=options-framework" >'._x('Theme Options','atheme','azzu'.LANG_DN).'</a>';
            
        }

    }
    
    /**
     * Add menus
     *
     * @since 0.0.1
     */
    public function set_demo_menus() {}

    /**
     * add_widget_to_sidebar Import sidebars
     * @param  string $sidebar_slug    Sidebar slug to add widget
     * @param  string $widget_slug     Widget slug
     * @param  string $count_mod       position in sidebar
     * @param  array  $widget_settings widget settings
     *
     * @since 1.0
     *
     * @return null
     */
    public function add_widget_to_sidebar($sidebar_slug, $widget_slug, $count_mod, $widget_settings = array()){

        $sidebars_widgets = get_option('sidebars_widgets');

        if(!isset($sidebars_widgets[$sidebar_slug]))
           $sidebars_widgets[$sidebar_slug] = array('_multiwidget' => 1);

        $newWidget = get_option('widget_'.$widget_slug);

        if(!is_array($newWidget))
            $newWidget = array();

        $count = count($newWidget)+1+$count_mod;
        $sidebars_widgets[$sidebar_slug][] = $widget_slug.'-'.$count;

        $newWidget[$count] = $widget_settings;

        update_option('sidebars_widgets', $sidebars_widgets);
        update_option('widget_'.$widget_slug, $newWidget);

    }

    public function set_demo_data( $file ) {

	if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);

        require_once ABSPATH . 'wp-admin/includes/import.php';

        $importer_error = false;

        if ( !class_exists( 'WP_Importer' ) ) {

            $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	
            if ( file_exists( $class_wp_importer ) ){

                require_once($class_wp_importer);

            } else {

                $importer_error = true;

            }

        }

        if ( !class_exists( 'WP_Import' ) ) {

            $class_wp_import = dirname( __FILE__ ) .'/wordpress-importer.php';

            if ( file_exists( $class_wp_import ) ) 
                require_once($class_wp_import);
            else
                $importer_error = true;

        }

        if($importer_error){

            die("Error on import");

        } else {
			
            if(!is_file( $file )){

                echo "The XML file containing the dummy content is not available or could not be read .. You might want to try to set the file permission to chmod 755.<br/>If this doesn't work please use the Wordpress importer and import the XML file (should be located in your download .zip: Sample Content folder) manually ";

            } else {

               $wp_import = new WP_Import();
               $wp_import->fetch_attachments = false;
               if ( isset( $_POST['azu_attachments']) && $_POST['azu_attachments'] ) 
                   $wp_import->fetch_attachments = true;
               $wp_import->import( $file );

         	}

    	}

    }

    public function set_demo_theme_options( $file ) {

    	// File exists?
		if ( ! file_exists( $file ) ) {
			wp_die(
				__( 'Theme options Import file could not be found. Please try again.', 'azzu'.LANG_DN ),
				'',
				array( 'back_link' => true )
			);
		}

		// Get file contents and decode
		$data_encode = azu_file_get_c( $file );
        //get exported theme options
		$data = @unserialize(@azu_b64_decode($data_encode));

		// Have valid data?
		// If no data or could not decode
		if ( empty( $data ) || ! is_array( $data ) ) {
			wp_die(
				__( 'Theme options import data could not be read. Please try a different file.', 'azzu'.LANG_DN ),
				'',
				array( 'back_link' => true )
			);
		}

                //import theme options
                update_option($this->theme_option_name, array( 'import_export' => $data_encode) );
    }

    /**
     * Available widgets
     *
     * Gather site's widgets into array with ID base, name, etc.
     * Used by export and import functions.
     *
     * @since 1.0
     *
     * @global array $wp_registered_widget_updates
     * @return array Widget information
     */
    function available_widgets() {

    	global $wp_registered_widget_controls;

    	$widget_controls = $wp_registered_widget_controls;

    	$available_widgets = array();

    	foreach ( $widget_controls as $widget ) {

    		if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] ) ) { // no dupes

    			$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
    			$available_widgets[$widget['id_base']]['name'] = $widget['name'];

    		}

    	}

    	return apply_filters( 'azu_theme_import_widget_available_widgets', $available_widgets );

    }


    /**
     * Process import file
     *
     * This parses a file and triggers importation of its widgets.
     *
     * @since 1.0
     *
     * @param string $file Path to .wie file uploaded
     * @global string $widget_import_results
     */
    function process_widget_import_file( $file ) {

    	// File exists?
    	if ( ! file_exists( $file ) ) {
    		wp_die(
    			__( 'Widget Import file could not be found. Please try again.', 'azzu'.LANG_DN ),
    			'',
    			array( 'back_link' => true )
    		);
    	}

    	// Get file contents and decode
    	$data = azu_file_get_c( $file );
    	$data = json_decode( $data );

    	// Delete import file
    	//unlink( $file );

    	// Import the widget data
    	// Make results available for display on import/export page
    	$this->widget_import_results = $this->import_widgets( $data );

    }


    /**
     * Import widget JSON data
     *
     * @since 1.0
     * @global array $wp_registered_sidebars
     * @param object $data JSON widget data from .wie file
     * @return array Results array
     */
    public function import_widgets( $data ) {

    	global $wp_registered_sidebars;

    	// Have valid data?
    	// If no data or could not decode
    	if ( empty( $data ) || ! is_object( $data ) ) {
    		wp_die(
    			__( 'Widget import data could not be read. Please try a different file.', 'azzu'.LANG_DN ),
    			'',
    			array( 'back_link' => true )
    		);
    	}

    	// Hook before import
    	$data = apply_filters( 'azu_theme_import_widget_data', $data );

    	// Get all available widgets site supports
    	$available_widgets = $this->available_widgets();

    	// Get all existing widget instances
    	$widget_instances = array();
    	foreach ( $available_widgets as $widget_data ) {
    		$widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
    	}

    	// Begin results
    	$results = array();

    	// Loop import data's sidebars
    	foreach ( $data as $sidebar_id => $widgets ) {

    		// Skip inactive widgets
    		// (should not be in export file)
    		if ( 'wp_inactive_widgets' == $sidebar_id ) {
    			continue;
    		}

    		// Check if sidebar is available on this site
    		// Otherwise add widgets to inactive, and say so
    		if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
    			$sidebar_available = true;
    			$use_sidebar_id = $sidebar_id;
    			$sidebar_message_type = 'success';
    			$sidebar_message = '';
    		} else {
    			$sidebar_available = false;
    			$use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
    			$sidebar_message_type = 'error';
    			$sidebar_message = __( 'Sidebar does not exist in theme (using Inactive)', 'azzu'.LANG_DN );
    		}

    		// Result for sidebar
    		$results[$sidebar_id]['name'] = ! empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
    		$results[$sidebar_id]['message_type'] = $sidebar_message_type;
    		$results[$sidebar_id]['message'] = $sidebar_message;
    		$results[$sidebar_id]['widgets'] = array();

    		// Loop widgets
    		foreach ( $widgets as $widget_instance_id => $widget ) {

    			$fail = false;

    			// Get id_base (remove -# from end) and instance ID number
    			$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
    			$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

    			// Does site support this widget?
    			if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
    				$fail = true;
    				$widget_message_type = 'error';
    				$widget_message = __( 'Site does not support widget', 'azzu'.LANG_DN ); // explain why widget not imported
    			}

    			// Filter to modify settings before import
    			// Do before identical check because changes may make it identical to end result (such as URL replacements)
    			$widget = apply_filters( 'azu_theme_import_widget_settings', $widget );

    			// Does widget with identical settings already exist in same sidebar?
    			if ( ! $fail && isset( $widget_instances[$id_base] ) ) {

    				// Get existing widgets in this sidebar
    				$sidebars_widgets = get_option( 'sidebars_widgets' );
    				$sidebar_widgets = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go

    				// Loop widgets with ID base
    				$single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
    				foreach ( $single_widget_instances as $check_id => $check_widget ) {

    					// Is widget in same sidebar and has identical settings?
    					if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {

    						$fail = true;
    						$widget_message_type = 'warning';
    						$widget_message = __( 'Widget already exists', 'azzu'.LANG_DN ); // explain why widget not imported

    						break;

    					}

    				}

    			}

    			// No failure
    			if ( ! $fail ) {

    				// Add widget instance
    				$single_widget_instances = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
    				$single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
    				$single_widget_instances[] = (array) $widget; // add it

    					// Get the key it was given
    					end( $single_widget_instances );
    					$new_instance_id_number = key( $single_widget_instances );

    					// If key is 0, make it 1
    					// When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
    					if ( '0' === strval( $new_instance_id_number ) ) {
    						$new_instance_id_number = 1;
    						$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
    						unset( $single_widget_instances[0] );
    					}

    					// Move _multiwidget to end of array for uniformity
    					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
    						$multiwidget = $single_widget_instances['_multiwidget'];
    						unset( $single_widget_instances['_multiwidget'] );
    						$single_widget_instances['_multiwidget'] = $multiwidget;
    					}

    					// Update option with new widget
    					update_option( 'widget_' . $id_base, $single_widget_instances );

    				// Assign widget instance to sidebar
    				$sidebars_widgets = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
    				$new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
    				$sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
    				update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data

    				// Success message
    				if ( $sidebar_available ) {
    					$widget_message_type = 'success';
    					$widget_message = __( 'Imported', 'azzu'.LANG_DN );
    				} else {
    					$widget_message_type = 'warning';
    					$widget_message = __( 'Imported to Inactive', 'azzu'.LANG_DN );
    				}

    			}

    			// Result for widget instance
    			$results[$sidebar_id]['widgets'][$widget_instance_id]['name'] = isset( $available_widgets[$id_base]['name'] ) ? $available_widgets[$id_base]['name'] : $id_base; // widget name or ID if name not available (not supported by site)
    			$results[$sidebar_id]['widgets'][$widget_instance_id]['title'] = (isset($widget->title) && $widget->title) ? $widget->title : __( 'No Title', 'azzu'.LANG_DN ); // show "No Title" if widget instance is untitled
    			$results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
    			$results[$sidebar_id]['widgets'][$widget_instance_id]['message'] = $widget_message;

    		}

    	}

    	// Hook after import
    	do_action( 'azu_theme_import_widget_after_import' );

    	// Return results
    	return apply_filters( 'azu_theme_import_widget_results', $results );

    }

}

?>