<?php
/**
 * Blog post content. 
 *
 * @package azzu
 * @since azzu 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if(!is_single())
    do_action('azzu_before_post');

    $post_type = azum()->get('template');
    if(empty($post_type)){
        $post_type = str_replace( 'azu_', '', get_post_type() );
    }
    // show all kind of posts
    azut()->azu_all_content($post_type); 
    
if(!is_single())
    do_action('azzu_after_post'); ?>