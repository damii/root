<?php
/**
 * @author   	Damii
 * @copyright	Copyright (c) 2014
 * @package  	Azu
 * @version  	0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists('azu_tags') ) :
class azu_tags extends azu_template_tags {

    protected function add_actions() { 
        parent::add_actions();
    }

    
    public function azzu_content_text($type,$attr,$empty_media=true){
            $bottom_left = null;
            if(empty($attr['readmore']) && $attr['columns'] > 1){
                $near_title = '';
                $bottom_left = azuh()->azzu_new_posted_on( 'post' );
            }
            else {
                $near_title = azuh()->azzu_new_posted_on( 'post' );
            }
            echo '<div class="'.azus()->get('azu-entry-content',azuf()->azzu_compute_col($attr['image_size'],array('invert' =>true,'media_empty' => $empty_media, 'offset'=> $attr['align']=='1' ))).'">';
                    echo $near_title; 

                    echo '<'.AZU_POST_TITLE_H.' class="'.azus()->get('azu-entry-title').'">'
                            . sprintf( '<a href="%s" title="%s" rel="bookmark">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), get_the_title() )
                     .'</'.AZU_POST_TITLE_H.'>';


                    echo azuh()->azzu_the_content(); 
                    if($bottom_left === null)
                        $bottom_left = azuh()->azzu_post_readmore_link();
                    echo azut()->azzu_get_post_meta_wrap( $bottom_left.azuh()->azzu_post_edit_link().azut()->azzu_get_post_bottom(), azus()->get('azu-post-bottom') );
            echo '</div>';
    }
    
    
public function azzu_content_testimonials(){
    ?>
    <div class="<?php azus()->_class('azu-testimonial'); ?>">
	<?php 
                $attr = azum()->get('attr');
		$post_id = get_the_ID();

		$html = '';

		$content = get_the_content();

		// get avatar ( featured image )
		$avatar = '<span class="no-avatar"><img src="'.AZZU_THEME_URI.'/images/no-avatar.gif" alt="no avatar" height="auto" width="100%"></span>';
		if ( has_post_thumbnail( $post_id ) ) {

			$media_id = get_post_thumbnail_id( $post_id );
			$avatar = azuf()->azu_get_thumb_img( array(
				'img_meta'      => wp_get_attachment_image_src( $media_id, 'full' ),
				'img_id'		=> $media_id,
                                'class'		=> '',
				'options'       => array( 'w' => 140, 'h' => 140 ),
				'echo'			=> false,
				'wrap'			=> '<img %IMG_CLASS% %SRC% %SIZE% %IMG_TITLE% %ALT% />',
			) );

			$avatar = '<span class="no-preload">' . $avatar . '</span>';
		}

		// get link
		$link = get_post_meta( $post_id, '_azu_testimonial_options_link', true );
		if ( $link ) {
			$link = esc_url( $link );
			$avatar = '<a href="' . $link . '" target="_blank">' . $avatar . '</a>';
		} else {
			$link = '';
		}

		// get position
		$position = get_post_meta( $post_id, '_azu_testimonial_options_position', true );
		if ( $position ) {
			$position = '<span class="azu-testimonial-text">' . $position . '</span>';
		} else {
			$position = '';
		}

		// get title
		$title = get_the_title();
		if ( $title ) {

			if ( $link ) {
				$title = '<a href="' . $link . '" target="_blank"><'.AZU_TESTIMONIAL_TITLE_H.' class="text-primary">' . $title . '</'.AZU_TESTIMONIAL_TITLE_H.'></a>';
			} else {
				$title = '<'.AZU_TESTIMONIAL_TITLE_H.' class="text-primary">' . $title . '</'.AZU_TESTIMONIAL_TITLE_H.'>';
			}
		} else {
			$title = '';
		}

		// get it all together
		$html = sprintf(
			'<article>' . "\n\t" . '<div class="azu-testimonial-avator">%2$s</div><div class="azu-testimonial-content">%1$s</div>' . "\n\t" . '<div class="azu-testimonial-vcard"><div class="azu-testimonial-title">%3$s</div></div>' . "\n" . '</article>' . "\n",
			$content, $avatar, $title . $position
		);

		echo $html;
        ?>
    </div>
    <?php
}


public function azzu_content_team(){
                $attr = azum()->get('attr');
		$post_id = get_the_ID();
		
		if ( !$post_id ) return '';

		$html = '';
                $content ='';
                $class ='azu-rollover';
                $class_border = '';
                if(empty($attr['image_size']))
                    $attr['image_size'] = '12';
                $class_col = 'col-sm-'.$attr['image_size'];
                $wrap = '<div %CLASS%><img %IMG_CLASS% %SRC% %IMG_TITLE% %ALT% %SIZE% />';
                if($attr['descriptions'])
                    $content = get_the_content( $post_id );
		if ( $content ) $content = '<div class="team-content"><p>' .  $content  . '</p></div>';
                if($attr['hover']){
                    $class = azus()->get('azu-team-hover', 'azu-social-icons azu-social-reverse');
                }
                
                if($attr['border'])
                    $class_border .= 'azu-border';
                if($attr['round'])
                    $class_border .= ' azu-round-border';
		// get featured image
		$image = '';
		if ( has_post_thumbnail( $post_id ) ) {

			$media_id = get_post_thumbnail_id( $post_id );

			$teammate_thumb_args = array(
				'img_meta'      => wp_get_attachment_image_src( $media_id, 'full' ),
				'img_id'	=> $media_id,
                                'class'		=> $class,
				'options'       => array( 'w' => $attr['column_width'], 'z' => 0 ),
				'echo'			=> false,
				'wrap'			=> $wrap,
			);

			/**
			 * Applied filters:
			 * 
			*/
                        $teammate_thumb_args = azut()->azzu_thumbnail_proportions( $teammate_thumb_args, $attr );

			$image = azuf()->azu_get_thumb_img( $teammate_thumb_args );

		}

                // get links
		$links = array();

                $link = get_post_meta( $post_id, '_azu_teammate_options_social', false ); //azuf()->azzu_get_team_links_array()
                $common_links = azuf()->azzu_get_social_icons_data();

                $link_title ='';
                if ( is_array( $link ) ) {
                    foreach ( $link as $data ) {
                        $data = (array)$data;
                        $link_title = array_key_exists( $data['icon'] , $common_links) ? $common_links[$data['icon']] : '';
                        $links[] = azuh()->azzu_get_social_icon( $data['icon'], $data['url'], $link_title );
                    }
                }
		if ( empty($links) ) {
			$links = '';
		} else {
			$links = '<div class="social-ico">' . implode('', $links) . '</div>';
		}

		// get position
		$position = get_post_meta( $post_id, '_azu_teammate_options_position', true );
		if ( $position ) {
			$position = '<p>' . $position . '</p>';
		} else {
			$position = '';
		}

		// get title
		$title = get_the_title( $post_id );
		if ( $title ) {
			$title = '<'.AZU_TEAM_AUTHER_H.' class="azu-author-name">' . $title . '</'.AZU_TEAM_AUTHER_H.'>';
		} else {
			$title = '';
		}

		$author_block = $title . $position;
		if ( $author_block ) {
			$author_block = '<div class="azu-team-author">' . $author_block . '</div>';
		}
                
                $class_col_desc = $class_col;
                if($attr['hover']){
                    if(!empty($image))
                        $links .= '</div>';
                    $image .= $links;
                }
                else {
                    if(!empty($image))
                        $image .= '</div>';
                    $content .= $links;
                    $class_col_desc .= ' azu-social-icons';
                }
                
		// get it all togeather
		$html = sprintf(
			'<div class="'.azus()->get('team-container', $class_border).'"><div class="row">' . "\n\t" . '<div class="'.azus()->get('azu-team-media',$class_col).'">%1$s</div><div class="'.azus()->get('team-desc',$class_col_desc).'">%2$s</div>' . "\n\t" . '</div></div>' . "\n",
			$image, $author_block . $content
		);

		echo $html;
}

public function azzu_content_portfolio(){
global $post;

$attr = azum()->get('attr');
$wide_mode = !( is_search() || is_archive() ) ? azum()->get('preview',0) : 0;
$long_mode = !( is_search() || is_archive() ) ? azum()->get('long',0) : 0;
$hover_effect = $attr['hover_effect'];
$description = $attr['descriptions'];
$desc_on_hover = ( '' != $hover_effect );

$custom_link = esc_url(get_post_meta( $post->ID, '_azu_project_options_link', true ));
$project_link = azuh()->azzu_get_project_link( 'project-link' );
$details_button = azuh()->azzu_post_readmore_link();

$show_links = $attr['show_link'] && $project_link;
$show_like = $attr['show_like'];
$show_title = $attr['show_title'] && get_the_title();
$change_link = ( !empty($custom_link) && $attr['override_link']);
$azu_permalink = $change_link ? $custom_link :  get_permalink( $post->ID );
$show_details = $attr['show_details'];
$show_excerpts = $attr['show_excerpt'] && $post->post_excerpt;
$show_terms = $attr['meta_info'];
$show_zoom = $attr['show_zoom'];

$show_content = $show_zoom || $show_links || $show_title || $show_details || $show_excerpts || $show_terms || $show_like;
$show_post_buttons = ( $project_link && $show_links ) || $show_zoom || $details_button;
$buttonts_count = count( array_keys( array( $project_link && $show_links, (bool) $show_zoom, (bool) $details_button ), true ) );


$before_media = '';
$after_media = '';
$before_content = '';
$after_content = '';

$link_classes = '';
$rollover_class = '';
$post_class = array('project-post');

if ( $show_content && $desc_on_hover ) {
	
        $rollover_class = 'effect-'.$hover_effect;
	if ( 0 == $buttonts_count ) {
		$rollover_class .= ' forward-post';
	} else if ( $buttonts_count < 2 ) {
		$rollover_class .= ' rollover-active';
	}

	$before_media = '<figure class="' . $rollover_class . '">';
	$after_media = '</figure>';
        $before_content = '<figcaption>';
        $after_content = '</figcaption>';
        
	$link_classes = 'link show-content';
}


if(!$desc_on_hover){
    $rollover_class .= ' '.azus()->get('azu-rollover', 'azu-rollover-portpolio');
}

if( $description )
        $post_class[] = 'azu-portfolio-desc';


$like_link = '';
$zoom_link = '';
$video_url = '';

		$media = '';
		$is_pass_protected = post_password_required();
		if ( !$is_pass_protected || $desc_on_hover ) {

				if ( has_post_thumbnail() ) {
					$media_id = get_post_thumbnail_id();
					$media_meta = wp_get_attachment_image_src( $media_id, 'full' );
					$attachment_post = get_post( $media_id );
                                        if(!isset($attachment_post))
                                            $attachment_post = get_post( $post->ID );
                                        
					$video_url = esc_url( get_post_meta( $media_id, 'azu-video-url', true ) );
                                        $hide_title = azuh()->azzu_image_title_is_hidden( $media_id );
					$zoom_link = sprintf(
						'<a href="%s" class="azu-single-mfp-popup azu-mfp-item %s" title="%s" data-azu-img-description="%s">%s<span></span></a>',
						($video_url ? esc_url($video_url) : esc_url($media_meta[0])),
						($video_url ? 'mfp-iframe project-video' : 'mfp-image project-zoom') . ($description ? ' btn-zoom' : ''),
						$hide_title ? '' : esc_attr($attachment_post->post_title),
						esc_attr($attachment_post->post_content),
						__('Zoom', 'azzu'.LANG_DN)
					);
                                        
                                        $like_link = azu_love_this('',false);

				} else {
					$media_id = 0;
					$media_meta = azuf()->azzu_get_default_image();
				}

				$media_args = array(
					'img_meta' 	=> $media_meta,
					'img_id'	=> $media_id,
					'img_class' => 'preload-img',
					'class'		=> $link_classes,
					'href'		=> $azu_permalink,
					'echo'		=> false,
				);
				$media_args['wrap'] = '<a %HREF% %CLASS% %TITLE% %CUSTOM%><img %IMG_CLASS% %SRC% %ALT% %SIZE% /></a>';

                                if ( ($wide_mode && !$attr['same_width'] && AZZU_MOBILE_DETECT!='1') ) {
                                        $target_image_width = $attr['column_width'] * 2;
                                        $media_args['options'] = array( 'w' => round($target_image_width), 'z' => 0 );
                                } else {
                                        $target_image_width = $attr['column_width'];
                                        $media_args['options'] = array( 'w' => round($target_image_width), 'z' => 0 );
                                }
                                //long mode
                                if ( array_key_exists( 'proportion', $attr ) && !empty($attr['proportion']) && !$attr['same_width'] && AZZU_MOBILE_DETECT!='1'){
                                    $padding_double = 1;
                                    if($attr['padding']>0){
                                        $padding_double = $attr['padding'] * 2 / $attr['column_width'];
                                        if($wide_mode && !$long_mode)
                                            $padding_double = $padding_double/2;
                                        $padding_double +=1;
                                    }
                                    if($wide_mode && !$long_mode)
                                        $attr['proportion'] = $attr['proportion']*2 * $padding_double;
                                    else if(!$wide_mode && $long_mode)
                                        $attr['proportion'] = $attr['proportion']/2 / $padding_double;

                                }

                                $media_args = azut()->azzu_thumbnail_proportions( $media_args, $attr );
                                
				$media = azuf()->azu_get_thumb_img( $media_args );

		}

		// create post buttons set
		$post_buttons = '';
		if ( $show_post_buttons ) {
			$post_buttons .= ( $show_like ? $like_link : '' ) . ( $show_details && !$change_link ? $details_button : '' ) . ( $show_links ? $project_link : '' ) . ( $show_zoom ? $zoom_link : '' );
		}

		if ( $post_buttons ) {
			$post_buttons = '<div class="links-container">' . $post_buttons . '</div>';
		}

		if ( $post_buttons && $media && !$desc_on_hover ) {
			$rollover_class .= ' rollover-project';

			if ( 0 == $buttonts_count ) {
				$rollover_class .= ' forward-post';
			} else if ( $buttonts_count < 2 ) {
				$rollover_class .= ' rollover-active';
			}

			$media = sprintf(
				'<div class="%s">%s<div class="rollover-content ">%s</div></div>',
				$rollover_class,
				$media,
				$post_buttons
			);
		}

	echo $before_media;

		echo $media;
                
        echo $before_content;
                if($desc_on_hover)
                       echo $post_buttons;
                echo '<div class="azu-hover-desc">';
                    $print_hover_desc = '';

                    if ( $show_title && !$description && $desc_on_hover)
                    {
                            $title = get_the_title();
                            $title = sprintf( '<a href="%s" title="%s" rel="bookmark">%s</a>',
                                    $azu_permalink,
                                    the_title_attribute( 'echo=0' ),
                                    $title
                            );
                            $print_hover_desc .= $title;
                    }

                    if($print_hover_desc)
                        echo '<'.AZU_PORTFOLIO_TITLE_H.' class="azu-hover-title">'.$print_hover_desc.'</'.AZU_PORTFOLIO_TITLE_H.'>';
                    if(!$description && $desc_on_hover && ($show_terms || $show_excerpts)){
                            echo '<div class="azu-hover-text">';
                                    if ( $show_terms ) {
                                            $post_meta_info = azuh()->azzu_new_posted_on( 'azu_portfolio' );
                                            $post_meta_info = preg_replace( "/(?<=href=(\"|'))[^\"']+(?=(\"|'))/", 'javascript: void(0);', $post_meta_info );
                                            echo $post_meta_info;
                                    }
                                    if ( $show_excerpts) {
                                           the_excerpt();
                                    }
                            echo '</div>';
                    }
                echo '</div>';
        echo $after_content;
        echo $after_media;
        if($description){
                echo '<div class="'.azus()->get('azu-entry-content').'">';
                
                ?>
                <<?php echo AZU_POST_TITLE_H; ?> class="<?php azus()->_class('azu-entry-title'); ?>">
                <a href="<?php echo $azu_permalink; ?>" title="<?php echo the_title_attribute( 'echo=0' ); ?>" rel="bookmark"><?php the_title(); ?></a>
                </<?php echo AZU_POST_TITLE_H; ?>>
                <?php
                
                if ( $show_terms ) {
                        $post_meta_info = azuh()->azzu_new_posted_on( 'azu_portfolio' );
                        echo $post_meta_info;
                }
                if ( $show_excerpts )
                    the_excerpt();
                
                echo '</div>';
        }
}

public function azzu_content_single(){
    global $post;
    $attr = azum()->get('attr');
    // thumbnail visibility
    $hide_thumbnail = false;
    if('post' == get_post_type())
        $hide_thumbnail = (bool) get_post_meta($post->ID, '_azu_post_options_hide_thumbnail', true);
    elseif('azu_portfolio' == get_post_type())
        $hide_thumbnail = (bool) get_post_meta($post->ID, '_azu_project_options_hide_thumbnail', true);

    do_action('azzu_before_post_content'); 
    if( !post_password_required() ) {

	$img_class = '';
	$img_options = array( 'w' => azuf()->azu_calculate_width_size(1), 'z' => 0 );
        $media = '';
	$post_format = ('post' == get_post_type()) ? get_post_format() : get_post_type();
        if( !$hide_thumbnail ){
            switch ( $post_format ) {
                    case 'azu_portfolio':
                        $video_url = '';
                        // thumbnail
                        if ( has_post_thumbnail()) {
                            $mfp_class = 'mfp-image';
                            $video_url = esc_url( get_post_meta( get_post_thumbnail_id(), 'azu-video-url', true ) );
                            $media_id = get_post_thumbnail_id();
                            $media_meta = wp_get_attachment_image_src( $media_id, 'full' );

                            $media_args = array(
                                    'img_meta' 	=> $media_meta,
                                    'img_id'	=> $media_id,
                                    'options' 	=> $img_options,
                                    'echo'		=> false,
                                    'wrap'		=> '<a %HREF% %CLASS% %CUSTOM% title="%RAW_ALT%" data-azu-img-description="%RAW_TITLE%"><img %IMG_CLASS% %SRC% %SIZE% %IMG_TITLE% %ALT% /></a>',
                            );

                            // video with play button on hover
                            if($video_url){
                                $media_args['href'] = $video_url;
                                $mfp_class = 'video-icon mfp-iframe';
                            }
                            $media_args['class'] = $img_class . ' '.azus()->get('azu-mfp-item', 'azu-single-mfp-popup azu-mfp-item '.$mfp_class);
                            $media = azuf()->azu_get_thumb_img( $media_args );
                        }
                        if ( $video_url ) {
                                $media = '<div class="' .azus()->get('azu-rollover-video').'" >' . $media . '</div>';
                        }
                        break;
                    case 'video':
                            $video_url = '';
                            if(has_post_thumbnail())
                                $video_url = esc_url( get_post_meta( get_post_thumbnail_id(), 'azu-video-url', true ) );
                            if ( empty($video_url) )
                                $video_url = esc_url( get_post_meta( $post->ID, '_azu_post_options_link', true ) );
                            // video player
                            if ( $video_url ) {
                                    $media = '<div class="azu-video-container alignnone">' . azuf()->azu_get_embed( $video_url ) . '</div>';
                            }
                            break;
                    case 'gallery':
                            $gallery = get_post_gallery( $post->ID, false );
                            if ( !empty($gallery['ids']) ) {
                                    $media_items = array_map( 'trim', explode( ',', $gallery['ids'] ) );

                                    // if we have post thumbnail and it's not hidden
                                    if ( has_post_thumbnail() && !get_post_meta( $post->ID, '_azu_post_options_hide_thumbnail', true ) ) {
                                            array_unshift( $media_items, get_post_thumbnail_id() );
                                    }

                                    $attachments_data = azuh()->azzu_get_attachment_post_data( $media_items );

                                    $gallery_style = (bool) get_post_meta( $post->ID, '_azu_post_options_gallery_style', true );
                                    $media_args = array( 'show_info' => array(), 'class' => array(''), 'img_width' => $attr['column_width'] );

                                    if( $gallery_style){
                                        $media = azuh()->azzu_get_post_media_slider( $attachments_data, $media_args );
                                    }
                                    else{
                                        $media = azuh()->azzu_get_gallery_image_list( $attachments_data, $media_args );
                                    }
                            }
                            break;
                    case 'audio': 
                            $audio_url = esc_url( get_post_meta( $post->ID, '_azu_post_options_link', true ) );
                            // audio player
                            if ( $audio_url ){
                                    $media = '<div class="azu-audio-content">'.azuf()->azu_get_embed( $audio_url ) . '</div>';
                            }
                            break;
                    case 'image':
                            $img_class = 'alignnone';
                            $img_options = false;
                    default:

                            // thumbnail
                            if ( has_post_thumbnail() ) {
                                    $media_id = get_post_thumbnail_id();
                                    $media_meta = wp_get_attachment_image_src( $media_id, 'full' );

                                    $media = azuf()->azu_get_thumb_img( array(
                                            'class'		=> $img_class . ' '.azus()->get('azu-mfp-item', 'azu-single-mfp-popup mfp-image'),
                                            'img_meta' 	=> $media_meta,
                                            'img_id'	=> $media_id,
                                            'options' 	=> $img_options,
                                            'echo'		=> false,
                                            'wrap'		=> '<a %HREF% %CLASS% %CUSTOM% title="%RAW_ALT%" data-azu-img-description="%RAW_TITLE%"><img %IMG_CLASS% %SRC% %SIZE% %IMG_TITLE% %ALT% /></a>',
                                    ) );
                            }
                    break;
            }
        }
        
        echo !empty($media) ? '<div class="azu-single-media">'.$media.'</div>' : '';
        $show_it = true;
        
        if('azu_portfolio' == get_post_type())
                $show_it = !get_post_meta($post->ID, '_azu_project_options_hide_meta', true);
        
        
        if('post' === get_post_type() || 'azu_portfolio' === get_post_type() && $show_it){
            echo azuh()->azzu_new_posted_on( get_post_type(), array('azu-single-meta') );
        }
        

        if ( ( !of_get_option( 'general-show_titles', '1' ) || (of_get_option('general-single-title','') && 'post' === get_post_type()) ) && azum()->get( 'header_title', true ) ) {
                echo '<'.AZU_POST_TITLE_H.' class="'.azus()->get('azu-entry-title').'">';
                        echo get_the_title();
                echo '</'.AZU_POST_TITLE_H.'>';
        } 
        the_content(); 
        
        echo $show_it ? '<div class="gap-2"></div>' : '';
        
        ?>
        
	<?php wp_link_pages( array( 'before' => '<div class="'.azus()->get('azu-page-links').'"><span>' . __( 'Pages:', 'azzu'.LANG_DN ).'</span>', 'after' => '</div>','separator' => ' &nbsp;' ) ); ?>

	<?php
        
        $post_meta = $share_buttons ='';
        $post_tags = of_get_option( 'general-blog_meta_tags', 1 );
        if ( 'post' === get_post_type() && $post_tags ) 
                $post_meta = azut()->azzu_get_post_tags();
        
	if($show_it)
            $share_buttons = azuh()->azzu_display_share_buttons(str_replace( 'azu_', '', get_post_type() ), array('echo' => false, 'extended' => true));

	if ( $share_buttons || $post_meta ) {
		printf( '<div class="'.azus()->get('azu-post-meta').'">%s%s</div>',
			$share_buttons ? $share_buttons : '',
                        $post_meta ? $post_meta : ''
		);
	}

	// 'theme options' -> 'general' -> 'show author info on blog post pages'
	if ( of_get_option('general-show_author_in_blog', true) && 'post' == get_post_type() ) {
		azuh()->azzu_display_post_author();
	}
	azut()->azu_paginator();
        
        if('post' == get_post_type())
                  azuh()->azzu_display_related_posts();
        elseif('azu_portfolio' == get_post_type())
                  azuh()->azzu_display_related_projects();

	if ( (!( post_password_required() || ( !comments_open() && '0' == get_comments_number() ) ) ) && 'boxed' != of_get_option('general-layout-style') ) {
		echo '<div class="hr-thin"></div>';
	}

        } else {
            the_content(); 
	} // !post_password_required 
        
        //edit_post_link( __( 'Edit', 'azzu'.LANG_DN ), '<span class="'.azus()->get('azu-edit-link').'">', '</span>' );
        do_action('azzu_after_post_content'); 
}

// override function
public function azzu_content_no_title( $type = '' ) {
    global $post;
    $post_link = '';
    if($type == 'link'){
        $post_link = get_post_meta( $post->ID, '_azu_post_options_link', true );
        if(!$post_link)
            $post_link = azuf()->azu_get_link_url();
    }
        $attr = azum()->get('attr');
        
        $bottom_left = null;
        if(empty($attr['readmore']) && $attr['columns'] > 1){
            $near_title = '';
            $bottom_left = azuh()->azzu_new_posted_on( 'post' );
        }
        else {
            $near_title = azuh()->azzu_new_posted_on( 'post' );
        }
        $class_padding = '';
        if(in_array($type,array('link','quote','status')))
        {
            $class_padding = azus()->get('azu-padding');
            if($attr['columns'] > 1)
                $near_title = '';
            $bottom_left = '';
        }
    	echo '<div class="'.azus()->get('azu-entry-content', azuf()->azzu_compute_col('',array('invert' => true, 'offset'=> $attr['align']=='1'))).'">';
                    echo '<div class="'.azus()->get('content-blog-'.$type).'">';
                        if(of_get_option( 'general-blog_meta_format_icon', 1 )) {
                            echo '<i class="'.azus()->get('azu-icon-post','azu-icon-post-'.$type).'"></i>';
                        }
                        echo $near_title;
                        if(!empty($post_link)){
                            echo '<a target="_blank" href="'.esc_url($post_link).'">';
                        }
                        echo '<div class="'.$class_padding.'">';
                            echo azuh()->azzu_the_content();
                            if(in_array($type,array('link','quote'))) {
                                echo '<div class="azu-link-custom">'.($type == 'link' ? $post_link : get_the_title()).'</div>';
                            }
                        echo '</div>';
                        if(!empty($post_link)){
                            echo '</a>';
                        }
                    echo '</div>';
                if($bottom_left === null)
                    $bottom_left = azuh()->azzu_post_readmore_link();
                echo azut()->azzu_get_post_meta_wrap( $bottom_left.azuh()->azzu_post_edit_link().azut()->azzu_get_post_bottom(), azus()->get('azu-post-bottom') );
        echo '</div>';
}

/**
 * Get post categories.
 */
public function azzu_get_post_categories( $html = '' ) {
        $post_type = get_post_type();

        if ( 'post' == $post_type ) {
                $categories_list = get_the_category_list( ', ' );
        } else {
                $categories_list = get_the_term_list( get_the_ID(), $post_type . '_category', '', ', ' );
        }

        if ( $categories_list && !is_wp_error($categories_list) ) {
                $category_icon = _x('%1$s','atheme','azzu'.LANG_DN);
                if(is_single() && $html != null)
                    $category_icon = _x('%1$s','atheme','azzu'.LANG_DN);//'<i class="azu-icon-cat"></i> %1$s';
                $categories_list = str_replace( array( 'rel="tag"', 'rel="category tag"' ), '', $categories_list);
                
                $html .= !empty($html) ? '<i class="azu-icon-sep"></i>' : '';
                $html .=  sprintf($category_icon, trim($categories_list));
        }

        return $html;
}

/**
 * Get post author.
 */
function azzu_get_post_author( $html = '' ) {
        $html .= !empty($html) ? '<i class="azu-icon-sep"></i>' : '';
        $html .= sprintf(
                '<a class="author vcard" href="%s" title="%s" rel="author">%s</a>',
                        esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ), // href
                        esc_attr( sprintf( _x( 'View all posts by %s', 'frontend post meta', 'azzu'.LANG_DN ), get_the_author() ) ), // title
                        get_the_author() // author
        );

        return $html;
}


/**
 * Get post tags.
 */
public function azzu_get_post_tags( $html = '' ) {
        $tags_list = get_the_tag_list('<span>'._x('Tags: ','atheme','azzu'.LANG_DN).'</span>', ', ');
        if ( $tags_list ) {
                $html .= sprintf(
                        '<div class="'.azus()->get('azu-entry-tags').'">%s</div>',
                                $tags_list
                );
        }

        return $html;
}



/**
 * Get post comments.
 */
function azzu_get_post_comments( $html = '' ) {
        if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) :
                ob_start();
                $html .= !empty($html) ? '<i class="azu-icon-sep"></i>' : '';
                $azu_comment = $azu_comments = '<i class="azu-icon-comment azu-tooltip" data-toggle="tooltip" data-placement="top" title="'._x('Comments','atheme','azzu'.LANG_DN).'"></i><span class="'.azus()->get('azu-love-count').'">%s</span>';
                comments_popup_link( sprintf($azu_comment , '0') , sprintf($azu_comments , '1'), sprintf($azu_comment , '%'),azus()->get('azu-comment') );
                $html .= ob_get_clean();
        endif;

        return $html;
}


/**
 * Override
 * 
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
                                <?php
				comment_reply_link( array_merge( $args, array(
					'add_below' => 'div-comment',
                                        'reply_text'=> _x('Reply','atheme','azzu'.LANG_DN),
					'depth'     => $depth,
					'max_depth' => $args['max_depth'],
					'before'    => '<div class="reply">',
					'after'     => '</div>',
				) ) ); 
                                ?>
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
			?>
                        </div><!-- .meta-bottom -->
		</article><!-- .comment-body -->

	<?php
	endif;
}


/**
 * Controlls display of post meta.
 */
function azzu_post_meta_new_controller() {
        // get theme options
        $post_meta = of_get_option( 'general-blog_meta_on', 1 );

        if ( $post_meta ) {

                $post_author = of_get_option( 'general-blog_meta_author', 1 );
                $post_categories = of_get_option( 'general-blog_meta_categories', 1 );
                $post_date = of_get_option( 'general-blog_meta_date', 1 );

                if ( $post_author ) 
                    add_filter('azzu_new_posted_on-post', array( &$this,'azzu_get_post_author'), 14);


                // add filters
                if ( $post_date ) 
                        add_filter('azzu_new_posted_on-post', array( &$this,'azzu_get_post_date'), 13);
                if ( $post_categories ) 
                        add_filter('azzu_new_posted_on-post', array( &$this,'azzu_get_post_categories'), 15);
                if(is_single()){
                    add_filter('azzu_new_posted_on-post', array( &$this,'azzu_get_post_bottom'), 16, 2);
                }
                // add wrap
                add_filter('azzu_new_posted_on-post', array( &$this,'azzu_get_post_meta_wrap'), 99, 2);
        }

}


/**
 * Controlls display of azu_portfolio meta.
 */
function azzu_portfolio_meta_new_controller() {

        // get theme options
        $post_meta = of_get_option( 'general-portfolio_meta_on', 1 );

        if ( $post_meta ) {

                $post_date = of_get_option( 'general-portfolio_meta_date', 1 );
                $post_author = of_get_option( 'general-portfolio_meta_author', 1 );
                $post_categories = of_get_option( 'general-portfolio_meta_categories', 1 );
                $post_comments = of_get_option( 'general-portfolio_meta_comments', 1 );
                $post_like = of_get_option( 'general-portfolio_meta_like', 1 );

                if ( $post_date ) {
                        add_filter('azzu_new_posted_on-azu_portfolio', array( &$this,'azzu_get_post_date'), 12);
                }
                if ( $post_author ) {
                        add_filter('azzu_new_posted_on-azu_portfolio', array( &$this,'azzu_get_post_author'), 13);
                }

                if ( $post_categories ) {
                        add_filter('azzu_new_posted_on-azu_portfolio', array( &$this,'azzu_get_post_categories'), 14);
                }

                if ( is_single() ) {
                    if($post_like)
                        add_filter('azzu_new_posted_on-azu_portfolio', array( &$this,'azzu_get_post_like'), 15);
                }
                else {
                    if ( $post_comments )
                        add_filter('azzu_new_posted_on-azu_portfolio', array( &$this,'azzu_get_post_comments'), 16);
                }
                add_filter('azzu_new_posted_on-azu_portfolio', array( &$this,'azzu_get_post_meta_wrap'), 99, 2);
        }

}

    
}
endif; // azu tag
