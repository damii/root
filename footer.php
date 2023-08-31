<?php
/**
 * The template for displaying the footer.
 *
 *
 * @package azzu
 * @since azzu 1.0
 */



?>
            </div>
        </div>
    </div><!-- #content -->
 
    <?php do_action('azzu_after_main_container'); ?>

    
    <?php if ( apply_filters( 'azzu_show_bottom_bar', true ) ): ?>

	<!-- !Bottom-bar -->
	<div id="azu-bottombar" class="<?php azus()->_class('azu-bottombar'); ?>" role="contentinfo">
		<div class="<?php azus()->_class('azu-bottombar-field'); ?>">
                    <div class="<?php azus()->_class('azu-bottombar-row'); ?>">

                        <?php
                        $bottom_logo = azuh()->azzu_get_logo_image( 'bottom-bar-logo', of_get_option('bottombar-bg-height',40) );
                        ?>
                        <div id="branding-bottom" style="<?php echo !$bottom_logo ? 'display: none;' :''; ?>" class="<?php azus()->_class('col-sm-auto', 'azu-bottombar-td-1'); ?>"><?php
                                echo $bottom_logo;
                        ?></div>

                        <?php do_action( 'azzu_credits' ); ?>

                        <?php
                        $copyrights = of_get_option('bottom_bar-copyrights', false);
                        ?>
                        <?php if ( $copyrights) : ?>
                                <div class="<?php azus()->_class('col-sm-auto', 'azu-bottombar-td-2'); ?>">
                                        <div class="<?php azus()->_class('azu-bottombar-copy'); ?>">
                                                <?php echo '<span>'.$copyrights.'</span>'; ?>
                                        </div>
                                </div>
                        <?php endif; ?>
                        
                        <?php azuh()->azzu_nav_menu_list('bottom', 'col-sm-auto-right azu-bottombar-td-3'); ?>
                        <?php azut()->azzu_widget_location('Bottombar','azu-bottombar-td-4'); ?>
		</div><!-- .row -->
            </div><!-- .field -->
	</div><!-- #bottom-bar -->
    <?php endif; // show_bottom_bar ?>
       
</div><!-- #page -->



<?php 
    $scrollup = absint(of_get_option('general-scrollup', 1));
 ?>
    <div style="<?php echo !$scrollup ? 'display: none !important;' :''; ?>" class="<?php azus()->_class('azu-scroll-top-wrapper', $scrollup==1 ? 'azu-mobile-hide' : ''); ?> ">
        <span class="<?php azus()->_class('azu-scroll-top-inner'); ?>">
            <i class="<?php azus()->_class('azu-scroll-icon'); ?>"></i>
        </span>
    </div>


<?php do_action('azzu_before_body'); ?>
<?php wp_footer(); ?>
</body>
</html>
