<?php
/**
 * @author   	Damii
 * @copyright	Copyright (c) 2014
 * @package  	Azu
 * @version  	0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists('azu_functions') ) :
class azu_functions extends theme_functions {
        
    protected function add_actions() { 
        parent::add_actions();
        add_filter( 'azu_widget_areas', array( &$this,'azu_additional_widget_areas'), 16 );
        add_filter( 'azu_register_nav_menus', array( &$this,'azu_additional_nav_menus'), 16 );
        add_filter( 'azzu_options_list_general', array( &$this,'additional_options'), 16 );
        
        add_filter( 'azzu_body_class', array( &$this,'additional_class'), 16 );
        
        add_filter( 'azzu_page_options_metaboxes', array( &$this,'azu_additional_page_options'), 16 );
    }
    
    public function azu_additional_page_options($op){
        // define global metaboxes array
        global $AZU_META_BOXES;
        if(is_array($AZU_META_BOXES) && array_key_exists('azu_page_box-header_options',$AZU_META_BOXES) && is_array($op)){
            $addo = $AZU_META_BOXES['azu_page_box-header_options'];
            $addo['fields'][] = //  Remove content padding
		array(
			'name'    	=> _x('Passepartout border:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "_azu_header_border_padding",
			'type'    		=> 'select',
                        'std'			=> '-1',
                        'options'	=> array(
                                -1	=>  _x('use global option', 'backend metabox', 'azzu'.LANG_DN),
                                0	=>  _x('0%', 'backend metabox', 'azzu'.LANG_DN),
                                1	=>  _x('1%', 'backend metabox', 'azzu'.LANG_DN),
                                2	=>  _x('2%', 'backend metabox', 'azzu'.LANG_DN),
                                3	=>  _x('3%', 'backend metabox', 'azzu'.LANG_DN),
                                4	=>  _x('4%', 'backend metabox', 'azzu'.LANG_DN),
                                5	=>  _x('5%', 'backend metabox', 'azzu'.LANG_DN),
                        ),
                        'after'	=> '<p><small>Passepartout border size.</small></p>',
                        'top_divider'	=> true
                );
            $op[] = $addo;
        }
        return $op;
    }
 
    
    public function additional_class($class) {
        if(is_array($class)){
            if(azum()->get('border_padding') >= 0 )
                $class[] ='azu-body-padding-'.azum()->get('border_padding');
        }
        return $class;
    }
    
    public function additional_options($additional_option) {
        if(is_array($additional_option))
        {
            $additional_option[] = $options[] = array(	"name" => _x('Theme additional', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );
            //slider
            $additional_option[] = array(
			"name"		=> _x( 'General passepartout border', 'theme-options', 'azzu'.LANG_DN ),
			"id"		=> 'general_body_padding',
			"wrap"		=> array('', '%'),
			"std"		=> 0,
                        "transport"     => "refresh",
			"type"          => "slider",
			"options"       => array( 'min' => 0, 'max' => 5, 'step' => 1 ),
			"sanitize"      => 'slider'
		);
                        
            $additional_option[] = array(	"type" => "block_end");
        }
        return $additional_option;
    }

    function azu_additional_widget_areas($areas){
        if(is_array($areas)){
            $areas[] = 'Menu-left';
        }
        return $areas;
    }
    
    function azu_additional_nav_menus($menus){
        if(is_array($menus)){
            $menus['center-left'] = __( 'Left menu of Center logo', 'azzu'.LANG_DN );
            $menus['center-right'] = __( 'Right menu of Center logo', 'azzu'.LANG_DN );
        }
        return $menus;
    }
    
    // Icon Picker
    public static function azuIconPicker() {
        return array("icon-rss","icon-pintrest","icon-foursquare","icon-skype","icon-google","icon-facebook","icon-twitter","icon-youtube","icon-vimeo","icon-vine","icon-500px","icon-instagram","icon-tumblr","icon-weibo","icon-xing","icon-linkedin","icon-flickr","icon-behance","icon-stumbleupon","icon-dribbble","icon-mail","icon-vkontakte","icon-lastfm","icon-forrst","icon-delicious","icon-github","icon-whatsapp","icon-deviantart","icon-yelp","icon-snapchat","icon-arrow_carrot_up_alt","icon-arrow_carrot-2down","icon-arrow_carrot-2down_alt2","icon-arrow_carrot-2dwnn_alt","icon-arrow_carrot-2left","icon-arrow_carrot-2left_alt","icon-arrow_carrot-2left_alt2","icon-arrow_carrot-2right","icon-arrow_carrot-2right_alt","icon-arrow_carrot-2right_alt2","icon-arrow_carrot-2up","icon-arrow_carrot-2up_alt","icon-arrow_carrot-2up_alt2","icon-arrow_carrot-down","icon-arrow_carrot-down_alt","icon-arrow_carrot-down_alt2","icon-arrow_carrot-left","icon-arrow_carrot-left_alt","icon-arrow_carrot-left_alt2","icon-arrow_carrot-right","icon-arrow_carrot-right_alt","icon-arrow_carrot-right_alt2","icon-arrow_carrot-up","icon-arrow_carrot-up_alt2","icon-arrow_condense","icon-arrow_condense_alt","icon-arrow_down","icon-arrow_down_alt","icon-arrow_expand","icon-arrow_expand_alt","icon-arrow_expand_alt2","icon-arrow_expand_alt3","icon-arrow_left","icon-arrow_left_alt","icon-arrow_left-down","icon-arrow_left-down_alt","icon-arrow_left-right","icon-arrow_left-right_alt","icon-arrow_left-up","icon-arrow_left-up_alt","icon-arrow_move","icon-arrow_right","icon-arrow_right_alt","icon-arrow_right-down","icon-arrow_right-down_alt","icon-arrow_right-up","icon-arrow_right-up_alt","icon-arrow_triangle-down","icon-arrow_triangle-down_alt","icon-arrow_triangle-down_alt2","icon-arrow_triangle-left","icon-arrow_triangle-left_alt","icon-arrow_triangle-left_alt2","icon-arrow_triangle-right","icon-arrow_triangle-right_alt","icon-arrow_triangle-right_alt2","icon-arrow_triangle-up","icon-arrow_triangle-up_alt","icon-arrow_triangle-up_alt2","icon-arrow_up","icon-arrow_up_alt","icon-arrow_up-down_alt","icon-arrow-up-down","icon-adjust-horiz","icon-adjust-vert","icon-archive","icon-archive_alt","icon-bag","icon-bag_alt","icon-balance","icon-blocked","icon-book","icon-book_alt","icon-box-checked","icon-box-empty","icon-box-selected","icon-briefcase","icon-briefcase_alt","icon-building","icon-building_alt","icon-calculator_alt","icon-calendar","icon-calulator","icon-camera","icon-camera_alt","icon-cart","icon-cart_alt","icon-chat","icon-chat_alt","icon-check","icon-check_alt","icon-check_alt2","icon-circle-empty","icon-circle-slelected","icon-clipboard","icon-clock","icon-clock_alt","icon-close","icon-close_alt","icon-close_alt2","icon-cloud","icon-cloud_alt","icon-cloud-download","icon-cloud-download_alt","icon-cloud-upload","icon-cloud-upload_alt","icon-cog","icon-cogs","icon-comment","icon-comment_alt","icon-compass","icon-compass_alt","icon-cone","icon-cone_alt","icon-contacts","icon-contacts_alt","icon-creditcard","icon-currency","icon-currency_alt","icon-cursor","icon-cursor_alt","icon-datareport","icon-datareport_alt","icon-desktop","icon-dislike","icon-dislike_alt","icon-document","icon-document_alt","icon-documents","icon-documents_alt","icon-download","icon-drawer","icon-drawer_alt","icon-drive","icon-drive_alt","icon-easel","icon-easel_alt","icon-error-circle","icon-error-circle_alt","icon-error-oct","icon-error-oct_alt","icon-error-triangle","icon-error-triangle_alt","icon-film","icon-floppy","icon-floppy_alt","icon-flowchart","icon-flowchart_alt","icon-folder","icon-folder_download","icon-folder_upload","icon-folder-add","icon-folder-add_alt","icon-folder-alt","icon-folder-open","icon-folder-open_alt","icon-genius","icon-gift","icon-gift_alt","icon-globe","icon-globe_alt","icon-globe-2","icon-grid-2x2","icon-grid-3x3","icon-group","icon-headphones","icon-heart","icon-heart_alt","icon-hourglass","icon-house","icon-house_alt","icon-id","icon-id_alt","icon-id-2","icon-id-2_alt","icon-image","icon-images","icon-info","icon-info_alt","icon-key","icon-key_alt","icon-laptop","icon-lifesaver","icon-lightbulb","icon-lightbulb_alt","icon-like","icon-like_alt","icon-link","icon-link_alt","icon-loading","icon-lock","icon-lock_alt","icon-lock-open","icon-lock-open_alt","icon-mail-1","icon-mail_alt","icon-map","icon-map_alt","icon-menu","icon-menu-circle_alt","icon-menu-circle_alt2","icon-menu-square_alt","icon-menu-square_alt2","icon-mic","icon-mic_alt","icon-minus_alt","icon-minus_alt2","icon-minus-06","icon-minus-box","icon-mobile","icon-mug","icon-mug_alt","icon-music","icon-ol","icon-paperclip","icon-pause","icon-pause_alt","icon-pause_alt2","icon-pencil","icon-pencil_alt","icon-pencil-edit","icon-pencil-edit_alt","icon-pens","icon-pens_alt","icon-percent","icon-percent_alt","icon-phone","icon-piechart","icon-pin","icon-pin_alt","icon-plus","icon-plus_alt","icon-plus_alt2","icon-plus-box","icon-printer","icon-printer-alt","icon-profile","icon-pushpin","icon-pushpin_alt","icon-puzzle","icon-puzzle_alt","icon-question","icon-question_alt","icon-question_alt2","icon-quotations","icon-quotations_alt","icon-quotations_alt2","icon-refresh","icon-ribbon","icon-ribbon_alt","icon-rook","icon-search","icon-search_alt","icon-search2","icon-shield","icon-shield_alt","icon-star","icon-star_alt","icon-star-half","icon-star-half_alt","icon-stop","icon-stop_alt","icon-stop_alt2","icon-table","icon-tablet","icon-tag","icon-tag_alt","icon-tags","icon-tags_alt","icon-target","icon-tool","icon-toolbox","icon-toolbox_alt","icon-tools","icon-trash","icon-trash_alt","icon-ul","icon-upload","icon-vol-mute","icon-vol-mute_alt","icon-volume-high","icon-volume-high_alt","icon-volume-low","icon-volume-low_alt","icon-wallet","icon-wallet_alt","icon-zoom-in","icon-zoom-in_alt","icon-zoom-out","icon-zoom-out_alt","icon-blogger","icon-blogger_circle","icon-blogger_square","icon-delicious-1","icon-delicious_circle","icon-delicious_square","icon-deviantart-1","icon-deviantart_circle","icon-deviantart_square","icon-dribbble-1","icon-dribbble_circle","icon-dribbble_square","icon-facebook-1","icon-facebook_circle","icon-facebook_square","icon-flickr-1","icon-flickr_circle","icon-flickr_square","icon-googledrive","icon-googledrive_alt2","icon-googledrive_square","icon-googleplus","icon-googleplus_circle","icon-googleplus_square","icon-instagram-1","icon-instagram_circle","icon-instagram_square","icon-linkedin-1","icon-linkedin_circle","icon-linkedin_square","icon-myspace","icon-myspace_circle","icon-myspace_square","icon-picassa","icon-picassa_circle","icon-picassa_square","icon-pinterest","icon-pinterest_circle","icon-pinterest_square","icon-rss-1","icon-rss_circle","icon-rss_square","icon-share","icon-share_circle","icon-share_square","icon-skype-1","icon-skype_circle","icon-skype_square","icon-spotify","icon-spotify_circle","icon-spotify_square","icon-stumbleupon_circle","icon-stumbleupon_square","icon-tumbleupon","icon-tumblr-1","icon-tumblr_circle","icon-tumblr_square","icon-twitter-1","icon-twitter_circle","icon-twitter_square","icon-vimeo-1","icon-vimeo_circle","icon-vimeo_square","icon-wordpress","icon-wordpress_circle","icon-wordpress_square","icon-youtube-1","icon-youtube_circle","icon-youtube_square","icon-adjustments","icon-alarmclock","icon-anchor-1","icon-aperture","icon-attachments","icon-bargraph","icon-basket-1","icon-beaker-1","icon-bike","icon-book-open","icon-briefcase-1","icon-browser","icon-calendar-1","icon-camera-1","icon-caution","icon-chat-1","icon-circle-compass","icon-clipboard-1","icon-clock-1","icon-cloud-1","icon-compass-1","icon-desktop-1","icon-dial","icon-document-1","icon-documents-1","icon-download-1","icon-dribbble-2","icon-edit-1","icon-envelope","icon-expand-1","icon-facebook-2","icon-flag-1","icon-focus","icon-gears","icon-genius-1","icon-gift-1","icon-global","icon-globe-1","icon-googleplus-1","icon-grid","icon-happy","icon-hazardous","icon-heart-1","icon-hotairballoon","icon-hourglass-1","icon-key-1","icon-laptop-1","icon-layers","icon-lifesaver-1","icon-lightbulb-1","icon-linegraph","icon-linkedin-2","icon-lock-1","icon-magnifying-glass","icon-map-1","icon-map-pin","icon-megaphone-1","icon-mic-1","icon-mobile-1","icon-newspaper-1","icon-notebook","icon-paintbrush","icon-paperclip-1","icon-pencil-1","icon-phone-1","icon-picture-1","icon-pictures","icon-piechart-1","icon-presentation","icon-pricetags","icon-printer-1","icon-profile-female","icon-profile-male","icon-puzzle-1","icon-quote","icon-recycle-1","icon-refresh-1","icon-ribbon-1","icon-rss-2","icon-sad","icon-scissors-1","icon-scope","icon-search-1","icon-shield-1","icon-speedometer","icon-strategy","icon-streetsign","icon-tablet-1","icon-telescope","icon-toolbox-1","icon-tools-1","icon-tools-2","icon-traget","icon-trophy","icon-tumblr-2","icon-twitter-2","icon-upload-1","icon-video-1","icon-wallet-1","icon-wine","icon-website","icon-eye","icon-pencil-2","icon-play-circled2","icon-volume-up","icon-volume-off","icon-volume-down","icon-play-circled2-1","icon-pause-1","icon-swarm","icon-edit","icon-search-2","icon-resize-full","icon-share-1","icon-export","icon-videocam","icon-video","icon-mail-2","icon-up-big","icon-down-big","icon-left-big","icon-right-big","icon-down-open","icon-left-open","icon-right-open","icon-up-open","icon-up-open-big","icon-right-open-big","icon-left-open-big","icon-down-open-big","icon-videocam-1","icon-link-1","icon-search-3","icon-menu-1","icon-basket");
    }
    
    /**
     * override a Theme icons for VC
     *
     * @param $icons - taken from filter - vc_map param field settings['source'] provided icons (default empty array).
     * If array categorized it will auto-enable category dropdown
     *
     * @since 1.0
     * @return array - of icons for iconpicker, can be categorized, or not.
     */
    public static function azu_iconpicker_for_vc( $icons ) {
            $theme_icons = array();
            $azu_icons = self::azuIconPicker();
            foreach($azu_icons as $t_icon){
                $theme_icons[] = array( $t_icon => $t_icon);
            }
            return $theme_icons;
    }

 
    
/**
 * Returns color defaults array.
*
* @return array.
* @since azzu 1.0
*/
public function azzu_themeoptions_get_color_defaults() {
	$listbox_color = array(
		//'' => array('label'=> '','desc' => ''),
                'content-bg-color' => array('label'=> 'background of card','desc' => 'inside background of content & sidebar on card mode', 'std'=>'#ffffff'),
		'content-dividers-color' => array('label'=> 'dividers color of content','desc' => 'separator line color of content & sidebar on content divider mode', 'std'=>'#f0f2f4'),
		'page-bg-color' => array('label'=> 'background of content','desc' => 'background of main content', 'std'=>'#ffffff'),
		'base-title-color' => array('label'=> 'text color of title','desc' => 'h1-h6 headings color', 'std'=>'#333'),
		'base-bg-color' => array('label'=> 'background of items','desc' => 'background of form input, button, pagination, breadcrumb, code .etc', 'std'=>'#ffffff'),
                'base-text-color' => array('label'=> 'text color of main body','desc' => 'text color of content & sidebar', 'std'=>'#777'),
                'base-brand-color' => array('label'=> 'Accent color','desc' => 'primary color of site', 'std'=>'#0099ff'),
                'active-item-color' => array('label'=> 'active item color','desc' => 'active elements color: main menu, pagination, breadcrumb etc.', 'std'=>'#242424'),
                'general-bg-color' => array('label'=> 'boxed background color','desc' => 'main background, it used boxed layout mode only', 'std'=>'#000'),
		'general-hover-bg-color' => array('label'=> 'background of hover','desc' => 'general hover background color', 'std'=>'#ffffff'),
                'general-link-hover-color' => array('label'=> 'text color of link hover','desc' => '<a> tag\'s hover color', 'std'=>'#333'),
		'general-link-color' => array('label'=> 'text color of link','desc' => '<a> tag\'s color', 'std'=>'#999'),
		'general-highlight-color' => array('label'=> 'selected text color','desc' => 'selected text color', 'std'=>'#3399ff'),
		'general-border-color' => array('label'=> 'border color of items','desc' => 'border color of pagination, button, social icon and form input etc.', 'std'=>'#e8e8e8'),
		'header-bg-color' => array('label'=> 'background of header','desc' => 'background of header section', 'std'=>'#ffffff'),
		'navbar-dividers-color' => array('label'=> 'dividers color of menu','desc' => 'divider & border color of main menu', 'std'=>'#f0f2f4'),
		'navbar-text-color' => array('label'=> 'text color of menu','desc' => 'text color of main menu item', 'std'=>'#666'),
		'navbar-bg-color' => array('label'=> 'background of menu','desc' => 'main menu background color', 'std'=>'#ffffff'),
                'header-title-bg-color' => array('label'=> 'background of breadcrumb','desc' => 'background of page title & breadcrumb', 'std'=>'#f4f7f9'),
		'navbar-submenu-dividers-color' => array('label'=> 'dividers color of submenu','desc' => 'dividers color of submenu', 'std'=>'#2f3133'),
		'navbar-submenu-text-color' => array('label'=> 'text color of submenu','desc' => 'text color of submenu item', 'std'=>'#666'),
		'navbar-submenu-bg-color' => array('label'=> 'background of submenu','desc' => 'background of submenu', 'std'=>'#ffffff'),
		'topbar-bg-color' => array('label'=> 'background of top bar','desc' => 'background of top bar', 'std'=>'#ffffff'),
		'topbar-text-color' => array('label'=> 'text color of top bar','desc' => 'text color of top bar', 'std'=>'#888'),
		'footer-bg-color' => array('label'=> 'background of footer','desc' => 'background of footer', 'std'=>'#ffffff'),
		'footer-dividers-color' => array('label'=> 'dividers color of footer','desc' => 'divider & border color of footer & bottom bar', 'std'=>'#2f3133'),
		'footer-headers-color' => array('label'=> 'text color of title in footer','desc' => 'headings text color of footer', 'std'=>'#333'),
		'footer-text-color' => array('label'=> 'text color of footer','desc' => 'text color of footer', 'std'=>'#888'),
		'bottombar-bg-color' => array('label'=> 'background of bottom bar','desc' => 'background color of bottom bar', 'std'=>'#ffffff'),
		'bottombar-text-color' => array('label'=> 'text color of bottom bar','desc' => 'text color of bottom bar', 'std'=>'#888'),
                'second-bg-color' => array('label'=> 'background of block','desc' => 'background of aside, quote, comment reply .etc', 'std'=>'#f0f2f4'),
                
                //theme special color
                'navbar-submenu-title-color' => array('label'=> 'title color of submenu','desc' => 'title text color of submenu', 'std'=>'#ffffff'),
        );
        return $listbox_color;
}

/**
 * Returns color group defaults array.
*
* @return array.
* @since azzu 1.0
*/
public function azzu_themeoptions_get_color_group() {
    $listbox_color_std = array(
		1=> array( 
                        'color' => '#fff',
                        'group' => array(
                            'content-dividers-color' => array(), 
                            'navbar-dividers-color' => array(), 
                            'navbar-submenu-dividers-color' => array(), 
                            'header-title-bg-color' => array(), 
                            'general-border-color' => array(),
                            'footer-dividers-color' => array(),   
                            'second-bg-color' => array()
                        )),
		2 => array('color' => '#0099ff',
                        'group' => array( 
                            'base-brand-color' => array(), 
                            'general-highlight-color' => array(),  
                            'general-link-hover-color' => array(),
                            'active-item-color' => array(),
                         )),
		3 => array('color' => '#242628',
                        'group' => array(
                            'base-title-color' => array(),
                            'general-link-color' => array(),
                            'footer-bg-color' => array(), 
                            'bottombar-bg-color' => array(), 
                            'navbar-submenu-bg-color' => array(), 
                            )),
		4 => array('color' => '#fff',
                        'group' => array(
                            'page-bg-color' => array(),
                            'base-bg-color' => array(),
                            'general-bg-color' => array(),
                            'header-bg-color' => array(),
                            'navbar-bg-color' => array( 'option' => 97),
                            'topbar-bg-color' => array(),
                            'general-hover-bg-color' => array(),
                            'footer-headers-color' => array(), 
                            'navbar-submenu-title-color' => array(), 
                            'content-bg-color' => array() )),

		5 => array('color' => '#81868a',
                        'group' => array(           
                            'topbar-text-color' => array(), 
                            'bottombar-text-color' => array(),
                            'footer-text-color' => array(), 
                            'base-text-color' => array(),
                            'navbar-text-color' => array(), 
                            'navbar-submenu-text-color'  => array() )),
		6 => array('color' => '#fff',
                        'group' => array( )),
                7 => array('color' => '#000',
                        'group' => array()),
                8 => array('color' => '#fff',
                        'group' => array()),
                9 => array('color' => '#fff',
                        'group' => array()),
                10 => array('color' => '#fff',
                        'group' => array())
	);
    return $listbox_color_std;
}


/**
 * Returns font defaults array.
*
* @return array.
* @since azzu 1.0
*/
public function azzu_themeoptions_get_typography_defaults() {
        $listbox_font = array(
		//'' => array('label'=> '','desc' => ''),
		'base-font-family' => array('label'=> 'base font','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
		'h1-font' => array('label'=> 'H1','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
		'h2-font' => array('label'=> 'H2','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
		'h3-font' => array('label'=> 'H3','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
		'h4-font' => array('label'=> 'H4','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
		'h5-font' => array('label'=> 'H5','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
		'h6-font' => array('label'=> 'H6','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
		'buttons-font-family' => array('label'=> 'buttons font','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
		'menu-font-family' => array('label'=> 'menu font','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
                'input-font-family' => array('label'=> 'form inputs','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
                'mini-menu-font-family' => array('label'=> 'submenu font','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
                'footer-font-family' => array('label'=> 'footer font','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
                'sidebar-font-family' => array('label'=> 'sidebar font','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
                'bottombar-font-family' => array('label'=> 'bottombar font','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
                'header-font-family' => array('label'=> 'header font','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
		'topbar-font-family' => array('label'=> 'topbar font','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
                'breadcrumb-font-family' => array('label'=> 'page title & breadcrumb font','desc' => '', 'std'=>AZZU_THEME_DEFAULT_FONT),
		
                //theme special font
                'quote-font-family' => array('label'=> 'subtitle font','desc' => '', 'std' => AZZU_THEME_DEFAULT_FONT)
                
                );
        return $listbox_font;
}


/**
 * Returns font group defaults array.
*
* @return array.
* @since azzu 1.0
*/
public function azzu_themeoptions_get_typography_group() {
        $listbox_font_std = array(
                        1 => array('font' => AZZU_THEME_DEFAULT_FONT , 'group' => array('base-font-family' => array(),'footer-font-family' => array('Size' => 'small') )),
                        2 => array('font' => AZZU_THEME_DEFAULT_FONT, 'group' => array(
                                'h1-font' => array('ls' => -0.9),
                                'h2-font' => array('ls' => -0.7),
                                'h3-font' => array('ls' => -0.3),
                                'h4-font' => array('ls' => -0.2),
                                'h5-font' => array('ls' => -0.2),
                                'h6-font' => array('Weight' => '700'))),
                        3 => array('font' => AZZU_THEME_DEFAULT_FONT, 'group' => array(
                                
                                'sidebar-font-family' => array('Size' => 'small'),
                                'bottombar-font-family' => array('Size' => 'small'),
                                'header-font-family' => array(),
                                'topbar-font-family' => array('Size' => 'small'),
                                'breadcrumb-font-family' => array('uc'=>'uppercase'),
                                'buttons-font-family' => array('Weight' => '700','Size' => 'xsmall'),
                                'input-font-family' => array(),
                                'menu-font-family' => array('Size' => 'large') , 
                                'mini-menu-font-family' => array('Size' => 'small'))),
                        4 => array('font' => 'Bitter', 'group' => array('quote-font-family'=>array('Weight' => '400italic'))),
        );
        return $listbox_font_std;
}

/**
 * Returns default font sizes array.
*
* @return array.
* @since azzu 1.0
*/
public function azzu_themeoptions_get_font_size_defaults($full = true) {
    $font_sizes = array(
		'xsmall'   => array(
                        'std'   => 13,
                        'lh'   => 20,
                        'desc'  => _x( 'Extra small font size', 'theme-options', 'azzu'.LANG_DN )
			),
		'small'   => array(
                        'std'   => 15,
                        'lh'   => 25,
                        'desc'  => _x( 'Small font size', 'theme-options', 'azzu'.LANG_DN )
			),
		'normal'   => array(
			'std'   => 16,
                        'lh'   => 26,
			'desc'  => _x( 'Normal font size', 'theme-options', 'azzu'.LANG_DN )
			),
		'large'   => array(
			'std'   => 17,
                        'lh'   => 28,
			'desc'  => _x( 'Large font size', 'theme-options', 'azzu'.LANG_DN )
			)
	);
    
    if(!$full){
        foreach ($font_sizes as $key => $value) {
            $font_sizes[$key] = $value['desc'];
        }
    }
    return $font_sizes;
}
    
/**
 * Returns headers defaults array.
*
* @return array.
* @since azzu 1.0
*/
public function azzu_themeoptions_get_headers_defaults() {

	$headers = array(
			'h1'	=> array(
					'desc'	=> _x('H1', 'theme-options', 'azzu'.LANG_DN),
					'fs'	=> 36,	// font size
					'lh'	=> 42,	// line height
					'uc'	=> 1,	// upper case
			),
			'h2'	=> array(
					'desc'	=> _x('H2', 'theme-options', 'azzu'.LANG_DN),
					'fs'	=> 26,
					'lh'	=> 32,
					'uc'	=> 1
			),
			'h3'	=> array(
					'desc'	=> _x('H3', 'theme-options', 'azzu'.LANG_DN),
					'fs'	=> 22,
					'lh'	=> 28,
					'uc'	=> 1
			),
			'h4'	=> array(
					'desc'	=> _x('H4', 'theme-options', 'azzu'.LANG_DN),
					'fs'	=> 18,
					'lh'	=> 24,
					'uc'	=> 1
			),
			'h5'	=> array(
					'desc'	=> _x('H5', 'theme-options', 'azzu'.LANG_DN),
					'fs'	=> 16,
					'lh'	=> 20,
					'uc'	=> 0
			),
			'h6'	=> array(
					'desc'	=> _x('H6', 'theme-options', 'azzu'.LANG_DN),
					'fs'	=> 13,
					'lh'	=> 16,
					'uc'	=> 1
			)
	);

	return $headers;
}

 // azzu_themeoptions_get_headers_defaults
    
}
endif; // azu tag