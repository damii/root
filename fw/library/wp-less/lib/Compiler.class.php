<?php
if ( !class_exists('lessc') ) {
	require dirname(__FILE__).'/vendor/lessphp/lessc.inc.php';
}

/**
 * LESS compiler
 *
 * @author oncletom
 * @extends lessc
 * @package wp-less
 * @subpackage lib
 * @since 1.2
 * @version 1.3
 */
class WPLessCompiler extends lessc
{
	/**
	 * Instantiate a compiler
	 *
   * @api
	 * @see	lessc::__construct
	 * @param $file	string [optional]	Additional file to parse
	 */
	public function __construct($file = null)
	{
  	do_action('wp-less_compiler_construct_pre', $this, $file);
		parent::__construct(apply_filters('wp-less_compiler_construct', $file));
	}

  /**
   * Registers a set of functions
   *
   * @param array $functions
   */
  public function registerFunctions(array $functions = array())
  {
    foreach ($functions as $name => $args)
    {
      $this->registerFunction($name, $args['callback']);
    }
  }

	/**
	 * Returns available variables
	 *
	 * @since 1.5
	 * @return array Already defined variables
	 */
	public function getVariables()
	{
		return $this->registeredVars;
	}

	public function setVariable($name, $value)
	{
		$this->registeredVars[ $name ] = $value;
	}

	public function getImportDir()
	{
		return (array)$this->importDir;
	}

	/**
	 * Smart caching and retrieval of a tree of @import LESS stylesheets
	 *
	 * @since 1.5
	 * @param WPLessStylesheet $stylesheet
	 * @param bool $force
	 */
	public function cacheStylesheet(WPLessStylesheet $stylesheet, $force = false)
	{
		$azu_ajax_only_css = (isset($_POST['wp_customize']) && $_POST['wp_customize']=='on' && !(isset($_POST['action']) && $_POST['action'] =='customize_save'));
		$cache_name = 'wp_less_compiled_'.md5($stylesheet->getSourcePath());
		$s_cache_name = 'wp_less_stylesheet_data_'.md5($stylesheet->getSourcePath());

		$compiled_cache = get_transient($cache_name);
		
		if( !$force && !file_exists( $stylesheet->getTargetPath() ) ) $force = true;

		$compiled_cache = $this->cachedCompile($compiled_cache ? $compiled_cache : $stylesheet->getSourcePath(), $force);
		
		// saving compiled stuff
		if (isset($compiled_cache['compiled']) && $compiled_cache['compiled'])
		{
			$stylesheet->setSourceTimestamp($compiled_cache['updated']);
			if(!$azu_ajax_only_css)
				$this->saveStylesheet($stylesheet, $compiled_cache['compiled']);

			// since 30.09.2013
			remove_action( 'wp-less_stylesheet_save_post', 'azzu_stylesheet_is_writable' );
			$s_cache = array();
			$s_cache['target_uri'] = $stylesheet->getTargetUri();

			// if can not create compiled css - save pure css
			if ( !get_option( 'azzu_less_css_is_writable' ) ||  $azu_ajax_only_css) {
				$s_cache['compiled'] = preg_replace( "/\r|\n/", "", $compiled_cache['compiled'] );
				$s_cache['compiled'] = apply_filters('wp-less_stylesheet_save', $s_cache['compiled'], $stylesheet);
			}
			
			if($azu_ajax_only_css)
                            $s_cache_name .= '_ajax';
			update_option( $s_cache_name, $s_cache );

			$compiled_cache['compiled'] = NULL;

			set_transient( $cache_name, $compiled_cache );

		}
	}

	/**
	 * Process a WPLessStylesheet
	 *
	 * This logic was previously held in WPLessStylesheet::save()
	 *
	 * @since 1.4.2
	 * @param WPLessStylesheet $stylesheet
	 * @param null $css
	 */
	public function saveStylesheet(WPLessStylesheet $stylesheet, $css = null)
	{
		wp_mkdir_p(dirname($stylesheet->getTargetPath()));

		try
		{
			do_action('wp-less_stylesheet_save_pre', $stylesheet, $this->getVariables());

			if ($css === null)
			{
				$css = $this->compileFile($stylesheet->getSourcePath());
			}

			if ( false === azu_file_put_c( $stylesheet->getTargetPath(), apply_filters('wp-less_stylesheet_save', $css, $stylesheet) ) ) {
				throw new Exception("Error Saving Stylesheet", 1);
			}

			chmod( $stylesheet->getTargetPath(), 0666 );

			$stylesheet->save();
			do_action('wp-less_stylesheet_save_post', $stylesheet);
		}
		catch(Exception $e)
		{
			// wp_die($e->getMessage());
			do_action( 'wp-less_save_stylesheet_error', $e );
		}
	}
}
