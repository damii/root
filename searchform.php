<?php 

?>

<form role="search" method="get" class="<?php azus()->_class('search-form'); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input type="search" class="<?php azus()->_class('search-field'); ?>" placeholder="<?php echo esc_attr( _x( 'Search &hellip;', 'atheme', 'azzu'.LANG_DN) ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php echo esc_attr( _x( 'Search for:', 'atheme', 'azzu'.LANG_DN) ); ?>">
    <i class="azu-icon-search"></i>
    <input type="submit" class="<?php azus()->_class('search-submit'); ?>" value="<?php echo esc_attr(''); ?>">
</form>
