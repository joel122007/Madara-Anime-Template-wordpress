<?php
	/**
	 * The Sidebar containing the main widget areas.
	 *
	 * @package madara
	 */

	if ( ! is_404() && ! is_search() || ( class_exists( 'WP_MANGA' ) && ! is_manga_search_page() ) ) {

		?>
        <div id="main-sidebar" class="main-sidebar" role="complementary">
			<?php
				if ( class_exists( 'WP_MANGA' ) && is_singular( 'wp-manga' ) ) {
					if ( is_manga_reading_page() && is_active_sidebar( 'manga_reading_sidebar' ) ) {
						dynamic_sidebar( 'manga_reading_sidebar' );
					} elseif ( class_exists( 'WP_MANGA' ) && is_manga_single() && is_active_sidebar( 'manga_single_sidebar' ) ) {
						dynamic_sidebar( 'manga_single_sidebar' );
					} else {
						dynamic_sidebar( 'main_sidebar' );
					}
				} elseif ( class_exists( 'WP_MANGA' ) && is_manga_archive() && is_active_sidebar( 'manga_archive_sidebar' ) ) {
					dynamic_sidebar( 'manga_archive_sidebar' );
				} elseif ( is_single() && is_active_sidebar( 'single_post_sidebar' ) ) {
					dynamic_sidebar( 'single_post_sidebar' );
				} else {
					dynamic_sidebar( 'main_sidebar' );
				}

			?>
        </div>
		<?php
	}
