<?php 

get_header(); ?>

    <div id="primary" class="<?php azus()->_class('azu-content-area'); ?>">
        <div id="main" class="<?php azus()->_class('azu-main'); ?>" role="main">

            <div class="<?php azus()->_class('azu-error-404'); ?>">
                <header class="<?php azus()->_class('azu-result-header'); ?>">
                    <<?php echo AZU_PAGE_TITLE_H; ?> class="<?php azus()->_class('azu-result-title'); ?>"><?php _ex( 'Oops! That page can&rsquo;t be found.', 'atheme', 'azzu'.LANG_DN); ?></<?php echo AZU_PAGE_TITLE_H; ?>>
                </header><!-- .page-header -->

                <div class="<?php azus()->_class('azu-page-content'); ?>">
                    <p><?php _ex( 'It looks like nothing was found at this location. Maybe try to use a search?', 'atheme', 'azzu'.LANG_DN); ?></p>

                    <?php get_search_form(); 
                    ////////////////////
                    // Header widget //
                    ////////////////////
                    azut()->azzu_widget_location('404','azu-404-desc'); ?>
                </div><!-- .page-content -->
            </div><!-- .error-404 -->

        </div><!-- #main -->
    </div><!-- #primary -->
	
	<?php do_action('azzu_after_content'); ?>
	
<?php get_footer(); ?>
