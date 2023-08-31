<?php

class AzuMenuStandard extends AzuMenu{



	/**
	 * Set up the framework
	 *
	 * In addition to the core functionality, set up special functions for the plugin walkthrough,
	 * Style Generator, Control Panel, thumbnails, sidebars, Easy Integration
	 */
	function __construct($base_url = ''){
		
		parent::__construct($base_url);
		


		//ADMIN
		if( is_admin() ){

			//Media on nav panel
			add_action( 'admin_enqueue_scripts' , array( $this , 'enqueueMedia' ) );
			
		}


		//Add Thumbnail Support
		add_action( 'after_setup_theme', array( $this , 'addThumbnailSupport' ), 500 );	//go near the end, so we don't get overridden


	}

	function init(){
		parent::init();
		
		//Filters
		add_filter( 'wp_nav_menu_args' , array( $this , 'megaMenuFilter' ), 2000 );  	//filters arguments passed to wp_nav_menu

	}


	/**
	 * Handle loading the CSS and JS assets.
	 *
	 * In addition to core functionality, handle Style Generator CSS, loading on login pages, 
	 * and optional IE Fix
	 * 
	 */
	function loadAssets(){

		parent::loadAssets();

	}

	/* Load additional javascript */
	function loadJS(){

		parent::loadJS();

		if( $this->settings->op( 'load-google-maps') )
			wp_enqueue_script( 'google-maps', 'http://maps.googleapis.com/maps/api/js?sensor=false' , array( 'jquery' ), false, true ); 

	}



	/*
	 * Add Support for Thumbnails on Menu Items
	 *
	 * This function adds support without override the theme's support for thumbnails
	 * Note we could just call add_theme_support('post-thumbnails') without specifying a post type,
	 * but this would make it look like users could set featured images on themes that don't support it
	 * so we don't want that.
	 */
	function addThumbnailSupport(){
	
		global $_wp_theme_features;
		$post_types = array( 'nav_menu_item' );
	
		$alreadySet = false;
	
		//Check to see if some features are already supported so that we don't override anything
		if( isset( $_wp_theme_features['post-thumbnails'] ) && is_array( $_wp_theme_features['post-thumbnails'][0] ) ){
			$post_types = array_merge($post_types, $_wp_theme_features['post-thumbnails'][0]);
		}
		//If they already tuned it on for EVERY type, then we don't need to do anything more
		elseif( isset( $_wp_theme_features['post-thumbnails'] ) && $_wp_theme_features['post-thumbnails'] == 1 ){
			$alreadySet = true;
		}
	
		if(!$alreadySet) add_theme_support( 'post-thumbnails' , $post_types );
	
		add_post_type_support( 'nav_menu_item' , 'thumbnail' ); //wp33
	}




	function getWalker(){
		return new AzuMenuWalker();
	}

	function getMenuArgs( $args ){

		$args = parent::getMenuArgs( $args );
		
		$args['container'] 		= 'div';
		if( $this->settings->op( 'azumega-menubar-full' ) )
                    $args['container_class'].= ' fullwidth';
		

		$location = isset( $args['theme_location'] ) ? $args['theme_location'] : 'none';
		$args['container_class'].= ' themeloc-'.$location;
		
		return $args;
	}



	/*
	 * Apply options to the Menu via the filter
	 */
	function megaMenuFilter( $args ){

		
		//Only print the menu once
		if( $this->count > 0 ) return $args;

		//Current location check instance
		$location = isset( $args['theme_location'] ) ? $args['theme_location'] : '';
                
                //check allowed locations
                if(!in_array($location, array('primary','center-left','center-right'))){
                    return $args;
                }
		else if( $location ){	//if location is present
			if( !isset( $this->instances[$location] ) ) $this->instances[$location] = 0;	//initialize instance count
			$this->instances[$location]++;	//increment instance count
			$target_instance = $this->settings->op( 'theme-loc-instance' );	//get the target instance number
			if( !is_numeric( $target_instance ) ) $target_instance = 1;		//make sure our value is valid
			if( $this->instances[$location] != $target_instance ) return $args;	//If we're not on the specific instance of this theme location, quit
		}

		if( isset( $args['responsiveSelectMenu'] ) ) return $args;
		if( isset( $args['filter'] ) && $args['filter'] === false ) return $args;
 

		$items_wrap = '<ul id="%1$s" class="%2$s" data-theme-location="'.$location.'">%3$s</ul>'; //This is the default, to override any stupidity
		
		$args = $this->getMenuArgs( $args );

		return $args;
	}


	/* ADMIN */


	function adminInit(){

		parent::adminInit();
	}

	function enqueueMedia( $hook ){
		if( $hook == 'nav-menus.php' ){
			wp_enqueue_media();
			//wp_enqueue_style( 'azu-fonticonpicker', get_template_directory_uri() . '/css/iconpicker.css', array() );
			// fonticonpicker js
			wp_enqueue_script('azu-fonticonpicker-js', AZZU_THEME_URI.'/js/jquery.fonticonpicker.min.js', array(), AZZU_VERSION, true);
			// add icon data
        	wp_localize_script( 'azu-fonticonpicker-js', 'azuIconPicker', array('icons' => azuf()->azuIconPicker()) );
		}
	}

	function loadAdminNavMenuJS(){

		parent::loadAdminNavMenuJS();

	}


	/*
	 * Show a sidebar select box
	 */
	function sidebarSelect( $id , $_val ){
		
		$fid = 'edit-menu-item-sidebars-'.$id;
		$name = 'menu-item-sidebars['.$id.']';
		$selection = $_val; //get_post_meta( $id, '_menu_item_sidebars', true);
		
		$ops = $this->sidebarList();
		if( empty( $ops ) ) return '';
		
		$html = '<select id="'.$fid.'" name="'.$name.'" class="edit-menu-item-sidebars">';
		
		$html.= '<option value=""></option>';
		foreach( $ops as  $op ){
                        $opVal = sanitize_key($op);
			$selected = $opVal == $selection ? 'selected="selected"' : '';
			$html.= '<option value="'.$opVal.'" '.$selected.' >'.$op.'</option>';
		}
				
		$html.= '</select>';
		
		return $html;
	}

	/*
	 * List the available sidebars
	 */
	function sidebarList(){
                $sb = azuf()->azzu_get_widgetareas_options();
		return $sb;
	}

	/* 
	 * Show a sidebar
	 */
	function sidebar($name){
		
		if(function_exists('dynamic_sidebar')){
			ob_start();
			echo '<ul class="azu-sidebar" id="azumega-'.sanitize_title($name).'">';
			dynamic_sidebar($name);		
			echo '</ul>';
			return ob_get_clean();
		}
		return 'none';
	}

	/*
	 * Count the number of widgets in a sidebar area
	 */
	function sidebarCount($index){
		
		global $wp_registered_sidebars, $wp_registered_widgets;
	
		if ( is_int($index) ) {
			$index = "sidebar-$index";
		} else {
			$index = sanitize_title($index);
			foreach ( (array) $wp_registered_sidebars as $key => $value ) {
				if ( sanitize_title($value['name']) == $index ) {
					$index = $key;
					break;
				}
			}
		}
	
		$sidebars_widgets = wp_get_sidebars_widgets();
	
		if ( empty($wp_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_widgets) || !is_array($sidebars_widgets[$index]) || empty($sidebars_widgets[$index]) )
			return false;
	
		$sidebar = $wp_registered_sidebars[$index];
		
		return count($sidebars_widgets[$index]);
	}

	function showMenuOptions( $item_id ){

		global $azuMenu;
		$settings = $azuMenu->getSettings();

		parent::showMenuOptions( $item_id );

		$this->showCustomMenuOption(
			'notext', 
			$item_id, 
			array(
				'level' => '0-plus', 
				'title' => _x( 'Remove the Navigation Label text from the link.  Can be used, for example, with image-only links.', 'azumenu', 'azzu'.LANG_DN ), 
				'label' => _x( 'Disable Text', 'azumenu', 'azzu'.LANG_DN ), 
				'type' 	=> 'checkbox', 
			)
		);

		

//		$this->showCustomMenuOption(
//			'floatRight', 
//			$item_id, 
//			array(
//				'level' => '0', 
//				'title' => _x( 'Float the menu item to the right edge of the menu bar.', 'azumenu', 'azzu'.LANG_DN ), 
//				'label' => _x( 'Align Menu Item to Right Edge', 'azumenu', 'azzu'.LANG_DN ), 
//				'type' 	=> 'checkbox', 
//			)
//		);



		//CONTENT OVERRIDES AND WIDGET AREAS
		
		if ( $settings->op( 'azumega-shortcodes' ) ) {
			$this->showCustomMenuOption(
				'shortcode', 
				$item_id, 
				array(
					'level' => '0-plus', 
					'title' => _x( 'Display custom content in this menu item.  This input accepts shortcodes so you can display things like contact forms, search boxes, or galleries.  Check "Disable Link" above to display only this content, instead of a link.', 'azumenu', 'azzu'.LANG_DN ), 
					'label' => _x( 'Custom Content (Content Override)', 'azumenu', 'azzu'.LANG_DN ), 
					'type' 	=> 'textarea', 
				)
			);
		}


		$minSidebarLevel = 1;
		if( $settings->op( 'azumega-top-level-widgets' ) ){
			$minSidebarLevel = 0;
		}
		
		$this->showCustomMenuOption(
			'sidebars', 
			$item_id, 
			array(
				'level' => $minSidebarLevel . '-plus', 
				'title' => _x( 'Select the widget area to display', 'azumenu', 'azzu'.LANG_DN ), 
				'label' => _x( 'Display a Widget Area', 'azumenu', 'azzu'.LANG_DN ), 
				'type' => 'sidebarselect', 
			)
		);


		$this->showCustomMenuOption(
			'icon',
			$item_id,
			array(
				'level' => '0-plus',
				'title' => _x( 'FontIcons' , 'azumenu', 'azzu'.LANG_DN ),
				'label' => _x( 'Font icons' , 'azumenu', 'azzu'.LANG_DN ),
				'type' => 'iconpicker',
			)
		);


		do_action( 'azumenu_extended_menu_item_options' , $item_id , $this );	



		global $post_ID;
		$post_ID = $item_id;

		$iframeSrc = get_upload_iframe_src('image') . '&amp;tab=type&amp;width=640&amp;height=589';
		//media-upload.php?post_id=<?php echo $item_id; &amp;type=image&amp;TB_iframe=1&amp;width=640&amp;height=589
		$wp_mega_link = "Set Thumbnail";
		$wp_mega_img = $azuMenu->getImage( $item_id );

		if (!empty($wp_mega_img)) {
			$wp_mega_link = $wp_mega_img;
			$ajax_nonce = wp_create_nonce("set_post_thumbnail-$item_id");
			$wp_mega_link .= '<div class="remove-item-thumb" id="remove-item-thumb-' . $item_id . '"><a href="#" id="remove-post-thumbnail-' . $item_id . '" onclick="azumega_remove_thumb(\'' . $ajax_nonce . '\', ' . $item_id . ');return false;">' . esc_html_x( 'Remove image' , 'azumenu', 'azzu'.LANG_DN ) . '</a></div>';
		}


		?>

		<p class="azumega-custom-all">
			<a class="set-menu-item-thumb button azu_clear" 
				data-menu-item-id="<?php echo $item_id ; ?>" 
				data-uploader_title="Select Menu Item Image" 
				id="set-post-thumbnail-<?php echo $item_id;?>" 
				href="#" title="Set Thumbnail"><?php
						echo $wp_mega_link;
					?></a></p>

		<?php

	}



}
