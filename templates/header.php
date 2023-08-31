<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>
                    <?php if ( apply_filters( 'azzu_show_topbar', true ) ) : ?>
                        <?php get_template_part( 'templates/topbar' ); ?>
                    <?php endif; // show header  ?>

<header id="azu-mastheader" class="<?php azus()->_class('azu-mastheader'); ?>" role="banner">
                    
    
                    <?php if ( apply_filters( 'azzu_show_header', true ) ) : ?>
                        <?php if(!in_array(of_get_option('header-layout'), array('left','middle','side') )): ?>
                            <div id="azu-header" class="<?php azus()->_class('azu-header',apply_filters( 'azzu_header_classes', '' )) ?>" >
				
                                    <div class="<?php azus()->_class('azu-title'); ?>">
                                        <?php get_template_part( 'templates/branding' );
                                        ////////////////////
                                        // Header widget //
                                        ////////////////////
                                        azut()->azzu_widget_location('Header', 'azu-header-desc'); ?>
                                    </div>
                                
                            </div>
                        <?php endif;?>  
                    <?php endif; // show header ?>

        
        <?php if(azum()->get('slideshow_type')=='top'):
            do_action( 'azzu_slider_title' );
        endif; ?>

 
    
        <div id="start_navigation" class="<?php azus()->_class('azu-nav-start',(azum()->get('slideshow_type')=='transparent') ? azus()->get('azu-nav-transparent') :''); ?>">
            <div id="fixed_navigation">
                <nav id="site-navigation" class="<?php azus()->_class('azu-navigation'); ?>" role="navigation">
                        <?php
                            if ( apply_filters( 'azzu_show_mainmenu', true ) ) :
                                do_action( 'azzu_primary_navigation' );
                            endif; // menu
                        ?>
                </nav><!-- #site-navigation -->
            </div>
            <?php if(azum()->get('slideshow_type')!='top'):
                do_action( 'azzu_slider_title' );
            endif; ?>
        
    </div>
 </header><!-- #masthead -->
