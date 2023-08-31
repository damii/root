<?php 

//legacy
define('AZUMENU_NOTEXT', '--notext--');
define('AZUMENU_SKIP', '--divide--');
define('AZUMENU_DIVIDER', '<hr />'); // '<div class="azumega-divider"></div>');

/*
 * Walker for the Front End AzuMenu
 */
class AzuMenuWalkerCore extends Walker_Nav_Menu{

	protected $index = 0;
	protected $menuItemOptions;
	protected $noAzuOps;

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Calls parent function in wp-includes/claazu-wp-walker.php
	 */
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

		if ( !$element )
			return;

                $id_parent = $this->db_fields['parent'];
		$rootMega = false;
                if ($args instanceof stdClass)
			$args = array();
		else if( isset( $args['azumenu'] ) && is_array( $args['azumenu'] ) )
			$rootMega = $args['azumenu']['isMega'];
			
		if($depth == 0) 
			$rootMega = ( $this->getAzuOption( $element->ID, 'isMega' ) == 'on' );
		else if($depth == 1 && !isset( $args['azumenu']))
			$rootMega = ( $this->getAzuOption( $element->$id_parent, 'isMega' ) == 'on' );

		if ($rootMega)
			$args['azumenu'] =  array ( "isMega" => $rootMega);
		
		$azu_class = "";

		if( $depth == 1 && $rootMega )
			$azu_class .= "dropdown-menu azumm-content";
		else if($rootMega && $depth > 0)
			$azu_class .= "azu-mega-menu-item";
		else
			$azu_class .= "dropdown-menu";
		
		if($rootMega && $depth == 1){
			if( $this->getAzuOption( $element->$id_parent, 'fullWidth' ) == 'on' )
				$azu_class .= ' fullwidth';
			else
				$azu_class .= ' half';
		}
		$output = str_replace("__AZUMEGAMENUREP__",$azu_class,$output);
                
		if( $rootMega ){
			if($depth == 1) {
				$totalCol = 12;
				//Menu Item Columns
				$numCols = $this->getAzuOption( $element->ID, 'numCols' );
				if( is_numeric( $numCols ) && $numCols < $totalCol && $numCols > 0 ){
					$element->classes[] = 'col-sm-'.$numCols;
				}
				else {
                                        $current_id = false;
					$sz_parent = sizeof($children_elements[ $element->$id_parent ]);
                                        $j=0;
					foreach ($children_elements[ $element->$id_parent ] as $pel){
						$numCols = $this->getAzuOption( $pel->ID, 'numCols' );
                                                if($element->ID==$pel->ID)
                                                    $current_id=true;
                                                if($pel->title == AZUMENU_SKIP){
                                                    if($current_id) {
                                                        $sz_parent = $j;
                                                        break;
                                                    }
                                                    else
                                                    {
                                                        $sz_parent = $sz_parent - $j -1;
                                                        $totalCol=12;
                                                    }
                                                }
						if( is_numeric( $numCols ) && $numCols < $totalCol && $numCols > 0 ){
							$sz_parent--;
							$totalCol = $totalCol - $numCols;
						}
                                                $j++;
					}
					if($sz_parent <= 0)
						$sz_parent = 1;
					$element->classes[] = 'col-sm-'.(int)($totalCol/$sz_parent);
				}
			}
		}
		//Add indicators for top level menu items with submenus
		$id_field = $this->db_fields['id'];
		if (!empty( $children_elements[ $element->$id_field ] ) ) {
			if ( $depth > 0 )  {
				if(!$rootMega)
					$element->classes[] = 'dropdown-submenu';
			}
			else {
				$element->classes[] = 'dropdown';
			}
		}

		Walker_Nav_Menu::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

	function start_lvl( &$output , $depth = 0 , $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		if( $depth == 0 )
			$output .= "\n$indent<ul class=\"__AZUMEGAMENUREP__\" role=\"menu\" >\n";
		else 
			$output .= "\n$indent<ul class=\"__AZUMEGAMENUREP__\" >\n";
	}
	
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function getAzuOption( $item_id , $id ){
		//get_post_meta or from azu_options, depending on whether azu_options is set

		$option_id = 'menu-item-'.$id;

		//Initialize array
		if( !is_array( $this->menuItemOptions ) ){
			$this->menuItemOptions = array();
			$this->noAzuOps = array();
		}

		//We haven't investigated this item yet
		if( !isset( $this->menuItemOptions[ $item_id ] ) ){
			
			$azu_options = false;
			if( empty( $this->noAzuOps[ $item_id ] ) ) {
				$azu_options = get_post_meta( $item_id , '_azu_options', true );
				if( !$azu_options ) $this->noAzuOps[ $item_id ] = true; //don't check again for this menu item
			}

			//If $azu_options are set, use them
			if( $azu_options ){
				$this->menuItemOptions[ $item_id ] = $azu_options;
			} 
			//Otherwise get the old meta
			else{
				$option_id = '_menu_item_'.$id;
				return get_post_meta( $item_id, $option_id , true );
			}
		}
		return isset( $this->menuItemOptions[ $item_id ][ $option_id ] ) ? stripslashes( $this->menuItemOptions[ $item_id ][ $option_id ] ) : '';
	}
	
	function start_el( &$output, $item, $depth = 0, $args = array() , $current_object_id = 0 ){

		global $azuMenu;
		$settings = $azuMenu->getSettings();
		
		//For --Divides-- with no Content
		if( ( $item->title == '' || $item->title == AZUMENU_SKIP ) ){ 
                        $divider_class='dropdown-divider';
                        if($depth==1)
                            $divider_class.=' col-sm-12';
			if( $item->title == AZUMENU_SKIP ) $output.= '<li id="menu-item-'. $item->ID.'" class="'.$divider_class.'">'.AZUMENU_DIVIDER; //.'</li>'; 
			return; 
		}	//perhaps the filter should be called here
				  
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
 
		//Handle class names depending on menu item settings
		$class_names = $value = '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		
		//The Basics
		//if( $depth == 0 ) $classes[] = 'azu-nav-menu-item-'.$this->index++;
		//$classes[] = 'azu-nav-menu-item-depth-'.$depth;
		   
		//Megafy (top level)
		if( $depth == 0 && $this->getAzuOption( $item->ID, 'isMega' ) != 'off' ){
			$classes[] = 'azumm-fw';
		}
		//Flyouts
		else if($depth == 0){
			//Submenu Alignment
			$alignment = $this->getAzuOption( $item->ID, 'alignSubmenu' );	//right, left
			if( empty( $alignment )) $alignment = 'left';
			$classes[] = 'azu-flyout-align-'. $alignment;
		}
                else if($depth > 0){// && !($args instanceof stdClass) && $args['azumenu']['isMega']){
			$alignment = $this->getAzuOption( $item->ID, 'alignSubmenu' );	//right, left
			if( empty( $alignment )) $alignment = 'left';
			$classes[] = 'azu-nav-submenu-align-'. $alignment;
		}
		
//		//Right Align
//		if( $depth == 0 && $this->getAzuOption( $item->ID , 'floatRight' ) == 'on' ) $classes[] = 'azu-menu-item-float-right';
				
		//Second Level - Vertical Division
		if($depth == 1){
			if( $this->getAzuOption( $item->ID, 'horizontaldivision' ) == 'on' ) $classes[] = 'azu-nav-menu-divider';
		}
		
		//Third Level
		if($depth >= 1){
			if( $this->getAzuOption( $item->ID, 'isheader' ) == 'on' ) $classes[] = 'azu-nav-menu-header';			//Headers
//			if( $this->getAzuOption( $item->ID, 'newcol' ) == 'on' ){												//New Columns
//				$output.= '</ul></li>';
//				$output.= '<li class="col-sm-2">'.
//							'<span class="azu-anchoremulator">&nbsp;</span><ul class="">';
//			}
		}
		
		//Highlight
		if( $this->getAzuOption( $item->ID, 'highlight' ) == 'on' ) $classes[] = 'azu-nav-menu-highlight';		//Highlights
				
		
		//NoLink		
		$nolink = $this->getAzuOption( $item->ID, 'nolink' ) == 'on' ? true : false;
		
		
		$prepend = '<span class="azumega-link-title">';
		$append = '</span>';

		//Icon
		$icon = $this->getAzuOption( $item->ID, 'icon' );
		if( $icon ){
			$icon_markup = '<i class="'.apply_filters( 'azumenu-icon-class' , $icon ).'"></i> ';
                        if($menu_image_position)
                            $prepend = $icon_markup.$prepend; //If text, append to title
                        else
                            $append .= $icon_markup; 
			$classes[] = 'azu-nav-menu-with-icon';
		}

		//Description
		$description  = ! empty( $item->description ) ? '<span class="azumega-item-description">'. $item->description .'</span>' : '';
		
		if(	(	$depth == 0		&& 	!$settings->op( 'azumega-description-0' ) )	||
			(	$depth == 1		&& 	!$settings->op( 'azumega-description-1' ) )	||
			(	$depth >= 2		&& 	!$settings->op( 'azumega-description-2' ) )  ){
			$description = '';
		}
		
		if( !empty( $description ) ) $classes[] = 'azu-nav-menu-with-desc';
		
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="'. esc_attr( $class_names ) . '"';

		$output .= /*$indent . */'<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		//$attributes .= ! empty( $item->class )      ? ' class="'  . esc_attr( $item->class      ) .'"' : '';

		
		
		$item_output = '';
		
		/* Add title and normal link content - skip altogether if nolink and notext are both checked */
		if( !empty( $item->title ) && trim( $item->title ) != '' ){
			
			//Determine the title
			$title = apply_filters( 'the_title', $item->title, $item->ID );
			if( $item->title == AZUMENU_NOTEXT ) $title = $prepend = $append = '';

			//Horizontal Divider automatically skips the link
			if( $item->title == AZUMENU_SKIP ){
				$item_output.= AZUMENU_DIVIDER;
			}
			//A normal link or link emulator
			else{
				$item_output = $args->before;
				
				//To link or not to link?
				if( $nolink )  $item_output.= '<a href="#">';
				else $item_output.= '<a'. $attributes .'>';
					
					//Link Before (not added by AzuMenu)
					if( !$nolink ) $item_output.= $args->link_before;
				
						//Text - Title
						$item_output.= $prepend . $title . $append;
				
						//Description
						$item_output.= $description;
				
					//Link After (not added by AzuMenu)
					if( !$nolink ) $item_output.= $args->link_after;
				
				//Close Link or emulator
				$item_output.= '</a>';
				
				//Append after Link (not added by AzuMenu)
				$item_output .= $args->after;
			}
		}
		
		
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		
	}
	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>";

	}
}


class AzuMenuWalkerEdit extends Walker_Nav_Menu  {
	
	/**
	 * @see Walker_Nav_Menu::start_lvl()
	 * @since 1.0
	 *
	 * @param string $output Passed by reference.
	 */
	function start_lvl( &$output , $depth = 0 , $args = array() ) {}

	/**
	 * @see Walker_Nav_Menu::end_lvl()
	 * @since 1.0
	 *
	 * @param string $output Passed by reference.
	 */
	function end_lvl( &$output , $depth = 0 , $args = array() ) {}

	/**
	 * @see Walker::start_el()
	 * @since 1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param object $args
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		ob_start();
		$item_id = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) )
				$original_title = false;
		} elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title = $original_object->post_title;
		}

		$classes = array(
			'menu-item dropdown-submenu menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
		);

		$title = $item->title;

		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( __( '%s (Invalid)', 'azzu'.LANG_DN ), $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( __('%s (Pending)', 'azzu'.LANG_DN), $item->title );
		}

		$title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

		$submenu_text = '';
		if ( 0 == $depth )
			$submenu_text = 'style="display: none;"';

		?>
		<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
			<dl class="menu-item-bar">
				<dt class="menu-item-handle">
					<span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo $submenu_text; ?>><?php _e( 'sub item', 'azzu'.LANG_DN ); ?></span></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
								echo esc_url_raw(wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-up-menu-item',
											'menu-item' => urlencode($item_id),
										),
										esc_url( remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) ) )
									),
									'move-menu_item'
								));
							?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up','azzu'.LANG_DN); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
								echo esc_url_raw( wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-down-menu-item',
											'menu-item' => urlencode($item_id),
										),
										esc_url(remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) ))
									),
									'move-menu_item'
								));
							?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down','azzu'.LANG_DN); ?>">&#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item','azzu'.LANG_DN); ?>" href="<?php
							echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : esc_url(add_query_arg( 'edit-menu-item', $item_id, esc_url_raw( remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) ) ) );
						?>"><?php _e( 'Edit Menu Item', 'azzu'.LANG_DN ); ?></a>
					</span>
				</dt>
			</dl>

			<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
				<?php if( 'custom' == $item->type ) : ?>
					<p class="field-url description description-wide">
						<label for="edit-menu-item-url-<?php echo $item_id; ?>">
							<?php _e( 'URL', 'azzu'.LANG_DN ); ?><br />
							<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
						</label>
					</p>
				<?php endif; ?>
				<p class="description description-thin">
					<label for="edit-menu-item-title-<?php echo $item_id; ?>">
						<?php _e( 'Navigation Label', 'azzu'.LANG_DN ); ?><br />
						<input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
					</label>
				</p>
				<p class="description description-thin">
					<label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
						<?php _e( 'Title Attribute', 'azzu'.LANG_DN ); ?><br />
						<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
					</label>
				</p>
				<p class="field-link-target description">
					<label for="edit-menu-item-target-<?php echo $item_id; ?>">
						<input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
						<?php _e( 'Open link in a new window/tab', 'azzu'.LANG_DN); ?>
					</label>
				</p>
				<p class="field-css-classes description description-thin">
					<label for="edit-menu-item-classes-<?php echo $item_id; ?>">
						<?php _e( 'CSS Classes (optional)', 'azzu'.LANG_DN ); ?><br />
						<input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
					</label>
				</p>
				<p class="field-xfn description description-thin">
					<label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
						<?php _e( 'Link Relationship (XFN)', 'azzu'.LANG_DN ); ?><br />
						<input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
					</label>
				</p>
				<p class="field-description description description-wide">
					<label for="edit-menu-item-description-<?php echo $item_id; ?>">
						<?php _e( 'Description', 'azzu'.LANG_DN ); ?><br />
						<textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
						<span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.', 'azzu'.LANG_DN); ?></span>
					</label>
				</p>

				<p class="field-move hide-if-no-js description description-wide">
					<label>
						<span><?php _e( 'Move', 'azzu'.LANG_DN ); ?></span>
						<a href="#" class="menus-move-up"><?php _e( 'Up one', 'azzu'.LANG_DN ); ?></a>
						<a href="#" class="menus-move-down"><?php _e( 'Down one', 'azzu'.LANG_DN ); ?></a>
						<a href="#" class="menus-move-left"></a>
						<a href="#" class="menus-move-right"></a>
						<a href="#" class="menus-move-top"><?php _e( 'To the top', 'azzu'.LANG_DN ); ?></a>
					</label>
				</p>
				
				<?php do_action( 'azumenu_menu_item_options', $item_id );?>

				<div class="menu-item-actions description-wide submitbox">
					<?php if( 'custom' != $item->type && $original_title !== false ) : ?>
						<p class="link-to-original">
							<?php printf( __('Original: %s', 'azzu'.LANG_DN), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
						</p>
					<?php endif; ?>
					<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
					echo esc_url_raw(wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'delete-menu-item',
								'menu-item' => urlencode($item_id),
							),
							admin_url( 'nav-menus.php' )
						),
						'delete-menu_item_' . urlencode($item_id)
					)); ?>"><?php _e( 'Remove', 'azzu'.LANG_DN ); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => urlencode($item_id), 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
						?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel', 'azzu'.LANG_DN); ?></a>
				</div>

				<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
				<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
				<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
				<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
				<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
				<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}
}

