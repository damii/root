<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! class_exists('style_class') ) :
class style_class extends azu_styles 
{
  public function __construct()
  {
      $main_container='container';
      $azu_containers=array();
      $azu_layout = azuf()->azu_get_option('general-layout', of_get_option('general-layout','wide'));
      $containers=array('azu-topbar-container','azu-title','azu-navigation-field','azu-header-field','azu-content-row','azu-footer-field','azu-bottombar-field');
      if($azu_layout=='boxed'){
            $main_container='container';
            $this->styleObj['azu-page'] .= ' container nopadding';
      }
      else if($azu_layout=='menu'){
            //$azu_containers=array('azu-topbar','azu-navigation','azu-header','azu-page-header','azu-content','azu-footer','azu-bottombar');
            $azu_containers=array('azu-topbar','azu-header','azu-page-header','azu-content','azu-footer','azu-bottombar');
      }
      else if($azu_layout=='top'){
            $azu_containers=array('azu-navigation','azu-header','azu-page-header','azu-content','azu-footer');
      }
      else if($azu_layout=='header'){
            $azu_containers=array('azu-page-header','azu-content');
      }
      
      foreach ($azu_containers as $value) {
         $this->styleObj[$value] .= ' container nopadding';
      }
      
      foreach ($containers as $value) {
         $this->styleObj[$value] .= ' '.$main_container;
      }
      
      $header_layout = of_get_option('header-layout','left');
      if($header_layout == 'center'){
          $this->styleObj['azu-branding'] .=' col-sm-12';
          $this->styleObj['azu-header-desc'] .=' col-sm-12';
      }
      else if($header_layout == 'right'){
          $this->styleObj['azu-branding'] .=' col-sm-6';
          $this->styleObj['site-title'] .=' col-sm-6';
          $this->styleObj['azu-header-desc'] .=' col-sm-6';
      }
      
      $this->reset($this->styleObj);
  }
  
  public $styleObj = array(
  ///* #header start */
	"azu-header" => 'azu-header',
	"azu-branding" => 'azu-branding',
        "site-title" => 'site-title',
	"azu-title" => 'azu-title',
	"azu-header-desc" => 'azu-header-desc',
	"azu-navigation" => 'navbar navbar-azu azumm azu-navigation',
	"azu-navigation-field" => 'azu-navigation-field',
        "azu-navbar" => 'navbar-nav',
	"azu-content" => 'azu-content',
        "azu-content-row" => 'azu-content-row',
        "azu-content-column" => 'azu-content-column row',
        //"azu-mini-nav" => 'azu-mini-nav',
        //"azu-mini-menu" => 'azu-mini-menu',
        //"azu-slider-container" => 'azu-slider-container',
        //"azu-nav-transparent" => 'azu-nav-transparent',
        //"azu-nav-start" => 'azu-nav-start',
	
	//"azu-mastheader" => 'azu-mastheader',
	"azu-page" => 'azu-page', //hfeed site
	//404
	"azu-error-404" => 'azu-error-404 not-found',
	"azu-page-header" => 'azu-page-header',
        "azu-header-field" => 'azu-header-field',
	//"azu-page-title" => 'azu-page-title',
	//"azu-page-content" => 'azu-page-content',
        //"azu-breadcrumb" => 'azu-breadcrumb',
	//archive
	//"vcard" => 'vcard',
	//"taxonomy-description" => 'taxonomy-description',
        //top bar
        "azu-topbar-arrow" => 'azu-topbar-arrow hidden-lg hidden-md',
        "azu-topbar" => 'azu-topbar',
        "azu-topbar-container" => 'azu-topbar-container',
        "azu-topbar-inside" => 'azu-topbar-inside row',
        //"azu-widget-contact" => 'azu-widget-contact',
        //"azu-topbar-content" => 'azu-topbar-content',
	// #header end	
  
  ///* #content start */
        "content-blog-media" => 'content-blog-media nopadding',
	//"azu-entry-header" => 'azu-entry-header',
	//"azu-entry-title" => 'azu-entry-title',
	//"azu-entry-meta" => 'azu-entry-meta',
	//"azu-entry-summary" => 'azu-entry-summary',
	//"azu-entry-content" => 'azu-entry-content',
	//"azu-comments-link" => 'azu-comments-link',
	//"azu-meta-nav" => 'azu-meta-nav',
	//"azu-page-links" => 'azu-page-links',
	//"azu-edit-link" => 'azu-edit-link',
	//"azu-content-area" => 'azu-content-area', 
	//"azu-main" => 'azu-main',
	// comment
	"comments-area" => 'comments-area container-fluid nopadding',
	//"comments-title" => 'comments-title',
	//"comment-navigation" => 'comment-navigation',
	//"screen-reader-text" => 'screen-reader-text',
	//"nav-previous" => 'nav-previous',
	//"nav-next" => 'nav-next',
	//"comment-list" => 'comment-list',
	//"screen-reader-text" => 'screen-reader-text',
        //"form-fields" => 'form-fields row',
        "comment-form-author" => 'form-group comment-form-author', //col-sm-4
        "comment-form-email" => 'form-group comment-form-email', //col-sm-4
        "comment-form-url" => 'form-group comment-form-url', //col-sm-4
        //"comment-form-comment" => 'comment-form-comment',
        "form-allowed-tags" => 'form-allowed-tags help-block',
        //"must-log-in" => 'must-log-ins',
        //"logged-in-as" => 'logged-in-as',
        "comment-notes" => 'comment-notes help-block',
	// no-result
	"no-results" => 'no-results not-found',
	// search form
	//"search-form" => 'search-form',
	"search-field" => 'search-field form-control',
	//"search-submit" => 'search-submit',
	// sidebar
	//"azu-sidebar" => 'azu-sidebar', 
        //"azu-sidebar-column" => 'azu-sidebar-column', 
	//"azu-sidebar-area" => 'azu-sidebar-area',
	// image
	//"entry-date" => 'entry-date', 
	//"entry-caption" => 'entry-caption',
	//"image-attachment" => 'image-attachment',
		
        //post
        //"azu-post" => 'azu-post',
        //"azu-readmore" => 'azu-readmore',
        //"azu-entry-author" => 'azu-entry-author',
        "azu-rel-post-cell" => 'azu-rel-post-cell col-sm-6',
        "azu-rel-post-container" => 'azu-rel-post-container container-fluid nopadding',
        //"azu-post-meta" => 'azu-post-meta',
        //"azu-entry-share" => 'azu-entry-share',
        //"azu-mfp-item" => 'azu-mfp-item',
        //"azu-rollover" => 'azu-rollover',
  ///* #content end */
  
  ///* #footer start */
  	"azu-footer" => 'azu-footer',
	"azu-footer-field" => 'azu-footer-field',
      	"azu-footer-container" => 'azu-footer-container row',
        //"azu-scroll-top-wrapper" => 'azu-scroll-top-wrapper',
        //"azu-scroll-top-inner" => 'azu-scroll-top-inner',
        //"azu-scroll-icon" => 'azu-scroll-icon',
        //"azu-page-loader" => 'azu-page-loader',
        //bottom bar
        "azu-bottombar" => 'azu-bottombar',
        "azu-bottombar-field" => 'azu-bottombar-field',
        "azu-bottombar-row" => 'azu-bottombar-row row',
        //"azu-bottombar-td" => 'azu-bottombar-td',
        //"azu-bottombar-td-1" => 'azu-bottombar-td-1',
        //"azu-bottombar-td-2" => 'azu-bottombar-td-2',
        //"azu-bottombar-td-3" => 'azu-bottombar-td-3',
        //"azu-bottombar-td-4" => 'azu-bottombar-td-4',
        //"azu-bottombar-copy" => 'azu-bottombar-copy',
  ///* #footer end */
  
  
  ///* other start */
      "team-container" => 'team-container container-fluid nopadding',
      //"hr-thick" => 'hr-thin',
      "azu-mobile-hide" => 'hidden-sm hidden-xs',
  ///* other end */
  );
  


}
endif; // style


