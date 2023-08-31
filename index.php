<?php 


azum()->set('template', 'blog');

get_header(); ?>

    <div id="primary" class="<?php azus()->_class('azu-content-area'); ?>">
        <div id="main" class="<?php azus()->_class('azu-main'); ?>" role="main">

        <?php if ( have_posts() ) : ?>

			<?php do_action( 'azzu_before_loop' ); ?>
			
            <?php /* Start the Loop */ ?>
            <?php while ( have_posts() ) : the_post(); ?>

                <?php
                    /* Include the Post-Format-specific template for the content.
                     * If you want to override this in a child theme, then include a file
                     * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                     */
                    get_template_part( 'content', get_post_format() );
                ?>

            <?php endwhile; ?>

			<?php do_action( 'azzu_after_loop' ); ?>
			
            <?php azut()->azu_paginator(); ?>

        <?php else : ?>

            <?php azut()->azzu_no_result(); ?>

        <?php endif; ?>

        </div><!-- #main -->
    </div><!-- #primary -->
	
	<?php do_action('azzu_after_content'); ?>

<?php get_footer(); ?>
