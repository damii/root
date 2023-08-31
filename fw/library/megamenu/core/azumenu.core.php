<?php

require_once( 'AzuMenu.class.php' );
require_once( 'AzuMenuWalkerCore.class.php' );

function azuMenu_direct( $theme_location = AZUMENU_LOCATION , $filter = true , $echo = true , $args = array() ){
	global $azuMenu;
	return $azuMenu->directIntegration( $theme_location, $filter , $echo , $args );
}


if( !function_exists( 'azussd' ) ):
function azussd($v){
	echo '<pre>';
	print_r( $v );
	echo '</pre>';
}
endif;