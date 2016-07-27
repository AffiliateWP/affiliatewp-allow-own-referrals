<?php
/**
 * Plugin Name: AffiliateWP - Allow Own Referrals
 * Plugin URI: https://affiliatewp.com/addons/allow-own-referrals
 * Description: Allows an affiliate to earn commission on their own referrals
 * Author: AffiliateWP, LLC
 * Author URI: https://affiliatewp.com
 * Version: 1.0.2
 * Domain Path: languages
 */

add_filter( 'affwp_is_customer_email_affiliate_email', '__return_false' );

function affwp_allow_own_referrals_tracking_override( $ret, $affiliate_id ) {

	if ( 'active' === affwp_get_affiliate_status( $affiliate_id ) ) {
		$ret = true;
	}

	return $ret;
}
add_filter( 'affwp_tracking_is_valid_affiliate', 'affwp_allow_own_referrals_tracking_override', 10, 2 );
