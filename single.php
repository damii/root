<?php 

azum()->base_init();
azum()->set('template', 'single');
get_header(); ?>

	<div id="primary" class="<?php azus()->_class('azu-content-area'); ?>">
		<div id="main" class="<?php azus()->_class('azu-main'); ?>" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', str_replace( 'azu_', '', get_post_type() ) ); ?>

                        
			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() )
					comments_template();
			?>

		<?php endwhile; // end of the loop. ?>

		</div><!-- #main -->
	</div><!-- #primary -->
        <?php do_action('azzu_after_content'); ?>
<?php get_footer(); ?>
