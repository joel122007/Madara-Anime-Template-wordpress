<?php
	/**
	 * The template for displaying the footer.
	 *
	 * Contains the closing of the #content div and all content after
	 *
	 * @package madara
	 */

	use App\Madara;

	$madara_copyright = Madara::getOption( 'copyright', '' );

	$madara_ParseSocials = new App\Views\ParseSocials();

	$madara_social_accounts = $madara_ParseSocials->renderSocialAccounts( false );

	$manga_hover_details = Madara::getOption( 'manga_hover_details', 'off' );

	if ( ! is_404() ) {

		?>
        </div><!-- <div class="site-content"> -->

		<?php get_template_part( 'html/main-bottom' ); ?>

        <footer class="site-footer">

			<?php echo apply_filters( 'madara_ads_footer', madara_ads_position( 'ads_footer', 'footer-ads col-md-12' ) ); ?>

			<?php if ( $madara_social_accounts && $madara_social_accounts != '' ) { ?>
                <div class="top-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="wrap_social_account">
									<?php echo wp_kses_post( $madara_social_accounts ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			<?php } ?>
			
			<?php if ( is_active_sidebar( 'footer_sidebar' ) ) { ?>
			<div class="c-footer-sidebar">
				<div class="container">
					<div class="row c-row">
						<?php dynamic_sidebar( 'footer_sidebar' ); ?>
					</div>
				</div>
			</div>
			<?php } ?>

            <div class="bottom-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">

							<?php
								if ( has_nav_menu( 'footer_menu' ) ) {
									echo '<div class="nav-footer"><ul class="list-inline font-nav">';
									wp_nav_menu( array(
										'theme_location' => 'footer_menu',
										'container'      => false,
										'items_wrap'     => '%3$s',
										'depth'          => '1',
									) );
									echo '</ul></div>';
								}
							?>

                            <div class="copyright">
								<?php
									$madara_copyright = Madara::getOption( 'copyright', '' );
									if ( $madara_copyright != '' ) {
										echo '<p>' . wp_kses_post( $madara_copyright ) . '</p>';
									} else {
										echo '<p>' . sprintf(esc_html__( '&copy; %s Madara Inc. All rights reserved', 'madara' ), date('Y')) . '</p>';
									}
								?>
                            </div>
                            
                            <?php
                            if(Madara::getOption('polylang_footer', 'off') == 'on' && function_exists('pll_the_languages')){?>
                            <div class="languages">
                                <span class="title"><?php esc_html_e('Language:','madara');?></span> <?php pll_the_languages( ['dropdown'=>1] );?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

        </footer>
		<?php
		echo apply_filters( 'madara_ads_wall_left', madara_ads_position( 'ads_wall_left', 'wall-ads-control wall-ads-left' ) );
		echo apply_filters( 'madara_ads_wall_right', madara_ads_position( 'ads_wall_right', 'wall-ads-control wall-ads-right' ) );
		?>

		<?php if ( $manga_hover_details == 'on' ) { ?>
            <div id="hover-infor"></div>
		<?php } ?>

        </div> <!-- class="wrap" --></div> <!-- class="body-wrap" -->

	<?php } ?>

<?php wp_footer(); ?>

</body></html>