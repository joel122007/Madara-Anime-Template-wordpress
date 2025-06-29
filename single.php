<?php
	
	use App\Views\MadaraView;
	use App\Madara;

	get_header();

	$madara_sidebar = madara_get_theme_sidebar_setting();

	$madara_postMeta = new App\Views\ParseMeta();

?>

    <div class="c-page-content style-1">
        <div class="content-area">
            <div class="container">
                <div class="row <?php echo esc_attr($madara_sidebar == 'left' ? 'sidebar-left' : ''); ?>">

                    <div class="<?php echo esc_attr($madara_sidebar !== 'full' && ( is_active_sidebar( 'single_post_sidebar' ) || is_active_sidebar( 'main_sidebar' ) ) ? 'main-col col-md-8 col-sm-8' : 'col-md-12 col-sm-12'); ?>">

						<?php get_template_part( 'html/main-bodytop' ); ?>

                        <div class="main-col-inner">
                            
                            <?php do_action('before_page_content'); ?>

							<?php while ( have_posts() ) : the_post(); ?>

								<?php get_template_part( 'html/single/content', get_post_format() ); ?>

								<?php MadaraView::render( 'PostNavigation' ); ?>

								<?php
								// If comments are open or we have at least one comment, load up the comment template
								if ( (comments_open() || '0' != get_comments_number()) && Madara::getOption('enable_comment', 'on') == 'on' ) :
									comments_template();
								endif;
								?>

							<?php endwhile; // end of the loop. ?>

                        </div>

						<?php get_template_part( 'html/main-bodybottom' ); ?>

                    </div>


					<?php
						if ( $madara_sidebar !== 'full' && ( is_active_sidebar( 'single_post_sidebar' ) || is_active_sidebar( 'main_sidebar' ) ) ) {
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
