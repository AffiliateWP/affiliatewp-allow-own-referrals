<?php
/**
 * Allow Own Referrals Base Class
 *
 * @package AffiliateWP\AffiliateWP_Allow_Own_Referrals
 * @since   1.0.0
 */

if ( ! class_exists( 'AffiliateWP_Allow_Own_Referrals' ) ) {

	/**
	 * Setup class.
	 *
	 * @since 1.0.3
	 * @final
	 */
	final class AffiliateWP_Allow_Own_Referrals {

		/**
		 * Holds the instance.
		 *
		 * Ensures that only one instance of the plugin bootstrap exists in memory at any
		 * one time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @since 1.0.3
		 * @var   \AffiliateWP_Allow_Own_Referrals
		 * @static
		 */
		private static $instance;

		/**
		 * Plugin loader file.
		 *
		 * @since 1.0.3
		 * @var   string
		 */
		private $file = '';

		/**
		 * The version number.
		 *
		 * @since 1.0.3
		 * @var    string
		 */
		private $version = '1.0.3';

		/**
		 * Generates the main bootstrap instance.
		 *
		 * Insures that only one instance of bootstrap exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 *
		 * @param string $file Path to the main plugin file.
		 * @return \AffiliateWP_Allow_Own_Referrals The one true bootstrap instance.
		 */
		public static function instance( $file = '' ) {
			// Return if already instantiated.
			if ( self::is_instantiated() ) {
				return self::$instance;
			}

			// Setup the singleton.
			self::setup_instance( $file );

			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->hooks();

			return self::$instance;
		}

		/**
		 * Setup the singleton instance
		 *
		 * @since 1.0.3
		 *
		 * @param string $file File path to the main plugin file.
		 */
		private static function setup_instance( $file ) {
			self::$instance       = new AffiliateWP_Allow_Own_Referrals;
			self::$instance->file = $file;
		}

		/**
		 * Return whether the main loading class has been instantiated or not.
		 *
		 * @since 1.0.3
		 *
		 * @return bool True if instantiated. False if not.
		 */
		private static function is_instantiated() {

			// Return true if instance is correct class
			if ( ! empty( self::$instance ) && ( self::$instance instanceof AffiliateWP_Allow_Own_Referrals ) ) {
				return true;
			}

			// Return false if not instantiated correctly.
			return false;
		}

		/**
		 * Throws an error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0.3
		 *
		 * @return void
		 */
		protected function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-allow-own-referrals' ), '1.0' );
		}

		/**
		 * Disables unserialization of the class.
		 *
		 * @since 1.0.3
		 *
		 * @return void
		 */
		protected function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-allow-own-referrals' ), '1.0' );
		}

		/**
		 * Sets up the class.
		 *
		 * @since 1.0.3
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Resets the instance of the class.
		 *
		 * @since 1.0.3
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Sets up plugin constants.
		 *
		 * @since 1.0.3
		 *
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version.
			if ( ! defined( 'AFFWP_AOR_VERSION' ) ) {
				define( 'AFFWP_AOR_VERSION', $this->version );
			}

			// Plugin Folder Path.
			if ( ! defined( 'AFFWP_AOR_PLUGIN_DIR' ) ) {
				define( 'AFFWP_AOR_PLUGIN_DIR', plugin_dir_path( $this->file ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'AFFWP_AOR_PLUGIN_URL' ) ) {
				define( 'AFFWP_AOR_PLUGIN_URL', plugin_dir_url( $this->file ) );
			}

			// Plugin Root File.
			if ( ! defined( 'AFFWP_AOR_PLUGIN_FILE' ) ) {
				define( 'AFFWP_AOR_PLUGIN_FILE', $this->file );
			}
		}

		/**
		 * Includes necessary files.
		 *
		 * @since 1.0.3
		 *
		 * @return void
		 */
		private function includes() {
			// Bring in the autoloader.
			require_once trailingslashit( dirname( $this->file ) ) . 'includes/lib/autoload.php';

			// require_once AFFWP_AOR_PLUGIN_DIR . 'includes/file-name.php';
		}

		/**
		 * Sets up the default hooks and actions.
		 *
		 * @since 1.0.3
		 *
		 * @return void
		 */
		private function hooks() {
			// Plugin meta.
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );
			add_filter( 'affwp_is_customer_email_affiliate_email', '__return_false' );
			add_filter( 'affwp_tracking_is_valid_affiliate', [ $this, 'tracking_override' ], 10, 2 );
		}

		/**
		 * Overrides valid affiliate tracking to make it possible for affiliates to refer themselves.
		 *
		 * @since 1.0.3
		 *
		 * @param bool $valid        The current filtered status of this affiliate's validity.
		 * @param int  $affiliate_id The affiliate ID to check against.
		 * @return bool True if the affiliate is valid, otherwise false.
		 */
		public function tracking_override( $valid, $affiliate_id ) {

			if ( 'active' === affwp_get_affiliate_status( $affiliate_id ) ) {
				$valid = true;
			}

			return $valid;
		}

		/**
		 * Modifies the plugin list table meta links.
		 *
		 * @since 1.0.3
		 *
		 * @param array  $links The current links array.
		 * @param string $file  A specific plugin table entry.
		 * @return array The modified links array.
		 */
		public function plugin_meta( $links, $file ) {

			if ( $file == plugin_basename( $this->file ) ) {

				$url = admin_url( 'admin.php?page=affiliate-wp-add-ons' );

				$plugins_link = array( '<a alt="' . esc_attr__( 'Get more add-ons for AffiliateWP', 'affiliatewp-allow-own-referrals' ) . '" href="' . esc_url( $url ) . '">' . __( 'More add-ons', 'affiliatewp-allow-own-referrals' ) . '</a>' );

				$links = array_merge( $links, $plugins_link );
			}

			return $links;

		}
	}
}

/**
 * The main function responsible for returning the one true bootstrap instance
 * to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $affiliatewp_allow_own_referrals = affiliatewp_allow_own_referrals(); ?>
 *
 * @since 1.0.3
 *
 * @return \AffiliateWP_Allow_Own_Referrals The one true bootstrap instance.
 */
function affiliatewp_allow_own_referrals() {
	if ( ! class_exists( 'Affiliate_WP' ) ) {

		if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
			require_once 'includes/class-activation.php';
		}

		$activation = new AffiliateWP_Activation( plugin_dir_path( $this->file ), basename( $this->file ) );
		$activation = $activation->run();

	} else {

		return AffiliateWP_Allow_Own_Referrals::instance();

	}
}

add_action( 'plugins_loaded', 'affiliatewp_allow_own_referrals', 100 );

