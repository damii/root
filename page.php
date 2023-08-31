<?php 

azum()->base_init();
azum()->set('template', 'page');

if ( azum()->get('page_override') ) {
        // add menu filter here
        add_filter( 'azu_menu_options', array('azu_tags','azzu_page_menu_filter') );

        $hide_header = azum()->get('page_header');
        $hide_topbar = azum()->get('top_bar-show');
        $hide_menu = azum()->get('page_menu');
        $hide_page_title = azum()->get('page_page_title');
        $hide_floating_menu = azum()->get('header-show_floating_menu');

        if ( $hide_topbar ) 
                add_filter( 'azzu_show_topbar', '__return_false' );

        if ( $hide_header && $hide_floating_menu ) {
                add_filter( 'azzu_show_header', '__return_false' );
        } else if ( $hide_header ) {
                // see main_tags.class.php
                add_filter( 'azzu_header_classes', array( 'azu_tags','azzu_page_header_classes') );
        }
        if( $hide_page_title )
            add_filter( 'azzu_show_page_title', '__return_true' );
        if( $hide_menu )
            add_filter( 'azzu_show_mainmenu', '__return_false' );

        if ( $hide_floating_menu )
                add_filter( 'azzu_floating_menu', '__return_true' );


        if ( azum()->get('page_bottom_bar') ) {
                add_filter( 'azzu_show_bottom_bar', '__return_false' );
        }
}

get_header(); ?>

        <div id="primary" class="<?php azus()->_class('azu-content-area'); ?>">
            <div id="main" class="<?php azus()->_class('azu-main'); ?>" role="main">

                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <?php do_action('azzu_before_loop'); ?>
                    <?php get_template_part( 'content' ); ?>
                    <?php azuh()->azzu_display_share_buttons( 'page' ); ?>
                    <?php
                        
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) 
				comments_template();
                    ?>

                <?php endwhile; // end of the loop. ?>
                <?php else : ?>
			<?php azut()->azzu_no_result(); ?>
		<?php endif; ?>

            </div><!-- #main -->
        </div><!-- #primary -->
            <?php do_action('azzu_after_content'); ?>
<?php get_footer(); ?>
