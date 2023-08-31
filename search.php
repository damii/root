<?php 

azum()->set('template', 'search');
$attr = azum()->get('attr');
$attr['proportion'] = 1;
$attr['image_size'] = 2;
$attr['column_width'] = 200 * azuf()->azu_device_pixel_ratio();
azum()->set('attr', $attr);
global $wp_query;
get_header(); ?>

    <div id="primary" class="<?php azus()->_class('azu-content-area'); ?>">
        <div id="main" class="<?php azus()->_class('azu-main','azu-search-results'); ?>" role="main">

        <?php if ( have_posts() ) : ?>

            <div class="">
                <<?php echo AZU_TITLE_H; ?>><?php _ex( 'New Search', 'atheme', 'azzu'.LANG_DN); ?></<?php echo AZU_TITLE_H; ?>>
                <p><?php _ex( 'If you are not happy with the results below please do another search', 'atheme', 'azzu'.LANG_DN); ?><p>
                <?php get_search_form(); ?>
            </div>
            <div class="hr-thin"></div>
            
            <?php if ( !of_get_option( 'general-show_titles', '1' ) ) : ?>
                <header class="<?php azus()->_class('azu-result-header'); ?>">
                    <<?php echo AZU_PAGE_TITLE_H; ?> class="<?php azus()->_class('azu-result-title'); ?>"><?php printf( _x( '%s Search results for: %s', 'atheme', 'azzu'.LANG_DN), $wp_query->found_posts, '<span>' . get_search_query() . '</span>' ); ?></<?php echo AZU_PAGE_TITLE_H; ?>>
                </header><!-- .page-header -->
            <?php endif; ?>

            <?php 
                //remove_filter('get_the_excerpt', 'wp_trim_excerpt');
                //remove_filter('the_excerpt', 'do_shortcode');
                //remove_all_filters('the_excerpt');
                //add_filter('get_the_excerpt', array('azu_tags','azzu_excerpt_search_filter')); 
            ?>
            <?php /* Start the Loop */ ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'content' ); ?>
            <?php endwhile; ?>

            <?php azut()->azu_paginator(); ?>
            <?php    
                //remove_filter('get_the_excerpt', array('azu_tags','azzu_excerpt_search_filter'));
            ?>
        <?php else : ?>

            <?php azut()->azzu_no_result(); ?>

        <?php endif; ?>

        </div><!-- #main -->
    </div><!-- #primary -->

<?php get_footer(); ?>
