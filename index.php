<?php
	/**
	 * @package madara
	 */

	use App\Madara;

	get_header();

	$madara_archive_heading_text = Madara::getOption( 'archive_heading_text' );
	$madara_archive_heading_icon = Madara::getOption( 'archive_heading_icon' );
	$madara_sidebar              = madara_get_theme_sidebar_setting();
	$archive_content_columns         = Madara::getOption( 'archive_content_columns', 3 );
	$archive_margin_top              = Madara::getOption( 'archive_margin_top', '' );
	set_query_var( 'archive_content_columns', $archive_content_columns );

?>

    <div class="c-page-content style-1">

        <div class="content-area" <?php echo esc_html($archive_margin_top != '' ? 'style="margin-top: ' . $archive_margin_top . 'px;"' : ''); ?>>

            <div class="container">

                <div class="row <?php echo esc_html($madara_sidebar == 'left' ? 'sidebar-left' : ''); ?>">

                    <div class="<?php echo esc_html($madara_sidebar !== 'full' && is_active_sidebar( 'main_sidebar' ) ? 'main-col col-md-8 col-sm-8' : 'col-md-12 col-sm-12'); ?>">

						<?php get_template_part( 'html/main-bodytop' ); ?>


                        <div class="main-col-inner">

							<?php if ( $madara_archive_heading_text != '' ) { ?>
                                <div class="c-blog__heading style-2 font-heading <?php echo esc_html($madara_archive_heading_icon == '' ? 'no-icon' : ''); ?>">

                                    <h4>

										<?php if ( $madara_archive_heading_icon != '' ) { ?>
                                            <i class="<?php echo esc_attr( $madara_archive_heading_icon ); ?>"></i>
										<?php } ?>

										<?php echo esc_html( $madara_archive_heading_text ); ?>

                                    </h4>
                                </div>
							<?php } ?>

                            <div class="c-blog-listing">
                                <div class="c-blog__inner">
                                    <div class="c-blog__content">

										<?php if ( have_posts() ) : ?>

                                            <div id="loop-content" class="page-content-listing">

												<?php
													$index = 1;
													set_query_var( 'madara_post_count', madara_get_post_count() );

												?>

												<?php while ( have_posts() ) : the_post(); ?>

													<?php

													set_query_var( 'madara_loop_index', $index );

													get_template_part( 'html/loop/content' );

													$index ++;

													?>

												<?php endwhile; ?>

                                            </div>

										<?php else : ?>

											<?php get_template_part( 'html/loop/content', 'none' ); ?>

										<?php endif; ?>

										<?php

											//Get Pagination
											$madara_pagination = new App\Views\ParsePagination();
											$madara_pagination->renderPageNavigation( '#loop-content', 'html/loop/content' );

										?>


                                    </div>
                                </div>
                            </div>

                        </div>


						<?php get_template_part( 'html/main-bodybottom' ); ?>

                    </div>


					<?php
						if ( $madara_sidebar != 'full' && is_active_sidebar( 'main_sidebar' ) ) {
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
