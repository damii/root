<?php
/**
 * Blog & Portfolio
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Page definition.
 */
$options[] = array(
		"page_title"	=> _x( "Blog ...", "theme-options", 'azzu'.LANG_DN ),
		"menu_title"	=> _x( "Blog ...", "theme-options", 'azzu'.LANG_DN ),
		"menu_slug"		=> "of-blog-menu",
		"type"			=> "page"
);

/**
 * Heading definition.
 */
$options[] = array( "name" => _x("Blog", "theme-options", 'azzu'.LANG_DN), "type" => "heading" );

	/**
	 * Image size
	 */
	$options[] = array(	"name" => _x('Image size', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

            // radio
            $options[] = array(
                    "name"		=> _x("Image width percentage", "theme-options", 'azzu'.LANG_DN),
                    "id"		=> "general-blog-image-size",
                    "std"		=> 12,
                    "transport"         => "refresh",
                    "type"		=> "radio",
                    "options"	=> array(
                        "3" => _x("25% extra small", "theme-options", 'azzu'.LANG_DN),
                        "4" => _x("33% small", "theme-options", 'azzu'.LANG_DN),
                        "6" => _x("50% normal", "theme-options", 'azzu'.LANG_DN),
                        "8" => _x("66% large", "theme-options", 'azzu'.LANG_DN),
                        "12" => _x("100% full", "theme-options", 'azzu'.LANG_DN)
                    )
            );

	$options[] = array(	"type" => "block_end");

        /**
	 * Related posts.
	 */
	$options[] = array(	"name" => _x('Related posts', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

		// radio
		$options[] = array(
			"desc"		=> '',
			"name"		=> _x('Show related posts', 'theme-options', 'azzu'.LANG_DN),
			"id"		=> 'general-show_rel_posts',
			"std"		=> '0',
                        "transport"     => "refresh",
			"type"		=> 'radio',
			"options"	=> $yes_no_options,
			"show_hide"	=> array( '1' => true ),
		);

		// hidden area
		$options[] = array( 'type' => 'js_hide_begin' );

			// input
			$options[] = array(
				"name"		=> _x( 'Title', 'theme-options', 'azzu'.LANG_DN ),
				"id"		=> 'general-rel_posts_head_title',
				"std"		=> __('Related posts', 'azzu'.LANG_DN),
				"type"		=> 'text',
			);

			// input
			$options[] = array(
				"name"		=> _x( 'Maximum number of related posts', 'theme-options', 'azzu'.LANG_DN ),
				"id"		=> 'general-rel_posts_max',
				"std"		=> 6,
                                "transport"     => "refresh",
				"type"		=> 'text',
				// number
				"sanitize"	=> 'ppp'
			);

		$options[] = array( 'type' => 'js_hide_end' );

	$options[] = array(	"type" => "block_end");

	/**
	 * Meta information.
	 */
	$options[] = array(	"name" => _x('Meta information', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

		// radio
		$options[] = array(
			"desc"		=> '',
			"name"		=> _x('Show meta information', 'theme-options', 'azzu'.LANG_DN),
			"id"		=> 'general-blog_meta_on',
			"std"		=> '1',
                        "transport"     => "refresh",
			"type"		=> 'radio',
			"options"	=> $yes_no_options,
			"show_hide"	=> array( '1' => true ),
		);

		// hidden area
		$options[] = array( 'type' => 'js_hide_begin' );

			// checkbox
			$options[] = array(
				"desc"  	=> '',
				"name"      => _x( 'Post format icon', 'theme-options', 'azzu'.LANG_DN ),
				"id"    	=> 'general-blog_meta_format_icon',
				"type"  	=> 'checkbox',
                                "transport"     => "refresh",
				'std'   	=> 1
			);

			// checkbox
			$options[] = array(
				"desc"  	=> '',
				"name"      => _x( 'Date', 'theme-options', 'azzu'.LANG_DN ),
				"id"    	=> 'general-blog_meta_date',
				"type"  	=> 'checkbox',
                                "transport"     => "refresh",
				'std'   	=> 1
			);

			// checkbox
			$options[] = array(
				"desc"  	=> '',
				"name"      => _x( 'Author', 'theme-options', 'azzu'.LANG_DN ),
				"id"    	=> 'general-blog_meta_author',
				"type"  	=> 'checkbox',
                                "transport"     => "refresh",
				'std'   	=> 1
			);

			// checkbox
			$options[] = array(
				"desc"  	=> '',
				"name"      => _x( 'Categories', 'theme-options', 'azzu'.LANG_DN ),
				"id"    	=> 'general-blog_meta_categories',
				"type"  	=> 'checkbox',
                                "transport"     => "refresh",
				'std'   	=> 1
			);

                        // checkbox
			$options[] = array(
				"desc"  	=> '',
				"name"      => _x( 'Like', 'theme-options', 'azzu'.LANG_DN ),
				"id"    	=> 'general-blog_meta_like',
				"type"  	=> 'checkbox',
                                "transport"     => "refresh",
				'std'   	=> 1
			);
                        
                        // checkbox
			$options[] = array(
				"desc"  	=> '',
				"name"      => _x( 'View count', 'theme-options', 'azzu'.LANG_DN ),
				"id"    	=> 'general-blog_meta_pageview',
				"type"  	=> 'checkbox',
                                "transport"     => "refresh",
				'std'   	=> 0
			);
                        
			// checkbox
			$options[] = array(
				"desc"  	=> '',
				"name"      => _x( 'Comments', 'theme-options', 'azzu'.LANG_DN ),
				"id"    	=> 'general-blog_meta_comments',
				"type"  	=> 'checkbox',
                                "transport"     => "refresh",
				'std'   	=> 1
			);
                        
			// checkbox
			$options[] = array(
				"desc"  	=> '',
				"name"      => _x( 'Share', 'theme-options', 'azzu'.LANG_DN ),
				"id"    	=> 'general-blog_meta_share',
				"type"  	=> 'checkbox',
                                "transport"     => "refresh",
				'std'   	=> 0
			);

		$options[] = array( 'type' => 'js_hide_end' );

	$options[] = array(	"type" => "block_end");
        
        
	/**
	 * Author info in posts
	 */
	$options[] = array(	"name" => _x('Single post', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

                // text
                $options[] = array(
                        "desc"		=> '',
                        "name"		=> _x('Title', 'theme-options', 'azzu'.LANG_DN),
                        "id"		=> "general-single-title",
                        "std"		=> 'READ OUR JOURNAL',
                        "type"		=> 'text'
                );
                
                // text
                $options[] = array(
                        "desc"		=> '',
                        "name"		=> _x('Subtitle', 'theme-options', 'azzu'.LANG_DN),
                        "id"		=> "general-single-subtitle",
                        "std"		=> '', //This is an optional subtitle
                        "type"		=> 'text'
                );
        
        	// checkbox
		$options[] = array(
			"name"      => _x( 'Pagination, previous &amp; next buttons', 'theme-options', 'azzu'.LANG_DN ),
			"id"    	=> 'general-next_prev_in_blog',
			"type"  	=> 'checkbox',
                        "transport"     => "refresh",
			'std'   	=> 1
		);
                
		// checkbox
		$options[] = array(
			"name"      => _x( 'Show author info in blog posts', 'theme-options', 'azzu'.LANG_DN ),
			"id"    	=> 'general-show_author_in_blog',
			"type"  	=> 'checkbox',
                        "transport"     => "refresh",
			'std'   	=> 1
		);
                
                // checkbox
                $options[] = array(
                        "desc"  	=> '',
                        "name"      => _x( 'Tags', 'theme-options', 'azzu'.LANG_DN ),
                        "id"    	=> 'general-blog_meta_tags',
                        "type"  	=> 'checkbox',
                        "transport"     => "refresh",
                        'std'   	=> 1
                );
                
                // checkbox
		$options[] = array(
			"name"      => _x( 'Show IP address in comment', 'theme-options', 'azzu'.LANG_DN ),
			"id"    	=> 'general-comment-ip',
			"type"  	=> 'checkbox',
                        "transport"     => "refresh",
			'std'   	=> 1
		);
                

	$options[] = array(	"type" => "block_end");

        
if ( azu_check_custom_posttype('portfolio') )  :  
/**
 * Heading definition.
 */
$options[] = array( "name" => _x("Portfolio", "theme-options", 'azzu'.LANG_DN), "type" => "heading" );

	/**
	 * Slugs
	 */
	$options[] = array( "name" => _x("Slugs", "theme-options", 'azzu'.LANG_DN), "type" => "block_begin" );

		// input
		$options[] = array(
			"name"		=> _x("Portfolio slug", "theme-options", 'azzu'.LANG_DN),
			"id"		=> "general-post_type_portfolio_slug",
			"std"		=> "project",
			"type"		=> "text",
			"class"		=> "mini"
		);

	$options[] = array( "type" => "block_end" );

	/**
	 * Previous & next buttons.
	 */
	$options[] = array(	"name" => _x('Pagination, previous &amp; next buttons', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

		// checkbox
		$options[] = array(
			"name"      => _x( 'Show in portfolio projects', 'theme-options', 'azzu'.LANG_DN ),
			"id"    	=> 'general-next_prev_in_portfolio',
			"type"  	=> 'checkbox',
                        "transport"     => "refresh",
			'std'   	=> 1
		);

	$options[] = array(	"type" => "block_end");

	/**
	 * Related projects.
	 */
	$options[] = array(	"name" => _x('Related projects', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

		// radio
		$options[] = array(
			"desc"		=> '',
			"name"		=> _x('Show related projects', 'theme-options', 'azzu'.LANG_DN),
			"id"		=> 'general-show_rel_projects',
			"std"		=> '0',
                        "transport"     => "refresh",
			"type"		=> 'radio',
			"options"	=> $yes_no_options,
			"show_hide"	=> array( '1' => true ),
		);

		// hidden area
		$options[] = array( 'type' => 'js_hide_begin' );

			// input
			$options[] = array(
				"name"		=> _x( 'Title', 'theme-options', 'azzu'.LANG_DN ),
				"id"		=> 'general-rel_projects_head_title',
				"std"		=> __('Related projects', 'azzu'.LANG_DN),
				"type"		=> 'text',
			);

			// input
			$options[] = array(
				"name"		=> _x( 'Maximum number of projects posts', 'theme-options', 'azzu'.LANG_DN ),
				"id"		=> 'general-rel_projects_max',
				"std"		=> 12,
                                "transport"     => "refresh",
				"type"		=> 'text',
				// number
				"sanitize"	=> 'ppp'
			);
                        
                        //slider
                        $options[] = array(
                                "desc"      => '',
                                "name"      => _x( 'Slides number', 'theme-options', 'azzu'.LANG_DN ),
                                "id"        => 'general-rel_projects_slides',
                                "std"       => 3, 
                                "transport"     => "refresh",
                                "type"      => "slider",
                                "sanitize"	=> 'slider',// integer value
                                "options"   => array( 'min' => 1, 'max' => 6 )
                        );

			// checkbox
			$options[] = array(
				"name"		=> _x('Show meta information', 'theme-options', 'azzu'.LANG_DN),
				"id"		=> 'general-rel_projects_meta',
				"std"		=> '0',
                                "transport"     => "refresh",
				"type"		=> 'checkbox'
			);

			// checkbox
			$options[] = array(
				"name"		=> _x('Show titles', 'theme-options', 'azzu'.LANG_DN),
				"id"		=> 'general-rel_projects_title',
				"std"		=> '0',
                                "transport"     => "refresh",
				"type"		=> 'checkbox'
			);

			// checkbox
			$options[] = array(
				"name"		=> _x('Show excerpts', 'theme-options', 'azzu'.LANG_DN),
				"id"		=> 'general-rel_projects_excerpt',
				"std"		=> '0',
                                "transport"     => "refresh",
				"type"		=> 'checkbox'
			);

			// checkbox
			$options[] = array(
				"name"		=> _x('Show links', 'theme-options', 'azzu'.LANG_DN),
				"id"		=> 'general-rel_projects_link',
				"std"		=> '1',
                                "transport"     => "refresh",
				"type"		=> 'checkbox'
			);

			// checkbox
			$options[] = array(
				"name"		=> _x('Show "Read more" link', 'theme-options', 'azzu'.LANG_DN),
				"id"		=> 'general-rel_projects_readmore',
				"std"		=> '1',
                                "transport"     => "refresh",
				"type"		=> 'checkbox'
			);
                        
                        

		$options[] = array( 'type' => 'js_hide_end' );

	$options[] = array(	"type" => "block_end");

	/**
	 * Meta information.
	 */
	$options[] = array(	"name" => _x('Meta information', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

		// radio
		$options[] = array(
			"desc"		=> '',
			"name"		=> _x('Show meta information', 'theme-options', 'azzu'.LANG_DN),
			"id"		=> 'general-portfolio_meta_on',
			"std"		=> '1',
                        "transport"     => "refresh",
			"type"		=> 'radio',
			"options"	=> $yes_no_options,
			"show_hide"	=> array( '1' => true ),
		);

		// hidden area
		$options[] = array( 'type' => 'js_hide_begin' );

			// checkbox
			$options[] = array(
				"desc"  	=> '',
				"name"      => _x( 'Date', 'theme-options', 'azzu'.LANG_DN ),
				"id"    	=> 'general-portfolio_meta_date',
				"type"  	=> 'checkbox',
                                "transport"     => "refresh",
				'std'   	=> 0
			);

			// checkbox
			$options[] = array(
				"desc"  	=> '',
				"name"      => _x( 'Author', 'theme-options', 'azzu'.LANG_DN ),
				"id"    	=> 'general-portfolio_meta_author',
				"type"  	=> 'checkbox',
                                "transport"     => "refresh",
				'std'   	=> 0
			);

			// checkbox
			$options[] = array(
				"desc"  	=> '',
				"name"      => _x( 'Categories', 'theme-options', 'azzu'.LANG_DN ),
				"id"    	=> 'general-portfolio_meta_categories',
				"type"  	=> 'checkbox',
                                "transport"     => "refresh",
				'std'   	=> 1
			);
                        
			// checkbox
			$options[] = array(
				"desc"  	=> '',
				"name"      => _x( 'Like', 'theme-options', 'azzu'.LANG_DN ),
				"id"    	=> 'general-portfolio_meta_like',
				"type"  	=> 'checkbox',
                                "transport"     => "refresh",
				'std'   	=> 1
			);
                        
			// checkbox
			$options[] = array(
				"desc"  	=> '',
				"name"      => _x( 'Comments', 'theme-options', 'azzu'.LANG_DN ),
				"id"    	=> 'general-portfolio_meta_comments',
				"type"  	=> 'checkbox',
                                "transport"     => "refresh",
				'std'   	=> 1
			);

		$options[] = array( 'type' => 'js_hide_end' );

	$options[] = array(	"type" => "block_end");
endif;

if ( class_exists( 'Woocommerce' ) )  :  
/**
 * Heading definition.
 */
$options[] = array( "name" => _x("Woocommerce", "theme-options", 'azzu'.LANG_DN), "type" => "heading" );
	/**
	 * Woocommerce archive
	 */
	$options[] = array(	"name" => _x('Shop', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );
                //slider
                $options[] = array(
                        "desc"      => '',
                        "name"      => _x( 'Columns', 'theme-options', 'azzu'.LANG_DN ),
                        "id"        => 'wc-archive-columns',
                        "std"       => 3, 
                        "transport"     => "refresh",
                        "type"      => "slider",
                        "sanitize"	=> 'slider',// integer value
                        "options"   => array( 'min' => 1, 'max' => 6 )
                );
                
                // checkbox
                $options[] = array(
                        "desc"  	=> '',
                        "name"      => _x( 'Snow ratings on shop', 'theme-options', 'azzu'.LANG_DN ),
                        "id"    	=> 'wc-star-rating',
                        "type"  	=> 'checkbox',
                        "transport"     => "refresh",
                        "interface"	=> $on_off_options,
                        "std"   	=> 0
                );
        $options[] = array(	"type" => "block_end");
	/**
	 * Woocommerce title in single
	 */
	$options[] = array(	"name" => _x('Single shop', 'theme-options', 'azzu'.LANG_DN), "type" => "block_begin" );

                // text
                $options[] = array(
                        "desc"		=> '',
                        "name"		=> _x('Title', 'theme-options', 'azzu'.LANG_DN),
                        "id"		=> "wc-single-title",
                        "std"		=> 'OUR SHOP',
                        "type"		=> 'text'
                );
                
                // text
                $options[] = array(
                        "desc"		=> '',
                        "name"		=> _x('Subtitle', 'theme-options', 'azzu'.LANG_DN),
                        "id"		=> "wc-single-subtitle",
                        "std"		=> '', //This is an optional subtitle
                        "type"		=> 'text'
                );
        $options[] = array(	"type" => "block_end");
endif;
	
/**
 * Heading definition.
 */
$options[] = array( "name" => _x("Social Sharing", "theme-options", 'azzu'.LANG_DN), "type" => "heading" );

        foreach ( azuf()->azzu_themeoptions_get_template_list() as $id=>$desc ) {
                /**
                 * Share buttons.
                 */
                $options[] = array(	"name" => $desc, "type" => "block_begin" );

                        // social_buttons
                        $options[] = array(
                                "id"		=> 'social_buttons-' . $id,
                                "std"		=> array(),
                                "transport"     => "refresh",
                                "type"		=> 'social_buttons',
                        );

                $options[] = array(	"type" => "block_end");
        }