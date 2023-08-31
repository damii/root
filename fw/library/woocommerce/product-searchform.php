<form role="search" method="get" class="woocommerce-product-search search-form" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
	<label class="screen-reader-text" for="s"><?php _e( 'Search for:', 'azzu'.LANG_DN ); ?></label>
	<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search Products&hellip;', 'placeholder', 'azzu'.LANG_DN ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'azzu'.LANG_DN ); ?>" />
	<i class="azu-icon-search"></i>
        <input type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'azzu'.LANG_DN ); ?>" />
	<input type="hidden" name="post_type" value="product" />
</form>
