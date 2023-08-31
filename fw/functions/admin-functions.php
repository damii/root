<?php
/**
 * Admin functions.
 *
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function azzu_register_required_plugins() {

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */

	$plugins = array(

		// Revolution slider
		array(
			'name' => 'Revolution Slider',
			'slug' => 'revslider',
			'source' => 'http://wp-theme.us/demo_content/plugins/revslider.zip',
			'required' => false,
			'version' => '5.2.6',
			'force_activation' => false,
			'force_deactivation' => false
		),

		// LayerSlider config
		array(
			'name' => 'LayerSlider WP',
			'slug' => 'LayerSlider',
			'source' => 'http://wp-theme.us/demo_content/plugins/layerslider.zip',
			'required' => false,
			'version' => '5.6.9',
			'force_activation' => false,
			'force_deactivation' => true
		),
            
            	// Azu Custom PostType
		array(
			'name' => 'Azu PostType (portfolio,team etc.)',
			'slug' => 'azu-posttype',
			'source' => 'http://wp-theme.us/demo_content/plugins/azu-posttype.zip',
			'required' => false,
			'version' => '1.0.0',
			'force_activation' => false,
			'force_deactivation' => false
		),
            
            	// WPBakery Visual Composer
		array(
			'name' => 'WPBakery Visual Composer',
			'slug' => 'js_composer',
			'source' => 'http://wp-theme.us/demo_content/plugins/js_composer.zip',
			'required' => true,
			'version' => '4.12',
			'force_activation' => false,
			'force_deactivation' => false
		),
            
            	// Ultimate addons config
		array(
			'name' => 'Ultimate VC addons',
			'slug' => 'Ultimate_VC_Addons',
			'source' => 'http://wp-theme.us/demo_content/plugins/ultimate-addons.zip',
			'required' => true,
			'version' => '3.16.6',
			'force_activation' => false,
			'force_deactivation' => false
		),
            	// Theme updater
		array(
			'name' => 'Envato WordPress Toolkit',
			'slug' => 'envato-wordpress-toolkit-master',
			'source' => 'https://github.com/envato/envato-wordpress-toolkit/archive/master.zip', //AZZU_PLUGINS_DIR.'/envato-wordpress-toolkit-master.zip',
			'required' => false,
			'version' => '1.7.3',
			'force_activation' => false,
			'force_deactivation' => false
		),
		array(
			'name' => 'Recent Tweets Widget',
			'slug' => 'recent-tweets-widget',
			'required' => false
		),		
                // Contact Form 7
		array(
			'name' => 'Contact Form 7',
			'slug' => 'contact-form-7',
			'required' => false
		)
	);


	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       		=> 'azzu'.LANG_DN,         	// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
		'parent_menu_slug' 	=> 'themes.php', 				// Default parent menu slug
		'parent_url_slug' 	=> 'themes.php', 				// Default parent URL slug
		'menu'         		=> 'tgmpa-install-plugins', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
		'message' 			=> '',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       			=> __( 'Install Required Plugins', 'azzu'.LANG_DN ),
			'menu_title'                       			=> __( 'Install Plugins', 'azzu'.LANG_DN ),
			'installing'                       			=> __( 'Installing Plugin: %s', 'azzu'.LANG_DN ), // %1$s = plugin name
			'oops'                             			=> __( 'Something went wrong with the plugin API.', 'azzu'.LANG_DN ),
			'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'azzu'.LANG_DN ), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'azzu'.LANG_DN ), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' , 'azzu'.LANG_DN), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'azzu'.LANG_DN ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'azzu'.LANG_DN ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'azzu'.LANG_DN ), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'azzu'.LANG_DN ), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'azzu'.LANG_DN ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'azzu'.LANG_DN ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'azzu'.LANG_DN ),
			'return'                           			=> __( 'Return to Required Plugins Installer', 'azzu'.LANG_DN ),
			'plugin_activated'                 			=> __( 'Plugin activated successfully.', 'azzu'.LANG_DN ),
			'complete' 									=> __( 'All plugins installed and activated successfully. %s', 'azzu'.LANG_DN ), // %1$s = dashboard link
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)
	);

	tgmpa( $plugins, $config );

}
add_action( 'tgmpa_register', 'azzu_register_required_plugins' );


/**
 * Admin notice.
 *
 */
function azzu_admin_notice() {

	// if less css file is writable - return
	$less_is_writable = get_option( 'azzu_less_css_is_writable' );
	if ( $less_is_writable || false === $less_is_writable ) {
		return;
	}

	$current_screen = get_current_screen();

	if ( 'options-framework' != $current_screen->parent_base ) {
		return;
	}

	?>
	<div class="updated">
		<p><strong><?php echo _x( 'Failed to create customization .CSS file. To improve your site performance, please check whether ".../wp-content/uploads/" folder is created, and its CHMOD is set to 777.', 'backend css file creation error', 'azzu'.LANG_DN ); ?></strong></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'azzu_admin_notice', 15 );



/**
 * Add video url field for attachments.
 *
 */
function azzu_attachment_fields_to_edit( $fields, $post ) {

	// hopefuly add new field only for images
	if ( strpos( get_post_mime_type( $post->ID ), 'image' ) !== false ) {
		$video_url = get_post_meta( $post->ID, 'azu-video-url', true );

		$fields['azu-video-url'] = array(
				'label' 		=> _x('Video url', 'attachment field', 'azzu'.LANG_DN),
				'input' 		=> 'text',
				'value'			=> $video_url ? $video_url : '',
				'show_in_edit' 	=> true
		);

	}

	return $fields;
}
add_filter( 'attachment_fields_to_edit', 'azzu_attachment_fields_to_edit', 10, 2 );

/**
 * Save vide url attachment field.
 *
 */
function azzu_save_attachment_fields( $attachment_id ) {

	// video url
	if ( isset( $_REQUEST['attachments'][$attachment_id]['azu-video-url'] ) ) {

		$location = esc_url($_REQUEST['attachments'][$attachment_id]['azu-video-url']);
		update_post_meta( $attachment_id, 'azu-video-url', $location );
	}

}
add_action( 'edit_attachment', 'azzu_save_attachment_fields' );




/**	
 * This function return array with thumbnail image meta for items list in admin are.
 * If fitured image not set it gets last image by menu order.
 * If there are no images and $noimage not empty it returns $noimage in other way it returns false
 *
 * @param integer $post_id
 * @param integer $max_w
 * @param integer $max_h
 * @param string $noimage
 */ 

function azu_get_admin_thumbnail ( $post_id, $max_w = 100, $max_h = 100, $noimage = '' ) {
	$post_type=  get_post_type( $post_id );
	$thumb = array();

	if ( has_post_thumbnail( $post_id ) ) {
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail' );
	}

	if ( empty( $thumb ) ) {

		if ( ! $noimage ) {
			return false;
		}

		$thumb = $noimage;
		$w = $max_w;
		$h = $max_h;
	} else {

		$sizes = wp_constrain_dimensions( $thumb[1], $thumb[2], $max_w, $max_h );
		$w = $sizes[0];
		$h = $sizes[1];
		$thumb = $thumb[0];
	}

	return array( esc_url( $thumb ), $w, $h );
}

/**
 * Description here.
 *
 * @param integer $post_id
 */
function azu_admin_thumbnail ( $post_id ) {
	$default_image = AZZU_THEME_URI . '/images/noimage-thumbnail.jpg';
	$thumbnail = azu_get_admin_thumbnail( $post_id, 100, 100, $default_image );

	if ( $thumbnail ) {

		echo '<a style="width: 100%; text-align: center; display: block;" href="post.php?post=' . absint($post_id) . '&action=edit" title="">
					<img src="' . esc_url($thumbnail[0]) . '" width="' . esc_attr($thumbnail[1]) . '" height="' . esc_attr($thumbnail[2]) . '" alt="" />
				</a>';
	}
}

/**
 * Add styles to admin.
 *
 */
function azzu_admin_print_scripts() {
?>
<style type="text/css">
#azzu-thumbs {
	width: 110px;
}
#azzu-sidebar,
#azzu-footer {
	width: 110px;
}
#wpbody-content .bulk-edit-row-page .inline-edit-col-right,
#wpbody-content .bulk-edit-row-post .inline-edit-col-right {
	width: 30%;
}
</style>
<?php
}
add_action( 'admin_print_scripts-edit.php', 'azzu_admin_print_scripts', 99 );

/**
 * Add styles to media.
 *
 */
function azzu_admin_print_scripts_for_media() {
?>
<style type="text/css">
.fixed .column-azzu-media-title {
	width: 10%;
}
.fixed .column-azzu-media-title span {
	padding: 2px 5px;
}
.fixed .column-azzu-media-title .azu-media-hidden-title {
	background-color: red;
	color: white;
}
.fixed .column-azzu-media-title .azu-media-visible-title {
	background-color: green;
	color: white;
}
</style>
<?php
}
add_action( 'admin_print_scripts-upload.php', 'azzu_admin_print_scripts_for_media', 99 );

/**
 * Add thumbnails column in posts list.
 *
 */
function azzu_add_thumbnails_column_in_admin( $defaults ){
	$head = array_slice( $defaults, 0, 1 );
	$tail = array_slice( $defaults, 1 );

	$head['azzu-thumbs'] = _x( 'Thumbnail', 'backend', 'azzu'.LANG_DN );

	$defaults = array_merge( $head, $tail );

	return $defaults;
}
add_filter('manage_edit-azu_portfolio_columns', 'azzu_add_thumbnails_column_in_admin');
add_filter('manage_edit-azu_team_columns', 'azzu_add_thumbnails_column_in_admin');
add_filter('manage_edit-azu_testimonials_columns', 'azzu_add_thumbnails_column_in_admin');

/**
 * Add sidebar and footer columns in posts list.
 *
 */
function azzu_add_sidebar_and_footer_columns_in_admin( $defaults ){
	$defaults['azzu-sidebar'] = _x( 'Sidebar', 'backend', 'azzu'.LANG_DN );
	$defaults['azzu-footer'] = _x( 'Footer', 'backend', 'azzu'.LANG_DN );
	return $defaults;
}
add_filter('manage_edit-page_columns', 'azzu_add_sidebar_and_footer_columns_in_admin');
add_filter('manage_edit-post_columns', 'azzu_add_sidebar_and_footer_columns_in_admin');
add_filter('manage_edit-azu_portfolio_columns', 'azzu_add_sidebar_and_footer_columns_in_admin');

/**
 * Add title column for media.
 *
 * @since azzu 1.0
 */
function azzu_add_title_column_for_media( $columns ) {
	$columns['azzu-media-title'] = _x( 'Image title', 'backend', 'azzu'.LANG_DN );
	return $columns;
}
add_filter('manage_media_columns', 'azzu_add_title_column_for_media');

/**
 * Show thumbnail in column.
 *
 */
function azzu_display_thumbnails_in_admin( $column_name, $id ){
	static $wa_list = -1;

	if ( -1 === $wa_list ) {
		$wa_list = azuf()->azzu_get_widgetareas_options();
	}

	switch ( $column_name ) {
		case 'azzu-thumbs': azu_admin_thumbnail( $id ); break;
		case 'azzu-sidebar':
			$wa = get_post_meta( $id, '_azu_sidebar_widgetarea_id', true );
			$wa_title = isset( $wa_list[ $wa ] ) ? $wa_list[ $wa ] : $wa_list['azu-sidebar'];
			echo esc_html( $wa_title );
			break;

		case 'azzu-footer':
			$wa = get_post_meta( $id, '_azu_footer_widgetarea_id', true );
			$wa_title = isset( $wa_list[ $wa ] ) ? $wa_list[ $wa ] : $wa_list['azu-footer'];
			echo esc_html( $wa_title );
			break;

		case 'azzu-slideshow-slug':
			if ( $azu_post = get_post( $id ) ) {
				echo $azu_post->post_name;
			} else {
				echo '&mdash;';
			}
			break;
	}
}
add_action( 'manage_posts_custom_column', 'azzu_display_thumbnails_in_admin', 10, 2 );
add_action( 'manage_pages_custom_column', 'azzu_display_thumbnails_in_admin', 10, 2 );

/**
 * Show title status in media list.
 *
 * @since azzu 1.0
 */
function azzu_display_title_status_for_media( $column_name, $id ) {
	if ( 'azzu-media-title' == $column_name ) {
		$hide_title = get_post_meta( $id, 'azu-img-hide-title', true );
		if ( '' === $hide_title ) {
			// $hide_title = 1;
		}

		if ( $hide_title ) {
			echo '<span class="azu-media-hidden-title">' . _x('Hidden', 'media title hidden', 'azzu'.LANG_DN) . '</span>';
		} else {
			echo '<span class="azu-media-visible-title">' . _x('Visible', 'media title visible', 'azzu'.LANG_DN) . '</span>';
		}
	}
}
add_action( 'manage_media_custom_column', 'azzu_display_title_status_for_media', 10, 2 );


