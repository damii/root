<?php 

/*
Name: AzuMenu - WordPress Mega Menu
Version: 1.0
*/

/* Constants */

define( 'AZUMENU_LOCATION', 'primary' );

global $azuMenu;
/* Load Required Files */
require_once( 'core/azumenu.core.php' );
require_once( 'core/AzuMenuMegamenu.class.php' );
require_once( 'core/AzuMenuWalker.class.php' );
$azuMenu = new AzuMenuStandard(AZZU_LIBRARY_URI . '/megamenu/core/');

?>