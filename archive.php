<?php 



azum()->set('template', 'archive');

get_header(); ?>

    <div id="primary" class="<?php azus()->_class('azu-content-area'); ?>">
        <div id="main" class="<?php azus()->_class('azu-main'); ?>" role="main">
		
		<?php do_action( 'azzu_before_loop' ); ?>
		
        <?php if ( have_posts() ) : ?>

            <?php /* Start the Loop */ ?>
            <?php while ( have_posts() ) : the_post(); ?>
                      <?php
                                get_template_part( 'content' );
                      ?>
            <?php endwhile; ?>

            <?php azut()->azu_paginator(); ?>

        <?php else : ?>

            <?php azut()->azzu_no_result(); ?>

        <?php endif; ?>
		
		<?php do_action( 'azzu_after_loop' ); ?>
		
        </div><!-- #main -->
    </div><!-- #primary -->
	
	<?php do_action('azzu_after_content'); ?>
	

<?php get_footer(); ?>
