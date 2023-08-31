<?php
/**
 * Product quantity inputs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="quantity azu-input-group">
          <span class="azu-input-group-btn">
              <button type="button" class="azu-btn-number" disabled="disabled" data-type="minus" data-field="<?php echo esc_attr( $input_name ); ?>">
                  <i class="azu-spinner-minus"></i>
              </button>
          </span>
            <input type="text" step="<?php echo esc_attr( $step ); ?>" <?php if ( is_numeric( $min_value ) ) : ?>min="<?php echo esc_attr( $min_value ); ?>"<?php endif; ?> <?php if ( is_numeric( $max_value ) ) : ?>max="<?php echo esc_attr( $max_value ); ?>"<?php endif; ?> name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'azzu'.LANG_DN ) ?>" class="input-text qty text azu-input-number" size="4" autocomplete="off" />
          <span class="azu-input-group-btn">
              <button type="button" class="azu-btn-number" data-type="plus" data-field="<?php echo esc_attr( $input_name ); ?>">
                  <i class="azu-spinner-plus"></i>
              </button>
          </span>
</div>