<?php

class AzuMenuWalker extends AzuMenuWalkerCore{

	function start_el( &$output, $item, $depth = 0, $args = array(), $current_object_id = 0 ){

		global $azuMenu;
		$settings = $azuMenu->getSettings();
		
		//Test override settings
		$override = $this->getAzuOption( $item->ID, 'shortcode' );
		$overrideOn = $settings->op( 'azumega-shortcodes' ) && !empty( $override ) ? true : false;
		
		//Test sidebar settings
		$sidebar = $this->getAzuOption( $item->ID, 'sidebars' );
		$sidebarOn = ( $settings->op( 'azumega-top-level-widgets' ) || $depth > 0 ) && !empty( $sidebar ) ? true : false;
		
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
//		if( $depth == 0) {
//			if( $this->getAzuOption( $item->ID , 'floatRight' ) == 'on' ) $classes[] = 'azu-menu-item-float-right';
//		}
				
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
		
		//Thumbnail
                $img_width = $img_height = $settings->op( 'azumega-image-width' );
                if( $depth == 0)
                    $square_size = of_get_option('header-icons_size');
                else
                    $square_size = of_get_option('header-submenu_icons_size');
                if(is_array($square_size)){
                    $img_width = $square_size['width'];
                    $img_height = $square_size['height'];
                }
		$thumb = $azuMenu->azu_getImage( $item->ID, $img_width, $img_height );
		if( !empty( $thumb ) ) $classes[] = 'azu-nav-menu-with-img';
		
		//NoText, NoLink		
		$notext = $this->getAzuOption( $item->ID, 'notext' ) == 'on' || $item->title == AZUMENU_NOTEXT ? true : false;
		$nolink = $this->getAzuOption( $item->ID, 'nolink' ) == 'on' ? true : false;
		
		if( $notext ) $classes[] = 'azu-nav-menu-notext';
		if( $nolink ) $classes[] = 'azu-nav-menu-nolink';
		
		if( $sidebarOn  ) $classes[] = 'azu-sidebar';
		if( $overrideOn ) $classes[] = 'azu-override';
		
		$prepend = '<span class="azumega-link-title">';
		$append = '</span>';

		//Icon
		$icon = $this->getAzuOption( $item->ID, 'icon' );
                $menu_image_position = in_array(of_get_option('menu-image-position'), array('left','top'));
                

		//Description
		$description  = ! empty( $item->description ) ? '<span class="azumega-item-description">'. $item->description .'</span>' : '';
		
		if(	(	$depth == 0		&& 	!$settings->op( 'azumega-description-0' ) )	||
			(	$depth == 1		&& 	!$settings->op( 'azumega-description-1' ) )	||
			(	$depth >= 2		&& 	!$settings->op( 'azumega-description-2' ) )  ){
			$description = '';
		}
		
		if( !empty( $description ) ) $classes[] = 'azu-nav-menu-with-desc';
		

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = esc_attr( $class_names ) ;
		
                if (strpos($item->url,'#') === false){
                    // current-menu-item to raplace bootstrap active
                    $class_names = str_replace("current-menu-item","current-menu-item azu-act",$class_names);
                }
		$hasChildren = false;
                
                
                $hasChildren = (strpos($class_names, 'menu-item-has-children') !== FALSE);
                
		
		
//		$class_str = explode(' ',$class_names);
//		$class_names="";
//		foreach ($class_str as $classname){
//			if(!(0 === strpos($classname, 'menu-item')))
//				$class_names.= ' '.$classname;		
//		}
//		$class_names= trim($class_names);
		$attributes='';
		if ($hasChildren) {
			if($depth == 0)
				$attributes .=  ' class="dropdown-toggle" data-toggle="dropdown" ';
		}
		
                if ($hasChildren && ($depth == 0) && of_get_option('header-caret-style') !='none')
                    $append .= '<b class="caret"></b>'; 

		if( $icon ){
			$icon_markup = '<i class="'.apply_filters( 'azumenu-icon-class' , $icon ).'"></i> ';
                        if($menu_image_position)
                            $prepend = $icon_markup.$prepend; //If text, append to title
                        else
                            $append .= $icon_markup; 
			$classes[] = 'azu-nav-menu-with-icon';
		}
                //Prepend Thumbnail
                if($menu_image_position)
                    $prepend = $thumb.$prepend; //If text, append to title
                else
                    $append .= $thumb; 


		$output .= /*$indent . */'<li id="menu-item-'. $item->ID . '"' . $value .' class="'. $class_names .'">';

		$attributes .= ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		
		$item_output = '';
		
		/* Add title and normal link content - skip altogether if nolink and notext are both checked */
		if( !empty( $item->title ) && trim( $item->title ) != '' && !( $nolink && $notext && !$icon ) ){

			//Determine the title
			$title = apply_filters( 'the_title', $item->title, $item->ID );
			if( $item->title == AZUMENU_NOTEXT || ( $notext && !$icon ) ) $title = $prepend = $append = '';

			//Horizontal Divider automatically skips the link
			if( $item->title == AZUMENU_SKIP ){
				$item_output.= AZUMENU_DIVIDER;
			}
			//A normal link or link emulator
			else{
				$item_output = $args->before;

				//Allow shortcodes in Title/Description?
				if( $settings->op( 'title-shortcodes' ) ){
					$title = do_shortcode( $title );
					$description = do_shortcode( $description );
				}

				//To link or not to link?
				if( $nolink )  $item_output.= '<a href="#">';
				else $item_output.= '<a'. $attributes .'>';
								

					//Link Before (not added by AzuMenu)
					if( !$nolink ) $item_output.= $args->link_before;
				
						//Text - Title
						if( !$notext ) $item_output.= $prepend . $title . $append;
						elseif( $icon ) $item_output.= $prepend . $append;
				
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
		
		/* Add overrides and widget areas */
		if( $overrideOn || $sidebarOn ){
			$class = 'azumega-nonlink';
			
			//Get the widget area or shortcode
			$gooeyCenter = '';
			//Content Overrides
			if( $overrideOn ){
				$gooeyCenter = do_shortcode( $override );
			}
			//Widget Areas
			if( $sidebarOn ){
				$class.= ' azumega-widgetarea azu-colgroup-'.$azuMenu->sidebarCount( $sidebar );	
				$gooeyCenter = $azuMenu->sidebar( $sidebar );
			}
			
			$item_output.= '<div class="'.$class.' clearfix">';
			$item_output.= $gooeyCenter;
			//$item_output.= '<div class="clear"></div>';
			$item_output.= '</div>';
		}
		

		
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}




}