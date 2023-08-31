<?php
/**
 * Version 0.0.2
 */

require_once(  dirname( __FILE__ ) .'/importer/azu-importer.php' ); //load admin theme data importer

class Azu_Theme_Demo_Data_Importer extends Azu_Theme_Importer {

    /**
     * Holds a copy of the object for easy reference.
     *
     * @since 0.0.1
     *
     * @var object
     */
    private static $instance;
    
    /**
     * Set the key to be used to store theme options
     *
     * @since 0.0.2
     *
     * @var object
     */
    public $theme_option_name = AZZU_DESIGN; //set theme options name here
		
	public $theme_options_file_name = 'theme_options.txt';
	
	public $widgets_file_name 	=  'widgets.json';
	
	public $content_demo_file_name  =  'content.xml';
	
	/**
	 * Holds a copy of the widget settings 
	 *
	 * @since 0.0.2
	 *
	 * @var object
	 */
	public $widget_import_results;
	
    /**
     * Constructor. Hooks all interactions to initialize the class.
     *
     * @since 0.0.1
     */
    public function __construct() {
    
		$this->demo_files_path = dirname(__FILE__) . '/main-demo/';
                $optionsframework_settings = get_option( 'optionsframework', array() );
                if(isset($optionsframework_settings['id']))
                    $this->theme_option_name = $optionsframework_settings['id'];
                self::$instance = $this;
		parent::__construct();

    }
	
	/**
	 * Add menus
	 *
	 * @since 0.0.1
	 */
	public function set_demo_menus(){
                $menu_terms = array('top' =>'Top menu','primary' =>'Main menu','footer' =>'Footer menu');
                $menu_locations = array();
		// Menus to Import and assign - you can remove or add as many as you want
                foreach ($menu_terms as $key => $val) {
                    $nav_menu_term = get_term_by('name', $val, 'nav_menu');
                    if($nav_menu_term)
                        $menu_locations[$key] = $nav_menu_term->term_id;
                }
		
		set_theme_mod( 'nav_menu_locations', $menu_locations );

	}

}

new Azu_Theme_Demo_Data_Importer;