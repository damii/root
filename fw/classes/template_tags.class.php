<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Theme
 */
if ( ! class_exists('azu_template_tags') ) :
abstract class azu_template_tags extends azu_base {

protected function __construct() {
    parent::__construct();
}

public function init(){}

protected function add_actions(){
        add_filter('post_class', array( &$this,'azzu_add_post_format_classes') );
        add_filter( 'azu_get_thumb_img-args', array( &$this,'azzu_add_preload_img_class_to_images'), 15 );
        add_filter( 'the_excerpt', array( &$this,'azzu_add_password_form_to_excerpts'), 99 );
        //add_filter( 'jpeg_quality', create_function( '', 'return 85;' ), 99);
        //add_filter( 'excerpt_length', create_function( '', 'return 55;' ), 99 );

        add_action('azzu_before_main_container', array( &$this,'azzu_widgetarea_controller'), 15);
        add_action('azzu_before_main_container', array( &$this,'azzu_post_meta_new_controller'), 15);
        add_action('azzu_before_main_container', array( &$this,'azzu_post_meta_new_default_controller'), 15);
        add_action('azzu_before_main_container', array( &$this,'azzu_portfolio_meta_new_controller'), 15);
        add_action('azzu_after_content', array( &$this,'azzu_add_sidebar_widgetarea'), 15);
        add_action('azzu_after_content', array( &$this,'azzu_add_sidebar_widgetarea_left'), 14);
        add_action('azzu_slider_title', array( &$this,'azzu_slideshow_controller'), 15);
        add_action('azzu_slider_title', array( &$this,'azzu_page_title_controller'), 16);
        add_action('azzu_primary_navigation', array( &$this,'azzu_add_primary_menu'), 15 );
        add_action( 'wp_head', array( $this, 'azu_count_view_post' ) );
        //footer
        add_action('azzu_after_main_container', array( &$this,'azzu_add_footer_widgetarea'), 15);
        
        if ( class_exists( 'Woocommerce' ) ) {
            //WooCommerce integration
            add_action( 'woocommerce_before_main_content', array( $this, 'azu_wc_before' ), 0 );
            add_action( 'woocommerce_after_main_content', array( $this, 'azu_wc_after' ), 9999 );
            add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'azu_wc_item_before' ), 0 );
            add_action( 'woocommerce_shop_loop_item_title', array( $this, 'azu_wc_item' ), 0 );
            add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'azu_wc_item_after' ), 9999 );
            add_action( 'woocommerce_single_product_summary', array( $this, 'azu_wc_category'), 4 );
            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
            add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 10 );
            add_action( 'woocommerce_product_meta_start', array( $this, 'azu_wc_share' ), 15 ); //'woocommerce_share'
            add_filter( 'woocommerce_pagination_args', array( $this, 'azu_wc_pagination_args' ), 9999);
            add_filter( 'woocommerce_breadcrumb_defaults', array( $this, 'azu_wc_breadcrumb_defaults' ), 9999);
            add_filter( 'wc_get_template', array( $this, 'azu_wc_get_template' ), 5, 9999 );
            add_filter( 'loop_shop_columns', array( $this, 'azu_wc_columns' ), 9 );
            add_filter( 'azzu_default_sidebar', array( $this, 'azu_wc_sidebar' ), 15 );
            add_filter( 'woocommerce_product_review_comment_form_args', array( $this, 'azu_wc_review_comment_form_args' ), 15 );
            
        }
        
}

/**
 * abstract functions
 */
abstract public function azzu_content_portfolio();
abstract public function azzu_content_team();
abstract public function azzu_content_testimonials();
abstract public function azzu_content_single();
abstract public function azzu_get_post_categories( $html = '' );
abstract public function azzu_get_post_author( $html = '' );
abstract public function azzu_get_post_tags( $html = '' );
abstract public function azzu_get_post_comments( $html = '' );
// Controlls display of post meta.
abstract public function azzu_post_meta_new_controller();
//Controlls display of azu_portfolio meta.
abstract public function azzu_portfolio_meta_new_controller();


// gallery override shortcode on single post
public function azu_gallery( $atts, $content = null ) {
    $attributes = wp_parse_args( $atts, array(
                        'ids'       => '',
			'columns'    => 1,
                        'pagination' => '1',
                        //'loop' => '0',
                        'padding' => '0',
                        'proportion' => '16:9',
                        'azu_arrow' => '1',
		) );
    $attributes['columns']  = absint($attributes['columns']);
    if( $attributes['columns'] > 1 )
        $attributes['proportion'] = '4:3';
    if( $attributes['columns'] > 5 )
        $attributes['columns'] = 5;
    $attributes['images'] = $attributes['ids'];
    $attributes['slides'] = $attributes['columns'];
    unset($attributes['ids']);
    unset($attributes['columns']);
    return $this->azzu_call_shortcode('azu_carousel',$attributes);
}


/**
  * azu_count_view_post 
  *
  * @access public
  * @since azzu  1.0
  *
  */
 function azu_count_view_post() {
        global $post;
        /* only run this on single pages */
        if ( is_single() && $post->post_type=='post') {
            $cookie = 'azuPageview_'.$post->ID;
            $comment_array = get_comments( array( 'post_id'=>$post->ID ) );
            if( isset( $comment_array ) )
                  update_post_meta( $post->ID, '_azu-popular-posts-comments', count( $comment_array ) );

            $pageviews = get_post_meta( $post->ID, '_azu-popular-posts-pageviews', true );
            if ( !isset($_COOKIE[$cookie]) ){
                if( !isset( $pageviews ) )
                      $pageviews = 0;
                update_post_meta( $post->ID, '_azu-popular-posts-pageviews', $pageviews + 1 );
            }
            if ( !isset($_COOKIE[$cookie]) ) :
                ?><script type="text/javascript">
                            // posts pageviews
                            var pageviews = '<?php echo $cookie; ?>';
                            var c=new Date;c.setTime(c.getTime()+864E5);
                            document.cookie = pageviews+'=<?php echo $pageviews; ?>;expires='+c.toGMTString()+';path=/';
                </script><?php
            endif;
        }
 }

/**
 * Add post password form to excerpts.
 *
 * @return string
 */
function azzu_add_password_form_to_excerpts( $content ) {
        if ( post_password_required() ) {
                $content = get_the_password_form();
        }
        return $content;
}



/**
 * Add sidebar widgetarea.
 */
function azzu_add_sidebar_widgetarea() {
        get_sidebar();
}

/**
 * Add sidebar widgetarea dual.
 */
function azzu_add_sidebar_widgetarea_left() {
        $this->azzu_get_sidebar(true);
}


/**
 * Add footer widgetarea.
 */
function azzu_add_footer_widgetarea() {
        $this->azzu_get_footer();
}

// Page header classes filter
static function azzu_page_header_classes( $class ='' ) {
        $class = 'hidden-header';
        return $class;
}
     

// excerpt search filter
static function azzu_excerpt_search_filter( $text = '' ) {
        //return strip_tags($text,'<b>,<i>,<p>,<a>,<em>,<strong>,<img>'); //wp_kses
        //return preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $text);
        return wp_filter_nohtml_kses($text);

        global $post;
        $raw_excerpt = $text;
        if ( '' == $text ) {
                $text = get_the_content('');
                $text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);
                $text = strip_shortcodes( $text );

                $text = apply_filters('the_content', $text);
                $text = str_replace(']]>', ']]&gt;', $text);

                /***Add the allowed HTML tags separated by a comma.***/
                $allowed_tags = '<b>,<i>,<p>,<a>,<em>,<strong>,<img>';  
                $text = strip_tags($text, $allowed_tags);

                /***Change the excerpt word count.***/
                $excerpt_length = apply_filters('excerpt_length', 55); 

                /*** Change the excerpt ending.***/
                $excerpt_end = ' <a href="'. get_permalink($post->ID) . '">' . '&raquo; Continue Reading.' . '</a>'; 
                $excerpt_more = apply_filters('excerpt_more', ' ' . $excerpt_end);

                $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
                if ( count($words) > $excerpt_length ) {
                    array_pop($words);
                    $text = implode(' ', $words);
                    $text = $text . $excerpt_more;
                } else {
                    $text = implode(' ', $words);
                }
        }
        return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}


/**
 * Page menu filter.
 *
 */
public static function azzu_page_menu_filter( $options = array() ) {
        global $post;

        if ( 'primary' == $options['location'] ) {

                $page_primary_menu = get_post_meta( $post->ID, '_azu_page_primary_menu', true );

                if ( $page_primary_menu ) {

                        $page_primary_menu = intval( $page_primary_menu );

                        if ( $page_primary_menu > 0 ) {
                                $options['params']['menu'] = $page_primary_menu;
                                $options['params']['azu_has_nav_menu'] = true;

                        } else {
                                $options['force_fallback'] = true;

                        }
                }

        }

        return $options;
}

/**
 * Primary navigation menu.
 *
 */
function azzu_add_primary_menu($data = array()) {
        global $azuMenu;
        $theme_location = 'primary';

        $defaults = array(
            'params'		=> array( 'act_class' => 'azu-act', 'fallback_cb' => false ),
            'force_fallback'	=> false,
            'location'		=> 'primary'
        );
        $options = wp_parse_args( $data, $defaults );

        $options = apply_filters( 'azu_menu_options', $options);
        
        $navbar_header = '<div class="%s">%s<button class="navbar-toggle" data-target=".navbar-offcanvas" data-toggle="offcanvas" data-canvas="body" type="button"><span class="sr-only"></span><span class="burger-bar"></span><span class="burger-bar"></span><span class="burger-bar"></span></button></div>';
        $navbar_class= 'navbar-header';
        $azu_branding ='';
        if( in_array(of_get_option('header-layout'), array('left','middle') ))
        {
            ob_start();
            get_template_part( 'templates/branding' );
            $azu_branding .= ob_get_clean();
        }
        
        if ( has_nav_menu($theme_location) ) 
        {
                //add_filter( 'wp_nav_menu_args' , array( $azuMenu , 'megaMenuFilter' ), 2000 );
                $main_nav_class = 'navbar-offcanvas offcanvas';

                echo '<div class="'.azus()->get('azu-navigation-field').'" id="azumegaMenu">';
                
                $args = array_merge( array('menu_class' => azus()->get('azu-navbar','nav')), $options['params'] );
                
                if(of_get_option('header-layout')=='middle')
                {
                    $args['container_class'] = 'azu-mid-nav-left';
                    $args['container_id'] = 'azu-navbar-left';
                    $args['theme_location'] = 'center-left';
                    wp_nav_menu( $args );
                    $navbar_class .= ' first_logo_hidden';
                    printf($navbar_header, $navbar_class ,$azu_branding );
                    $args['container_class'] = 'azu-mid-nav-right';
                    $args['container_id'] = 'azu-navbar-right';
                    $args['theme_location'] = 'center-right';
                    $main_nav_class .= ' azu-hide-primary';
                    wp_nav_menu( $args );
                }
                elseif(of_get_option('header-layout')!='side')
                    printf($navbar_header, $navbar_class ,$azu_branding );
                
                 $args['container_class'] = $main_nav_class;
                 $args['container_id'] = 'azu-navbar-collapse';
                 $args['theme_location'] = $theme_location;

                wp_nav_menu( $args );
                echo '<div class="'.azus()->get('azu-ui-mask-modal').'" ></div>';
                if(of_get_option('header-layout')=='side')
                {
                    printf($navbar_header, $navbar_class ,$azu_branding );
                }
                echo '</div>';
                //remove_filter( 'wp_nav_menu_args' , array( $azuMenu , 'megaMenuFilter' ), 2000 );
        }
        else if( in_array(of_get_option('header-layout'), array('left') )){
            ob_start();
                azut()->azzu_widget_location('Menu-right',  'azu-menu-widget-area area-right azu-burger-menu');
            $azu_widget_areas = ob_get_clean();
            
            if(strpos($azu_widget_areas, 'widget_nav_menu') !== FALSE){
                echo '<div class="'.azus()->get('azu-navigation-field').'" id="azumegaMenu">';
                $navbar_header = '<div class="%s">%s</div>';
                printf($navbar_header, $navbar_class ,$azu_branding );
                echo '<div class="">'.$azu_widget_areas.'</div>';
                echo '</div>';
            }
            else
                wp_page_menu(array('menu_class' => 'azu-navbar-page', 'depth' => 0,));
        }
        else {
            wp_page_menu(array('menu_class' => 'azu-navbar-page', 'depth' => 0,));
        }
}

/**
 * Slideshow controller.
 *
 */
function azzu_slideshow_controller() {
        global $post;

        if ( !azum()->get('slideshow_slider') ){
                return;
        }

        $slider_id = azum()->get('slideshow_sliders');
        $slideshow_type = azum()->get('slideshow_type');
        $container_class='';
        if($slideshow_type=='boxed')
            $container_class = 'container nopadding';

        if ( azuf()->azu_get_paged_var() > 1 ) {
                return;
        }
        
        $slideshow_tag = '<div id="main-slideshow" class="'.azus()->get('azu-slider-container',$container_class).'" data-mode="'.azum()->get('slideshow_mode').'">';

        switch ( azum()->get('slideshow_mode') ) {
                case 'revolution':
                        $rev_slider = azum()->get('slideshow_revolution_slider');

                        if ( $rev_slider && function_exists('putRevSlider') ) {
                                echo $slideshow_tag;
                                putRevSlider( $rev_slider );
                                echo '</div>';
                        }
                        break;
                case 'master':
                        $master_slider = azum()->get('slideshow_master_slider');
                        if ( $master_slider && function_exists('masterslider') ) {
                                echo $slideshow_tag;
                                masterslider( $master_slider );
                                echo '</div>';
                        }
                        break;
                case 'royal':
                        $royal_slider = azum()->get('slideshow_royal_slider');

                        if ( $royal_slider && function_exists('get_new_royalslider') ) {
                                register_new_royalslider_files($royal_slider);
                                echo $slideshow_tag;
                                echo get_new_royalslider( $royal_slider );
                                echo '</div>';
                        }
                        break;

                case 'layer':
                        $layer_slider = azum()->get('slideshow_layer_slider');

                        if ( $layer_slider && function_exists('layerslider') ) {
                                echo $slideshow_tag;
                                layerslider( $layer_slider );
                                echo '</div>';
                        }
                        break;
                default: break;
        } // switch
        
        do_action('azzu_after_slideshow');

}


/**
 * Page title controller.
 */
function azzu_page_title_controller() {
        global $post;

        $show_titles = of_get_option( 'general-show_titles', '1' );
        
        if ( !$show_titles || apply_filters( 'azzu_show_page_title', false )) {
                return;
        }

        $title_align = of_get_option( 'general-title_align', 'center' );
        $row_class = ($title_align =='center') ? 'col-sm-12' : 'col-sm-6';
        $title_classes = array( azus()->get('azu-page-header') );
        $title_class = azus()->get('azu-page-title',$row_class);

        $before_title = '<div class="' . esc_attr( implode( ' ', $title_classes ) ) . '"><div class="'.azus()->get('azu-header-field').'"><div class="row">';
        $after_title = '</div></div></div>';
        $title_template = '<div class="'.$title_class.'"><'.AZU_PAGE_TITLE_H.'>%s</'.AZU_PAGE_TITLE_H.'></div>';
        $breadcrumb_template = '<div class="'.azus()->get('azu-breadcrumb', $row_class).'">%s</div>';
        $title = '';
        $breadcrumbs = apply_filters( 'azu_sanitize_flag', of_get_option( 'general-show_breadcrumbs', 1 ) );
        $is_single = is_single();
        $is_woocommerce = class_exists( 'Woocommerce' ) && function_exists( 'is_woocommerce' ) && is_woocommerce();
        if ( is_page() || $is_single ) {
                // show title
                if ( azum()->get( 'header_title', true) ) {
                    
                        if ( $is_single ) {
                                $title_template = '<div class="'.$title_class.'"><'.AZU_PAGE_TITLE_H.' class="'.azus()->get('azu-entry-title').'">%s</'.AZU_PAGE_TITLE_H.'></div>';
                        }

                        $title = sprintf( $title_template, get_the_title() );

                } else {
                        $breadcrumbs = false;
                        $before_title = $after_title = '';
                        return;
                }

        } else if ( is_search() ) {
                global $wp_query;
                $message = sprintf( _x( '%s Search results for: %s', 'archive template title', 'azzu'.LANG_DN ), $wp_query->found_posts, '<span>' . get_search_query() . '</span>' );
                $title = sprintf( $title_template, $message );

        } else if ( is_archive() ) {

                if ( is_category() ) {
                        $message = sprintf( _x( 'Category Archives: %s', 'archive template title', 'azzu'.LANG_DN ), '<span>' . single_cat_title( '', false ) . '</span>' );

                } elseif ( is_tag() ) {
                        $message = sprintf( _x( 'Tag Archives: %s', 'archive template title', 'azzu'.LANG_DN ), '<span>' . single_tag_title( '', false ) . '</span>' );

                } elseif ( is_author() ) {
                        the_post();
                        $message = sprintf( _x( 'Author Archives: %s', 'archive template title', 'azzu'.LANG_DN ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' );
                        rewind_posts();

                } elseif ( is_day() ) {
                        $message = sprintf( _x( 'Daily Archives: %s', 'archive template title', 'azzu'.LANG_DN ), '<span>' . get_the_date() . '</span>' );

                } elseif ( is_month() ) {
                        $message = sprintf( _x( 'Monthly Archives: %s', 'archive template title', 'azzu'.LANG_DN ), '<span>' . get_the_date( 'F Y' ) . '</span>' );

                } elseif ( is_year() ) {
                        $message = sprintf( _x( 'Yearly Archives: %s', 'archive template title', 'azzu'.LANG_DN ), '<span>' . get_the_date( 'Y' ) . '</span>' );

                } elseif ( is_tax('azu_portfolio_category') ) {
                        $message = sprintf( _x( 'Portfolio Archives: %s', 'archive template title', 'azzu'.LANG_DN ), '<span>' . single_term_title( '', false ) . '</span>' );

                } else if($is_woocommerce){

                        if(function_exists( 'woocommerce_page_title' ))
                            $message = woocommerce_page_title( false );
                        else
                            $message = _x( 'Archives:', 'archive template title', 'azzu'.LANG_DN );
                        if(empty($message)){
                            $message = _x( 'Products', 'archive template title', 'azzu'.LANG_DN );
                        }
                        if(!$is_single) {
                            // remove wooCommerce Title
                            add_filter( 'woocommerce_show_page_title', '__return_false', 9999);
                        }
                }
                else {
                        $message = _x( 'Archives:', 'archive template title', 'azzu'.LANG_DN );
                }

                $title = sprintf( $title_template, $message );
        } elseif ( is_404() ) {
                $title = sprintf( $title_template, _x('Page not found', 'index title', 'azzu'.LANG_DN) );
        } else {
                $title = sprintf( $title_template, _x('Blog', 'index title', 'azzu'.LANG_DN) );
        }
        
        

        echo $before_title;
        $breadcrumb_template =  $breadcrumbs ? sprintf($breadcrumb_template, azuh()->azu_breadcrumb(array('echo'=>false))) : '';

        $single_title = of_get_option('general-single-title','');
        $single_subtitle = of_get_option('general-single-subtitle','');
        if($is_woocommerce) {
            $single_title = of_get_option('wc-single-title','');
            $single_subtitle = of_get_option('wc-single-subtitle','');
        }
        
        if($is_single && (get_post_type() == 'post' || $is_woocommerce)) {
            if($single_subtitle && $title_align == 'center'){
                $breadcrumb_template = '';
                echo sprintf('<div class="azu-vc-pt-subtitle">%s</div>',$single_subtitle);
            }
            if($single_title)
                $title = sprintf( $title_template, $single_title );
        }
        echo apply_filters( 'azzu_page_title', $title, $title_template );
        echo $breadcrumb_template;

        do_action('azzu_inside_title');
        
        echo $after_title;
}


/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
public static function theme_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<div class="comment-body">
			<?php _ex( 'Pingback:', 'atheme', 'azzu'.LANG_DN); ?> <?php comment_author_link(); ?> <?php edit_comment_link( _x( 'Edit', 'atheme', 'azzu'.LANG_DN), '<span class="edit-link">', '</span>' ); ?>
		</div>

	<?php else : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
                        <div class="comment-author-avatar">
                            <?php if ( 0 != $args['avatar_size'] ) echo '<a href="#">'.get_avatar( $comment, 50 * azuf()->azu_device_pixel_ratio() ).'</a>'; ?>
                        </div><!-- .comment-author -->
			<div class="comment-meta">
                                <div class="comment-author vcard">
                                    <?php printf( _x( '%s <span class="says"></span>', 'atheme', 'azzu'.LANG_DN) ,sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
                                </div><!-- .comment-vcard -->
				<div class="comment-metadata">
					<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
						<time datetime="<?php comment_time( 'c' ); ?>">
							<?php 
                                                            $diff = (current_time('timestamp') - get_comment_time('U'))/86400;
                                                            $human_time = of_get_option('general-human-time',0);
                                                            if($human_time == 1 || ($human_time > 1 && $diff >= 0 && $diff <= 7))
                                                                printf( _x(' %s ago ','atheme','azzu'.LANG_DN),human_time_diff( get_comment_time('U'), current_time('timestamp') ));
                                                            else
                                                                echo get_comment_date(get_option('date_format'));
                                                        ?>
						</time>
					</a>
				</div><!-- .comment-metadata -->

				<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _ex( 'Your comment is awaiting moderation.', 'atheme', 'azzu'.LANG_DN); ?></p>
				<?php endif; ?>
			</div><!-- .comment-meta -->

			<div class="comment-content">
				<?php comment_text(); ?>
			</div><!-- .comment-content -->
                        <div class="comment-meta-bottom">
                            
                            <?php if ( of_get_option('general-comment-ip',1) ) : ?>
                                <span class="comment-ip"><?php comment_author_IP($comment->comment_ID); ?> </span>
                            <?php endif; ?>
                        <?php
                                edit_comment_link( '<i class="azu-icon-edit"></i>', '<span class="edit-link azu-tooltip" data-toggle="tooltip" data-placement="top" title="'._x( 'Edit', 'atheme', 'azzu'.LANG_DN).'">', '</span>' );
				comment_reply_link( array_merge( $args, array(
					'add_below' => 'div-comment',
                                        'reply_text'=> _x('Reply','atheme','azzu'.LANG_DN),
					'depth'     => $depth,
					'max_depth' => $args['max_depth'],
					'before'    => '<div class="reply">',
					'after'     => '</div>',
				) ) );
			?>
                        </div><!-- .meta-bottom -->
		</article><!-- .comment-body -->

	<?php
	endif;
}



/**
 * Prints the attached image with a link to the next attached image.
 */
public function theme_the_attached_image() {
	$post                = get_post();
	$attachment_size     = apply_filters( 'theme_attachment_size', array( 1200, 1200 ) );
	$next_attachment_url = wp_get_attachment_url();

	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the
	 * URL of the next adjacent image in a gallery, or the first image (if
	 * we're looking at the last image in a gallery), or, in a gallery of one,
	 * just the link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}

public function azu_paginator( $query = null, $opts = array() ) {
        if(azuh()->azzu_is_post_navigation_enabled()){
            global $wpdb, $wp_query, $paged;

            $defaults = array(
                    'end_size' => 4,
                    'mid_size' => 2,
                    'previouspagelink' => '<i class="azu-icon-left-pagi"></i>'._x( 'Prev','pagination', 'azzu'.LANG_DN ),
                    'nextpagelink'     => _x( 'Next','pagination' ,'azzu'.LANG_DN ).'<i class="azu-icon-right-pagi"></i>',
                );

            if(is_single())
                $defaults = array(
                     'next_or_number'   => 'next',
                     'before'           => '<ul class="pager">',
                     'previouspagelink' => _x( 'Prev','pagination', 'azzu'.LANG_DN ),
                     'nextpagelink'     => _x( 'Next','pagination' ,'azzu'.LANG_DN ),
                );
            $opts = wp_parse_args( $opts, $defaults );

            ?>
            <div class="<?php azus()->_class('text-center'); ?>">
                <?php 
                    if(is_archive()) 
                        wpbootstrap_archive_pager( $opts );
                    else if(is_single()) 
                        wpbootstrap_post_pager( $opts );
                    else
                        wpbootstrap_archive_pager( $opts );

                ?>
            </div>
            <?php
        }
}

/**
 * Controlls display of post meta for page.
 */
public function azzu_post_meta_new_default_controller() {
        // add filters
        add_filter('azzu_new_posted_on', array( &$this,'azzu_get_post_date'), 13);
        add_filter('azzu_new_posted_on', array( &$this,'azzu_get_post_author'), 14);
        add_filter('azzu_new_posted_on', array( &$this,'azzu_get_post_categories'), 15);

        // add wrap
        add_filter('azzu_new_posted_on', array( &$this,'azzu_get_post_meta_wrap'), 16, 2);
}



/**
 * Get post date.
 */
function azzu_get_post_date( $html = '' ) {

        $href = 'javascript: void(0);';

        if ( 'post' == get_post_type() ) {

                // remove link if in date archive
                if ( !(is_day() && is_month() && is_year()) ) {

                        $archive_year  = get_the_time('Y');
                        $archive_month = get_the_time('m');
                        $archive_day   = get_the_time('d');
                        $href = get_day_link( $archive_year, $archive_month, $archive_day );
                }
        }
        $show_date = get_the_date(get_option('date_format'));
        $diff = (current_time('timestamp') - get_the_time('U'))/86400;
        $human_time = of_get_option('general-human-time',0);
        if($human_time == 1 || ($human_time > 1 && $diff >= 0 && $diff <= 7))
            $show_date =  sprintf( _x(' %s ago','atheme','azzu'.LANG_DN),human_time_diff( get_the_time('U'), current_time('timestamp') ));

        $html .= sprintf(
                '<a href="%s" title="%s" rel="bookmark"><time class="'.azus()->get('entry-date').'" datetime="%s">%s</time></a>',
                        $href,	// href
                        esc_attr( get_the_time() ),	// title
                        esc_attr( get_the_date( 'c' ) ),	// datetime
                        esc_html( $show_date )	// date
        );

        return $html;
}

/**
 * Add post format classes to post.
 */
function azzu_add_post_format_classes( $classes = array() ) {
        global $post;

        if(azum()->get('template') == 'portfolio'){
            $classes[] = 'azu_portfolio';
        }
        if ( 'post' != get_post_type( $post ) ) {
                return $classes;
        }
        $post_format = null;
        if ( 'post' == get_post_type() ) {
                $post_format = get_post_format();
        }

        if(is_single())
            $classes[] = azus()->get('azu-single');
        else
            $classes[] = azus()->get('azu-post');
        

        $post_format_class = ($post_format == null) ? '' : 'azu-post-'.$post_format;
        if ( $post_format_class ) {
                $classes[] = $post_format_class;
        }

        return array_unique($classes);
}


/**
 * Get small post comments.
 */
function azzu_get_small_post_comments( $html = '' ) {
        if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) :
                ob_start();

                $azu_comment = '<span class="">%s '._x('Comment','atheme','azzu'.LANG_DN).'</span>';
                $azu_comments = '<span class="">%s '._x('Comments','atheme','azzu'.LANG_DN).'</span>';

                comments_popup_link( sprintf($azu_comment , '0') , sprintf($azu_comments , '1'), sprintf($azu_comment , '%'),azus()->get('azu-comment') );
                $html .= ob_get_clean();
        endif;

        return $html;
}

/**
 * Return post comment,like and share.
 *
 * @return string
 */
function azzu_get_post_bottom($output = '') {
        $post_meta = of_get_option( 'general-blog_meta_on', 1 );
        $post_comments = of_get_option( 'general-blog_meta_comments', 1 );
        $post_like = of_get_option( 'general-blog_meta_like', 1 );
        $post_share = of_get_option( 'general-blog_meta_share', 0 );
        $post_pageview = of_get_option( 'general-blog_meta_pageview', 0 );

        if ( !$post_meta ) {
                return $output;
        }
        if($post_like)
            $output .= azu_love_this();

        if($post_pageview)
            $output .= $this->azu_get_pageview();

        if ( $post_comments ) {
                $output .= $this->azzu_get_post_comments();
        }
        if(!is_single() && $post_share)
            $output .= azuh()->azzu_display_share_buttons('post', array('echo' => false));

        return $output;
}

/**
 * Return post comment,like and share.
 *
 * @return string
 */
function azzu_get_post_like($html = '') {
        if(!azum()->get('project_like_hide',false))
            $html .= azu_love_this();
        return $html;
}
/**
 * Get post pageview.
 */
function azu_get_pageview() {
        global $post;
        if($post->post_type!='post')
            return '';
        $pageviews = get_post_meta( $post->ID, '_azu-popular-posts-pageviews', true );
        if(empty($pageviews))
            $pageviews = 0;
        return '<a href="#" class="'. azus()->get('azu-post-pageview', 'azu-tooltip') .'" data-toggle="tooltip" data-placement="top" title="'._x('Views','atheme','azzu'.LANG_DN).'"><i class="azu-icon-view"></i>'. '<span class="'.azus()->get('azu-love-count').'">'. $pageviews .'</span>' .'</a>';
}


/**
 * Get post meta wrap.
 */
function azzu_get_post_meta_wrap( $html = '', $class = '' ) {
        if ( empty( $html ) ) {
                return $html;
        }
        $current_post_type = get_post_type();

        if ( !is_array($class) ) {
                $class = explode(' ', $class);
        }

        if ( $current_post_type == 'azu_portfolio' ) {
                $class[] = 'portfolio-categories '.azus()->get('azu-entry-meta');
        } else {
                $class[] = ' '.azus()->get('azu-entry-meta');
        }

        $html = '<div class="' . esc_attr( implode(' ', $class) ) . '">' . $html . '</div>';

        return $html;
}

// azu widget area
function azzu_widget_location($widget_name='', $class=''){
       $pre_name = strtolower(AZU_WIDGET_PREFIX . sanitize_key($widget_name));
       if ( is_active_sidebar( $pre_name ) ) : ?>
            <div id="<?php echo strtolower($widget_name); ?>-widget" class="<?php azus()->_class('azu-widget-area',$class); ?>" role="complementary">
                    <?php dynamic_sidebar( $pre_name ); ?>
            </div><!-- #widget -->
       <?php endif; 
}

// footer widget action function
function footer_dynamic_widget(){
        echo '___footer_dynamic_widget___';
}

// footer widget generator
function azu_footer_widget($footer_sidebar){
        $footer_layout_style= azuf()->azu_get_option('footer_show');
        ob_start();
        add_action( 'dynamic_sidebar',array( &$this,'footer_dynamic_widget'),15);
        dynamic_sidebar( $footer_sidebar );
        remove_action( 'dynamic_sidebar',array( &$this,'footer_dynamic_widget'),15);
        $footer_widgets = ob_get_clean();

        $f_widget = preg_split('/ class=\"azu-widget /', $footer_widgets );
        $footer_widgets ='';

        foreach ($f_widget as $j => $value) {
            if($j>0){
                $n=4;
                switch ($footer_layout_style) {
                    case 'one':
                        $n=12;
                        break;
                    case 'six':
                        $n=2;
                        break;
                    case 'two':
                        $n=6;
                        break;
                    case 'three1':
                        if($j%3 == 1)
                            $n=6;
                        else
                            $n=3;
                        break;
                    case 'three2':
                        if($j%3 == 0)
                            $n=6;
                        else
                            $n=3;
                        break;
                    case 'three':
                        $n=4;
                        break;
                    case 'four':
                        $n=3;
                        break;
                    default:
                        break;
                }
                $footer_widgets .=' class="azu-widget col-sm-'.$n.' ';
            }
            $footer_widgets .= $value;
        }

        $f_widget = preg_split('/___footer_dynamic_widget___/', $footer_widgets );
        $footer_widgets ='';
        foreach ($f_widget as $j => $value) {
           if($j>1){
                $print_divider=false;
                switch ($footer_layout_style) {
                    case 'two':
                        if($j%2 == 1)
                            $print_divider=true;
                        break;
                    case 'one':
                        break;
                    case 'six':
                        if($j%6 == 1)
                            $print_divider=true;
                        break;
                    case 'four':
                        if($j%4 == 1)
                            $print_divider=true;
                        break;
                    default:
                        if($j%3 == 1)
                            $print_divider=true;
                        break;
                }
                if($print_divider)
                    $footer_widgets .='<div class="footer-divider col-sm-12"><hr></div>';
            }
            $footer_widgets .= $value;
        }
        echo $footer_widgets; 
}

/**
 * Add proportions to images.
 *
 * @return array.
 */
function azzu_thumbnail_proportions($args = array(),$attr=array()){
        if ( array_key_exists( 'proportion', $attr ) && !empty($attr['proportion'])){
            $thumb_proportions = $attr['proportion'];
            if ( $thumb_proportions ) {
                    $args['prop'] = $thumb_proportions;
            }
        }
        return $args;
}


/**
 * Add post close div for masonry layout.
 */
static function azzu_after_post_masonry() {
        echo '</div>';
}

/**
 * Add post open div for masonry layout.
 */
static function azzu_before_post_masonry() {
        global $post;

        $post_type = get_post_type();

        // get post width settings
        $post_preview = 'normal';

        $iso_classes = array( 'iso-item' );

        if ( 'masonry' == azum()->get('layout') ) {
                $iso_classes[] = 'isope-item';
        }

        if ( azum()->get('preview')) {
                $iso_classes[] = 'azu-media-wide';
        }

        if ( in_array( azum()->get('template'), array('portfolio') ) ) {

                // set taxonomy based on post_type
                $tax = null;
                switch ( $post_type ) {
                        case 'azu_portfolio': $tax = 'azu_portfolio_category'; break;
                        default: $tax = 'category'; break;
                }

                // add terms to classes
                $terms = wp_get_object_terms( $post->ID, $tax, array('fields' => 'ids') );
                if ( $terms && !is_wp_error($terms) ) {

                        foreach ( $terms as $term_id ) {

                                $iso_classes[] = 'category-' . $term_id;
                        }
                } else {

                        $iso_classes[] = 'category-0';
                }
        }

        $iso_classes = esc_attr(implode(' ', $iso_classes));

        $clear_title = $post->post_title;

        $data_attr = array(
                'data-date="' . get_the_date( 'c' ) . '"',
                'data-name="' . esc_attr($clear_title) . '"',
                'data-post-id="' . get_the_ID() . '"'
        );

        echo '<div class="' . $iso_classes . '" ' . implode(' ', $data_attr) . '>';
}

/**
 * Add preload-img to every image that created with azu_get_thumb_img().
 *
 */
function azzu_add_preload_img_class_to_images( $args = array() ) {
        $img_class = explode(" ",$args['img_class']);

        if(!in_array('no-preload',$img_class)){
                // add class
                $img_class[] = 'preload-img';
        }
        // clear duplicate
        $img_class = array_unique( $img_class );
		
        $args['img_class'] = implode(" ",$img_class);

        $id = absint($args['img_id']);
        // use image title instead alt
        if(azuh()->azzu_image_title_is_hidden( $id ))
            $args['alt'] = get_the_title( $id );
        else 
            $args['img_title'] = false;

        return $args;
}


/**
 * Controlls display of widgetarea.
 */
function azzu_widgetarea_controller() {
        global $post;

        if ( is_404() ) {
                remove_action('azzu_after_main_container', array( &$this,'azzu_add_footer_widgetarea'), 15);
                remove_action('azzu_after_content', array( &$this,'azzu_add_sidebar_widgetarea'), 15);
                remove_action('azzu_after_content', array( &$this,'azzu_add_sidebar_widgetarea_left'), 16);
        }

        // index or search or archive or no post data
        if ( is_archive() || is_search() || is_home() || is_404() || empty($post) ) {
                return;
        }

        $footer_display = azuf()->azu_get_option('footer_show');
        $sidebar_position = azuf()->azu_get_option('sidebar_position');

        if ( 'disabled' == $footer_display ) {
                remove_action('azzu_after_main_container', array( &$this,'azzu_add_footer_widgetarea'), 15);
        }

        if ( 'disabled' == $sidebar_position ) {
                remove_action('azzu_after_content', array( &$this,'azzu_add_sidebar_widgetarea'), 15);
                remove_action('azzu_after_content', array( &$this,'azzu_add_sidebar_widgetarea_left'), 16);
        }
}


/**
 * Call shortcode.
 */
function azzu_call_shortcode($name = '',$attr = array()) {
        $output = ''; 
        $attribute='';
        $shortcode_map = '['.$name.' %1$s]';
        foreach($attr as $i => $val){
            $attribute .=  ' '.$i.'="'.esc_attr($val).'" ';
        }

        $shortcode_map = sprintf($shortcode_map,$attribute);

        if(has_shortcode($shortcode_map, $name))
                $output =  do_shortcode($shortcode_map);
        return $output;
}

public function azu_all_content( $type='' ) {
    global $post;
    $print_article = true;
    if( in_array($type, array('page') ))
        $print_article = false;
    
    if($print_article)
        echo '<article id="post-'.$post->ID.'" class="'.implode(" ",get_post_class( is_single() ? 'azu-single' : 'azu-post')).'">';
    if(is_single())
        do_action('azzu_before_post_content');
    if($type == 'archive'){
        $type = str_replace( 'azu_', '', get_post_type() );
    }

    if($type == 'page'){
        $this->azzu_content_page();
    }
    else if($type == 'search'){
        $this->azzu_content_search();
    }
    else if($type == 'single'){
        $this->azzu_content_single();
    }
    else if($type == 'portfolio'){
        $this->azzu_content_portfolio();
    }
    else if($type == 'team'){
        $this->azzu_content_team();
    }
    else if($type == 'testimonials'){
        $this->azzu_content_testimonials();
    }
    else if($type == 'image'){
        $this->azzu_content_image();
    }
    else {
        $format = get_post_format();
        switch($format)
        {
          case 'aside':
          case 'link':
          case 'quote':
          case 'chat':
          case 'status':
              $this->azzu_content_no_title( $format );
              break;
          case 'image':
          case 'video':
          case 'audio':
          case 'gallery':
          default:
              $this->azzu_content_with_title( $format );
              break;
        }
    }
    if(is_single())
        do_action('azzu_after_post_content'); 
    if($print_article)
        echo '</article><!-- #post-'.$post->ID.' -->';
}
      
public function azzu_content_no_title( $type = '' ) {
    global $post;

    $post_link = '';
    $main_tag = 'div';
    if($type == 'link'){
        $post_link = get_post_meta( $post->ID, '_azu_post_options_link', true );
        if(!$post_link)
            $post_link = azuf()->azu_get_link_url();
    }
    else if($type == 'quote'){
        $main_tag = 'blockquote';
    }

    	echo '<div class="'.azus()->get('azu-entry-content', azuf()->azzu_compute_col('',array('invert' => true))).'">';
            if(!empty($post_link)){
                echo '<a target="_blank" href="'.esc_url($post_link).'">';
            }
                    echo '<'.$main_tag.' class="'.azus()->get('content-blog-'.$type).'">';
                        echo '<div class="'.azus()->get('azu-padding').'">';
                            echo azuh()->azzu_the_content(); 
                        echo '</div>';
                        if(of_get_option( 'general-blog_meta_format_icon', 1 )) {
                            echo '<i class="'.azus()->get('azu-icon-post','azu-icon-post-'.$type).'"></i>';
                        }
                    echo '</'.$main_tag.'>';
            if(!empty($post_link)){
                echo '</a>';
            }
                echo azut()->azzu_get_post_meta_wrap( azuh()->azzu_post_readmore_link().azuh()->azzu_post_edit_link().azut()->azzu_get_post_bottom(), azus()->get('azu-post-bottom') );
        echo '</div>';
}

public function azzu_content_image(){
        global $post;
        $img_meta = wp_get_attachment_image_src( $post->ID, 'full' );
        $img_args = array(
                'img_meta'      => $img_meta,
                'img_id'		=> $post->ID,
                'options'		=> array( 'w' => azuf()->azu_calculate_width_size(1), 'z' => 0 ),
                'custom'		=> 'data-azu-img-description="' . esc_attr(get_the_excerpt()) . '"',
                'class'			=> azus()->get('azu-mfp-item','azu-rollover'),
                'title'			=> get_the_title(),
                'wrap'			=>'<a %HREF% %CLASS% %CUSTOM% %TITLE%><img %IMG_CLASS% %SRC% %ALT% %SIZE% /></a>'
        );

        azuf()->azu_get_thumb_img( $img_args );
        
        the_content();
}


public function azzu_content_media($type,$attr){
        global $post;
        $media = '';
        $wide_mode = !( is_search() || is_archive() ) ? azum()->get('preview',0) : 0;
        
	if ( !post_password_required()) {
                $video_url ='';
                if($type=='gallery'){
                    $gallery = get_post_gallery( $post->ID, false );
                    if ( !empty($gallery['ids']) ) {
                            $media_items = array_map( 'trim', explode( ',', $gallery['ids'] ) );

                            // if we have post thumbnail and it's not hidden
                            if ( has_post_thumbnail() && !get_post_meta( $post->ID, '_azu_post_options_hide_thumbnail', true ) ) {
                                    array_unshift( $media_items, get_post_thumbnail_id() );
                            }

                            $attachments_data = azuh()->azzu_get_attachment_post_data( $media_items );
                            $style = ' style=""'; //width: 100%;

                            $gallery_style = (bool) get_post_meta( $post->ID, '_azu_post_options_gallery_style', true );
                            $class = array( '' );
                            $media_args = array( 'show_info' => array(), 'class' => $class, 'img_width' => $attr['column_width'],  'style' => $style );
                            
                            $prop = array_key_exists( 'proportion', $attr ) && !empty($attr['proportion']);
                            if( $gallery_style || $prop && azum()->get('layout') == 'grid' ){
                                if ( $prop ) {
                                    $media_args['proportion'] = $attr['proportion'];
                                }
                                $media = azuh()->azzu_get_post_media_slider( $attachments_data, $media_args );
                            }
                            else{
                                $media = azuh()->azzu_get_gallery_image_list( $attachments_data, $media_args );
                            }
                            
                    }
                }
                else if($type=='audio') {
                    $post_link = get_post_meta( $post->ID, '_azu_post_options_link', true );
                    // Audio embed
                    if ( $post_link !== '' ) {
                            $media = '<div class="'. azus()->get('content-blog-audio').'">'.azuf()->azu_get_embed( $post_link ).'</div>';
                    }
                }
                else if(has_post_thumbnail()) {
                    // thumbnail meta
                    $media_id = get_post_thumbnail_id();
                    $media_meta = wp_get_attachment_image_src( $media_id, 'full' );

                    $class = $custom = '';
                    $media_options = array();

                    $media_args = array(
                            'img_meta' 	=> $media_meta,
                            'img_id'	=> $media_id,
                            'options'	=> $media_options,
                            'echo'	=> false,
                    );
                    
                    if($type=='image'){
                        $media_args['wrap'] = '<a %HREF% %CLASS% %CUSTOM% title="%RAW_ALT%" data-azu-img-description="%RAW_TITLE%"><img %IMG_CLASS% %SRC% %IMG_TITLE% %ALT% %SIZE% /></a>';
                        $class = azus()->get('azu-mfp-item','azu-single-mfp-popup azu-rollover mfp-image');
                    }
                    else if($type=='video'){
                        $video_url = esc_url( get_post_meta( $media_id, 'azu-video-url', true ) );
                        $media_args['wrap'] = '<a %HREF% %CLASS% title="%RAW_ALT%" data-azu-img-description="%RAW_TITLE%"><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% /></a>';
                        // video with play button on hover
                        $media_args['href'] = $video_url;
                        $class = 'video-icon '. azus()->get('mfp-iframe','azu-single-mfp-popup azu-mfp-item');
                    }
                    else {
                        $media_args['wrap'] = '<a %HREF% %CLASS% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% /></a>';
                        $media_args['href'] = get_permalink();
                        $class = azus()->get('azu-rollover');
                    }
                    $media_args['custom'] = $custom;
                    $media_args['class'] = $class;
                    
                    if ( ($wide_mode && !$attr['same_width'] && AZZU_MOBILE_DETECT!='1') ) {
                            $target_image_width = $attr['column_width'] * 2;
                            $media_args['options'] = array( 'w' => round($target_image_width), 'z' => 0 );
                    } else {
                            $target_image_width = $attr['column_width'];
                            $media_args['options'] = array( 'w' => round($target_image_width), 'z' => 0 );
                    }
                    $media_args = azut()->azzu_thumbnail_proportions( $media_args, $attr );
                    $media = azuf()->azu_get_thumb_img( $media_args );
                }
                //not used
                else if($type=='thumbnail' && has_post_thumbnail()) {
                    $media = get_the_post_thumbnail(get_post_thumbnail_id(), 'thumbnail'); //medium large full
                }
                
                if($type=='video'){
                    if ( !$video_url )
                        $video_url = esc_url(get_post_meta( $post->ID, '_azu_post_options_link', true ));

                    if ( $media ) {
                            $media = '<div class="' .azus()->get('azu-rollover-video').'" ' . $custom . '>' . $media . '</div>';
                    }
                    else if(!empty($video_url)){
                        $media = '<div class="azu-video-container">' . azuf()->azu_get_embed( $video_url ) . '</div>';
                    }
                }
                
	}
        
        return $media;
}

public function azzu_content_text($type,$attr,$empty_media=true){
        echo '<div class="'.azus()->get('azu-entry-content',azuf()->azzu_compute_col($attr['image_size'],array('invert' =>true,'media_empty' => $empty_media) )).'">';
		echo '<'.AZU_POST_TITLE_H.' class="'.azus()->get('azu-entry-title').'">'
                        . sprintf( '<a href="%s" title="%s" rel="bookmark">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), get_the_title() )
		 .'</'.AZU_POST_TITLE_H.'>';

		echo azuh()->azzu_new_posted_on( 'post' ); 
		echo azuh()->azzu_the_content(); 
                echo azut()->azzu_get_post_meta_wrap( azuh()->azzu_post_readmore_link().azuh()->azzu_post_edit_link().azut()->azzu_get_post_bottom(), azus()->get('azu-post-bottom') );
        echo '</div>';
}

public function azzu_content_with_title( $type = '' ) {
        $attr = azum()->get('attr');
        $media = $this->azzu_content_media($type,$attr);
        echo '<div class="'.azus()->get('content-blog-media',azuf()->azzu_compute_col($type !='audio' ? $attr['image_size'] : '')).'">' . $media . '</div>';
	$this->azzu_content_text($type,$attr,empty($media));
}


public function azzu_content_search(){
        $attr = azum()->get('attr');
        $media ='';
	if ( !post_password_required() && has_post_thumbnail() ) {

		// thumbnail meta
		$media_id = get_post_thumbnail_id();
		$media_meta = wp_get_attachment_image_src( $media_id, 'full' );

		$custom = '';
		$media_options = array();

		$media_args = array(
			'img_meta' 	=> $media_meta,
			'img_id'	=> $media_id,
			'class'		=> azus()->get('azu-rollover'),
			'custom'	=> $custom,
			'href'		=> get_permalink(),
			'options'	=> $media_options,
			'echo'		=> false,
			'wrap'		=> '<a %HREF% %CLASS% %CUSTOM% %TITLE%><img %IMG_CLASS% %SRC% %ALT% %IMG_TITLE% %SIZE% /></a>',
		);
                $media_args['options'] = array( 'w' => round($attr['column_width']), 'z' => 0 );
                
		$media_args = azut()->azzu_thumbnail_proportions( $media_args, $attr );

		$media = '<div class="'.azus()->get('content-blog-media',azuf()->azzu_compute_col($attr['image_size']).' '.azuf()->azzu_compute_col($attr['image_size']+1,array('media_size' =>'col-xs-'))).'">' . azuf()->azu_get_thumb_img( $media_args ) . '</div>';
                echo $media;
        }
	?>

	<div class="<?php azus()->_class('azu-entry-content',azuf()->azzu_compute_col($attr['image_size'],array('invert' => true,'media_empty' => empty($media))).' '.azuf()->azzu_compute_col($attr['image_size']+1,array('invert' => true,'media_empty' => empty($media),'media_size' =>'col-xs-'))); ?>">
		<?php
                    $azu_post_type = '';
                    if('post' == get_post_type())
                        $azu_post_type ='post';
                    echo azuh()->azzu_new_posted_on($azu_post_type);
		?>
                <<?php echo AZU_POST_TITLE_H; ?> class="<?php azus()->_class('azu-entry-title'); ?>">
			<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
		</<?php echo AZU_POST_TITLE_H; ?>>



                <p>
		<?php if ( 'product' == get_post_type() || ('post' == get_post_type() && get_post_format()=='gallery') ): ?>
			<?php
			echo wp_trim_excerpt();
			?>
		<?php else: ?>
			<?php the_excerpt(); ?>
		<?php endif; ?>
                </p>
                
		<?php 
                    //echo azuh()->azzu_post_readmore_link(); 
                ?>

	</div>
    <?php
}

public function azzu_content_page(){
    ?>
    	<div class="<?php azus()->_class('azu-entry-content'); ?>">
		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<div class="'.azus()->get('azu-page-links').'">' . __( 'Pages:', 'azzu'.LANG_DN ), 'after' => '</div>' ) ); ?>
		<?php edit_post_link( __( 'Edit', 'azzu'.LANG_DN ), '<span class="'.azus()->get('azu-edit-link').'">', '</span>' ); ?>
	</div><!-- .entry-content -->
    <?php
}


public function azzu_get_footer(){
    global $post;
    $footer_sidebar = false;

    if ( !( is_page() || is_single() ) ) {
            $footer_sidebar = false;
    } else if ( !empty( $post ) ) {
            $footer_sidebar = get_post_meta( $post->ID, '_azu_footer_widgetarea_id', true ); 
    }

    if ( !$footer_sidebar) {
            $footer_sidebar = apply_filters( 'azzu_default_footer_sidebar', strtolower(AZU_WIDGET_PREFIX.'footer') );
    }
    $footer_layout_style= azuf()->azu_get_option('footer_show');

    ?>
    <?php if ( is_active_sidebar( $footer_sidebar ) && $footer_layout_style!='disabled'  ) : ?>

            <!-- !Footer -->
            <footer id="footer" class="<?php azus()->_class('azu-footer'); ?>">
                    <?php do_action('azzu_before_inside_footer'); ?>
                    <div class="<?php azus()->_class('azu-footer-field'); ?>">
                            <div class="<?php azus()->_class('azu-footer-container'); ?>">
                                    <?php do_action( 'azzu_before_footer_widgets' ); ?>
                                    <?php 
                                        azut()->azu_footer_widget($footer_sidebar);
                                    ?>
                            </div><!-- .azu-footer-container -->
                    </div><!-- .azu-footer-field -->
            </footer><!-- #footer -->
    <?php endif; 
}

public function azzu_get_sidebar($is_dual = false){
    
    $sidebar_position = azuf()->azu_get_option('sidebar_position');
    if($sidebar_position == 'disabled' || ( $is_dual && azuf()->azu_get_option('sidebar_position') != 'dual' ))
        return;

    if($is_dual)
        $widgetarea = '_azu_sidebar_widgetarea_id2';
    else
        $widgetarea = '_azu_sidebar_widgetarea_id';
    
    global $post;
    $sidebar = false;

    if ( !( is_page() || is_single() ) ) {
            $sidebar = false;
    } elseif ( !empty($post) ) {
            $sidebar = get_post_meta( $post->ID, $widgetarea, true );
    }

    // default sidebar
    if ( !$sidebar ) {
            $sidebar = apply_filters( 'azzu_default_sidebar', strtolower(AZU_WIDGET_PREFIX.'sidebar') );
    }
    $iswide = 3;
    $sidebar_class = '';

    if( azuf()->azu_get_option('sidebar_wide',0) )
        $iswide = 4;
    
    if($is_dual)
        $sidebar_class = sprintf('azu-sidebar-left col-sm-pull-%s col-sm-%s', (12 - 2 * $iswide) , $iswide);
    else
        $sidebar_class = 'col-sm-'.$iswide;
    ?>
                            <?php if ( is_active_sidebar( $sidebar ) ) : ?>
                                <div  class="<?php azus()->_class('azu-sidebar-column',$sidebar_class); ?>">
                                    <aside id="sidebar<?php echo $is_dual ? '-left' : '';?>" class="<?php azus()->_class('azu-sidebar-area'); ?>">
                                            <div class="<?php azus()->_class('azu-sidebar'); ?>">
                                            <?php do_action( 'azzu_before_sidebar_widgets' ); ?>
                                            <?php dynamic_sidebar( $sidebar ); ?>
                                            </div>
                                    </aside><!-- #sidebar -->
                                </div>
                            <?php endif; 
}

public function azzu_no_result(){
?>
<div class="<?php azus()->_class('no-results'); ?>">
    <header class="<?php azus()->_class('azu-result-header'); ?>">
        <<?php echo AZU_TITLE_H; ?> class="<?php azus()->_class('azu-result-title'); ?>"><?php _ex( 'Nothing Found', 'atheme', 'azzu'.LANG_DN); ?></<?php echo AZU_TITLE_H; ?>>
    </header><!-- .azu-result-header -->

    <div class="<?php azus()->_class('azu-page-content'); ?>">
        <?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

            <p><?php printf( _x( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'atheme', 'azzu'.LANG_DN), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

        <?php elseif ( is_search() ) : ?>

            <p><?php _ex( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'atheme', 'azzu'.LANG_DN); ?></p>
            <?php get_search_form(); ?>

        <?php else : ?>

            <p><?php _ex( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'atheme', 'azzu'.LANG_DN); ?></p>
            <?php get_search_form(); ?>

        <?php endif; ?>
    </div><!-- .azu-page-content -->
</div><!-- .no-results -->
<?php 
}

/**
 * Woocommerce before main content
 */
function azu_wc_before() {
        if(!( is_search() || is_archive() )) {
            azum()->base_init();
        }
        azum()->set('template', 'product');
        ?>
                <div id="primary" class="<?php azus()->_class('azu-content-area'); ?>">
                <div id="main" class="<?php azus()->_class('azu-main'); ?>">
        <?php
}


/**
 * Woocommerce after main content
 */
function azu_wc_after() {
    ?>
            </div><!-- #main -->
            </div><!-- #primary --> 
    <?php
}

/**
 * Woocommerce before item
 */
function azu_wc_item_before() {
        ?>
                <div class="<?php azus()->_class('azu-wc-item-media'); ?>">
        <?php
}

/**
 * Woocommerce before item title
 */
function azu_wc_item() {
        ?>
                </div><!-- #item media -->
                <div class="<?php azus()->_class('azu-wc-item-desc'); ?>">
        <?php
}


/**
 * Woocommerce share
 */
function azu_wc_share() {
    azuh()->azzu_display_share_buttons('woocommerce', array( 'extended' => true));
}

/**
 * Woocommerce category
 */
function azu_wc_category() {
    global $post, $product;
    $cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
    echo $product->get_categories( ', ', '<span class="posted_in"><span>' . _n( 'Category:', 'Categories:', $cat_count, 'azzu'.LANG_DN ) . '</span> ', '</span>' ); 
}




/**
 * Woocommerce after item
 */
function azu_wc_item_after() {
    ?>
            </div><!-- #item desc -->
    <?php
}

function azu_wc_pagination_args($args = array()){
        $args['prev_next'] = true;
        $args['prev_text'] = '<i class="azu-icon-left-pagi"></i>';
        $args['next_text'] = '<i class="azu-icon-right-pagi"></i>';
        return $args;
}


function azu_wc_columns($columns = 3){
        $columns = of_get_option('wc-archive-columns', 3);
        return $columns;
}

function azu_wc_sidebar($sidebar = ''){
        $is_woocommerce = class_exists( 'Woocommerce' ) && function_exists( 'is_woocommerce' ) && is_woocommerce();
        if(is_archive() && $is_woocommerce && !is_single())
            $sidebar = strtolower(AZU_WIDGET_PREFIX.'woocommerce');
        return $sidebar;
}



function azu_wc_breadcrumb_defaults($args = array()){
        $args['delimiter'] = '<i class="azu-icon-sep"></i>';
        return $args;
}


function azu_wc_review_comment_form_args($args = array()){
        $commenter = wp_get_current_commenter();
        $args['title_reply'] = have_comments() ? __( 'Add a review', 'azzu'.LANG_DN ) : __( 'Be the first to review', 'azzu'.LANG_DN );
        $args['fields']['author'] = '<p class="comment-form-author"><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" placeholder="' . __( 'Name', 'azzu'.LANG_DN  ) . ' *" /></p>';
        $args['fields']['email'] = '<p class="comment-form-email"><input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" placeholder="' . __( 'Email', 'azzu'.LANG_DN  ) . ' *" /></p>';
        return $args;
}

function azu_wc_get_template($located, $template_name, $args, $template_path, $default_path){
    switch ($template_name) {
        case 'global/quantity-input.php':
            $located = AZZU_LIBRARY_DIR.'/woocommerce/quantity-input.php';
            break;
        case 'single-product/meta.php':
            $located = AZZU_LIBRARY_DIR.'/woocommerce/meta.php';
            break;
        case 'single-product/rating.php':
            $located = AZZU_LIBRARY_DIR.'/woocommerce/rating.php';
            break;
        case 'product-searchform.php':
            $located = AZZU_LIBRARY_DIR.'/woocommerce/product-searchform.php';
            break;
        case 'single-product/product-thumbnails.php':
            $located = AZZU_LIBRARY_DIR.'/woocommerce/product-thumbnails.php';
            break;
        case 'loop/pagination.php':
            $located = AZZU_LIBRARY_DIR.'/woocommerce/pagination.php';
            break;   
        case 'loop/title.php':
            $located = AZZU_LIBRARY_DIR.'/woocommerce/title.php';
            break;

        default:
            break;
    }
        return $located;
}




} endif; // main tags
