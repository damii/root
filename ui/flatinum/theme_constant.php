<?php
/**
 * theme constant
 *
 * @package azzu
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/* Sets the default font. */
if ( !defined( 'AZZU_THEME_DEFAULT_FONT' ) ) {
	define( 'AZZU_THEME_DEFAULT_FONT', 'Roboto' );
}

/* Page title header tag. */
if ( !defined( 'AZU_PAGE_TITLE_H' ) ) {
	define( 'AZU_PAGE_TITLE_H', 'h1' );
}

/* Theme gutter default width. */
if ( !defined( 'AZZU_THEME_GUTTER' ) ) {
	define( 'AZZU_THEME_GUTTER', 30 );
}

/* Theme mobile default width. */
if ( !defined( 'AZZU_THEME_MOBILE_WIDTH' ) ) {
	define( 'AZZU_THEME_MOBILE_WIDTH', 768 );
}

/* Page sub title header tag. */
if ( !defined( 'AZU_TITLE_H' ) ) {
	define( 'AZU_TITLE_H', 'h2' );
}


/* Page post header tag. */
if ( !defined( 'AZU_POST_TITLE_H' ) ) {
	define( 'AZU_POST_TITLE_H', 'h3' );
}

/* Post header tag. */
if ( !defined( 'AZU_REL_POST_TITLE_H' ) ) {
	define( 'AZU_REL_POST_TITLE_H', 'h5' );
}


/* Page post header tag. */
if ( !defined( 'AZU_WIDGET_TITLE_H' ) ) {
	define( 'AZU_WIDGET_TITLE_H', 'h4' );
}


/* Page portfolio header tag. */
if ( !defined( 'AZU_PORTFOLIO_TITLE_H' ) ) {
	define( 'AZU_PORTFOLIO_TITLE_H', 'h3' );
}


/* Page team header tag. */
if ( !defined( 'AZU_TEAM_AUTHER_H' ) ) {
	define( 'AZU_TEAM_AUTHER_H', 'h3' );
}
