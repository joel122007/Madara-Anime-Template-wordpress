<?php

	/**
	 * The Template for displaying all single page.
	 *
	 * @package madara
	 */

	get_header();

	$madara_page_sidebar = madara_get_theme_sidebar_setting();

?>

    <div class="c-page-content style-2">
        <div class="content-area">
            <div class="container">

                <div class="row <?php echo esc_attr( $madara_page_sidebar == 'left' ? 'sidebar-left' : ''); ?>">

                    <div class="<?php echo esc_attr( $madara_page_sidebar !== 'full' && is_active_sidebar( 'main_sidebar' ) ? 'main-col col-md-8 col-sm-8' : 'col-md-12 col-sm-12'); ?>">

						<?php get_template_part( 'html/main-bodytop' ); ?>

                        <div class="main-col-inner">

							<?php while ( have_posts() ) : the_post(); ?>


								<?php get_template_part( 'html/single/content', 'page' ); ?>

								<?php
								// If comments are open or we have at least one comment, load up the comment template
								$madara_pagecomments = \App\Madara::getOption( 'page_comments', 'on' );

								if ( $madara_pagecomments == 'on' && ( comments_open() || '0' != get_comments_number() ) ) :
									comments_template();
								endif;
								?><?php endwhile; // end of the loop. ?>

                        </div>

						<?php get_template_part( 'html/main-bodybottom' ); ?>

                    </div>

					<?php
						if ( $madara_page_sidebar != 'full' && is_active_sidebar( 'main_sidebar' ) ) {
							?>
                            <div class="sidebar-col col-md-4 col-sm-4">
								<?php get_sidebar(); ?>
                            </div>
						<?php }
					?>


                </div>
            </div>
        </div>
    </div>


<?php

	get_footer();
