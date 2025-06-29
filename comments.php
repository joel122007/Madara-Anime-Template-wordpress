<?php
	/**
	 * The template for displaying Comments.
	 *
	 * The area of the page that contains both current comments
	 * and the comment form.
	 *
	 * @package madara
	 */

	/*
	 * If the current post is protected by a password and
	 * the visitor has not yet entered the password we will
	 * return early without loading the comments.
	 */
	if ( post_password_required() ) {
		return;
	}
?>

<div class="hr mv40"></div>

<div id="madara-comments" class="comments-area">

	<?php // You can start editing here -- including this comment! ?>

	<?php comment_form( App\Views\ParseComment::get_comment_form_args() ); ?>

	<?php 
	
						
	if ( have_comments() ) : ?>
        <h4 class="comments-title">
			<?php
				printf( _n( '1 Comment', '%d Comments', get_comments_number(), 'madara' ), number_format_i18n( get_comments_number() ) );
			?>
        </h4>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
            <nav id="comment-nav-above" class="comment-navigation" role="navigation">
                <h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'madara' ); ?></h1>

                <div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'madara' ) ); ?></div>
                <div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'madara' ) ); ?></div>
            </nav><!-- #comment-nav-above -->
		<?php endif; // check for comment navigation ?>

        <ol class="comment-list" id="comments">
			<?php
				wp_list_comments( array(
					'style'       => 'ol',
					'short_ping'  => true,
					'callback'    => array( 'App\Views\ParseComment', 'comment_item' ),
					'avatar_size' => 60
				) );
			?>
        </ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
            <nav id="comment-nav-below" class="comment-navigation" role="navigation">
                <h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'madara' ); ?></h1>

                <div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'madara' ) ); ?></div>
                <div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'madara' ) ); ?></div>
            </nav><!-- #comment-nav-below -->
		<?php endif; // check for comment navigation ?>

	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
			?>
            <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'madara' ); ?></p>
		<?php endif; ?>


</div><!-- #comments -->