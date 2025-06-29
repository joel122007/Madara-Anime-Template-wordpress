<?php

	use App\Madara;

	/**
	 * The Template for displaying all 404 pages.
	 *
	 * @package madara
	 */

	get_header();

?>

    <!-- #site-navigation -->
    <div class="container">
        <section class="c-page-content c-page-404 ">

            <div id="primary" class="content-area">
                <main id="main" class="site-main main-content">

                    <div id="main-content" class="row c-row">

                        <div class="col-12 c-column">

                            <section class="error-404 not-found">
                                <div class="error-404_content">
                                    <header class="entry-header">
                                        <div class="entry-featured-image">
											<?php
												$madara_featured_image = Madara::getOption( 'page404_featured_image' );

												if ( $madara_featured_image != '' ) {
													echo '<figure class="c-thumbnail"><img src="' . esc_url( $madara_featured_image ) . '" alt="' . esc_attr__( '404', 'madara' ) . '"/></figure>';
												} else {
													echo '<figure class="c-thumbnail"><img src="' . esc_url( get_template_directory_uri() . '/images/404.png' ) . '" alt="' . esc_attr__( '404', 'madara' ) . '"/></figure>';
												}
											?>
                                        </div>
                                        <div class="entry-title">
                                            <h3 class="heading">
												<?php
													$madara_heading = Madara::getOption( 'page404_title' );

													if ( $madara_heading != '' ) {
														echo esc_html( $madara_heading );
													} else {
														esc_html_e( 'Oops! page not found.', 'madara' );
													}
												?>
                                            </h3>
                                        </div>
                                    </header>

                                    <!-- .entry-header -->
									<?php $madara_content = Madara::getOption( 'page404_content' );

										if ( $madara_content != '' ) {
											echo '<div class="entry-content">' . wp_kses_post( $madara_content ) . '</div>';
										}
									?>
                                    <!-- .entry-content -->

                                    <div class="entry-footer">
                                        <a class="c-btn c-btn_style-3" href="<?php echo esc_url( home_url( '/' ) ); ?>"> <?php esc_html_e( 'Go home', 'madara' ) ?></a>
                                    </div>
                                    <!-- .entry-footer -->
                                </div>
                            </section>

                        </div>

                    </div>
                    <!-- row -->

                </main>
                <!-- main -->
            </div>
            <!-- primary -->

        </section>
        <!-- #c-page-content -->
    </div>

<?php
	get_footer();
