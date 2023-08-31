<?php
// Exit if accessed directly
if (! defined ( 'ABSPATH' )) {
	exit ();
}

?>

		<?php
                        $logo = azuh()->azzu_get_logo_image ( 'header-logo' ,of_get_option('header-bg-height',100) );
                ?>
                        <div id="branding" style="<?php echo !$logo ? 'display: none;' :''; ?>" class="<?php azus()->_class('azu-branding'); ?>">
				<?php echo $logo; ?>
                        </div>
                <?php 
                        if (!$logo) :
		?>
                    <<?php echo AZU_TITLE_H; ?> class="<?php azus()->_class('site-title',of_get_option('general-site-title') ? '': 'azu-seo-text');?>"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></<?php echo AZU_TITLE_H; ?>>
                    <div id="site-description" class="site-description azu-seo-text"><?php bloginfo( 'description' ); ?></div>
                <?php endif; ?>

