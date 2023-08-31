<?php
/**
 * post type meta boxes.
 *
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/***********************************************************/
// Post options
/***********************************************************/

$prefix = '_azu_post_options_';

$AZU_META_BOXES[] = array(
	'id'		=> 'azu_page_box-post_options',
	'title' 	=> _x('Post Options', 'backend metabox', 'azzu'.LANG_DN),
	'pages' 	=> array( 'post' ),
	'context' 	=> 'normal',
	'priority' 	=> 'high',
	'fields' 	=> array(

                                
		// Hide featured image on post page
		array(
			'name'    		=> __('Hide featured image:', 'azzu'.LANG_DN),
			'id'      		=> "{$prefix}hide_thumbnail",
			'type'    		=> 'checkbox',
			'std'			=> 0,
		),  
                
                //  Post preview width (radio buttons)
		array(
			'name'    	=> _x('Post wide width:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "{$prefix}preview",
                        'top_divider'	=> true,
			'type'    		=> 'checkbox',
			'std'			=> 0,
		),   
                                
		// Related posts category
		array(
			'name'    	=> _x('Related posts category:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "{$prefix}related_mode",
			'type'    		=> 'checkbox',
			'std'			=> 0,
			'hide_fields'	=> array( "{$prefix}related_categories" ),
			'top_divider'	=> true
		),                                
                                        
		// Taxonomy list
		array(
			'id'      => "{$prefix}related_categories",
			'type'    => 'taxonomy_list',
			'options' => array(
				// Taxonomy name
				'taxonomy' => 'category',
				// How to show taxonomy: 'checkbox_list' (default) or 'checkbox_tree', 'select_tree' or 'select'. Optional
				'type' => 'checkbox_list',
				// Additional arguments for get_terms() function. Optional
				'args' => array()
			),
			'multiple'    => true,
		),
                                
             	// Link
		array(
			'name'	=> _x('Link:', 'backend metabox', 'azzu'.LANG_DN),
			'id'    => "{$prefix}link",
			'type'  => 'text',
			'std'   => '',
			'before'	=> '<p><small>' . sprintf(
				_x('it can be show or hide from %sTheme Options / Blog / Related posts%s', 'backend metabox', 'azzu'.LANG_DN),
				'<a href="' .  esc_url(add_query_arg( 'page', 'of-blog-menu', get_admin_url() . 'admin.php' )) . '" target="_blank">',
				'</a>'
			) . '</small></p><div class="azu_hr"></div>',
			'after'	=> '<p><small>Post format link url OR embed Video/Audio url</small></p>',
		),
                //  gallery style
		array(
			'name'    	=> _x('For gallery enable slideshow:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "{$prefix}gallery_style",
                        'top_divider'	=> true,
			'type'    		=> 'checkbox',
			'std'			=> 0,
		),  

	),
);


                        
/***********************************************************/
// Portfolio post options
/***********************************************************/
if ( azu_check_custom_posttype('portfolio' ) ) :
$prefix = '_azu_project_options_';

$AZU_META_BOXES[] = array(
	'id'		=> 'azu_page_box-portfolio_post',
	'title' 	=> _x('Portfolio Options', 'backend metabox', 'azzu'.LANG_DN),
	'pages' 	=> array( 'azu_portfolio' ),
	'context' 	=> 'normal',
	'priority' 	=> 'high',
	'fields' 	=> array(

            
		// Hide featured image on project page
		array(
			'name'    		=> _x('Hide featured image:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      		=> "{$prefix}hide_thumbnail",
			'type'    		=> 'checkbox',
			'std'			=> 0
		),
                                
		// Hide meta on project single page
		array(
			'name'    		=> _x('Hide meta:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      		=> "{$prefix}hide_meta",
                        'top_divider'	=> true,
			'type'    		=> 'checkbox',
                        'after'	=> '<p><small>It can hide category, like, and share etc.</small></p>',
			'std'			=> 0
		),
                                
                //  Project preview width
		array(
			'name'    	=> _x('Project wide width:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "{$prefix}preview",
                        'top_divider'	=> true,
			'type'    		=> 'checkbox',
			'std'			=> 0,
		),
                                
                //  Project preview height
		array(
			'name'    	=> _x('Project long height:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "{$prefix}long",
                        'top_divider'	=> true,
			'type'    		=> 'checkbox',
			'std'			=> 0,
		),

                                
                // Related projects category
		array(
			'name'    	=> _x('Related projects category:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "{$prefix}related_mode",
			'type'    		=> 'checkbox',
			'std'			=> 0,
			'hide_fields'	=> array( "{$prefix}related_categories" ),
			'top_divider'	=> true,
		),

		// Taxonomy list
		array(
			'id'      => "{$prefix}related_categories",
			'type'    => 'taxonomy_list',
			'options' => array(
				// Taxonomy name
				'taxonomy' => 'azu_portfolio_category',
				// How to show taxonomy: 'checkbox_list' (default) or 'checkbox_tree', 'select_tree' or 'select'. Optional
				'type' => 'checkbox_list',
				// Additional arguments for get_terms() function. Optional
				'args' => array()
			),
		),
                                
		// Project link
		array(
			'name'    		=> _x('Project link:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      		=> "{$prefix}show_link",
			'type'    		=> 'checkbox',
			'std'			=> 0,
			'hide_fields'	=> array(
				"{$prefix}link",
				"{$prefix}link_name",
				"{$prefix}link_target",
			),
                        'before'	=> '<p><small>' . sprintf(
				_x('it can be show or hide from %sTheme Options / Blog / Portfolio / Related projects%s', 'backend metabox', 'azzu'.LANG_DN),
				'<a href="' .  esc_url(add_query_arg( 'page', 'of-blog-menu#options-group-2', get_admin_url() . 'admin.php' )) . '" target="_blank">',
				'</a>'
			) . '</small></p><div class="azu_hr"></div>'
		),

		// Link
		array(
			'name'	=> _x('Link:', 'backend metabox', 'azzu'.LANG_DN),
			'id'    => "{$prefix}link",
			'type'  => 'text',
			'std'   => '',
		),

		// Link name
		array(
			'name'	=> _x('Caption:', 'backend metabox', 'azzu'.LANG_DN),
			'id'    => "{$prefix}link_name",
			'type'  => 'text',
			'std'   => '',
		),

		// Target
		array(
			'name'    	=> _x('Target:', 'backend metabox', 'azzu'.LANG_DN),
			'id'      	=> "{$prefix}link_target",
			'type'    	=> 'radio',
			'std'		=> '',
			'options'	=> array(
				''			=> _x('_self', 'backend metabox', 'azzu'.LANG_DN),
				'_blank' 	=> _x('_blank', 'backend metabox', 'azzu'.LANG_DN),
			),
		),
                        

	),
);
endif;                        
                        
/***********************************************************/
// Testimonial options
/***********************************************************/
if ( azu_check_custom_posttype('testimonials' ) ) :
$prefix = '_azu_testimonial_options_';

$AZU_META_BOXES[] = array(
	'id'		=> 'azu_page_box-testimonial_options',
	'title' 	=> _x('Testimonial Options', 'backend metabox', 'azzu'.LANG_DN),
	'pages' 	=> array( 'azu_testimonials' ),
	'context' 	=> 'side',
	'priority' 	=> 'core',
	'fields' 	=> array(

		// Position
		array(
			'name'	=> _x('Position:', 'backend metabox', 'azzu'.LANG_DN),
			'id'    => "{$prefix}position",
			'type'  => 'textarea',
			'std'   => '',
		),

		// Link
		array(
			'name'	=> _x('Link:', 'backend metabox', 'azzu'.LANG_DN),
			'id'    => "{$prefix}link",
			'type'  => 'text',
			'std'   => '',
			'top_divider'	=> true
		),

	),
);
endif;
                        
/***********************************************************/
// Teammate options
/***********************************************************/
if ( azu_check_custom_posttype('team' ) ) :

$prefix = '_azu_teammate_options_';


$AZU_META_BOXES[] = array(
	'id'		=> 'azu_page_box-team_options',
	'title' 	=> _x('Team Options', 'backend metabox', 'azzu'.LANG_DN),
	'pages' 	=> array( 'azu_team' ),
	'context' 	=> 'side',
	'priority' 	=> 'core',
	'fields' 	=> array( 
            	// Position
		array(
			'name'	=> _x('Position:', 'backend metabox', 'azzu'.LANG_DN),
			'id'    => "{$prefix}position",
			'type'  => 'textarea',
			'std'   => '',
		),
                array(
                    'name'			=> _x('Social icons:', 'backend metabox', 'azzu'.LANG_DN),
                    'id'    		=> "{$prefix}social",
                    'type'  		=> 'socialicon',
                    'std'   		=> array(
                                                    array('icon' => 'facebook', 'url' => '#')
                                               ),
                    'top_divider'	=> true
                )
        ),
);
endif;          
