<?php
/**
 * Single Product Meta
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );


        ob_start(); ?>
                <?php do_action( 'woocommerce_product_meta_start' ); ?>
                <?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
                        <span class="sku_wrapper"><span><?php _e( 'SKU:', 'azzu'.LANG_DN  ); ?></span> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'azzu'.LANG_DN  ); ?></span></span>
                <?php endif; ?>
                <?php echo $product->get_tags( ', ', '<span class="tagged_as"><span>' . _n( 'Tag:', 'Tags:', $tag_count, 'azzu'.LANG_DN  ) . '</span> ', '</span>' ); ?>
                <?php do_action( 'woocommerce_product_meta_end' ); ?>
        <?php $azu_meta = ob_get_clean();

if(trim($azu_meta)) :
?>
<div class="product_meta">
    <?php echo $azu_meta; ?>
</div>
<?php

endif;
