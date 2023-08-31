<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( of_get_option('top_bar-show', 1) ) : ?>



<?php 
$class_hide = '';
$topbar_arrow_style = '';
if(of_get_option( 'top_bar-arrow'))
{
    $class_hide = 'azu-mobile-hide';
}
 else {
    $topbar_arrow_style = 'display: none;';
}
$topbar_arrow = '<span id="topbar-arrow" class="'.azus()->get('azu-topbar-arrow').'" style="'.$topbar_arrow_style.'"></span>';

?>
<!-- !Top-bar -->
<div id="azu-topbar" class="<?php azus()->_class('azu-topbar'); ?>" role="complementary">
	<div class="<?php azus()->_class('azu-topbar-container'); ?>">
		<div class="<?php azus()->_class('azu-topbar-inside', $class_hide); ?>">
                    
                        <?php
                        ////////////////////
                        // Top bar menu   //
                        ////////////////////

                        azuh()->azzu_nav_menu_list('top','col-sm-auto');
                        ////////////////////
                        // Top Bar text   //
                        ////////////////////
                        $topbar_text = of_get_option('top-bar-text', '');
                        ?>
                        <div class="<?php azus()->_class( 'azu-topbar-text','col-sm-auto'); ?>" style="<?php $topbar_text ? '' :'display: none;' ?>">
                                <?php echo '<span>'.$topbar_text.'</span>';  ?>
                        </div>
                        <?php

                        ////////////////////
                        // Top bar widget //
                        ////////////////////
                        azut()->azzu_widget_location('Topbar');
                        
                        ?>

		</div><!-- .inside -->
                <?php echo $topbar_arrow; ?>
	</div><!-- .container -->
</div><!-- #top-bar -->

<?php endif; ?>



