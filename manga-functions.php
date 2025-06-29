<?php


	use App\Madara;

	function madara_scripts() {

		if ( class_exists( 'WP_MANGA' ) ) {

			//dequeue duplicated script and style from manga plugin
			$styles = array(
				'wp-manga-plugin-css',
				'wp-manga-bootstrap-css',
				'wp-manga-slick-css',
				'wp-manga-slick-theme-css',
				'wp-manga-font-awesome',
				'wp-manga-ionicons',
				'wp-manga-font'
			);

			foreach ( $styles as $style ) {
				wp_dequeue_style( $style );
			}
			wp_dequeue_script( 'slick' );
			wp_dequeue_script( 'wp-manga-bootstrap-js' );

		}

		//enqueue madara ajax for manga archive
		if ( is_manga_archive() || ( is_page_template( 'page-templates/front-page.php' ) && Madara::getOption( 'page_content' ) == 'manga' ) ) {
			wp_enqueue_script( 'madara-ajax', get_parent_theme_file_uri( '/js/ajax.js' ), array( 'jquery' ), '', true );

			$manga_hover_details = Madara::getOption( 'manga_hover_details', 'off' );
			if ( $manga_hover_details == 'on' ) {
				wp_enqueue_script( 'madara_ajax_hover_content', get_parent_theme_file_uri( '/js/manga-hover.js' ), array( 'jquery' ), '', true );
				wp_localize_script( 'madara_ajax_hover_content', 'madara_hover_load_post', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			}
		}

		//user history for manga reading page
		if ( is_manga_reading_page() && is_user_logged_in() && Madara::getOption( 'madara_reading_history', 'on' ) == 'on' ) {
			global $wp_manga;

			wp_enqueue_script( 'user_history', get_parent_theme_file_uri( '/js/history.js' ), array( 'jquery' ), '1.6.6', true );
			
			$reading_chapter = function_exists('madara_permalink_reading_chapter') ? madara_permalink_reading_chapter() : false; 
			
			// support Madara Core before 1.6;
			if(!$reading_chapter){
				 // support Madara Core before 1.6
				 if($chapter_slug = get_query_var('chapter')){
					 global $wp_manga_functions;
					$reading_chapter = $wp_manga_functions->get_chapter_by_slug( get_the_ID(), $chapter_slug );
				 }
			}
			
			$chapter_slug = $reading_chapter ? $reading_chapter['chapter_slug'] : false;
			
			wp_localize_script( 'user_history', 'user_history_params', array(
				'ajax_url' => admin_url() . 'admin-ajax.php',
				'postID'   => get_the_ID(),
				'chapter'  => $chapter_slug ? $chapter_slug : '',
				'page'     => get_query_var($wp_manga->manga_paged_var) ? get_query_var($wp_manga->manga_paged_var) : 1,
				'interval' => intval( Madara::getOption( 'madara_reading_history_delay', '5' ) ) * 1000,
				'nonce'    => wp_create_nonce( 'madara_reading_history' )
			) );
		}

	}

	add_action( 'wp_enqueue_scripts', 'madara_scripts', 1001 );

	function madara_user_avatar( $args, $id_or_email ) {

		if ( is_numeric( $id_or_email ) ) {
			$user_id = $id_or_email;
		} elseif ( isset( $id_or_email->user_id ) ) {
			$user_id = $id_or_email->user_id;
		} else {
			return $args;
		}

		$avatar_id = get_user_meta( $user_id, '_wp_manga_user_avt_id', true );

		if ( ! empty( $avatar_id ) ) {

			if( isset( $args['size'] ) ){
				$size = array( $args['size'], $args['size'] );
			}else{
				$size = array( 96, 96 );
			}

			$url = wp_get_attachment_image_src( $avatar_id, $size );

			if ( !empty( $url ) ) {
				$args['url'] = $url[0];
			}
		}

		return $args;
	}

	add_filter( 'get_avatar_data', 'madara_user_avatar', 99, 2 );

	function madara_get_manga_archive_sidebar() {

		$wp_manga_functions = madara_get_global_wp_manga_functions();

		if ( is_manga_archive_page() || is_manga_archive_front_page() ) { //if this is Manga Archive Page or Manga Archive Page Front-page then use page sidebar from Edit Page > Page Sidebar
			$manga_archive_page  = $wp_manga_functions->get_manga_archive_page_setting();
			$madara_page_sidebar = get_post_meta( $manga_archive_page, 'page_sidebar', true );
		}

		if ( ! isset( $madara_page_sidebar ) || $madara_page_sidebar == 'default' || $madara_page_sidebar == '' ) {
			$madara_page_sidebar = Madara::getOption( 'manga_archive_sidebar', 'right' );
		}

		return $madara_page_sidebar;

	}

	function is_manga_archive_front_page() {

		$wp_manga_functions = madara_get_global_wp_manga_functions();

		return $wp_manga_functions->is_manga_archive_front_page();

	}

	function is_manga_archive_page() {

		$wp_manga_functions = madara_get_global_wp_manga_functions();

		return $wp_manga_functions->is_manga_archive_page();

	}

	function is_manga_posttype_archive() {

		$wp_manga_functions = madara_get_global_wp_manga_functions();

		return $wp_manga_functions->is_manga_posttype_archive();

	}

	function is_manga_search_page() {

		$wp_manga_functions = madara_get_global_wp_manga_functions();

		return $wp_manga_functions->is_manga_search_page();

	}

	function is_manga_single( $manga_id = 0) {

		$wp_manga_functions = madara_get_global_wp_manga_functions();

		return $wp_manga_functions->is_manga_single( $manga_id );

	}
    
    /**
     * Check if current page is manga oneshot page
     **/
    function is_manga_oneshot( $manga_id = 0 ){
        if(!$manga_id) $manga_id = get_the_ID();
        
        if(is_manga_single($manga_id) && Madara::getOption('manga_reading_oneshot') == 'oneshot'){
            return true;
        }
        
        return false;
    }

	function is_manga_reading_page($chapter_slug = '', $manga_id = 0) {

		$wp_manga_functions = madara_get_global_wp_manga_functions();

		return $wp_manga_functions->is_manga_reading_page($chapter_slug, $manga_id);

	}

	function is_manga_archive() {

		$wp_manga_functions = madara_get_global_wp_manga_functions();

		return $wp_manga_functions->is_manga_archive();

	}

	/*
	 *  Get Chapter ID by Chapter Slug
	 *  Enter chapter slug manually or automatic get it in manga reading page.
	 */
	function madara_get_chapter_id_by_chapter_slug( $slug = '' ) {

		$chapter_id   = '';
		$chapter_slug = '';

		if ( $slug == '' ) {
			$reading_chapter = function_exists('madara_permalink_reading_chapter') ? madara_permalink_reading_chapter() : false;
			
			if($reading_chapter) {
				$chapter_id = $reading_chapter['chapter_id'];
			}
		} else {
			$chapter = madara_get_global_wp_manga_chapter()->get_chapter_by_slug( get_the_ID(), $slug );
			
			if ( is_array( $chapter ) && ! empty( $chapter ) ) {
				$chapter_id = $chapter['chapter_id'];
			}
		}
		
		return $chapter_id;
	}

	/*
	 *  Get Chapter Name by Chapter Slug
	 *  Enter chapter slug manually or automatic get it in manga reading page.
	 */
	function madara_get_chapter_name_by_chapter_slug( $slug = '' ) {

		$chapter_name = '';

		if ( $slug == '' ) {
			$reading_chapter = function_exists('madara_permalink_reading_chapter') ? madara_permalink_reading_chapter() : false;
			if($reading_chapter){
				$chapter_name = $reading_chapter['chapter_name'];
			}
		} else {
			$chapter = madara_get_global_wp_manga_chapter()->get_chapter_by_slug( get_the_ID(), $slug );
			if ( is_array( $chapter ) && ! empty( $chapter ) ) {
				$chapter_name = $chapter['chapter_name'];
			}
		}
		
		return $chapter_name;
	}

	if ( ! function_exists( 'is_manga' ) ) {
		function is_manga() {

			if ( is_manga_single() || is_manga_archive() || is_manga_reading_page() || is_manga_search_page() ) {
				return true;
			}

			return false;
		}
	}
    
    /**
     * Get URL of the latest chapter in a manga
     *
     * if this manga is one shot, the always return manga detail page URL
     *
     **/
    function madara_get_latest_chapter_link($manga_id){
        $manga_type = get_post_meta($manga_id, 'manga_reading_oneshot', true);
        
        if($manga_type == 'oneshot' || ($manga_type == '' && Madara::getOption('manga_reading_oneshot') == 'oneshot'))
            return get_permalink($manga_id);
        
        global $wp_manga_functions;
        $latest_chapter = $wp_manga_functions->get_latest_chapters($manga_id, false, 1);
        
        if($latest_chapter){
            $c_url = $wp_manga_functions->build_chapter_url( $manga_id, $latest_chapter[0] );
            return $c_url;
        }
        
        return get_permalink($manga_id);
    }

	function madara_update_user_settings() {

		if( empty( $_POST['userID'] ) ){
			return;
		}

		//update account Settings
		$new_name    = isset( $_POST['user-new-name'] ) ? $_POST['user-new-name'] : '';
		$new_email   = isset( $_POST['user-new-email'] ) ? $_POST['user-new-email'] : '';
		$current_pwd = isset( $_POST['user-current-password'] ) ? $_POST['user-current-password'] : '';
		$new_pwd     = isset( $_POST['user-new-password'] ) ? $_POST['user-new-password'] : '';
		$confirm_pwd = isset( $_POST['user-new-password-confirm'] ) ? $_POST['user-new-password-confirm'] : '';

		$wp_nonce = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
		$user_id  = get_current_user_id();

		if ( empty( $user_id ) || empty( $wp_nonce ) ) {
			return new WP_Error( 'invalid_request', __( 'Invalid request, please try again', 'madara' ) );
		}

		if( ! wp_verify_nonce( $wp_nonce, '_wp_manga_save_user_settings' ) ){
			return new WP_Error( 'invalid_request', __( 'Session expired, please try again.', 'madara' ) );
		}

		// Get user object from ID
		$user = get_user_by( 'ID', $user_id );

		if( ! $user ){
			return new WP_Error( 'user_not_found', __( 'User not found, please try again later. ', 'madara' ) );
		}

		// Update account display name
		if ( ! empty( $new_name ) ) {

			update_user_meta( $user_id, 'nickname', $new_name );

			$user->user_nicename = $new_name;
			$user->display_name  = $new_name;

		}

		// Update account email
		if ( ! empty( $new_email ) ) {
			$user->user_email = $new_email;
		}

		// update password
		if ( ! empty( $new_pwd ) ) {

			if( empty( $current_pwd ) ){
				return new WP_Error( 'empty_pwd', __( 'Current Password cannot be empty', 'madara' ) );
			}elseif( empty( $confirm_pwd ) ){
				return new WP_Error( 'empty_pwd', __( 'Confirm Password cannot be empty', 'madara' ) );
			}elseif( $new_pwd === $current_pwd ){
				return new WP_Error( 'same_pwd', __( 'New password must be different with current password', 'madara' ) );
			}elseif( $new_pwd !== $confirm_pwd ){
				return new WP_Error( 'dismatch_pwd', __( 'New Password & Confirm Password must be matched.', 'madara' ) );
			}elseif( ! wp_check_password( $current_pwd, $user->data->user_pass, $user_id ) ){
				return new WP_Error( 'invalid_pwd', __( 'Incorrect current password, please check again.', 'madara' ) );
			}else{
				//$user->user_pass = $new_pwd;
			}

		}

		$resp = wp_update_user( $user );

		if ( is_wp_error( $resp ) ) {
			return $resp;
		}
        
        if ( ! empty( $new_pwd ) ) {
            wp_set_password($new_pwd, $user->ID);
        }

		return true;

	}

	function madara_search_filter_url( $filter ) {

		$wp_manga = madara_get_global_wp_manga();

		return $wp_manga->wp_manga_search_filter_url( $filter );

	}

	if ( ! function_exists( 'madara_manga_query' ) ) {

		function madara_manga_query( $manga_args ) {

			if ( ! class_exists( 'WP_MANGA' ) ) {
				return new WP_Query( $manga_args );
			}

			return madara_get_global_wp_manga()->mangabooth_manga_query( $manga_args );

		}
	}

	function madara_user_history() {

		$post_id      = isset( $_POST['postID'] ) ? $_POST['postID'] : '';
		$chapter_slug = isset( $_POST['chapterSlug'] ) ? $_POST['chapterSlug'] : '';
		$paged        = isset( $_POST['paged'] ) ? $_POST['paged'] : '';
		$img_id       = isset( $_POST['img_id'] ) ? $_POST['img_id'] : '';
		$user_id      = get_current_user_id();

		if ( empty( $post_id ) || empty( $chapter_slug ) || empty( $user_id ) || empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'madara_reading_history' ) ) {
			wp_send_json_error();
		}
        $data = compact( [
			'post_id',
			'chapter_slug',
			'user_id',
			'img_id',
			'paged'
		] );

		//get chapter name
		$response = madara_put_user_history_manga( $data );

		if ( $response == true ) {
			wp_send_json_success();
		}

		wp_send_json_error( $response );
	}

	add_action( 'wp_ajax_manga-user-history', 'madara_user_history' );
	add_action( 'wp_ajax_nopriv_manga-user-history', 'madara_user_history' );

	function madara_remove_history() {

		$post_id       = isset( $_POST['postID'] ) ? $_POST['postID'] : '';
		$user_id       = get_current_user_id();
		$history_manga = get_user_meta( $user_id, '_wp_manga_history', true );

		if ( empty( $post_id ) || empty( $history_manga ) ) {
			wp_send_json_error();
		}

		foreach ( $history_manga as $index => $manga ) {
			if ( $manga['id'] == $post_id ) {
				unset( $history_manga[ $index ] );
			}
		}

		$resp = update_user_meta( $user_id, '_wp_manga_history', $history_manga );

		if ( $resp == true ) {
			if ( empty( $history_manga ) ) {
				wp_send_json_success( array(
					'is_empty' => true,
					'msg'      => wp_kses( __( '<span>You haven\'t read any manga yet</span>', 'madara' ), array( 'span' => array() ) )
				) );
			};
			wp_send_json_success();
		}

		wp_send_json_error();
	}

	add_action( 'wp_ajax_manga-remove-history', 'madara_remove_history' );
	add_action( 'wp_ajax_nopriv_manga-remove-history', 'madara_remove_history' );

	function madara_get_reading_style( $user_reading_style = null, $manga_id = 0 ) {

		if ( is_user_logged_in() ) {

			if ( ! empty( $user_reading_style ) ) {
				if($user_reading_style == 'default'){
					if($manga_id){
						$user_reading_style = get_post_meta($manga_id, 'manga_reading_style', 'default');
						
						if($user_reading_style != 'default'){
							return $user_reading_style;
						}
					}
					
					$user_reading_style = Madara::getOption( 'manga_reading_style', 'paged' );
				}
				
				return $user_reading_style;
			}

		}

		$reading_style = Madara::getOption( 'manga_reading_style', 'paged' );

		return $reading_style;
	}
	
	add_filter( 'get_reading_style', 'madara_get_reading_style', 10, 2 );
	
	function madara_default_notification_chapter_reading_style($default){
		$default = Madara::getOption( 'manga_reading_style', $default );
		
		return $default;
	}
	
	add_filter('wp_manga_default_notification_chapter_reading_style', 'madara_default_notification_chapter_reading_style');

	function madara_get_img_per_page() {

		if ( is_user_logged_in() ) {

			$user_img_per_page = get_user_meta( get_current_user_id(), '_manga_img_per_page', true );

			if ( ! empty( $user_img_per_page ) && $user_img_per_page !== 'default' ) {
				return intval( $user_img_per_page );
			}
		}

		$img_per_page = Madara::getOption( 'manga_reading_images_per_page', 1 );

		if ( empty( $img_per_page ) ) {
			return 1;
		}

		return intval( $img_per_page );

	}

	function madara_actual_total_pages( $total_page ){

		$img_per_page = madara_get_img_per_page();

		if ( ! empty( $img_per_page ) && $img_per_page != 1 && is_numeric( $img_per_page ) ) {
			$total_page   = intval( $total_page );
			$img_per_page = intval( $img_per_page );

			$total_page = intval( $total_page / $img_per_page ) < $total_page / $img_per_page ? intval( $total_page / $img_per_page ) + 1 : intval( $total_page / $img_per_page );
		}

		return $total_page;
	}

	function madara_blog_search( $query ) {

		if ( !is_admin() && ! is_manga_search_page() && is_search() && $query->is_main_query() && $query->get( 'post_type' ) !== 'nav_menu_item' ) {
			$query->set( 'post_type', 'post' );
		}

		return $query;

	}

	add_filter( 'pre_get_posts', 'madara_blog_search' );

	function madara_info_filter( $value ) {
        $show_info = Madara::getOption('manga_single_info_visibility', 'off');
        if($show_info == 'on'){
            if ( empty( $value ) ) {
                $value = esc_html__( 'Updating', 'madara' );
            }
        }

		return $value;

	}

	add_filter( 'wp_manga_info_filter', 'madara_info_filter' );

	function madara_hover_load_post() {

		$post_id = isset( $_REQUEST['postid'] ) && $_REQUEST['postid'] != '' ? intval( $_REQUEST['postid'] ) : '';

		if ( $post_id != '' ) {
			$post_content = get_post( $post_id );
			$post_excerpt = $post_content->post_content;
			$post_excerpt = wp_trim_words( $post_excerpt, apply_filters( 'mangasteam_hover_summary', 35 ) );

			$wp_manga_functions = madara_get_global_wp_manga_functions();
			$thumb_size         = array( 193, 278 );
			$alternative        = $wp_manga_functions->get_manga_alternative( $post_id );
			$authors            = $wp_manga_functions->get_manga_authors( $post_id );
			$artists            = $wp_manga_functions->get_manga_artists( $post_id );
			$genres             = $wp_manga_functions->get_manga_genres( $post_id );
            $tags             = $wp_manga_functions->get_manga_tags( $post_id );
			
			ob_start();

			?>

            <div id="manga-hover-<?php echo esc_attr( $post_id ) ?>" class="infor_items">
                <div class="infor_item__wrap">
                    <div class="item_thumb">
                        <div class="thumb_img">
							<?php
								if ( has_post_thumbnail( $post_id ) ) {
									?>
                                    <a href="<?php echo get_the_permalink( $post_id ); ?>" title="<?php echo get_the_title( $post_id ); ?>">
										<?php echo madara_thumbnail( $thumb_size, $post_id ); ?>
                                    </a>
									<?php
								}
							?>
                        </div>
                        <div class="post-title font-title">
                            <h5>
                                <a href="<?php echo get_the_permalink( $post_id ); ?>"><?php echo get_the_title( $post_id ); ?></a>
                            </h5>
                        </div>
                    </div>
                    <div class="item_content">
                        <div class="post-content">

                            <div class="meta-item rating">
								<?php
									$wp_manga_functions->manga_rating_display( $post_id );
								?>
                            </div>

                            <div class="post-content_item item_rank">
                                <div class="summary-heading">
                                    <h5>
										<?php echo esc_attr__( 'Rank', 'madara' ); ?>
                                    </h5>
                                </div>
                                <div class="summary-content">
									<?php $wp_manga_functions->print_ranking_views( $post_id ); ?>
                                </div>
                            </div>
							<?php if ( $alternative ): ?>
                                <div class="post-content_item item_alternative">
                                    <div class="summary-heading">
                                        <h5>
											<?php echo esc_attr__( 'Alternative', 'madara' ); ?>
                                        </h5>
                                    </div>
                                    <div class="summary-content">
										<?php echo wp_kses_post( $alternative ); ?>
                                    </div>
                                </div>
							<?php endif ?>

							<?php if ( $authors ): ?>
                                <div class="post-content_item item_authors">
                                    <div class="summary-heading">
                                        <h5>
											<?php echo esc_attr__( 'Author(s)', 'madara' ); ?>
                                        </h5>
                                    </div>
                                    <div class="summary-content">
										<?php echo wp_kses_post( $authors ); ?>
                                    </div>
                                </div>
							<?php endif ?>

							<?php if ( $artists ): ?>
                                <div class="post-content_item item_artists">
                                    <div class="summary-heading">
                                        <h5>
											<?php echo esc_attr__( 'Artist(s)', 'madara' ); ?>
                                        </h5>
                                    </div>
                                    <div class="summary-content">
                                        <div class="artist-content">
											<?php echo wp_kses_post( $artists ); ?>
                                        </div>
                                    </div>
                                </div>
							<?php endif ?>

							<?php if ( $genres ): ?>
                                <div class="post-content_item item_genres">
                                    <div class="summary-heading">
                                        <h5>
											<?php echo esc_attr__( 'Genre(s)', 'madara' ); ?>
                                        </h5>
                                    </div>
                                    <div class="summary-content">
                                        <div class="genres-content">
											<?php echo wp_kses_post( $genres ); ?>
                                        </div>
                                    </div>
                                </div>
							<?php endif ?>
                            
                            <?php if ( $tags ): ?>
                                <div class="post-content_item item_tags">
                                    <div class="summary-heading">
                                        <h5>
											<?php echo esc_attr__( 'Tag(s)', 'madara' ); ?>
                                        </h5>
                                    </div>
                                    <div class="summary-content">
                                        <div class="tags-content">
											<?php echo wp_kses_post( $tags ); ?>
                                        </div>
                                    </div>
                                </div>
							<?php endif ?>
                            
                            <?php 
                            
                            do_action('wp-manga-after-manga-properties', $post_id);?>

							<?php if ( $post_excerpt ): ?>
                                <div class="post-content_item item_summary">
                                    <div class="summary-heading">
                                        <h5>
											<?php echo esc_attr__( 'Summary', 'madara' ); ?>
                                        </h5>
                                    </div>
                                    <div class="summary-content">
										<?php echo wp_kses_post( $post_excerpt ); ?>
                                    </div>
                                </div>
							<?php endif ?>
                        </div>
                    </div>
                </div>
            </div>

			<?php

			$html = ob_get_contents();

			ob_end_clean();

			echo madara_filter_content($html);

		}

		die();

	}

	add_action( 'wp_ajax_nopriv_madara_hover_load_post', 'madara_hover_load_post' );
	add_action( 'wp_ajax_madara_hover_load_post', 'madara_hover_load_post' );

	add_filter( 'madara_thumbnail_size_filter', 'madara_thumbnail_size_filter', 10, 2 );
	function madara_thumbnail_size_filter( $size, $post_id ) {

		if ( has_post_thumbnail( $post_id ) ) {
			$thumb_url  = get_the_post_thumbnail_url( $post_id );
			$thumb_type = 'gif';
			if ( $thumb_url != '' ) {
				$type = substr( $thumb_url, - 3 );
			}
		}

		$allow_thumb_gif = Madara::getOption( 'manga_single_allow_thumb_gif', 'off' );

		if ( $allow_thumb_gif == 'on' && $thumb_type == $type ) {
			$size = 'full';
		}

		return $size;
	}

	function madara_page_reading_ajax() {

		$reading_style = isset( $_GET['style'] ) ? $_GET['style'] : madara_get_reading_style();

		if ( $reading_style == 'paged' ) {
			$ajax = Madara::getOption( 'manga_page_reading_ajax', 'on' );

			if ( $ajax == 'on' ) {
				return true;
			}
		}

		return false;

	}

	add_filter( 'pre_get_document_title', 'madara_manga_single_title' );
	function madara_manga_single_title( $title ) {
		global $post, $wp_manga_setting;
		$reading_chapter = function_exists('madara_permalink_reading_chapter') ? madara_permalink_reading_chapter() : false;
		if ( is_single() && isset( $post->post_type ) && $post->post_type == 'wp-manga' && $reading_chapter ) {

			$single_manga_seo = $wp_manga_setting->get_manga_option( 'single_manga_seo', 'manga' );
			$site_name        = get_bloginfo( 'name' );

			$chapter_name = $reading_chapter['chapter_name'];

            $c_name        = $reading_chapter['chapter_name'];
            $c_name_extend = $reading_chapter['chapter_name_extend'];

            $chapter_full_name = "{$c_name}" . ( !empty( $c_name_extend ) ? " - {$c_name_extend}" : "" );

            $chapter_title_seo = Madara::getOption( 'seo_chapter_title', null );
            $chapter_index = $reading_chapter['chapter_index'];
            
            $origin_title = get_the_title(); // manga title
            $site_title = $origin_title . ' - ' . $reading_chapter['chapter_name'];
            
            if( !empty( $chapter_title_seo ) ){
                $chapter_title = str_replace( '%chapter%', $chapter_full_name, $chapter_title_seo );
                
                $chapter_title = str_replace( '%title%', $origin_title, $chapter_title );
                $chapter_title = str_replace( '%chapter_index%', $chapter_index, $chapter_title);

                $site_title = $chapter_title;
            }
            
			if ( $single_manga_seo == 1 ) {
				$site_title .= ' - ' . $site_name;
			}

			return $site_title;
		}

		return $title;
	}

	function hide_meta_content_user_page() {

		if ( class_exists( 'WP_MANGA' ) ) {
			global $wp_manga_setting;

			$user_page = $wp_manga_setting->get_manga_option( 'user_page', null );

			if ( ! empty( $user_page ) && $user_page == get_the_ID() ) {
				?>
                <style>
                    .c-blog-post .entry-header .entry-meta {
                        display: none;
                    }
                </style>
				<?php
			}

		}
	}

	add_action( 'wp_head', 'hide_meta_content_user_page' );

	/**
	 * User History helper functions
	 */

	function madara_put_user_history_manga( $args ) {

		if ( ! isset( $args['post_id'] ) || ! isset( $args['chapter_slug'] ) || ! isset( $args['paged'] ) || ! isset( $args['img_id'] ) || ! isset( $args['user_id'] ) ) {
			return null;
		}

		extract( $args );

		if ( class_exists( 'WP_MANGA' ) ) {
			$chapter = madara_get_global_wp_manga_chapter()->get_chapter_by_slug( $post_id, $chapter_slug );
		} else {
			return null;
		}

		$this_history = array(
			'id' => $post_id,
			'c'  => $chapter['chapter_id'],
			'p'  => $paged,
			'i'  => $img_id,
			't'  => current_time( 'timestamp' ),
		);

		do_action( 'manga_history', $user_id, $this_history );

		$current_history = get_user_meta( $user_id, '_wp_manga_history', true );

		if ( empty( $current_history ) ) {

			$current_history = array( $this_history );

		} elseif ( is_array( $current_history ) ) { //if history already existed

			$number = intval( Madara::getOption( 'madara_reading_history_items', 12 ) );

			if($number != -1){
				//if there are more than the maximum of numbers manga in history
				if ( count( $current_history ) > $number ) {
					// order then cut the history array
					$current_history = madara_history_order( $current_history );
					$current_history = array_slice( $current_history, 0, $number );
				}
			}

			//check if current chapter is existed
			$index = array_search( $post_id, array_column( $current_history, 'id' ) );
			if ( $index !== false ) {
				$current_history[ $index ] = $this_history;
			} else {
				$current_history[] = $this_history;
			}
		}

		return update_user_meta( $user_id, '_wp_manga_history', $current_history );

	}
	
	// return chapter ID of current reading manga
	function madara_get_current_reading_chapter($user_id, $manga_id){
		$history_manga = get_user_meta( $user_id, '_wp_manga_history', true );

		if ( empty( $history_manga ) || ! is_array( $history_manga ) ) {
			return false;
		}

		$chapter_id = 0;

		// make sure that retrieve manga is still exists
		foreach( $history_manga as $index => $manga ){

			$unset = false;

			if ( isset( $manga['id'] ) && $manga['id'] == $manga_id ) {
				$chapter_id = $manga;
				break;
			}
		}

		return $chapter_id;
	}

	function madara_get_user_history_manga( $user_id = null ) {

		if ( empty( $user_id ) ) {
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
			} else {
				return null;
			}
		}

		$history_manga = get_user_meta( $user_id, '_wp_manga_history', true );

		if ( empty( $history_manga ) || ! is_array( $history_manga ) ) {
			return false;
		}

		$history_manga = madara_history_order( $history_manga );

		$output = array();

		// Check on number of history items
		$number       = intval( Madara::getOption( 'madara_reading_history_items', 12 ) );
		$check_number = 0;

		// make sure that retrieve manga is still exists
		foreach( $history_manga as $index => $manga ){

			if( $check_number === $number ){ //If there is enough items then break the loop
				$needs_update = true;
				break;
			}

			$unset = false;

			if ( ! isset( $manga['id'] ) ) {
				$unset = true;
			} else {
				$manga_status = get_post_status( $manga['id'] );

				if ( empty( $manga_status ) ) { //post doesn't exist
					$unset = true;
				} elseif ( $manga_status == 'publish' ) { //make sure that manga has published status
					$output[ $manga['id'] ] = $manga;
				}
			}

			if ( $unset ) {
				unset( $history_manga[ $index ] );
				$needs_update = true;
			}else{
				$check_number++;
			}

		}

		if( !empty( $needs_update ) ){
			update_user_meta( $user_id, '_wp_manga_history', $history_manga );
		}

		return $output;

	}

	function madara_history_order( $history_manga ) {
		uasort( $history_manga, function ( $a, $b ) {
			if ( $a['t'] == $b['t'] ) {
				return 0;
			}

			return $a['t'] > $b['t'] ? - 1 : 1;
		} );

		return $history_manga;
	}


	function madara_manga_meta_tags( $origin_title = null, $origin_desc = null, $site_name = null ){
        $title = $origin_title;
        $description = $origin_desc;
        $url = get_permalink();
        
		if( is_single() ){

			$madara_echo_tags = Madara::getOption( 'echo_meta_tags', 'on' ) == 'on' ? true : false;

			global $post;

			if( $origin_title == null ){
				$origin_title = get_the_title();
			}

			if( $site_name == null ){
				$site_name = get_bloginfo( 'name' );
			}

			if( class_exists('WP_MANGA') && is_manga_reading_page() ){

				global $wp_manga_chapter, $wp_manga_setting, $wp_manga_functions;

				$seo = $wp_manga_setting->get_manga_option('single_manga_seo', 'manga');

				$c_slug        = get_query_var('chapter');
				$chapter       = $wp_manga_functions->get_chapter_by_slug( $post->ID, $c_slug );
				$c_name        = $chapter['chapter_name'];
				$c_name_extend = $chapter['chapter_name_extend'];

				$chapter_full_name = "{$c_name}" . ( !empty( $c_name_extend ) ? " - {$c_name_extend}" : "" );

				$chapter_summary = wp_trim_words($post->post_content, 55);

				$chapter_type = get_post_meta( $post->ID, '_wp_manga_chapter_type', true );

				if($chapter_type == 'text') {
					$chapter_content = new WP_Query( array(
						'post_parent' => $chapter['chapter_id'],
						'post_type'   => 'chapter_text_content'
					) );

					if ( $chapter_content->have_posts() ) {

						$chapter_content->the_post();

						$chapter_summary = wp_trim_words( get_the_content(), 55);

						wp_reset_postdata();
					}
				}

				$chapter_title = Madara::getOption( 'seo_chapter_title', null );
				$chapter_desc  = (isset( $chapter['chapter_seo'] ) && trim($chapter['chapter_seo']) != '') ? $chapter['chapter_seo'] : Madara::getOption( 'seo_chapter_desc', null );
                $chapter_index = $chapter['chapter_index'];

				if( !empty( $chapter_title ) ){
					$chapter_title = str_replace( '%chapter%', $chapter_full_name, $chapter_title );
					$chapter_title = str_replace( '%title%', $origin_title, $chapter_title );
                    $chapter_title = str_replace( '%chapter_index%', $chapter_index, $chapter_title);

					$manga_title = $chapter_title;
				}elseif( $madara_echo_tags && $origin_title ){
					$manga_title = "{$origin_title} - {$chapter_full_name}";
				}else{
					$manga_title = null;
				}

				if( !empty( $chapter_desc ) ){
					$chapter_desc = str_replace( '%chapter%', $chapter_full_name, $chapter_desc );
					$chapter_desc = str_replace( '%title%', $origin_title, $chapter_desc );
                    $chapter_desc = str_replace( '%chapter_index%', $chapter_index, $chapter_desc);
					$chapter_desc = str_replace ( '%summary%', $chapter_summary, $chapter_desc);

					$description = $chapter_desc;
				}elseif( $madara_echo_tags && $origin_desc ){
					$description = "{$origin_title}. $chapter_full_name. {$origin_desc}";
				}else{
					$description = null;
				}

				$keywords = "{$origin_title} {$c_name}" . ( !empty( $c_name_extend ) ? ", {$c_name_extend}" : "" );

				if ( !empty( $seo ) && !empty( $manga_title ) ) {
					$manga_title .= " - {$site_name}";
					$keywords .= ", {$site_name}";
				}

				$title = $manga_title;
				$url   = $wp_manga_functions->build_chapter_url( $post->ID, $c_slug );

			} elseif( function_exists('is_manga_single') && is_manga_single() ){

				$custom_meta_title = get_post_meta( get_the_ID(), 'manga_meta_title', true );

				if( !empty( $custom_meta_title ) ){
					$manga_title = $custom_meta_title;
				}else{
					$manga_title = Madara::getOption( 'seo_manga_title', null );
				}

				if( !empty( $manga_title ) ){
					$manga_title = str_replace( '%title%', $origin_title, $manga_title );
					$title = $manga_title;
				}

				$custom_meta_desc = get_post_meta( get_the_ID(), 'manga_meta_desc', true );

				if( !empty( $custom_meta_desc ) ){
					$manga_desc = $custom_meta_desc;
				}else{
					$manga_desc  = Madara::getOption( 'seo_manga_desc', null );
				}

				if( !empty( $manga_desc ) ){
					$manga_desc = str_replace( '%title%', $origin_title, $manga_desc );
					$description = $manga_desc;
				}elseif( $origin_desc ){
					$description = $origin_desc;
				}else{
					$description = '';
				}

			}
		}
        
        $title = apply_filters('madara_meta_tag_title', $title);

		if( ! isset( $title ) ){
			$title = null;
		}
        
        $description = apply_filters('madara_meta_tag_description', $description);

		if( ! isset( $description ) ){
			$description = null;
		}
        
        $url = apply_filters('madara_meta_tag_url', $url);

		if( ! isset( $url ) ){
			$url = null;
		}

		return apply_filters('madara_seo_metas', compact( 'title', 'description', 'url' ));
	}

	/*
	* Supports Yoast SEO
	*/
	function madara_manga_yoast_filters(){

		if( function_exists( 'wpseo_auto_load' ) ){

			extract( madara_manga_meta_tags() );

			if( !empty( $title ) ){
				add_filter( 'wpseo_title', function( $yoast_title ) use( $title ){
					return $title;
				} );
			}

			if( !empty( $description ) ){
				add_filter( 'wpseo_metadesc', function( $yoast_desc ) use ( $description ){
					return $description;
				} );
			}

			if( !empty( $url ) ){
				add_filter( 'wpseo_opengraph_url', function( $yoast_url ) use( $url ){
					return $url;
				} );
			}

		}

	}
	add_action( 'wp', 'madara_manga_yoast_filters' );

	add_filter( 'madara_archive_chapter_date', 'madara_filter_archive_chapter_date', 10, 4 );
	function madara_filter_archive_chapter_date( $date, $chapter_id, $chapter_date, $chapter_link ) {

		$manga_new_chapter = Madara::getOption( 'manga_new_chapter', 'off' );

		if ( $manga_new_chapter == 'off' || ( $chapter_id == '' || $chapter_date == '' ) ) {
			return $date;
		}

		$wp_manga_functions = madara_get_global_wp_manga_functions();
		$chapter_time_range = intval( Madara::getOption( 'manga_new_chapter_time_range', 3 ) );
		$current_time       = current_time( 'timestamp' );
		$day_time           = 86400;
		$time_range         = $chapter_time_range * $day_time;
		$time_diff          = $wp_manga_functions->get_time_diff( $chapter_date );
		$chapter_date       = strtotime( $chapter_date );

		if ( ( $chapter_date + $time_range ) >= $current_time ) {
			$date = apply_filters( 'madara_new_chapter_tag', '<a href="' . esc_url( $chapter_link ) . '" title="' . esc_attr( $time_diff ) . '" class="c-new-tag"><!-- --></a>', $date, $chapter_id, $chapter_date, $chapter_link, $time_diff );
		}

		return $date;

	}
	
	add_action('init', 'madara_init_hooks');
	function madara_init_hooks(){
		if(Madara::getOption('manga_detail_lazy_chapters', 'on') == 'off'){
			remove_action('wp-manga-chapter-listing', 'wp_manga_single_manga_info_chapters');
			add_action('wp-manga-chapter-listing', 'madara_single_manga_info_chapters');
			
			remove_action('wp-manga-reading-chapters-selectbox', 'wp_manga_reading_chapters_selectbox', 10, 3);
			add_action('wp-manga-reading-chapters-selectbox', 'madara_reading_chapters_selectbox', 10, 3);
		}
	}
	
	// load Chapters List of a Manga, and echo
	function madara_single_manga_info_chapters( $manga_id ){
		global $wp_manga_functions, $wp_manga_database;
		
		$sort_option = $wp_manga_database->get_sort_setting();
		
		$manga = $wp_manga_functions->get_all_chapters( $manga_id, $sort_option['sort'] );
		
		$current_read_chapter = 0;
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$history = madara_get_current_reading_chapter($user_id, $manga_id);
			if($history){
				$current_read_chapter = $history['c'];
			}
		}
		
		global $wp_manga_template;
		include $wp_manga_template->load_template('single/info','chapters', false);
	}
	
	function madara_reading_chapters_selectbox($cur_chap, $all_chaps, $manga_type){
        if(!$cur_chap) return;
        
		if( !empty( $_GET['style'] ) ){
			$style = $_GET['style'];
		} else {
			global $wp_manga_functions;
			$style = $wp_manga_functions->get_reading_style();
		}
		
		if($manga_type == 'manga'){
			?>
			<div class="chapters_selectbox_holder" data-manga="<?php echo esc_attr($cur_chap['post_id']);?>" data-chapter="<?php echo esc_attr($cur_chap['chapter_slug']);?>" data-vol="<?php echo esc_attr($cur_chap['volume_id']);?>" data-type="manga" data-style="<?php echo esc_attr($style);?>">
				<!-- place holder -->
				<?php
				global $wp_manga_chapter_type;
                
				$wp_manga_chapter_type->reading_chapters_nav($cur_chap['post_id'], $all_chaps, $cur_chap['volume_id'], $cur_chap['chapter_slug'], $style);
				?>
			</div>
			<?php
		} else { ?>
			<div class="c-selectpicker selectpicker_chapter chapters_selectbox_holder" data-manga="<?php echo esc_attr($cur_chap['post_id']);?>" data-chapter="<?php echo esc_attr($cur_chap['chapter_slug']);?>" data-vol="<?php echo esc_attr($cur_chap['volume_id']);?>" data-type="content">
				<?php
				global $wp_manga_text_type;
				$wp_manga_text_type->reading_chapters_nav($cur_chap['post_id'], $all_chaps, $cur_chap['volume_id'], $cur_chap['chapter_slug']);
				?>
			</div>
		<?php 
		}
	}
	
	add_action('wp_manga_before_manga_discussion', 'madara_before_manga_discussion');
	function madara_before_manga_discussion(){
		$reading_chapter = function_exists('madara_permalink_reading_chapter') ? madara_permalink_reading_chapter() : false;
		$chapter_comments_heading = Madara::getOption('manga_reading_discussion_heading', 'on');
		if($reading_chapter && $chapter_comments_heading == 'on'){
		?>
		<h4><?php echo sprintf(esc_html__('Comments for chapter "%s"', 'madara'), $reading_chapter['chapter_name']);?></h4>
		<?php
		}
	}
