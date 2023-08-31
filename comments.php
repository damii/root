<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to theme_comment() which is
 * located in the fw/template-tags.php file.
 *
 * @package Theme
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
	return;
	

?>

	<div id="comments" class="<?php azus()->_class('comments-area'); ?>">

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
		<<?php echo AZU_TITLE_H; ?> class="<?php azus()->_class('comments-title'); ?>">
			<?php
				printf( _nx( '%1$s Comment', '%1$s Comments', get_comments_number(), 'comments title', 'azzu'.LANG_DN),
					number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</<?php echo AZU_TITLE_H; ?>>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-above" class="<?php azus()->_class('comment-navigation'); ?>" role="navigation">
			<<?php echo AZU_PAGE_TITLE_H; ?> class="<?php azus()->_class('screen-reader-text'); ?>"><?php _ex( 'Comment navigation', 'atheme', 'azzu'.LANG_DN); ?></<?php echo AZU_PAGE_TITLE_H; ?>>
			<div class="<?php azus()->_class('nav-previous'); ?>"><?php previous_comments_link( _x( '&larr; Older Comments', 'atheme', 'azzu'.LANG_DN) ); ?></div>
			<div class="<?php azus()->_class('nav-next'); ?>"><?php next_comments_link( _x( 'Newer Comments &rarr;', 'atheme', 'azzu'.LANG_DN) ); ?></div>
		</nav><!-- #comment-nav-above -->
		<?php endif; // check for comment navigation ?>

		<ol class="<?php azus()->_class('comment-list'); ?>">
			<?php
				/* Loop through and list the comments. Tell wp_list_comments()
				 * to use theme_comment() to format the comments.
				 * If you want to override this in a child theme, then you can
				 * define theme_comment() and that will be used instead.
				 * See theme_comment() in fw/template-tags.php for more.
				 */
				wp_list_comments( array( 'callback' => array('azu_tags','theme_comment') ) );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="<?php azus()->_class('comment-navigation'); ?>" role="navigation">
			<<?php echo AZU_PAGE_TITLE_H; ?> class="<?php azus()->_class('screen-reader-text'); ?>"><?php _ex( 'Comment navigation', 'atheme', 'azzu'.LANG_DN); ?></<?php echo AZU_PAGE_TITLE_H; ?>>
			<div class="<?php azus()->_class('nav-previous'); ?>"><?php previous_comments_link( _x( '&larr; Older Comments', 'atheme', 'azzu'.LANG_DN) ); ?></div>
			<div class="<?php azus()->_class('nav-next'); ?>"><?php next_comments_link( _x( 'Newer Comments &rarr;', 'atheme', 'azzu'.LANG_DN) ); ?></div>
		</nav><!-- #comment-nav-below -->
		<?php endif; // check for comment navigation ?>

	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="<?php azus()->_class('no-comments'); ?>"><?php _ex( 'Comments are closed.', 'atheme', 'azzu'.LANG_DN); ?></p>
	<?php endif; ?>


	<?php 
        
        $commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$required_text = sprintf( ' ' . __('Required fields are marked %s', 'azzu'.LANG_DN), '<span class="required">*</span>' );
        $comment_form_args = array(
		'fields'	=> apply_filters( 'comment_form_default_fields', array(

			'author' => '<div class="'.azus()->get('form-fields').'"><div class="'.azus()->get('comment-form-author').'">' . '<label class="azu-label-name" for="author">' . __( 'Name &#42;', 'azzu'.LANG_DN ) . '</label><input id="author" class="form-control" name="author" type="text" placeholder="' . __( 'Name&#42;', 'azzu'.LANG_DN ) . '" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></div>',

			'email' => '<div class="'.azus()->get('comment-form-email').'"><label class="azu-label-email" for="email">' . __( 'Email &#42;', 'azzu'.LANG_DN ) . '</label><input id="email" class="form-control" name="email" type="text" placeholder="' . __( 'Email&#42;', 'azzu'.LANG_DN ) . '" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></div>',

			'url' => '<div class="'.azus()->get('comment-form-url').'"><label class="azu-label-website" for="url">' . __( 'Website', 'azzu'.LANG_DN ) . '</label><input id="url" class="form-control" name="url" type="text" placeholder="' . __( 'Website', 'azzu'.LANG_DN ) . '" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></div></div>'

			)
		),

		'comment_field'	=> '<p class="'.azus()->get('comment-form-comment').'"><label class="azu-label-comment" for="comment">' . __( 'Comment', 'azzu'.LANG_DN ) . '</label><textarea id="comment" class="form-control" placeholder="' . __( 'Comment', 'azzu'.LANG_DN ) . '" name="comment" cols="45" rows="8" aria-required="true" ></textarea></p>',

		'comment_notes_after' => '<p id="azu-form-allowed-tags" style="display:none;" class="'.azus()->get('form-allowed-tags').'">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s', 'azzu'.LANG_DN ), ' <code>' . allowed_tags() . '</code>' ) . '</p>',

		'must_log_in' => '<p class="'.azus()->get('must-log-in').'">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.', 'azzu'.LANG_DN ), wp_login_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',

		'logged_in_as' => '<p class="'.azus()->get('logged-in-as').'">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'azzu'.LANG_DN ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',

		'comment_notes_before' => '<p class="'.azus()->get('comment-notes').'" style="display:none;">' . __( 'Your email address will not be published.', 'azzu'.LANG_DN ) . ( $req ? $required_text : '' ) . '</p>',

	);
        
        comment_form($comment_form_args); ?>

</div><!-- #comments -->
