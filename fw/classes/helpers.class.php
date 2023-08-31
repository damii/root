<?php
/**
 * Azzu helpers.
 *
 * @package azzu
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class AzuCoreHelpers extends azu_base {

protected function __construct() {
    parent::__construct();
}
// add actions inside
protected function add_actions(){}

/**
 * Favicon.
 */
public function azzu_favicon() {
        $output_icon_src = of_get_option('general-favicon', '');
        if ( $output_icon_src ) {
                return azuf()->azu_get_favicon( $output_icon_src );
        }
        return '';
}


/**
 * Get logo image.
 *
 * @return mixed.
 */
function azzu_get_logo_image( $logoname = '', $logo_height = 50, $only_url = false ) {
        $logo = of_get_option( $logoname, array('', 0) );
        if ( azum()->get('page_override') ) {
            $logoblank = '';
            if($logoname == 'header-logo')
                $logoblank = azum()->get('page_header_logo');
            else if($logoname == 'bottom-bar-logo')
                $logoblank = azum()->get('page_bottom_logo');
            if(isset($logoblank) && absint($logoblank) > 0) {
                $logo = array('', $logoblank);
                $logoname = $logoblank;
            }
        }

        $logo_height = absint($logo_height) * azuf()->azu_device_pixel_ratio(2);
        // get default logo
        $default_logo = azuf()->azu_get_uploaded_logo($logo);

        if ( empty($default_logo) ) { return false; }
        if($only_url) return $default_logo[0];

        $alt = ' alt="' .esc_attr( get_bloginfo( 'name' ) ). '"';

        $href ='';
        if ( azum()->get('page_override') ) {
            global $post;
            $href = get_post_meta( $post->ID, '_azu_page_logo_link', true );
        }
        if(empty($href))
            $href = home_url ( '/' );
        
        if($logoname == 'header-logo'){
            // default logo
            $images = azuf()->azu_get_logo_image( $default_logo, $logo_height, $alt, 'logo-default' );
            // float logo 
            $images .= $this->azzu_get_logo_additional( $default_logo, 'header-float-logo', $logo_height, $alt, 'logo-float' );
            // light logo
            $images .= $this->azzu_get_logo_additional( $default_logo, 'header-light-logo', $logo_height, $alt, 'logo-light' );
        }
        else 
            $images = azuf()->azu_get_logo_image( $default_logo, $logo_height, $alt, 'azu-the-image' );
        
        
        $logo = '<a href="'.esc_url ( $href ).'" >'.$images.'</a>';

        return $logo;
}       

/**
 * Additional logo images.
 *
 * @since azzu 1.0
 */
function azzu_get_logo_additional($default_logo, $logoname, $height, $alt, $class) {
    $logo = of_get_option( $logoname, array('', 0) );
    if(is_array($logo) && next( $logo ))
        $default_logo = azuf()->azu_get_uploaded_logo( $logo );
    return azuf()->azu_get_logo_image( $default_logo, $height, $alt, $class );
}

/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @since azzu 1.0
 */
function azzu_new_posted_on( $type = '', $classes = array() ) {

        if ( $type ) {
                $type = '-' . strtolower($type);
        }
        array_push($classes, azus()->get('azu-posted-on'));

        $posted_on = apply_filters("azzu_new_posted_on{$type}", '', $classes);

        return $posted_on;
}

/**
 * Button helper.
 * Look for filters in template-tags.php
 *
 * @return string HTML.
 */
function azzu_get_button_html( $options = array() ) {
        $default_options = array(
                'title'		=> '',
                'class'	=> 'btn',
				'target'	=> '',
                'href'		=> '',
                'custom'	=> ''
        );

        $options = wp_parse_args( $options, $default_options );

        $html = sprintf(
                '<a href="%1$s" class="%2$s"%3$s %5$s>%4$s</a>',
                $options['href'],
                esc_attr($options['class']),
                $options['target'] ? ' target="_blank"' : '',
                $options['title'],
                $options['custom']
        );

        return apply_filters('azzu_get_button_html', $html, $options);
}

/**
 * Add anchor #more-{$post->ID} to href.
 *
 * @return string
 */
function azzu_add_more_anchor( $content = '' ) {
        global $post;

        if ( $post ) {
                $content = preg_replace( '/href=[\'"]?([^\'" >]+)/', 'href="$1#more-' . $post->ID . '"', $content );
        }

        // added in helpers.class.php
        remove_filter( 'azzu_post_readmore_link', array( &$this,'azzu_add_more_anchor'), 15 );
        return $content;
}

/**
 * Next page button.
 *
 */
function azu_get_pagination_type( $mode = 0, $total, $guid='', $current_page = 1) {
    $output = '';
    $load_style = absint($mode);
    $opts = array(
                'end_size' => 4,
                'mid_size' => 2,
                'previouspagelink' => '<i class="azu-icon-left-pagi"></i>'._x( 'Prev','pagination', 'azzu'.LANG_DN ),
                'nextpagelink'     => _x( 'Next','pagination' ,'azzu'.LANG_DN ).'<i class="azu-icon-right-pagi"></i>',
                'echo'     => false
            );
    if($load_style == 1)
         $output = '<div class="'.azus()->get('text-center','paginator with-ajax').'" role="navigation" >'.WPBootstrap_Pager::posttype_pager($opts ,$current_page, $total).'</div>';
    else if($load_style > 1)
         $output = azuh()->azu_get_loadmore_button( $total, 'paginator paginator-more-button with-ajax',$guid );

    return $output;
}


/**
 * Next page button.
 *
 */
function azu_get_loadmore_button( $max, $class = '', $guid='' ) {
        $next_posts_link = azuf()->azu_get_next_posts_url( $max );
        $btn_class = 'btn';
        $loading_text = __('Loading', 'azzu'.LANG_DN);
        $button_text = __('Load more', 'azzu'.LANG_DN);
        if(azum()->get('template') == 'portfolio'){
            $loading_text = __('Loading ...', 'azzu'.LANG_DN);
            $btn_class .= ' azu-btn-reverse';
            $button_text = __('Load more works', 'azzu'.LANG_DN);
        }
            
        if ( $next_posts_link ) {
                return '<div class="' . esc_attr($class) . '" data-guid="'.$guid.'">
                        <a class="'.$btn_class.'" href="javascript: void(0);" data-azu-page="' . azuf()->azu_get_paged_var() .'" data-loading="'.esc_attr( $loading_text ).'"><div class="azu-loading" ></div><span>' . $button_text . '</span></a></div>';
        }
        return '';
}

/**
 * Show content with funny details button.
 *
 */
function azzu_the_content($excerpt = true) {
        global $post, $more, $pages;
        $more = 0;
        $content = '';
        
        // remove azzu_the_content() filter
        remove_filter( 'azzu_post_readmore_link', 'azzu_return_empty_string', 15 );
        
        if ( $excerpt && !has_excerpt( $post->ID ) ) {

                $excerpt_length = apply_filters('excerpt_length', 55);
                $content = $this->azzu_get_the_clear_content();

                // check for more tag
                if ( preg_match( '/<!--more(.*?)?-->/', $post->post_content, $matches ) ) {
                        $content .= apply_filters( 'azzu_get_content-more', '' );

                        if ( count($pages) > 1 ) {
                                add_filter( 'azzu_post_readmore_link', 'azzu_return_empty_string', 15 );
                        } else {
                                add_filter( 'azzu_post_readmore_link', array( &$this,'azzu_add_more_anchor'), 15 );
                        }
                // full length OR check content length
                } elseif ( $excerpt_length == 0 || azuf()->azu_count_words( $content ) <= $excerpt_length ) {
                        add_filter( 'azzu_post_readmore_link', 'azzu_return_empty_string', 15 );
                } else {
                        $content = '';
                }

        }

        if(!$excerpt){
            $content = the_content('');
            if ( !preg_match( '/<!--more(.*?)?-->/', $post->post_content, $matches ) ) {
                add_filter( 'azzu_post_readmore_link', 'azzu_return_empty_string', 15 );
            }
        }
        // if we got excerpt or content more than $excerpt_length
        else if ( empty($content) && get_the_excerpt() ) {

                $content = apply_filters( 'the_excerpt', get_the_excerpt() );
        }
        return $content;
}


/**
 * Azzu readmore button.
 *
 * @param int $post_id Post ID.Default is null.
 * @param mixed $class Custom classes. May be array or string with classes separated by ' '.
 */
function azzu_post_readmore_link( $post_id = null, $class = '' ) {
        global $post;
        $attr = azum()->get('attr');
        
        if ( !$post_id && !$post ) {
                return '';
        }elseif ( !$post_id ) {
                $post_id = $post->ID;
        }

        if ( post_password_required( $post_id ) ) {
                return '';
        }

        if ( empty( $class ) ) {
            $class = azus()->get('azu-readmore');
        }

        $output = '';
        $url = get_permalink( $post_id );
        
        $read_more_text = _x( 'Read more', 'atheme', 'azzu'.LANG_DN );
        if(isset($attr) && is_array($attr) && array_key_exists( 'readmore', $attr )){
            if(empty($attr['readmore']))
                $url = false;
            else
                $read_more_text = $attr['readmore'];
        }
        
        //$read_more_text = apply_filters( 'azzu_post_readmore_text', $read_more_text);
        
        if ( $url ) {
                $output = sprintf(
                        '<a href="%1$s" class="%2$s" rel="nofollow">%3$s<span></span></a>',
                        $url,
                        esc_attr( $class  ),
                        $read_more_text
                );
        }

        return apply_filters( 'azzu_post_readmore_link', $output, $post_id, $class );
}


/**
 * Azzu edit link.
 *
 * @param int $post_id Post ID.Default is null.
 * @param mixed $class Custom classes. May be array or string with classes separated by ' '.
 */
function azzu_post_edit_link( $post_id = null, $class = array('azu-tooltip') ) {
        $output = '';
        if ( current_user_can( 'edit_posts' ) ) {
                global $post;

                if ( !$post_id && !$post ) {
                        return '';
                }

                if ( !$post_id ) {
                        $post_id = $post->ID;
                }


                if ( !is_array( $class ) ) {
                        $class = explode( ' ', $class );
                }

                $url = get_edit_post_link( $post_id );

                $final_classes = array_merge( array(azus()->get('azu-edit-link')), $class );

                if ( $url ) {
                        $output = sprintf(
                                '<a href="%1$s" data-toggle="tooltip" data-placement="top" class="%2$s" target="_blank" title="%3$s"><i class="azu-icon-edit"></i><span></span></a>',
                                $url,
                                esc_attr( implode( ' ', $final_classes ) ),
                                _x( 'Edit', 'edit button', 'azzu'.LANG_DN )
                        );
                }
        }
        return apply_filters( 'azzu_post_edit_link', $output, $post_id, $class );
}



/**
 * Return content passed through these functions:
 *	strip_shortcodes( $content );
 *	apply_filters( 'the_content', $content );
 *	str_replace( ']]>', ']]&gt;', $content );
 *
 * @return string
 */
function azzu_get_the_clear_content() {
        $content = get_the_content( '' );
        $content = strip_shortcodes( $content );
        $content = apply_filters( 'the_content', $content );
        $content = str_replace( ']]>', ']]&gt;', $content );

        return $content;
}




/**
 * Get attachments post data.
 *
 * @param array $media_items Attachments id's array.
 * @return array Attachments data.
 */
function azzu_get_attachment_post_data( $media_items, $orderby = 'post__in', $order = 'DESC', $posts_per_page = -1 ) {
        if ( empty( $media_items ) ) {
                return array();
        }

        global $post;

        // sanitize $media_items
        $media_items = array_diff( array_unique( array_map( "absint", $media_items ) ), array(0) );

        if ( empty( $media_items ) ) {
                return array();
        }

        // get attachments
        $query = new WP_Query( array(
                'no_found_rows'     => true,
                'posts_per_page'    => $posts_per_page,
                'post_type'         => 'attachment',
                'post_mime_type'    => 'image',
                'post_status'       => 'inherit',
                'post__in'			=> $media_items,
                'orderby'			=> $orderby,
                'order'				=> $order,
        ) );

        $attachments_data = array();

        if ( $query->have_posts() ) {

                // backup post
                $post_backup = $post;

                while ( $query->have_posts() ) { $query->the_post();
                        $post_id = get_the_ID();
                        $data = array();

                        // attachment meta
                        $data['full'] = $data['width'] = $data['height'] = '';
                        $meta = wp_get_attachment_image_src( $post_id, 'full' );
                        if ( !empty($meta) ) {
                                $data['full'] = esc_url($meta[0]);
                                $data['width'] = absint($meta[1]);
                                $data['height'] = absint($meta[2]);
                        }

                        $data['thumbnail'] = wp_get_attachment_image_src( $post_id, 'thumbnail' );

                        $data['alt'] = esc_attr( get_post_meta( $post_id, '_wp_attachment_image_alt', true ) );
                        $data['caption'] = wp_kses_post( $post->post_excerpt );
                        $data['description'] = wp_kses_post( $post->post_content );
                        $data['title'] = $this->azzu_image_title_is_hidden( $post_id ) ? '' : get_the_title( $post_id );
                        $data['permalink'] = get_permalink( $post_id );
                        $data['video_url'] = esc_url( get_post_meta( $post_id, 'azu-video-url', true ) );
                        $data['mime_type_full'] = get_post_mime_type( $post_id );
                        $data['mime_type'] = azuf()->azu_get_short_post_myme_type( $post_id );
                        $data['ID'] = $post_id;

                        // attachment meta
                        $data['meta'] = $this->azzu_new_posted_on();

                        $attachments_data[] = apply_filters( 'azzu_get_attachment_post_data-attachment_data', $data, $media_items );
                }

                // restore post
                $post = $post_backup;
                setup_postdata( $post );
        }

        return $attachments_data;
}


/**
 * Shows a breadcrumb for all types of pages.  This is a wrapper function for the AzuBreadcrumb class,
 * which should be used in theme templates.
 *
 * @since azzu 1.0
 * @access public
 * @param  array $args Arguments to pass to AzuBreadcrumb.
 * @return void
 */
public function azu_breadcrumb( $args = array() ) {

        if ( function_exists( 'is_bbpress' ) && is_bbpress() )
                $breadcrumb = new bbPress_AzuBreadcrumb( $args );
        if ( class_exists( 'Woocommerce' ) && function_exists( 'is_woocommerce' ) && is_woocommerce() )
                $breadcrumb = new woo_AzuBreadcrumb( $args );
        else
                $breadcrumb = new AzuBreadcrumb( $args );

        return $breadcrumb->trail();
}

/**
 * Register font for Theme.
 *
 * @since azzu 1.0
 *
 * @return string
 */
function azu_font_url() {
        $font_url = '';
        /*
         * Translators: If there are characters in your language that are not supported
         * by Default font, translate this to 'off'. Do not translate into your own language.
         */
        $font_url = add_query_arg( 'family', urlencode( AZZU_THEME_DEFAULT_FONT.':300,400,700,300italic,400italic,700italic' ), "//fonts.googleapis.com/css" );

        return esc_url_raw($font_url);
}


/**
 * Azzu web fonts enqueue.
 *
 * @since azzu 1.0
 */
function azzu_enqueue_web_fonts() {

        $fonts = array();

        // fonts
        for ( $i=1;$i<=4;$i++ ) {
                $lisbox_font_weight = get_option( 'azu_lisbox_font_weight');
                if($lisbox_font_weight !== false && array_key_exists('listbox_font'.$i,$lisbox_font_weight)){
                     $lisbox_font_weight = $lisbox_font_weight['listbox_font'.$i];
                }
                else 
                   $lisbox_font_weight = array(400,300,100);
                
                $font_name = of_get_option('azu-font-family'.$i);
                
                // we do not want duplicates
                $isDuplicate = false;
                foreach ( $fonts as $id=>$font ) {
                    if($font['font'] == $font_name){
                        $fonts[$id]['weight'] = array_unique (array_merge($fonts[$id]['weight'],$lisbox_font_weight));
                        $isDuplicate = true;
                        break;
                    }
                }
                if(!$isDuplicate)
                $fonts[ $i ] = array(
                        'weight' => $lisbox_font_weight,
                        'font' => $font_name
                    );
        }

        foreach ( $fonts as $id=>$font ) {
                if ( azu_stylesheet_maybe_web_font($font['font']) && ($font_uri = azuf()->azu_make_web_font_uri($font['font'], $font['weight'])) ) {
                        wp_enqueue_style('azu-font-family' .$id , $font_uri);
                }
        }
}


/**
 * Display topbar social icons. Data grabbed from theme options.
 *
 */
function azzu_get_topbar_social_icons($instance) {
        $saved_icons = $instance['icons'];

        if ( !is_array($saved_icons) || empty($saved_icons) ) {
                return '';
        }

        // reverse array coz it's have float: right and shown in front end in opposite order
        //$saved_icons = array_reverse( $saved_icons );

        $icons_data = azuf()->azzu_get_social_icons_data();
        $icons_white_list = array_keys($icons_data);
        $clean_icons = array();
        foreach ( $saved_icons as $saved_icon ) {

                if ( !is_array($saved_icon) ) {
                        continue;
                }

                if ( empty($saved_icon['icon']) || !in_array( $saved_icon['icon'], $icons_white_list ) ) {
                        continue;
                }

                if ( empty($saved_icon['url']) ) {
                        $saved_icon['url'] = '#';
                }

                $icon = $saved_icon['icon'];

                $clean_icons[] = array(
                        'icon' =>  $icon,
                        'title' => $icons_data[ $icon ],
                        'link' => $saved_icon['url']
                );
        }

        $output = '';
        if ( $clean_icons ) {

                $soc_icons_class = azus()->get('social-ico');

                $output .= '<div class="' . $soc_icons_class . '">';

                $output .= $this->azzu_get_social_icons( $clean_icons );

                $output .= '</div>';

        }

        return $output;
}        

/**
 * Generate social icons links list.
 * $icons = array( array( 'icon_class', 'title', 'link' ) )
 *
 * @param $icons array
 *
 * @return string
 */
function azzu_get_social_icons( $icons = array(), $common_classes = array() ) {
        if ( empty($icons) || !is_array($icons) ) {
                return '';
        }

        $classes = $common_classes;
        if ( !is_array($classes) ) {
                $classes = explode( ' ', trim($classes) );
        }

        $output = array();
        foreach ( $icons as $icon ) {

                if ( !isset($icon['icon'], $icon['link'], $icon['title']) ) {
                        continue;
                }

                $output[] = $this->azzu_get_social_icon( $icon['icon'], $icon['link'], $icon['title'], $classes );
        }

        return apply_filters( 'azzu_get_social_icons', implode( '', $output ), $output, $icons, $common_classes );
}

/**
 * Get social icon.
 *
 * @return string
 */
function azzu_get_social_icon( $icon = '', $url = '#', $title = '', $classes = array(), $target = '_blank' ) {

        $clean_target = esc_attr( $target );
        $hide_class = 'azu-seo-text';
        $icon_classes = is_array($classes) ? $classes : array();
        $icon_classes[] = 'icon-'.$icon;

        // check for skype
        if ( 'skype' == $icon){
                $clean_url = esc_attr( $url );
        } else if( 'phone' == $icon) {
                $title = esc_attr($url);
                $clean_url = '#';
                $hide_class = '';
        } else if ( 'mail' == $icon && is_email($url) ) {
                $clean_url = 'mailto:' . esc_attr($url);
                $clean_target = '_top';
        } else {
                $clean_url = esc_url( $url );
        }

        $output = sprintf(
                '<a title="%2$s" target="%4$s" href="%1$s" class="%3$s"><span class="%5$s">%2$s</span></a>',
                $clean_url,
                esc_attr( $title ),
                esc_attr( implode( ' ',  $icon_classes ) ),
                $clean_target,
                $hide_class
        );

        return $output;
}


/**
 * Share buttons lite.
 *
 */
function azzu_get_share_buttons_for_photo( $place = '', $options = array() ) {
        global $post;
        $buttons = array();
        $social_networks=of_get_option('social_buttons-' . $place, array());

        foreach ( $social_networks as $social=>$network ) {
            if($network)
                $buttons[] = $social;
        }

        if ( empty($buttons) ) { return ''; }

        $default_options = array(
                'id'	=> null,
        );
        $options = wp_parse_args($options, $default_options);

        $options['id'] = $options['id'] ? absint($options['id']) : $post->ID;

        $html = '';

        $html .= sprintf(
                ' data-azu-share="%s"',
                esc_attr( str_replace( '+', '', implode( ',', $buttons ) ) )
        );

        return $html;
}

/**
 * Post author snippet.
 *
 * Use only in the loop.
 *
 * @since azzu 1.0
 */
function azzu_display_post_author() {

        $user_url = get_the_author_meta('user_url');
        $avatar = get_avatar( get_the_author_meta('ID'), 70, '' );
        ?>

        <div class="<?php azus()->_class('azu-entry-author'); ?>">
                <?php
                if ( $user_url ) {
                        printf( '<a href="%s" class="">%s</a>', esc_url($user_url), $avatar );
                } else {
                        echo $avatar;
                }
                ?>
                <p class="text-primary"><?php _e('Author: ', 'azzu'.LANG_DN); the_author_meta('display_name'); ?></p>
                <p class="text-small"><?php the_author_meta('description'); ?></p>
        </div>

<?php
}


/**
 * Check if post navigation enabled.
 *
 * @return boolean
 */
function azzu_is_post_navigation_enabled() {
        $post_type = get_post_type();

        // get navigation flag based on post type
        switch ( $post_type ) {
                case 'post' : $show_navigation = of_get_option( 'general-next_prev_in_blog', true ); break;
                case 'azu_portfolio' : $show_navigation = of_get_option( 'general-next_prev_in_portfolio', true ); break;
                default : $show_navigation = true;
        }
        return $show_navigation;
}

/**
 * Get first image associated with the post.
 *
 * @param integer $post_id Post ID.
 * @return mixed Return (object) attachment on success ar false on failure.
 */
function azzu_get_first_image( $post_id = null ) {
        if ( in_the_loop() && !$post_id ) {
                $post_id = get_the_ID();
        }

        if ( !$post_id ) {
                return false;
        }

        $args = array(
                'posts_per_page' 	=> 1,
                'order'				=> 'ASC',
                'post_mime_type' 	=> 'image',
                'post_parent' 		=> $post_id,
                'post_status'		=> 'inherit',
                'post_type'			=> 'attachment',
        );

        $attachments = get_children( $args );

        if ( $attachments ) {
                return current($attachments);
        }

        return false;
}

/**
 * Get posts by categories.
 *
 * @return object WP_Query Object. 
 */
function azzu_get_posts_in_categories( $options = array() ) {

        $default_options = array(
                'post_type'	=> 'post',
                'taxonomy'	=> 'category',
                'field'		=> 'term_id',
                'cats'		=> array( 0 ),
                'select'	=> 'all',
                'args'		=> array(),
        );

        $options = wp_parse_args( $options, $default_options );

        $args = array(
                'posts_per_page'	=> -1,
                'post_type'			=> $options['post_type'],
                'no_found_rows'     => 1,
                'post_status'       => 'publish',
                'tax_query'         => array( array(
                        'taxonomy'      => $options['taxonomy'],
                        'field'         => $options['field'],
                        'terms'         => $options['cats'],
                ) ),
        );

        $args = array_merge( $args, $options['args'] );

        switch( $options['select'] ) {
                case 'only': $args['tax_query'][0]['operator'] = 'IN'; break;
                case 'except': $args['tax_query'][0]['operator'] = 'NOT IN'; break;
                default: unset( $args['tax_query'] );
        }

        $query = new WP_Query( $args );

        return $query;
}


/**
 * Get project link.
 *
 * return string HTML.
 */
function azzu_get_project_link( $class = 'btn') {
        if ( post_password_required() || !in_the_loop() ) {
                return '';
        }

        global $post;

        // project link
        $project_link = '';
        if ( get_post_meta( $post->ID, '_azu_project_options_show_link', true ) ) {
                $title = get_post_meta( $post->ID, '_azu_project_options_link_name', true );
                $title = $title ? $title : __('Link', 'azzu'.LANG_DN);
                $title .= '<span></span>';
                $project_link = $this->azzu_get_button_html( array(
                        'title'		=> $title,
                        'href'		=> esc_url(get_post_meta( $post->ID, '_azu_project_options_link', true )),
                        'target'	=> get_post_meta( $post->ID, '_azu_project_options_link_target', true ),
                        'class'		=> $class,
                ) );
        }

        return $project_link;
}


/**
 * Get related posts attachments data slightly modified.
 *
 * @return array Attachments data.
 */
function azzu_get_related_posts( $options = array() ) {
        $default_options = array(
                'select'			=> 'only',
                'exclude_current'	=> true,
                'args'				=> array(),
        );

        $options = wp_parse_args( $options, $default_options );

        // exclude current post if in the loop
        if ( in_the_loop() && $options['exclude_current'] ) {
                $options['args'] = array_merge( $options['args'], array( 'post__not_in' => array( get_the_ID() ) ) );
        }

        $posts = $this->azzu_get_posts_in_categories( $options );

        $attachments_ids = array();
        $attachments_data_override = array();
        $posts_data = array();

        // get posts attachments id
        if ( $posts->have_posts() ) {

                while ( $posts->have_posts() ) { $posts->the_post();

                        // thumbnail or first attachment id
                        if ( has_post_thumbnail() ) {
                                $attachment_id = get_post_thumbnail_id();

                        } else if ( $attachment = $this->azzu_get_first_image() ) {
                                $attachment_id = $attachment->ID;

                        } else {
                                $attachment_id = 0;

                        }

                        switch ( get_post_type() ) {
                                case 'post':
                                        $post_meta = $this->azzu_new_posted_on( 'post' );
                                        break;
                                case 'azu_portfolio':
                                        $post_meta = $this->azzu_new_posted_on( 'azu_portfolio' );
                                        break;
                                default:
                                        $post_meta = $this->azzu_new_posted_on();
                        }

                        $post_data = array();

                        /////////////////////////
                        // attachment data //
                        /////////////////////////

                        $post_data['full'] = $post_data['width'] = $post_data['height'] = '';
                        $meta = wp_get_attachment_image_src( $attachment_id, 'full' );
                        if ( !empty($meta) ) {
                                $post_data['full'] = esc_url($meta[0]);
                                $post_data['width'] = absint($meta[1]);
                                $post_data['height'] = absint($meta[2]);
                        }

                        $post_data['thumbnail'] = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );

                        $post_data['caption'] = '';
                        $post_data['video_url'] = esc_url( get_post_meta( $attachment_id, 'azu-video-url', true ) );
                        $post_data['mime_type_full'] = get_post_mime_type( $attachment_id );
                        $post_data['mime_type'] = azuf()->azu_get_short_post_myme_type( $attachment_id );
                        $post_data['ID'] = $attachment_id;

                        $post_data['image_attachment_data'] = array(
                                'caption' => $post_data['caption'],
                                'description' => wp_kses_post( get_post_field( 'post_content', $attachment_id ) ),
                                'title' => $this->azzu_image_title_is_hidden( $attachment_id ) ? '' : get_the_title( $attachment_id ),
                                'permalink' => get_permalink( $attachment_id ),
                                'video_url' => $post_data['video_url'],
                                'ID' => $attachment_id
                        );

                        ///////////////////
                        // post data //
                        ///////////////////

                        $post_data['title'] = get_the_title();
                        $post_data['permalink'] = get_permalink();
                        $post_data['link'] = $this->azzu_get_project_link('project-link');
                        $post_data['description'] = get_the_excerpt();
                        $post_data['alt'] = get_the_title();
                        $post_data['parent_id'] = get_the_ID();
                        $post_data['meta'] = $post_meta;

                        // save data
                        $posts_data[] = $post_data;
                }
                wp_reset_postdata();

        }

        return $posts_data;
}

/**
* Check image title status.
*
*/
function azzu_image_title_is_hidden( $img_id ) {
       return false;
}

/**
 * Description here.
 *
 * Some sort of images list with some description and post title and date ... eah
 *
 * @return array Array of items or empty array.
 */
function azzu_get_posts_small_list( $attachments_data, $options = array() ) {
        if ( empty( $attachments_data ) ) {
                return array();
        }

        global $post;
        $default_options = array(
                'links_rel'		=> '',
                'is_date'	=> false,
                'is_comment'	=> false,
                'class'         => 'alignleft azu-rollover rollover-small',
                'show_title'	=> true,
                'image_size'    => 60,
                'show_images'	=> true
        );
        $options = wp_parse_args( $options, $default_options );

        $image_args = array(
                'img_class' => '',
                'class'		=> $options['class'],
                'custom'	=> $options['links_rel'],
                'options'	=> array( 'w' => $options['image_size'], 'h' => $options['image_size'], 'z' => true ),
                'echo'		=> false,
        );

        $articles = array();
        $post_was_changed = false;
        $post_backup = $post;

        foreach ( $attachments_data as $data ) {
                $class = '';
                $new_post = null;

                if ( isset( $data['parent_id'] ) ) {

                        $post_was_changed = true;
                        $new_post = get_post( $data['parent_id'] );

                        if ( $new_post ) {
                                $post = $new_post;
                                setup_postdata( $post );
                        }
                }

                $permalink = esc_url($data['permalink']);

                $attachment_args = array(
                        'href'		=> $permalink,
                        'img_meta' 	=> array( $data['full'], $data['width'], $data['height'] ),
                        'img_id'	=> empty($data['ID']) ? 0 : $data['ID'],
                        'echo'		=> false,
                        //'custom'        => 'data-toggle="tooltip" data-placement="top" data-original-title="'.esc_html($data['title']).'"',
                        'wrap'		=> '<a %CLASS% %HREF% %CUSTOM%><img %IMG_CLASS% %SRC% %SIZE% %ALT% /></a>',
                );

                // show something if there is no title
                if ( empty($data['title']) ) {
                        $data['title'] = _x('No title', 'atheme', 'azzu'.LANG_DN);
                }

                if ( !empty( $data['parent_id'] ) ) {
                        $post_format = get_post_format( $data['parent_id'] );
                        if(empty($post_format))
                            $class = 'post-standart';
                        else
                            $class = 'post-'.$post_format;
                        if ( empty($data['ID']) ) {
                                $attachment_args['wrap'] = '<a %HREF% %CLASS% %TITLE%></a>';
                                $attachment_args['class'] = $image_args['class'] . ' no-avatar';
                                $attachment_args['img_meta'] = array('', 0, 0);
                                $attachment_args['options'] = false;
                        }
                }
                if($options['show_title']){
                    $class .= ' clearfix';
                }
                $sub_data = '';
                if($options['is_date']){
                    $show_date = get_the_date(get_option('date_format'));
                    $diff = (current_time('timestamp') - get_the_time('U'))/86400;
                    $human_time = of_get_option('general-human-time',0);
                    if($human_time == 1 || ($human_time > 1 && $diff >= 0 && $diff <= 7))
                        $show_date = sprintf( _x('%s ago','atheme','azzu'.LANG_DN),human_time_diff( get_the_time('U'), current_time('timestamp') ));

                    $sub_data .= '<time class="text-secondary" datetime="' . get_the_date('c') . '">' . $show_date . '</time>';
                }
                elseif($options['is_comment']){
                    $sub_data .= azut()->azzu_get_small_post_comments();
                }
                
                $img_tag = '<div class="'. azus()->get('azu-rel-media').'">'.azuf()->azu_get_thumb_img( array_merge($image_args, $attachment_args) ).'</div>';
                
                $article = sprintf(
                        '<article class="%s">%s<div class="'. azus()->get('azu-rel-content') .'">%s%s</div></article>',
                        $class,
                        $options['show_images'] && (strpos($img_tag, '<img') !== false) ? $img_tag : '',
                        $options['show_title'] ? '<a href="' . $permalink . '" title="'.esc_attr($data['title']).'">' . esc_html($data['title']) . '</a><br />' : '',
                        $sub_data
                );

                $articles[] = $article;
        }

        if ( $post_was_changed ) {
                $post = $post_backup;
                setup_postdata( $post );
        }

        return $articles;
}


/**
 * Display related posts.
 *
 */
function azzu_display_related_posts() {
        if ( !of_get_option( 'general-show_rel_posts', false ) ) {
                return '';
        }

        global $post;

        $html = '';
        $r_mode = get_post_meta( $post->ID, '_azu_post_options_related_mode', true );

        if ( $r_mode ) 
                $terms = get_post_meta( $post->ID, '_azu_post_options_related_categories', true ); 
        else  
                $terms = wp_get_object_terms( $post->ID, 'category', array('fields' => 'ids') );

        if ( $terms && !is_wp_error($terms) ) {

                $attachments_data = $this->azzu_get_related_posts( array(
                        'cats'		=> $terms,
                        'post_type' => 'post',
                        'taxonomy'	=> 'category',
                        'args'		=> array( 'posts_per_page' => intval(of_get_option('general-rel_posts_max', 12)) )
                ) );

                $head_title = esc_html(of_get_option( 'general-rel_posts_head_title', 'Related posts' ));

                $posts_list = $this->azzu_get_posts_small_list( $attachments_data , array('is_date' => true) );
                if ( $posts_list ) {

                        foreach ( $posts_list as $p ) {
                                $html .= sprintf( '<div class="'.azus()->get('azu-rel-post-cell').'"><div class="borders">%s</div></div>', $p );
                        }

                        $html = '<section class="'.azus()->get('azu-rel-post-container').'"><div class="row">' . $html . '</div></section>';

                        // add title
                        if ( $head_title ) {
                                $html = '<div class="azu-rel-title-con"><'.AZU_REL_POST_TITLE_H.' id="azu-rel-posts-title" class="'.azus()->get('azu-entry-title').'">' . $head_title . '</'.AZU_REL_POST_TITLE_H.'></div>' . $html;
                        }

                }
        }

        echo (string) apply_filters( 'azzu_display_related_posts', $html );
}

/**
 * Display related projects.
 *
 */
function azzu_display_related_projects() {
        global $post;
        $html = '';
        // if related projects turn on in theme options
        if ( of_get_option( 'general-show_rel_projects', false ) ) {

                $r_mode = (bool) get_post_meta( $post->ID, '_azu_project_options_related_mode', true );

                if ( $r_mode ) 
                        $terms = get_post_meta( $post->ID, '_azu_project_options_related_categories', true ); 
                else  
                        $terms = wp_get_object_terms( $post->ID, 'azu_portfolio_category', array('fields' => 'ids') );
                
                if ( $terms && !is_wp_error($terms) ) {

                        $slider_title = of_get_option( 'general-rel_projects_head_title', 'Related projects' );
                        $html .= '<div class="azu-rel-title-con"><'.AZU_REL_POST_TITLE_H.' id="azu-rel-projects-title">'.$slider_title.'</'.AZU_REL_POST_TITLE_H.'></div>';
                        $slider_class = array('related-projects');
                        $attr = array(
                            'type'                  => 'slider',
                            'hover_effect'          => 'apollo',
                            'padding'               => '0',
                            'category'              => implode(",",$terms),
                            'class'                 => implode(" ",$slider_class),
                            'columns'               => of_get_option('general-rel_projects_slides', 1),
                            'number'                => absint(of_get_option('general-rel_projects_max', 12)),
                            'show_title'            => of_get_option('general-rel_projects_title', 0),
                            'show_excerpt'          => of_get_option('general-rel_projects_excerpt', 0),
                            'show_details'          => of_get_option('general-rel_projects_readmore', 1),
                            'show_link'             => of_get_option('general-rel_projects_link', 1),
                            'meta_info'             => of_get_option('general-rel_projects_meta', 1),
                            'proportion'            => '4:3'
                        );
                        $attr['slides'] = $attr['columns'];
                        azum()->set('project_like_hide',true);
                        $html .= azut()->azzu_call_shortcode('azu_portfolio',$attr);
                        
                }
        }

        echo (string) apply_filters('azzu_display_related_projects', $html);
}


/**
 * Display share buttons.
 */
function azzu_display_share_buttons( $place = '', $options = array() ) {
        global $post;
        $buttons = array();
        $social_networks=of_get_option('social_buttons-' . $place, array());

        foreach ( $social_networks as $social=>$network ) {
            if($network)
                $buttons[] = $social;
        }
        if ( empty($buttons) ) { return ''; }

        $default_options = array(
                'extended'  => false,
                'echo'      => true,
                'class'     => array(),
                'id'        => null,
                'share'     => _x('Share:','atheme','azzu'.LANG_DN),
        );
        $options = wp_parse_args($options, $default_options);

        $class = $options['class'];
        if ( !is_array($class) ) { $class = explode(' ', $class); }

        $class[] = azus()->get('azu-entry-share','azu-tooltip');

        // get title
        if ( !$options['id'] ) {
                $options['id'] = $post->ID;
                $t = isset( $post->post_title ) ? $post->post_title : '';
        } else {
                $_post = get_post( $options['id'] );
                $t = isset( $_post->post_title ) ? $_post->post_title : '';
        }

        // get permalink
        $u = get_permalink( $options['id'] );

        $protocol = "http";
        if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) $protocol = "https";

        $buttons_list = azuf()->azzu_themeoptions_get_social_buttons_list();

        $html = '';

        if($options['extended'])
            $html .= '<div class="' . esc_attr(implode(' ', $class)) .  '" ><div class="'.azus()->get('azu-social-share','azu-share-extended').'"><span style="padding-right: 12px" class="azu-icon-share">'.$options['share'].'</span>';
        else    
            $html .= '<div class="' . esc_attr(implode(' ', $class)) .  ' dropup" data-toggle="tooltip" data-placement="top" title="'.$options['share'].'">
                                <div class="'.azus()->get('azu-social-share','azu-icon-share dropdown-toggle').'" data-toggle="dropdown" aria-expanded="false" role="menu"></div><div class="dropdown-menu"><svg id="azu-svg-divider" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="5px" viewBox="0 0 24 5" version="1.1"><g stroke-width="1" fill="none" fill-rule="evenodd" ><g ><path d="M0 0 C0 0 5 0 6 0 C8.5 0 10.3 4 12 4 C13.7 4 15.5 0 18 0 C19 0 24 0 24 0 L24 1 C24 1 18.5 1 18 1 C16 1 14 5 12 5 C10 5 8 1 6 1 C6 1 0 1 0 1 L0 0 Z" /></g></g></svg>';

        foreach ( $buttons as $index => $button ) {
                $classes = array( 'share-button' );
                $url = '';
                $desc = $buttons_list[ $button ];
                $share_title = _x('share', 'share buttons', 'azzu'.LANG_DN);
                $custom = '';

                switch( $button ) {
                        case 'twitter':

                                $classes[] = 'icon-twitter';
                                $share_title = _x('tweet', 'share buttons', 'azzu'.LANG_DN);
                                $url = add_query_arg( array('status' => urlencode($t . ' ' . $u) ), $protocol . '://twitter.com/home' );
                                break;
                        case 'facebook':

                                $url_args = array( 's=100', urlencode('p[url]') . '=' . esc_url($u), urlencode('p[title]') . '=' . urlencode($t) );
                                if ( has_post_thumbnail( $options['id'] ) ) {
                                        $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $options['id'] ), 'full' );
                                        if ( $thumbnail ) {
                                                $url_args[] = urlencode('p[images][0]') . '=' . esc_url($thumbnail[0]);
                                        }
                                }

                                // mobile args
                                $url_args[] = 't=' . urlencode($t);
                                $url_args[] = 'u=' . esc_url($u);

                                $classes[] = 'icon-facebook';

                                $url = $protocol . '://www.facebook.com/sharer.php?' . implode( '&', $url_args );
                                break;
                        case 'google+':

                                $t = str_replace(' ', '+', $t);
                                $classes[] = 'icon-google';
                                $url = add_query_arg( array('url' => urlencode($u), 'title' => $t), $protocol . '://plus.google.com/share' );
                                break;
                        case 'linkedin':   
                            $classes[] = 'icon-linkedin';
                            $url = add_query_arg( array('url' => urlencode($u), 'title' => urlencode($t)), $protocol . '://www.linkedin.com/shareArticle?mini=true' );
                        break;
                        case 'pinterest':

                                $url = '//pinterest.com/pin/create/button/';
                                $custom = ' data-pin-config="above" data-pin-do="buttonBookmark"';

                                $thumb_id = get_post_thumbnail_id($options['id']);
                                // if image
                                if ( $thumb_id )  //wp_attachment_is_image($options['id'])
                                {
                                        $image = wp_get_attachment_image_src($thumb_id, 'full');

                                        if ( !empty($image) ) {
                                                $url = add_query_arg( array(
                                                        'url'			=> urlencode($u),
                                                        'media'			=> urlencode($image[0]),
                                                        'description'	=> urlencode($t)
                                                        ), $url
                                                );

                                                $custom = '';
                                        }
                                }

                                $classes[] = 'icon-pinterest';
                                $share_title = _x('pin it', 'share buttons', 'azzu'.LANG_DN);

                                break;
                }

                $desc = esc_attr($desc);
                $share_title = esc_attr($share_title);
                $classes_str = esc_attr( implode(' ', $classes) );
                $url = esc_url( $url );

                
                if($options['extended']){
                    $custom .= ' data-toggle="tooltip" data-placement="top"';
					$classes_str .= ' azu-tooltip';
                    // tooltip hack fix right margin
                    if($index === (count($buttons) - 1))
                        $custom .=' style="margin-right: 0;"';
                }
                
                $share_button = sprintf(
                        '<a href="%2$s" class="%1$s" target="_blank" title="%3$s"%5$s><span class="azu-seo-text">%3$s</span><span class="share-content">%4$s</span></a>',
                        $classes_str,
                        $url,
                        $desc,
                        $share_title,
                        $custom
                );

                $html .= apply_filters( 'azzu_share_button', $share_button, $button, $classes, $url, $desc, $share_title, $t, $u );
        }

        $html .= '</div>
                </div>';

        $html = apply_filters( 'azzu_display_share_buttons', $html );

        if ( $options['echo'] ) {
                echo $html;
        }
        return $html;
}

/**
 * Make top/bottom menu.
 *
 * @param $menu_name string Valid menu name.
 * @param $style string Align of menu. May be left or right. right by default.
 *
 * @since azzu 1.0
 */
function azzu_nav_menu_list( $menu_name = '', $class='' ) {
        $menu_list = '';

        if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {

                $menu = wp_get_nav_menu_object( $locations[ $menu_name ] );

                if ( !$menu ) {
                        return '';
                }

                $menu_list .= '<div class="' . azus()->get('azu-mini-nav', $class) . '">';

                $menu_list .= wp_nav_menu( array(
                        'items_wrap'		=>   '<ul id="%1$s" class="%2$s">%3$s</ul>',
                        'menu_class'            => azus()->get('azu-mini-menu'),
                        'echo'            => false,
                        'depth' => 1,
                        //'walker' => new Walker_Nav_Menu,
                        'theme_location' => $menu_name
                ) );
                $menu_list .= '</div>';
        }
        echo $menu_list;
}

/**
 * Returns terms names list separated by separator based on terms term_id
 *
 * @since azzu 1.0
 * @param  array  $args Default arguments: array( 'term_id' => array(), 'taxonomy' => 'category', 'separator' => ', ', 'titles' => array() ).
 * Default titles: array( 'empty_id' => __( 'All', 'azzu'.LANG_DN ), 'no_result' => __('There is no categories', 'azzu'.LANG_DN) )
 * @return string       Terms names list or title
 */
function azzu_get_terms_list_by_id( $args = array() ) {

        $default_args = array(
                'term_id' => array(),
                'taxonomy' => 'category',
                'separator' => ', ',
                'titles' => array()
        );

        $default_titles = array(
                'empty_ids' => __( 'All', 'azzu'.LANG_DN ),
                'no_result' => __('There is no categories', 'azzu'.LANG_DN)
        );

        $args = wp_parse_args( $args, $default_args );
        $args['titles'] = wp_parse_args( $args['titles'], $default_titles );

        // get categories names list or show all
        if ( empty( $args['term_id'] ) ) {
                $output = $args['titles']['empty_ids'];

        } else {

                $terms_names = array();
                foreach ( $args['term_id'] as $term_slug ) {
                        $term = get_term_by( 'term_id', $term_slug, $args['taxonomy'] );

                        if ( $term ) {
                                $terms_names[] = $term->name;
                        }

                }

                if ( $terms_names ) {
                        asort( $terms_names );
                        $output = join( $args['separator'], $terms_names );

                } else {
                        $output = $args['titles']['no_result'];

                }

        }

        return $output;
}

/**
 * Language flags for wpml.
 *
 */
function azzu_language_selector_flags($options = array()) {
        $languages = icl_get_languages('skip_missing=0&orderby=custom');

        if(!empty($languages) && is_array($languages)){
                $defaults = array( 
                    'hide_active'    => 0,
                    'wpml_flag'     => 1,
                    'wpml_name'     => 0,
                    'wpml_translated_name' => 0
                );
                $options = wp_parse_args( $options, $defaults );

                if(!$options['wpml_name'] && !$options['wpml_translated_name'])
                    $options['wpml_flag'] = 1;
                $text_attr='';
                if($options['wpml_translated_name'])
                    $text_attr = 'translated_name';
                else if($options['wpml_name'])
                    $text_attr = 'native_name';

                echo '<div class="'.azus()->get('azu-wpml').'"><ul class="'. ( !$options['wpml_flag'] ? 'azu-wpml-globe' : '' ) .'">';

                foreach($languages as $l){
                        if($l['active'] && $options['hide_active'])
                            continue;
                        echo '<li class="azu-wpml-'.$l['language_code'].'">';

                        if(!$l['active']) echo '<a href="'.$l['url'].'">';

                        if($options['wpml_flag'])
                            echo '<img class="azu-wpml-image" src="'.$l['country_flag_url'].'" alt="'.$l['language_code'].'" title="'.$l['native_name'].'" />';
                        if(!empty($text_attr))
                            echo '<span class="azu-wpml-text">'.$l[$text_attr].'</span>';

                        if(!$l['active']) echo '</a>';

                        echo '</li>';
                }

                echo '</ul></div>';

        }

}


// TODO: refactor this!
/**
 * Categorizer.
 */
function azzu_get_category_list( $args = array() ) {
        global $post;

        $defaults = array(
                'item_wrap'         => '<a href="%HREF%" %CLASS% data-filter="%CATEGORY_ID%">%TERM_NICENAME%</a>',
                'hash'              => '#!term=%TERM_ID%&amp;page=%PAGE%&amp;orderby=date&amp;order=DESC',
                'item_class'        => '',    
                'all_class'        	=> 'show-all',
                'other_class'		=> '',
                'class'             => 'filter',
                'current'           => 'all',
                'page'              => '1',
                'ajax'              => false,
                'all_btn'           => true,
                'echo'				=> false,
                'data'				=> array(),
                'before'			=> '<div class="filter-categories">',
                'after'				=> '</div>',
                'act_class'			=> 'act',
        );
        $args = wp_parse_args( $args, $defaults );
        $args = apply_filters( 'azzu_get_category_list-args', $args );

        $data = $args['data'];

        $args['hash'] = str_replace( array( '%PAGE%' ), array( $args['page'] ), $args['hash'] );
        $output = $all = '';

        if ( isset($data['terms']) &&
                ( ( count( $data['terms'] ) == 1 && !empty( $data['other_count'] ) ) ||
                count( $data['terms'] ) > 1 )
        ) {
                if ( !empty( $args['item_class'] ) ) {
                        $args['item_class'] = 'class="' . esc_attr($args['item_class']) . '"';
                }

                $replace_list = array( '%HREF%', '%CLASS%', '%TERM_DESC%', '%TERM_NICENAME%', '%TERM_SLUG%', '%TERM_ID%', '%COUNT%', '%CATEGORY_ID%' );

                foreach( $data['terms'] as $term ) {

                        $item_class = array();

                        if ( !empty( $args['item_class'] ) ) {
                                $item_class[] = $args['item_class'];
                        }

                        if ( in_array( $args['current'], array($term->term_id, $term->slug) ) ) {
                                $item_class[] = $args['act_class'];
                        }

                        if ( $item_class ) {
                                $item_class = sprintf( 'class="%s"', esc_attr( implode( ' ', $item_class ) ) );
                        } else {
                                $item_class = '';
                        }

                        $output .= str_replace(
                                $replace_list,
                                array(
                                        esc_url( str_replace( array( '%TERM_ID%' ), array( $term->term_id ), $args['hash'] ) ),
                                        $item_class,
                                        $term->category_description,
                                        $term->cat_name,
                                        esc_attr($term->slug),
                                        esc_attr($term->term_id),
                                        $term->count,
                                        esc_attr('.category-' . $term->term_id),
                                ), $args['item_wrap']
                        );
                }

                // all button
                if ( $args['all_btn'] ) {
                        $all_class = array();

                        if ( !empty( $args['all_class'] ) ) {
                                $all_class[] = $args['all_class'];
                        }

                        if ( 'all' == $args['current'] ) {
                                $all_class[] = $args['act_class'];
                        }

                        if ( $all_class ) {
                                $all_class = sprintf( 'class="%s"', esc_attr( implode( ' ', $all_class ) ) );
                        } else {
                                $all_class = '';
                        }

                        $all = str_replace(
                                $replace_list,
                                array(
                                        esc_url( str_replace( array( '%TERM_ID%' ), array( '' ), $args['hash'] ) ),
                                        $all_class,
                                        _x( 'All posts', 'category list', 'azzu'.LANG_DN ),
                                        _x( 'All', 'category list', 'azzu'.LANG_DN ),
                                        '',
                                        '',
                                        $data['all_count'],
                                        '*',
                                ), $args['item_wrap']
                        );
                }

                $output = $args['before'] . $all . $output . $args['after'];
                $output = str_replace( array( '%CLASS%' ), array( $args['class'] ), $output );
        }

        $output = apply_filters( 'azzu_get_category_list', $output, $args );

        if ( $args['echo'] ) {
                echo $output;
        } else {
                return $output;
        }
        return false;
}


/**
 * Post media slider.
 *
 * Based on swiper slider. Properly works only in the loop.
 *
 * @return string HTML.
 */
function azzu_get_post_media_slider( $attachments_data, $options = array() ) {

        if ( !$attachments_data ) {
                return '';
        }

        $default_options = array(
                'class'	=> array(),
                'proportion' => '',
                'img_width' => '', 
                'height' => '',
                'style'	=> ' style="width: 100%"'
        );
        $options = wp_parse_args( $options, $default_options );
        $options['swiper'] = array(
                            //'calculateHeight' => true, 
							'loop' => false,
                            'paginationClickable' => true,
                            //'paginationHide'    => false,
                            'pagination'	=> '.azu-swiper-container .carousel-indicator');
        $slideshow = $this->azzu_get_carousel_slider( $attachments_data, $options );

        return $slideshow;
}

/**
 * Generate Swiper.
 *
 * @param array $media_items Attachments id's array.
 * @return string HTML.
 */
function azzu_generate_carousel_slider( $html_inside='', $indicators='', $options = array() ) {
        if ( empty( $html_inside ) ) {
                return '';
}
        $default_options = array(
                'echo'		=> false,
                'title'         => '',
                'proportion'        => null,
                'padding'       => null,
                'min-width'     => null,
                'width'		=> null,
                'height'	=> null,
                'enable_arrow'  => true,
                'class'		=> array(),
                'swiper'	=> array('loop'=>false),
                'custom'	=> '',
                'style'		=> ''
        );
        $options = wp_parse_args( $options, $default_options );

        // common classes
        $options['class'][] = 'azu-swiper-container azu-swiper-ease';

        $container_class = implode(' ', $options['class']);

        $data_attributes = '';

        if ( !empty($options['custom']) ) {
                $data_attributes .= $options['custom'];
        }

        if ( !empty($options['padding']) ) {
                $data_attributes .= ' data-padding="' . absint($options['padding']) . '"';
        }
        if ( !empty($options['min-width']) ) {
                $data_attributes .= ' data-min-width="' . absint($options['min-width']) . '"';
        }
        if ( !empty($options['proportion']) ) {
                $data_attributes .= ' data-ratio="' . esc_attr($options['proportion']) . '"';
        }
        if ( !empty($options['width']) ) {
                $data_attributes .= ' data-width="' . absint($options['width']) . '"';
        }

        if ( !empty($options['height']) ) {
                $data_attributes .= ' data-height="' . absint($options['height']) . '"';
        }

        if ( !empty($options['swiper']) ) {
                $data_attributes .= ' data-option="' . esc_attr(json_encode($options['swiper'])) . '"';
        }
        $html = '';
        if(!empty($options['title']) )
            $html .= '<'.AZU_TITLE_H.'>'.$options['title'].'</'.AZU_TITLE_H.'>';
        $html .= "\n" . '<div class="' . esc_attr($container_class) . '"' . $data_attributes . $options['style'] . '>';

        if(!empty($html_inside))
            $html .= '<div class="swiper-wrapper" >'.$html_inside.'</div>';
        if(!empty($indicators))
            $html .= '<div class="carousel-indicator">'.$indicators.'</div>';
        $arrow_class_left='';
        $arrow_class_right='';
        if($options['enable_arrow'])
        {
            $arrow_class_left='azu-icon-slider-left';
            $arrow_class_right='azu-icon-slider-right'; 
        }
        $html .= '<a class="carousel-arrow-left" href="#"><i class="'.$arrow_class_left.'"></i></a>
                  <a class="carousel-arrow-right" href="#"><i class="'.$arrow_class_right.'"></i></a>
                  </div>';

        if ( $options['echo'] ) {
                echo $html;
        }

        return $html;
}

/**
 * Swiper media slider.
 *
 * @param array $media_items Attachments id's array.
 * @return string HTML.
 */
function azzu_get_carousel_slider( $attachments_data, $options = array() ) {
        if ( empty( $attachments_data ) ) {
                return '';
        }

        $default_options = array(
                'show_info'	=> array( 'title', 'link', 'description' ),
                'attr'          => array(),
                'custom'	=> ''
        );
        $options = wp_parse_args( $options, $default_options );

        $indicators = '';
        $html_inside='';
        $i = 0;
        $initialSlide = 0;
        if( isset($options['swiper']['initialSlide']))
            $initialSlide = absint($options['swiper']['initialSlide']);
        foreach ( $attachments_data as $data ) {

                if ( empty($data['full']) ) { continue; }

                $is_video = !empty( $data['video_url'] );

                $active ='';
                if(isset($options['swiper']['pagination']))
                    $indicators .='<span class="swiper-pagination-switch"></span>';

                if( $initialSlide==$i)
                        $active ='swiper-slide-active swiper-slide-visible';
                $i++;

                $html_inside .= "\n\t" . '<div class="swiper-slide'.( !empty($active) ? ' '.$active : '' ) . ( ($is_video) ? ' ' . azus()->get('azu-rollover-video') : '' ) . '">';

                $image_args = array(
                        'img_meta' 	=> array( $data['full'], $data['width'], $data['height'] ),
                        'options'	=> array( 'w' => $options['img_width'], 'z' => 1 ),
                        'img_id'	=> isset($data['ID']) ? $data['ID'] : '',
                        'alt'		=> isset($data['alt']) ? $data['alt'] : '',
                        'title'		=> isset($data['title']) ? $data['title'] : '',
                        'img_caption'	=> isset($data['caption']) ? $data['caption'] : '',
                        'img_class'     => 'bcImg no-preload',
                        'custom'	=> '',
                        'class'		=> isset($data['class']) ? $data['class'] : '',
                        'echo'		=> false,
                        'wrap'		=> isset($data['wrap']) ? $data['wrap'] : '<img %IMG_CLASS% %SRC% %SIZE% %ALT% %CUSTOM% />',
                );

                if ( $is_video ) {
                        $video_url = esc_url(remove_query_arg( array('iframe', 'width', 'height'), $data['video_url'] ));
                        $image_args['custom'] = 'data-srVideo="' . $video_url . '"';
                }
                $thumb_args = azut()->azzu_thumbnail_proportions( $image_args, $options );
                $image = azuf()->azu_get_thumb_img( $thumb_args );

                $html_inside .= "\n\t\t" . $image;

                $caption_html = '';

                if ( !empty($data['title']) && in_array('title', $options['show_info']) ) {
                        $caption_html .= "\n\t\t\t\t" . '<'.AZU_REL_POST_TITLE_H.'>' . esc_html($data['title']) . '</'.AZU_REL_POST_TITLE_H.'>';
                }

                if ( !empty($data['description']) && in_array('description', $options['show_info']) ) {
                        $caption_html .= "\n\t\t\t\t" . $data['description'];
                }

                if ( $caption_html ) {
                        $html_inside .= "\n\t\t" . '<div class="carousel-title">' . "\n\t\t" . $caption_html . "\n\t\t" . '</div>';
                }

                $html_inside .= '</div>';

        }

        $html = $this->azzu_generate_carousel_slider( $html_inside, $indicators, $options );

        return $html;
}
        
/**
 * Gallery helper.
 *
 * @param array $attachments_data Attachments data array.
 * @return string HTML.
 */
function azzu_get_gallery_image_list( $attachments_data, $options = array() ) {
        if ( empty( $attachments_data ) ) {
                return '';
        }

        $default_options = array(
                'echo'			=> false,
                'class'			=> array(),
                'links_rel'		=> '',
                'style'			=> '',
                'first_big'		=> true,
        );
        $options = wp_parse_args( $options, $default_options );

        $options['class'] = (array) $options['class']; 
        $options['class'][] = 'azu-gallery-container';
        if(count($attachments_data) > 1 )
            $options['class'][] = 'gallery-multi-col';
        
        $container_class = implode( ' ', $options['class'] );

        $html = '<div class="' . esc_attr( $container_class ) . '"' . $options['style'] . '>';

        // clear attachments_data
        foreach ( $attachments_data as $index=>$data ) {
                if ( empty($data['full']) ) unset($attachments_data[ $index ]);
        }
        unset($data);

        if ( empty($attachments_data) ) {
                return '';
        }

        $image_custom = $options['links_rel'];

        $image_args = array(
                'img_class' => '',
                'class'		=> azus()->get('azu-mfp-item','azu-rollover no-preload'),
                'echo'		=> false,
        );

        $video_args = array_merge( $image_args, array(
                'class'		=> 'azu-mfp-item mfp-iframe video-icon no-preload',
        ) );
        $image_w = azuf()->azu_calculate_width_size(1);
        $image_h = round($image_w * 2 / 3);
        
        foreach ( $attachments_data as $index => $data ) {
                if ( $options['first_big'] && $index == 0 ) { 
                    $additional_class = ' big-img';
                    if(count($attachments_data) > 1)
                        $image_w = $image_h = azuf()->azu_calculate_width_size(2);
                }
                elseif($index < 2) {
                    $additional_class = '';
                    $image_w = $image_h = azuf()->azu_calculate_width_size(4);
                    $image_h = round($image_w * 3 / 4);
                    if(count($attachments_data) == 2 && $index == 1){
                        $image_h = $image_h * 2;
                    }
                }
                elseif($index == 3) {
                    $additional_class = ' azu-gallery-hidden';
                    $image_w = get_option( 'thumbnail_size_w' );
                    $image_h = get_option( 'thumbnail_size_h' );
                }
                    
                $media_args = array(
                        'img_meta' 	=> array( $data['full'], $data['width'], $data['height'] ),
                        'img_id'	=> empty( $data['ID'] ) ? $data['ID'] : 0, 
                        'options'	=> array( 'w' => $image_w, 'h' => $image_h, 'z' => true ),
                        'alt'		=> $data['title'],
                        'title'		=> $data['title'],
                        'echo'		=> false,
                        'custom'	=> $image_custom . ' data-azu-img-description="' . esc_attr($data['description']) . '"'
                );
                
                
                if ( empty($data['video_url']) ) {
                        $media_args['class'] = $image_args['class'] . $additional_class;
                        $image = azuf()->azu_get_thumb_img( array_merge( $image_args, $media_args ) );
                } else {
                        $media_args['class'] = $video_args['class'] . $additional_class;
                        $media_args['href'] = $data['video_url'];
                        $media_args['wrap'] = '<a %HREF% %TITLE% %CLASS% %CUSTOM%><img %SRC% %IMG_CLASS% %ALT% %IMG_TITLE% %SIZE% /></a>';

                        $image = azuf()->azu_get_thumb_img( array_merge( $video_args, $media_args ) );

                        if ( $image ) {
                                $image = '<div class="' .azus()->get('azu-rollover-video') . '">' . $image . '</div>';
                        }
                }

                $html .= $image;
        }
        if(count($attachments_data) > 3)
        {
            $html .= '<div class="azu-gallery-more">+'. (count($attachments_data)-3).'</div>';
        }

        $html .= '</div>';

        return $html;
}

        
}