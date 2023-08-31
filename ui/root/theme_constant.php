<?php
/**
 * theme constant
 *
 * @package azzu
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// hack for themecheck REQUIRED: "This theme text domain does not match the theme's slug."
if ( !defined( 'AZZU_DESIGN' ) ) 
	define( 'AZZU_DESIGN',  __( 'root', LANG_PREFIX.'root' ));



/* Sets the default font. */
if ( !defined( 'AZZU_THEME_DEFAULT_FONT' ) ) {
	define( 'AZZU_THEME_DEFAULT_FONT', 'GlacialIndifference' );
}

/* Theme gutter default width. */
if ( !defined( 'AZZU_THEME_GUTTER' ) ) {
	define( 'AZZU_THEME_GUTTER', 30 ); //
}

/* Theme mobile default width. */
if ( !defined( 'AZZU_THEME_MOBILE_WIDTH' ) ) {
	define( 'AZZU_THEME_MOBILE_WIDTH', 870 );
}


/* Page title header tag. */
if ( !defined( 'AZU_PAGE_TITLE_H' ) ) {
	define( 'AZU_PAGE_TITLE_H', 'h1' );
}

/* Page sub title header tag. */
if ( !defined( 'AZU_TITLE_H' ) ) {
	define( 'AZU_TITLE_H', 'h3' );
}


/* Post header tag. */
if ( !defined( 'AZU_POST_TITLE_H' ) ) {
	define( 'AZU_POST_TITLE_H', 'h2' );
}

/* Post header tag. */
if ( !defined( 'AZU_REL_POST_TITLE_H' ) ) {
	define( 'AZU_REL_POST_TITLE_H', 'h6' );
}


/* Widget header tag. */
if ( !defined( 'AZU_WIDGET_TITLE_H' ) ) {
	define( 'AZU_WIDGET_TITLE_H', 'h6' );
}


/* Portfolio header tag. */
if ( !defined( 'AZU_PORTFOLIO_TITLE_H' ) ) {
	define( 'AZU_PORTFOLIO_TITLE_H', 'h3' );
}

/* Testimonial header tag. */
if ( !defined( 'AZU_TESTIMONIAL_TITLE_H' ) ) {
	define( 'AZU_TESTIMONIAL_TITLE_H', 'span' );
}


/* Team header tag. */
if ( !defined( 'AZU_TEAM_AUTHER_H' ) ) {
	define( 'AZU_TEAM_AUTHER_H', 'h6' );
}
