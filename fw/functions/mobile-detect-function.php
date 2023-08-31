<?php
/**
 * Mobile Detect.
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * azu_mobile_detect.
 *
 */
function azu_mobile_detect(){
            $device_type = '0';
            if ( isset( $_COOKIE['azu_mobile_detect'] ) && !empty($_COOKIE['azu_mobile_detect']) )
                $device_type = absint($_COOKIE['azu_mobile_detect']);
            else {
                if ( !class_exists('Mobile_Detect') ) 
                    require_once( AZZU_LIBRARY_DIR . '/mobile-detect.php' );
                // detect device type
                $detect = new Mobile_Detect;
                $device_type = ($detect->isMobile() ? ($detect->isTablet() ? '2' : '1') : '0');
                if( !(is_admin() || azu_is_login_page()))
                    setcookie( 'azu_mobile_detect', $device_type, time()+86400, COOKIEPATH, COOKIE_DOMAIN, false);
            }
            if ( !defined( 'AZZU_MOBILE_DETECT' ) )
                define( 'AZZU_MOBILE_DETECT',  $device_type);
}