<?php
/**
 * Attachment template.
 *
 * @package azzu
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
azum()->set('template', 'image');

get_header(); ?>

                        <div id="primary" class="<?php azus()->_class('azu-content-area'); ?>">
                            <div id="main" class="<?php azus()->_class('azu-main'); ?>" role="main">

				<?php if ( have_posts() ) : ?>

					<?php while ( have_posts() ) : the_post(); ?>
                                                    <?php
                                                    get_template_part( 'content' );
                                                    azuh()->azzu_display_share_buttons( 'photo', array( 'extended' => true) );
                                                    ?>
					<?php endwhile; ?>

				<?php endif; ?>

                            </div><!-- #main -->
                        </div><!-- #primary -->

			<?php do_action('azzu_after_content'); ?>

<?php get_footer(); ?>