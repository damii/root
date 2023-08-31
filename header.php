<?php
/**
 * The Header for our theme.
 *
 *
 * @package azzu
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?><!DOCTYPE html>
<!--[if lt IE 9 ]><html class="old-ie" <?php language_attributes(); ?>><![endif]--> 
<!--[if (gt IE 8) | !(IE)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url');?>">
    <link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url');?>">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
	<script src="<?php echo AZZU_JS_URI;?>/html5.js" type="text/javascript"></script>
	<![endif]-->
<style type="text/css" id="static-stylesheet"></style>
<?php 

    if ( !is_preview() ) {
            echo azuh()->azzu_favicon();
    }

    wp_head();
    
    if ( !is_preview() ){
        // Custom JS or Google analytics code
        echo '<script id="azu_custom_js">'.of_get_option('general-tracking_code', '').'</script>';
    }
?>
</head>

<body <?php body_class(); ?>>
    
    <div id="preloader" class="<?php azus()->_class('azu-page-loader'); ?>" <?php if(!of_get_option('general-preloader',1) || AZZU_MOBILE_DETECT!='0'): ?>style="display:none;"<?php endif; ?> onClick="this.style.opacity = 0;"></div>
    <?php if((of_get_option('general_body_padding', 0) > 0 && azum()->get('border_padding') == 0) || azum()->get('border_padding') > 0 ): ?>
        <div class="azu-pageborder azu-pageborder-top" <?php echo is_admin_bar_showing() ? 'style="margin-top: 32px;"':''; ?>></div>
        <div class="azu-pageborder azu-pageborder-left"></div>
        <div class="azu-pageborder azu-pageborder-bottom"></div>
        <div class="azu-pageborder azu-pageborder-right"></div>
    <?php endif; ?>
        <?php do_action( 'azzu_body_top' ); ?>
<div id="page" class="<?php azus()->_class('azu-page'); ?>">
        <?php get_template_part( 'ui/'.AZZU_DESIGN.'/templates/header'); ?>
        <?php do_action( 'azzu_before_main_container' ); ?>
        
    <div id="content" class="<?php azus()->_class('azu-content'); ?>">
        <div class="<?php azus()->_class('azu-content-row'); ?>">
            <div  class="<?php azus()->_class('azu-content-column'); ?>">
                <?php do_action( 'azzu_before_content' ); ?>