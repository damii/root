<?php

class AzuOptions {

	public $id;
	
	public $defaultOp = array();
	
	function __construct($id=''){
		$this->id = $id;
		$this->defaultOp = array(
		"azumega-transition" => 'slide',
		"azumega-include-jquery" => false,
		"azumega-animation-time" => 300,
		"azumega-orientation" => 'horizontal', //horizontal vertical
		"azumega-hover-interval" => 20,
		"azumega-hover-timeout" => 400,
		"azumega-submenu-full" => false,
		"load-google-maps" => false,
		"responsive-menu" => true,
		"responsive-menu-toggle" => true,
		"responsive-menu-toggle-text" => '',
		"azumega-jquery" => true,
		"azumega-disable-img-tooltips" => false,
		"azumega-resizeimages" => true,
		"azumega-load-on-login" => false,
		"azumega-menubar-full" => false,
		"azumega-description-0" => false,
		"azumega-description-1" => false,
		"azumega-description-2" => false,
		"azumega-image-width" => 14,
		"theme-loc-instance" => '1',
		"azumega-shortcodes" => false,
		"title-shortcodes" => false,
		"azumega-top-level-widgets" => false
	);
	}
	
	function op( $id , $invalid = '' ){
                $default_val=$this->defaultOp[$id];
                switch ($id) {
                    case 'azumega-image-width':
                        $square_size = of_get_option('header-icons_size');
                        if(is_array($square_size))
                            $default_val = $square_size['width'];
                        break;
                    default:
                        break;
                }
		return of_get_option($id, ($id==NULL || empty($id))  ? $invalid : $default_val);
	}

}

/* AzuMenu */
class AzuMenu{
	
	protected $settings;
	protected $baseURL;
	
	protected $menuItemOptions;
	protected $optionDefaults;

	protected $count = 0;
	protected $instances = array();

	function __construct( $base_url = '' ){
		
		//Integrated theme version
		$this->baseURL = $base_url;
		
		$this->settings = new AzuOptions();
		$this->menuItemOptions = array();
		
		
		//ADMIN
		if( is_admin() ){
			
			add_action( 'admin_menu' , array( $this , 'adminInit' ) );

			add_action( 'wp_ajax_azumega-add-menu-item', array( $this , 'addMenuItem_callback' ) );
			
			add_action( 'azumenu_menu_item_options', array( $this , 'menuItemCustomOptions' ), 10, 1);		//Must go here for AJAX purposes
			
			
			
			//AJAX Load Image
			add_action( 'wp_ajax_azumenu_getMenuImage', array( $this, 'getMenuImage_callback' ) );
			
			//Appearance > AzuMenu Preview
			add_filter( 'wp_nav_menu_args' , array( $this , 'megaMenuFilter' ), 2000 );  	//filters arguments passed to wp_nav_menu
			
			add_action( 'admin_notices',  array( $this, 'checkPostLimits' ) );

			$this->optionDefaults = array(

				'menu-item-shortcode'			=> '',
				'menu-item-sidebars'			=> '',
				'menu-item-highlight'			=> 'off',
				'menu-item-notext'				=> 'off',
				'menu-item-nolink'				=> 'off',
				'menu-item-isheader'			=> 'off',
				'menu-item-horizontaldivision'	=> 'off',
				'menu-item-newcol'				=> 'off',
				'menu-item-isMega'				=> 'off',
				'menu-item-alignSubmenu'		=> 'left',
				'menu-item-floatRight'			=> 'off',
				'menu-item-fullWidth'			=> 'off',
				'menu-item-numCols'				=> 'auto',
				'menu-item-icon'				=> '',
			);

			$this->optionDefaults = apply_filters( 'azuMenu_menu_item_options_value_defaults' , $this->optionDefaults );
			
		}
		//FRONT END
		else{
		
			add_action( 'after_setup_theme' , array( $this , 'init' ) );
			add_action( 'after_setup_theme' , array( $this , 'fire_custom_preset_hook' ) );
		}
		

	}
	
	
	function init(){
		
		$this->loadAssets();
		
		
	}
	
	function loadAssets(){
		
		//Load on front end, as well as on login and registration pages if setting is enabled
		if( !is_admin() && 
		  ( $this->settings->op( 'azumega-load-on-login' ) || !in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) ) {
			
			//Load Javascript unless disabled
			if( $this->settings->op( 'azumega-jquery' ) ) add_action( 'init', array( $this , 'loadJS' ), 500);
			
		}

	}
	
	
	
	function loadJS(){
		
		// Load jQuery - optionally disable for when dumb themes don't include jquery properly
		if( $this->settings->op( 'azumega-include-jquery' ) ) wp_enqueue_script( 'jquery' );

		
	
	}
	
	
	
	function fire_custom_preset_hook(){

		do_action( 'azuMenu_register_styles' );

	}
	

	function getSettings(){
		return $this->settings;
	}
	

	/*
	 * Default walker, but this can be overridden
	 */
	function getWalker(){
		return new AzuMenuWalkerCore();
	}
	
	


	function directIntegration( $theme_location = AZUMENU_LOCATION , $filter = false , $echo = true , $args = array() ){
		
		$args['theme_location'] = $theme_location;
		$args['filter'] = $filter;
		$args['echo'] = $echo;

		$args = $this->getMenuArgs( $args );

		if( $echo ) wp_nav_menu( $args );
		else return wp_nav_menu( $args );
	}

	function getMenuArgs( $args ){
		$items_wrap 	= '<ul id="%1$s" class="%2$s">%3$s</ul>'; 
                
                $menu_name = '';
                $menu_widget_class = 'azu-menu-widget-area';
                if(of_get_option('header-layout')=='middle')
                {
                    if($args['container_id'] == 'azu-navbar-left'){
                        $menu_name = 'Menu-left';
                        $menu_widget_class .= ' area-left';
                    }
                    elseif($args['container_id'] == 'azu-navbar-right'){
                        $menu_name = 'Menu-right';
                        $menu_widget_class .= ' area-right';
                    }
                }
                elseif($args['container_id'] == 'azu-navbar-collapse')
                        $menu_name = 'Menu-right';
                
                if($menu_name) {
                    ob_start();
                    azut()->azzu_widget_location($menu_name, $menu_widget_class);
                    $azu_menu_widget_area = ob_get_clean();
                    
                    if(of_get_option('header-layout')=='side'){
                        ob_start();
                        get_template_part( 'templates/branding' );
                        $azu_branding = ob_get_clean();
                        $items_wrap = $azu_branding.$items_wrap.$azu_menu_widget_area;
                    }
                    else
                        $items_wrap = $azu_menu_widget_area.$items_wrap;
                }
                
		$args['walker'] 		= $this->getWalker();
		//$args['container_id'] 		= 'azumegaMenu';
		//$args['container_class'] 	= azus()->get('azu-navigation-field');
                if($args['menu_class'] === 'menu')
                    $args['menu_class']		= 'nav';
		$args['depth']			= 0;
		$args['items_wrap']		= $items_wrap;
		$args['link_before']		= '';
		$args['link_after']		= '';
                
                
		if( $this->settings->op( 'azumega-orientation' ) == 'vertical' )	$args['menu_class'].= ' navbar-stacked';
		



		if( $this->settings->op( 'azumega-submenu-full' ) )				$args['container_class'].= ' fullwidth';  
		

		return $args;
	}
	
	

	/*
	 * Apply options to the Menu via the filter
	 */
	function megaMenuFilter( $args ){

		//Only print the menu once
		if( $this->count > 0 ) return $args;
		
		if( isset( $args['responsiveSelectMenu'] ) ) return $args;

		$this->count++; //increment count
		
		$args = $this->getMenuArgs( $args );

		return $args;
	}


	
	
	
	function getImage( $id, $w = 16, $h = 16 ){
		if( empty( $w ) ) $w = 16; 
		if( empty( $h ) ) $h = 16;
	
		if( has_post_thumbnail( $id ) ){
			$img_id = get_post_thumbnail_id( $id );
			$attachment = get_post( $img_id );
			
			$image = wp_get_attachment_image_src( $img_id, 'single-post-thumbnail' );
			$src = $image[0];
			
			if( is_ssl() ) $src = str_replace('http://', 'https://', $src);
			
			$alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
			$title = trim( strip_tags( $attachment->post_title ) );
			if( empty( $alt ) ) $alt = $title;

			if( $this->settings->op( 'azumega-disable-img-tooltips' ) ) $title = '';
			else $title = 'title="'.$title.'"';

			if( $this->settings->op( 'azumega-resizeimages' ) ){
				return '<img class="azu-img azu-img-resize" height="'.$h.'" width="'.$w.'" src="'.$src.'" alt="'.$alt.'" '.$title.' />';
			}
			else return '<img class="azu-img azu-img-noresize" src="'.$src.'" alt="'.$alt.'" '.$title.' />';
			
		}
		return '';
	}
        
        
	function azu_getImage( $id, $w = 16, $h = 16 ){
		if( empty( $w ) ) $w = 16;
		if( empty( $h ) ) $h = 16;
	
		if( has_post_thumbnail( $id ) ){
			$media_id = get_post_thumbnail_id( $id );
                        
			$media_image = azuf()->azu_get_thumb_img( array(
				'img_meta'      => wp_get_attachment_image_src( $media_id, 'full' ),
				'img_id'		=> $media_id,
				'options'       => array( 'w' => $w, 'h' => $h ),
				'echo'			=> false,
				'wrap'			=> '<img %CLASS% %SRC% %SIZE% %IMG_TITLE% %ALT% />',
			) );
			
                        return $media_image;
		}
		return '';
	}
	

	/*
	 * Get the Post Thumbnail Image
	 */
	function getPostImage( $id, $w=30, $h=30, $default_img = false ){

		if( empty( $w ) ) $w = 30; if( empty( $h ) ) $h = 30;
		
		if ( has_post_thumbnail( $id ) ){
			$img_id = get_post_thumbnail_id( $id );
			$attachment = get_post( $img_id );
			//$image = wp_get_attachment_image_src( $img_id , 'single-post-thumbnail' );
			$image = wp_get_attachment_image_src( $img_id , array( $w, $h ) );
			$src = $image[0];
			
			$alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
			$title = trim( strip_tags( $attachment->post_title ) );
			if( empty( $alt ) ) $alt = $title;
			if( $this->settings->op( 'azumega-disable-img-tooltips' ) ) $title = '';
			else $title = 'title="'.$title.'"';
					
			return $this->buildImg( $src, $w, $h , $title, $alt );
		}
		else if( $default_img ){
			//Use Default Image if Post does not have featured image
			return $this->buildImg( $default_img, $w, $h , '', '' );
		}
		return '';
	}
	
	function buildImg( $src, $w, $h, $title, $alt ){

		if( is_ssl() ) $src = str_replace('http://', 'https://', $src);
		
		return '<img height="'.$h.'" width="'.$w.'" src="'.$src.'" alt="'.$alt.'" '.$title.' />';
	}
	
	
	
	
	/* ADMIN */
	
	function adminInit(){
		
		//Appearance > Menus : load additional styles and scripts
		add_action( 'admin_print_styles-nav-menus.php', array( $this , 'loadAdminNavMenuJS' ) ); 
		add_action( 'admin_print_styles-nav-menus.php', array( $this , 'loadAdminNavMenuCSS' )); 
		
		//Appearance > Menus : modify menu item options
		add_filter( 'wp_edit_nav_menu_walker', array( $this , 'editWalker' ) , 2000);
		
		//Appearance > Menus : save custom menu options
		add_action( 'wp_update_nav_menu_item', array( $this , 'updateNavMenuItem' ), 10, 3); //, $menu_id, $menu_item_db_id, $args;
		
		
		do_action( 'azuMenu_register_styles' );
				
		//For extensibility
		do_action( 'azuMenu_after_init' );
		
	}
	
	function loadAdminNavMenuJS(){
		
		wp_enqueue_script('jquery');	// Load jQuery

		wp_enqueue_script('thickbox');
	
		//Admin Extras	
		wp_enqueue_script('azumenu-admin-js', $this->baseURL.'js/azumenu.admin.js', array(), AZZU_VERSION, true);	
		


	}
	
	function loadAdminNavMenuCSS(){
                global $wp_version;
		wp_enqueue_style('azumenu-admin-css', 	$this->baseURL.'styles/admin.css', 	false, AZZU_VERSION, 'all');
		wp_enqueue_style('thickbox');
		
		wp_enqueue_style( 'azu-fontello', AZZU_UI_URI.'/'.AZZU_DESIGN . '/css/fontello.min.css', array(), $wp_version );
		// fonticonpicker css
		wp_enqueue_style('azu-fonticonpicker-css', 	AZZU_UI_URI.'/'.AZZU_DESIGN.'/css/jquery.fonticonpicker.css', 	array(), AZZU_VERSION);
		
	}
	
	
	
	/*
	 * Custom Walker Name - to be overridden by Standard
	 */
	function editWalker( $className ){
		return 'AzuMenuWalkerEdit';
	}
	
	
	/*
	 * Get the Image for a Menu Item via AJAX
	 */
	function getMenuImage_callback(){
		
		$menu_item_id = $_POST['menu_item_id'];
		$thumbnail_id = $_POST['thumbnail_id'];
		
		$id = $menu_item_id; //substr($menu_item_id, (strrpos($menu_item_id, '-')+1));

		//set the featured image
		set_post_thumbnail( $id , $thumbnail_id );
		
		
		$data = array();
		//$data['menu_item_id'] = $menu_item_id;
		$data['thumbnail_id'] = $thumbnail_id;
		
		$ajax_nonce = wp_create_nonce( "set_post_thumbnail-$id" );
		$rmvBtn = '<div class="remove-item-thumb" id="remove-item-thumb-'.$id.
					'"><a href="#" id="remove-post-thumbnail-'.$id.
					'" onclick="azumega_remove_thumb(\'' . $ajax_nonce . '\', '.
					$id.');return false;">' . esc_html_x( 'Remove image' , 'azumenu', 'azzu'.LANG_DN ) . '</a></div>';
		
		$data['remove_nonce'] = $ajax_nonce;// $rmvBtn;
		$data['id'] = $id;
		
		$data['image'] = $this->getImage( $id );
		$this->JSONresponse( $data );
	}
	
	
	
	/*
	 * Save the Menu Item Options for AzuMenu
	 */
	function updateNavMenuItem( $menu_id, $menu_item_db_id, $args ){

		//Parse the serialized string of AzuMenu Options into an array
		$azu_options_string = isset( $_POST['azu_options'][$menu_item_db_id] ) ? $_POST['azu_options'][$menu_item_db_id] : '';
		$azu_options = array();
		parse_str( $azu_options_string, $azu_options );
		
		$azu_options = wp_parse_args( $azu_options, $this->optionDefaults ); //only allow registered options

		update_post_meta( $menu_item_db_id, '_azu_options', $azu_options );
	
	}



	/**
	 * This function is paired with a JavaScript Override Function so that we can use our custom Walker rather
	 * than the built-in version.  This allows us to include the AzuMenu Options as soon as an item is added to the menu,
	 * 
	 * This is a slightly edited version of case 'add-menu-item' : located in wp-admin/admin-ajax.php
	 * 
	 * In the future, if WordPress provides a hook or filter, this should be updated to use that instead.
	 * 
	 */
	function addMenuItem_callback(){
		
		check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) )
			wp_die('-1');

		require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
	
		// For performance reasons, we omit some object properties from the checklist.
		// The following is a hacky way to restore them when adding non-custom items.
	
		$menu_items_data = array();
		foreach ( (array) $_POST['menu-item'] as $menu_item_data ) {
			if (
				! empty( $menu_item_data['menu-item-type'] ) &&
				'custom' != $menu_item_data['menu-item-type'] &&
				! empty( $menu_item_data['menu-item-object-id'] )
			) {
				switch( $menu_item_data['menu-item-type'] ) {
					case 'post_type' :
						$_object = get_post( $menu_item_data['menu-item-object-id'] );
					break;
	
					case 'taxonomy' :
						$_object = get_term( $menu_item_data['menu-item-object-id'], $menu_item_data['menu-item-object'] );
					break;
				}
	
				$_menu_items = array_map( 'wp_setup_nav_menu_item', array( $_object ) );
				$_menu_item = array_shift( $_menu_items );
	
				// Restore the missing menu item properties
				$menu_item_data['menu-item-description'] = $_menu_item->description;
			}
	
			$menu_items_data[] = $menu_item_data;
		}
	
		$item_ids = wp_save_nav_menu_items( 0, $menu_items_data );
		if ( is_wp_error( $item_ids ) )
			wp_die( 0 );
	
		$menu_items = array();

		foreach ( (array) $item_ids as $menu_item_id ) {
			$menu_obj = get_post( $menu_item_id );
			if ( ! empty( $menu_obj->ID ) ) {
				$menu_obj = wp_setup_nav_menu_item( $menu_obj );
				$menu_obj->label = $menu_obj->title; // don't show "(pending)" in ajax-added items
				$menu_items[] = $menu_obj;
			}
		}
	
		if ( ! empty( $menu_items ) ) {
			$args = array(
				'after' => '',
				'before' => '',
				'link_after' => '',
				'link_before' => '',
				//'walker' => new $walker_class_name,
				'walker' =>	new AzuMenuWalkerEdit,			//EDIT FOR AZUMENU
			);
			echo trim( walk_nav_menu_tree( $menu_items, 0, (object) $args ) );
		}
		wp_die();
	}



	function getNavMenus(){
		$menus = array();
		foreach( wp_get_nav_menus() as $m ){
			if( is_object( $m ) ) $menus[$m->slug] = $m->name;
		}
		return $menus;
	}


	function menuItemCustomOptions( $item_id ){
		
		?>
	
			<!--  START MEGAWALKER ATTS -->
			<div class="azu_clear">	
				<div class="azumega-atts azumega-unprocessed">
					<input id="azu_options-<?php echo $item_id;?>" class="azu_options_input" name="azu_options[<?php echo $item_id;?>]" 
						type="hidden" value="" />

					<?php $this->showMenuOptions( $item_id ); ?>
									
				</div>
				<!--  END MEGAWALKER ATTS -->
			</div>
	<?php
	}

	function showMenuOptions( $item_id ){

		global $azuMenu;
		$settings = $azuMenu->getSettings();

		$this->showCustomMenuOption(
			'isMega', 
			$item_id, 
			array(
				'level'	=> '0', 
				'title' => _x( 'Make this item\'s submenu a mega menu.  Leave unchecked to use a flyout menu, or for menu items without submenus (this will remove the submenu indicator).' , 'azumenu', 'azzu'.LANG_DN ), 
				'label' => _x( 'Enable Mega Menu' , 'azumenu', 'azzu'.LANG_DN ), 
				'type' 	=> 'checkbox',
				'default' => 'off'
			)
		);
		
		
		
		$this->showCustomMenuOption(
			'fullWidth', 
			$item_id, 
			array(
				'level' => '0', 
				'title' => _x( 'Make this item\'s submenu the full width of the menu bar.  Note that with javascript disabled, submenus are full-width by default.  This is horizontal-orientation specific.  To make a vertical menu full-width, set the width appropriately in the Basic Configuration Options.' , 'azumenu', 'azzu'.LANG_DN ), 
				'label' => _x( 'Full Width Submenu' , 'azumenu', 'azzu'.LANG_DN ), 
				'type' 	=> 'checkbox', 
                                'default' => 'on'
			)
		);

		$this->showCustomMenuOption(
			'alignSubmenu', 
			$item_id, 
			array(
				'level' => '0-plus', 
				'title' => _x( 'Select where to align the submenu.  Note that only mega submenus can be centered, and only if jQuery Enhancements are enabled.  Horizontal-orientation specific.' , 'azumenu', 'azzu'.LANG_DN ), 
				'label' => _x( 'Align Submenu' , 'azumenu', 'azzu'.LANG_DN ), 
				'type' 	=> 'select', // 'checkbox',
				'ops'	=>	array(
                                        'left'	=>	_x( 'Left' , 'azumenu', 'azzu'.LANG_DN ),
                                        'right'	=>	_x( 'Right' , 'azumenu', 'azzu'.LANG_DN ),
				),
				'default'	=>	'left'
			)
		);

		
		$this->showCustomMenuOption(
			'numCols', 
			$item_id, 
			array(
				'level' => '1', 
				'title' => _x( '<strong>Only valid for full-width submenus</strong>.  Set how many columns should be in each row in the submenu.  Columns will be sized evenly.' , 'azumenu', 'azzu'.LANG_DN ), 
				'label' => _x( 'Submenu Columns [FullWidth]' , 'azumenu', 'azzu'.LANG_DN ), 
				'type' 	=> 'select', // 'checkbox',
				'ops'	=>	array(
					'auto'	=> _x( 'Automatic' , 'azumenu', 'azzu'.LANG_DN ),
					'1'		=>	_x( '1 column - 1/12', 'azumenu', 'azzu'.LANG_DN ),
					'2'		=>	_x( '2 columns - 1/6', 'azumenu', 'azzu'.LANG_DN ),
					'3'		=>	_x( '3 columns - 1/4', 'azumenu', 'azzu'.LANG_DN ),
					'4'		=>	_x( '4 columns - 1/3', 'azumenu', 'azzu'.LANG_DN ),
					'5'		=>	_x( '5 columns - 5/12', 'azumenu', 'azzu'.LANG_DN ),
					'6'		=>	_x( '6 columns - 1/2', 'azumenu', 'azzu'.LANG_DN ),
					'7'		=>	_x( '7 columns - 7/12', 'azumenu', 'azzu'.LANG_DN ),
					'8'		=>	_x( '8 columns - 2/3', 'azumenu', 'azzu'.LANG_DN ),
					'9'		=>	_x( '9 columns - 3/4', 'azumenu', 'azzu'.LANG_DN ),
					'10'	=>	_x( '10 columns - 5/6', 'azumenu', 'azzu'.LANG_DN ),
					'11'	=>	_x( '11 columns - 11/12', 'azumenu', 'azzu'.LANG_DN ),
                                        '12'	=>	_x( '12 columns - 1/1', 'azumenu', 'azzu'.LANG_DN ),
				),
				'default'	=>	'auto'
			)
		);
		
		
		//here
		
		$this->showCustomMenuOption(
			'isheader', 
			$item_id, 
			array(
				'level' => '1-plus', 
				'title' => _x( 'Display this item as a header, like second-level menu items.  Good for splitting columns vertically without starting a new row' , 'azumenu', 'azzu'.LANG_DN ), 
				'label' => _x( 'Header Display' , 'azumenu', 'azzu'.LANG_DN ), 
				'type' => 'checkbox', 
			)
		);

		$this->showCustomMenuOption(
			'highlight', 
			$item_id, 
			array(
				'level' => '0-plus', 
				'title' => _x( 'Make this item stand out.' , 'azumenu', 'azzu'.LANG_DN ), 
				'label' => _x( 'Highlight this item' , 'azumenu', 'azzu'.LANG_DN ), 
				'type' => 'checkbox', 
			)
		);

//		$this->showCustomMenuOption(
//			'newcol', 
//			$item_id, 
//			array(
//				'level' => '2-plus', 
//				'title' => _x( 'Use this on the item that should start the second column under the same header - for example, have two columns under "Sports"' , 'azumenu', 'azzu'.LANG_DN ), 
//				'label' => _x( 'Start a new column (under same header)?' , 'azumenu', 'azzu'.LANG_DN ), 
//				'type' => 'checkbox', 
//			)
//		);

		$this->showCustomMenuOption(
			'horizontaldivision', 
			$item_id, 
			array(
				'level' => '1', 
				'title' => _x( 'Bottom line after this item.' , 'azumenu', 'azzu'.LANG_DN ), 
				'label' => _x( 'Bottom Divider' , 'azumenu', 'azzu'.LANG_DN ), 
				'type' => 'checkbox', 
			)
		);

		$this->showCustomMenuOption(
			'nolink', 
			$item_id, 
			array(
				'level' => '0-plus', 
				'title' => _x( 'Remove the link altogether.  Can be used, for example, with content overrides or widgets.' , 'azumenu', 'azzu'.LANG_DN ), 
				'label' => _x( 'Disable Link' , 'azumenu', 'azzu'.LANG_DN ), 
				'type' 	=> 'checkbox', 
			)
		);
		
			

	}

	function getMenuItemOption( $item_id , $id ){		

		$option_id = 'menu-item-'.$id;

		//We haven't investigated this item yet
		if( !isset( $this->menuItemOptions[ $item_id ] ) ){
			
			$azu_options = get_post_meta( $item_id , '_azu_options', true );
			//If $azu_options are set, use them
			if( $azu_options ){
				//echo '<pre>'; print_r( $azu_options ); echo '</pre>';
				$this->menuItemOptions[ $item_id ] = $azu_options;
			} 
			//Otherwise get the old meta
			else{
				return get_post_meta( $item_id, '_menu_item_'.$id , true );
			}
		}

		return isset( $this->menuItemOptions[ $item_id ][ $option_id ] ) ? stripslashes( $this->menuItemOptions[ $item_id ][ $option_id ] ) : '';

	}

	function showCustomMenuOption( $id, $item_id, $args ){
		extract( wp_parse_args(
			$args, array(
				'level'	=> '0-plus',
				'title' => '',
				'label' => '',
				'type'	=> 'text',
				'ops'	=>	array(),
				'default'=> '',
			) )
		);

		$_val = $this->getMenuItemOption( $item_id , $id );
	
		global $azuMenu;
		$settings = $azuMenu->getSettings();
		
		$desc = '<span class="azu-desc">'.$label.'</span>';
		?>
				<p class="field-description description description-wide azumega-custom azumega-l<?php echo $level;?> azumega-<?php echo $id;?>">
					<label for="edit-menu-item-<?php echo $id;?>-<?php echo $item_id;?>">
						
						<?php
						
						switch($type) {
							
							case 'text': 
								echo $desc;
								?>						
								<input type="text" id="edit-menu-item-<?php echo $id;?>-<?php echo $item_id;?>" 
									class="edit-menu-item-<?php echo $id;?>" 
									name="menu-item-<?php echo $id;?>[<?php echo $item_id;?>]" 
									size="30" 
									value="<?php echo htmlspecialchars( $_val );?>" />
								<?php
								
								break;
							case 'iconpicker':
								echo $desc;
								?>
								<input type="text" id="edit-menu-item-<?php echo $id;?>-<?php echo $item_id;?>" 
									class="edit-menu-item-<?php echo $id;?> azuIconPicker" 
									name="menu-item-<?php echo $id;?>[<?php echo $item_id;?>]" 
									size="30" 
									value="<?php echo htmlspecialchars( $_val );?>" />
								<?php
																
								break;
							case 'textarea':
								echo $desc;
								?>
								<textarea id="edit-menu-item-<?php echo $id;?>-<?php echo $item_id;?>"
									 class="edit-menu-item-<?php echo $id;?>"
									 name="menu-item-<?php echo $id;?>[<?php echo $item_id;?>]" ><?php
										echo htmlspecialchars( $_val );
									 ?></textarea>
								<?php
								break;

							case 'checkbox':
								?>
								<input type="checkbox" 
									id="edit-menu-item-<?php echo $id;?>-<?php echo $item_id;?>" 
									class="edit-menu-item-<?php echo $id;?>" 
									name="menu-item-<?php echo $id;?>[<?php echo $item_id;?>]" 
									<?php
										if ( ( $_val == '' && $default == 'on' ) || 
												$_val == 'on')
											echo 'checked="checked"';
									?> />
								<?php
								echo $desc;
								break;
								
							case 'select':
								echo $desc;
								//$_val = get_post_meta($item_id, '_menu_item_' . $id, true);
								if( empty($_val) ) $_val = $default;
								?>
								<select 
									id="edit-menu-item-<?php echo $id; ?>-<?php echo $item_id; ?>"
									class="edit-menu-item-<?php echo $id; ?>"
									name="menu-item-<?php echo $id;?>[<?php echo $item_id;?>]">
									<?php foreach( $ops as $opval => $optitle ): ?>
										<option value="<?php echo $opval; ?>" <?php if( $_val == $opval ) echo 'selected="selected"'; ?> ><?php echo $optitle; ?></option>
									<?php endforeach; ?>
								</select>
								<?php
								break;
								
							case 'sidebarselect':
								echo $desc;
                                                                $sb = $azuMenu->sidebarList();
								if( !empty($sb) ){
									echo $azuMenu->sidebarSelect( $item_id , $_val );
								}
								else echo '<div><small class="azu_clear">'._x( 'You currently have 0 widget areas set in your AzuMenu options.' , 'azumenu', 'azzu'.LANG_DN ).'</small></div>';
								break;
	
						}
 						?>
						
					</label>
				</p>
	<?php
	}

	
	//utility helper
	function orDefault( $val , $default , $zeroValid = false ){	
		if( $zeroValid ){
			if( $val === 0 || $val === '0' ) return $val;
		}
		return empty( $val ) ? $default : $val;
	}
	//utility helper
	function colorOrTransparent( $val, $default = 'transparent' ){
		if( empty( $val ) ){
			$val = $default;
		}
		//Only add '#' if it doesn't already exist
		else if( strpos( $val , '#' ) === false ) $val = '#'.$val;
		return $val;
	}

	
	/*
	 * Escape newlines, tabs, and carriage returns
	 */
	function escapeNewlines($html){
		
		$html = str_replace("\n", '\\n', $html);
		$html = str_replace("\t", '\\t', $html);
		$html = str_replace("\r", '\\r', $html);
		
		return $html;
		
	}
	
	
	/*
	 * Prints a json response
	 */
	function JSONresponse($data){
			
		header('Content-Type: application/json; charset=UTF-8');	//Set the JSON header so that JSON will validate Client-Side
		
		echo '{ '.$this->buildJSON($data).' }';					//Send the response
			
		die();
	}

	/*
	 * Builds a json object from an array
	 */
	function buildJSON($ar){
		if($ar == null) return '';
		$txt = '';
		$count = count($ar);
		$k = 1;
		foreach($ar as $key=>$val){	
			$comma = ',';
			if($count == 1 || $count == $k) $comma = '';
			
			if(is_array($val)){
				$txt.= '"'.$key.'" : { ';
				$txt.= $this->buildJSON($val);	//recurse
				$txt.= ' }'.$comma."\n ";
			}
			else{
				$quotes = is_numeric($val) ? FALSE : TRUE;	
				$txt.= '"' . str_replace('-', '_', $key).'" : ';
				if($quotes) $txt.= '"';
				$txt.= str_replace('"','\'', $val);
				if($quotes) $txt.= '"';
				$txt.= $comma."\n";			
			}
			$k++;
		}
		return $txt;
	}
	

	function checkPostLimits(){
		$screen = get_current_screen();
		if( $screen->id != 'nav-menus' ) return;

		$currentPostVars_count = $this->countPostVars();
		
		$r = array(); //restrictors

		$r['suhosin_post_maxvars'] = ini_get( 'suhosin.post.max_vars' );
		$r['suhosin_request_maxvars'] = ini_get( 'suhosin.request.max_vars' );
		$r['max_input_vars'] = ini_get( 'max_input_vars' );


		if( $r['suhosin_post_maxvars'] != '' ||
			$r['suhosin_request_maxvars'] != '' ||
			$r['max_input_vars'] != '' ){


			$message = array();

			if( ( $r['suhosin_post_maxvars'] != '' && $r['suhosin_post_maxvars'] < 1000 ) || 
				( $r['suhosin_request_maxvars']!= '' && $r['suhosin_request_maxvars'] < 1000 ) ){
				$message[] = _x( 'Your server is running Suhosin, and your current maxvars settings may limit the number of menu items you can save.' , 'azumenu', 'azzu'.LANG_DN );
			}


			//150 ~ 10 left
			foreach( $r as $key => $val ){
				if( $val > 0 ){
					if( $val - $currentPostVars_count < 150 ){
						$message[] = sprintf(_x( 'You are approaching the post variable limit imposed by your server configuration.  Exceeding this limit may automatically delete menu items when you save.  Please increase your <strong>$key</strong> directive in php.ini.  <a href="%s">More information</a>' , 'azumenu', 'azzu'.LANG_DN ),'http://www.wpbeginner.com/wp-tutorials/how-to-fix-the-custom-menu-items-limit-in-wordpress/');
					}
				}
			}

			if( !empty( $message ) ): ?>
			<div class="azzu-infobox azzu-infobox-warning error">
				<h4><?php _ex( 'Menu Item Limit Warning' , 'azumenu', 'azzu'.LANG_DN ); ?></h4>
				<ul>
				<?php foreach( $message as $m ): ?>
					<li><?php echo $m; ?></li>
				<?php endforeach; ?>
				</ul>

				<?php
				if( $r['max_input_vars'] != '' ) echo "<strong>max_input_vars</strong> :: ". 
					$r['max_input_vars']. " <br/>";
				if( $r['suhosin_post_maxvars'] != '' ) echo "<strong>suhosin.post.max_vars</strong> :: ".$r['suhosin_post_maxvars']. " <br/>";
				if( $r['suhosin_request_maxvars'] != '' ) echo "<strong>suhosin.request.max_vars</strong> :: ". $r['suhosin_request_maxvars'] ." <br/>";
				
				echo "<br/><strong>".__( 'Menu Item Post variable count on last save','azzu'.LANG_DN )."</strong> :: ". $currentPostVars_count."<br/>";
				if( $r['max_input_vars'] != '' ){
					$estimate = ( $r['max_input_vars'] - $currentPostVars_count ) / 15;
					if( $estimate < 0 ) $estimate = 0;
					echo "<strong>"._x( 'Approximate remaining menu items' , 'azumenu', 'azzu'.LANG_DN )."</strong> :: " . floor( $estimate );
				};

				echo "<br/><br/>Loaded configuration file: ". php_ini_loaded_file();

				?>


			</div>
			<?php endif; 

		}

	}

	function countPostVars() {

		if( isset( $_POST['save_menu'] ) ){

			$count = 0;
	    	foreach( $_POST as $key => $arr ){
				$count+= count( $arr );
			}

			update_option( 'azumenu-post-var-count' , $count );
		}
		else{
			$count = get_option( 'azumenu-post-var-count' , 0 );
		}

		return $count;
	}

	
}