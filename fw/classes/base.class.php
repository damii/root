<?php
/**
 * Parent class.
 *
 * @since azzu 1.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


// Declare the interface base interface
interface azu_ibase
{
    
}

if ( ! class_exists('azu_styles') ) :
//
// base class
// 
abstract class azu_base implements azu_ibase {
    protected function __construct() {
        $this->add_actions();
    }
    protected static $instance = array();
    public static function get_instance($className='',$classPath='') {
            if( empty($className))
                if(function_exists('get_called_class'))
                    $className = get_called_class();
                
            if( empty($classPath) )
                $classPath = AZZU_UI_DIR . '/'.AZZU_DESIGN.'/'.strtolower($className).'.php';
            if ( !isset(self::$instance[$className]) ) {
                    require_once( $classPath );
                    if(class_exists($className)) {
                        self::$instance[$className] = new $className;
                    }
            }

            return self::$instance[$className];
    }
    
    abstract protected function add_actions();

}
endif;