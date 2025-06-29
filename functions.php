<?php
	/**
	 * Madara Functions and Definitions
	 *
	 * @package madara
	 */
	require( get_template_directory() . '/app/theme.php' );
	update_site_option( 'madara_supported_until', date('M d, Y', strtotime('+1 years')) );
	update_site_option( 'madara_purchase_code' ,'e2eb9ef2-bc34-8ed2-39b4-ad59974c6f51');
	update_site_option( 'madara_activated', 'yes' );