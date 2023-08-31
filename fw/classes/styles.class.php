<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


// Implement the interface
// This will work
if ( ! class_exists('azu_styles') ) :
class azu_styles extends azu_base
{
  protected function add_actions(){}
  protected $classname = array();
 
  public function __construct()
  {
      parent::__construct();
      //$this->classname = array();
  }

  function __destruct() {
       //unset($this->$classname);
  }

  public function reset( $class = array() ) {
        $this->classname = $class;
  }
  
  public function set($name,$value = null)
  {
      $this->classname[$name] = $value;
  }
  
  
  public function _class($name='', $helper=''){
        echo $this->get($name, $helper);
  }
  
  public function get($name='', $helper='')
  {
      $class_names = $this->_get($name, $helper);
      if($name == 'azu-content-area'){
            $sidebar_position = azuf()->azu_get_option('sidebar_position',of_get_option('sidebar_position'));
            
            if($sidebar_position=='dual'){
                if(azuf()->azu_get_option('sidebar_wide',0))
                    $sidebar_iswide = 4;
                else 
                    $sidebar_iswide = 6;
            }
            else if(azuf()->azu_get_option('sidebar_wide',0))
                $sidebar_iswide = 8;
            else
                $sidebar_iswide = 9;
            
            if ( is_404() || is_search()  )
                $class_names.=' col-sm-12';
            else if($sidebar_position=='left')
                $class_names.=' azu-content-left col-sm-'.$sidebar_iswide;
            else if($sidebar_position=='right')
                $class_names.=' azu-content-right col-sm-'.$sidebar_iswide;  
            else if($sidebar_position=='dual')
                $class_names.=sprintf(' azu-content-dual col-sm-push-%s col-sm-%s', (12 - $sidebar_iswide)/2 ,$sidebar_iswide);  
            else
                $class_names.=' col-sm-12';
      }
      return $class_names;
  }
  
  protected function _get($name='', $helper='')
  {
  		if ( '' == $name ) {
			return $this->classname;
		}
                
                if(!empty($helper))
                {
                        $helpers = explode(" ", $helper);
                        $helper='';

                        foreach($helpers as $hs) {
                                $helper .= ' '.$this->_get_( $hs );
                        }
                }
                
		if ( array_key_exists( $name, $this->classname ) ) 
			return $this->classname[ $name ] . $helper;
		
		return $name. (!empty($helper) ? ' '.trim($helper) : '');
  }
  
  protected function _get_($name=''){
        if ( array_key_exists( $name, $this->classname ) )
            return $this->classname[ $name ];
        return $name;
  }
  
}
endif; // style