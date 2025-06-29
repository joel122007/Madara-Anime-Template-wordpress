<?php
	/**
	 * @package madara
	 */
    use App\Madara;
    
	get_header();

	$wp_query                 = madara_get_global_wp_query();
	$search_header_background = madara_output_background_options( 'search_header_background' );
    $madara_ajax_search = Madara::getOption('madara_ajax_search', 'on');

?>
    <div class="c-search-header__wrapper" style="<?php echo esc_attr($search_header_background != '' ? $search_header_background : 'background-image: url(' . get_parent_theme_file_uri( '/images/bg-search.jpg' ) . ');'); ?>">
        <div class="container">
            <div class="search-content">
                <form role="search" method="get" class="search-form <?php echo ($madara_ajax_search == 'on' ? 'ajax' : '');?>">
                    <label> <span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'madara' ); ?></span>
                        <input type="search" class="search-field" placeholder="<?php esc_html_e( 'Search...', 'madara' ); ?>" value="<?php echo esc_attr( $s ); ?>" name="s">
                    </label> <input type="submit" class="search-submit" value="<?php esc_html_e( 'Search', 'madara' ); ?>">
                </form>
            </div>
        </div>
    </div>
    <div class="c-page-content">
        <div class="content-area">
            <div class="container">
                <div class="row">
                    <div class="main-col col-md-12 sidebar-hidden">

						<?php get_template_part( 'html/main-bodytop' ); ?>

                        <!-- container & no-sidebar-->
                        <div class="main-col-inner">
							<?php
								if ( have_posts() ) {
							?>
                            <div class="search-wrap">
                                <div class="tab-wrap">
                                    <div class="c-blog__heading style-2 font-heading">
                                        <h4>
                                            <i class="<?php madara_default_heading_icon(); ?>"></i>
											<?php echo sprintf( _n( '%s result', '%s results', $wp_query->found_posts, 'madara' ), $wp_query->found_posts ); ?>
                                        </h4>
                                    </div>
                                </div>
                                <!-- Tab panes -->
                                <div class="tab-content-wrap">
                                    <div class="c-blog-listing">
                                        <div class="c-blog__inner">
                                            <div class="c-blog__content">
                                                <div id="loop-content" class="page-content-listing">
													<?php
														/* Start the Loop */
														$index = 1;
														set_query_var( 'madara_post_count', madara_get_post_count() );

														while ( have_posts() ) : the_post(); ?><?php

															set_query_var( 'madara_loop_index', $index );

															get_template_part( 'html/loop/content', get_post_format() );

															$index ++;
															?>

														<?php endwhile; ?>
                                                </div>
                                            </div>

											<?php

												//Get Pagination
												$madara_pagination = new App\Views\ParsePagination();
												$madara_pagination->renderPageNavigation( '#loop-content', 'html/loop/content' );

											?>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					<?php
						} else {
						?>
                        <div class="search-wrap no-results not-found">
                            <div class="results_content">
                                <div class="icon-not-found">
                                    <i class="icon ion-android-sad"></i>
                                </div>
                                <div class="not-found-content">
                                    <p><?php esc_html_e( 'No matches found. Try a different search...', 'madara' ); ?></p>
                                </div>
                            </div>
                        </div>
						<?php
						}
						
						get_template_part( 'html/main-bodybottom' ); ?>

                    </div>
                </div>
            </div>
        </div>
    </div></div>

<?php

	get_footer();
