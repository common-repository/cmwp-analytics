<?php

/**
 * Plugin Name: CMWP-Analytics
 * Plugin URI: https://wordpress.org/plugins/cmwp-analytics/
 * Description: Google Analytics Univsersal integration
 * Version: 0.4
 * Author: conversionmedia GmbH & Co. KG
 * Author URI: http://www.conversionmedia.de
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 4.7
 * Tested up to: 4.8.3
 *
 * @package cmwp-analytics
 * @category core
 * @author conversionmedia GmbH & Co. KG
 * @copyright 2017
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main CMWP_Analytics Class
 *
 * @class CMWP_Analytics
 * @version	0.3
 */
final class CMWP_Analytics {

	/**
	 * @var string
	 */
	public $version = '0.1';

	/**
	 * @var CMWP_Analytics The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * @var string
	 */
	public static $options_key = 'cmwp-analytics';

	/**
	 * Main CMWP_Analytics Instance
	 *
	 * Ensures only one instance of CMWP_Analytics is loaded or can be loaded.
	 *
	 * @static
	 * @see WC()
	 * @return CMWP_Analytics - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * CMWP_Analytics Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		// Load class instances
		$this->options = new CMWP_Analytics_Options;
		$this->frontend = new CMWP_Analytics_Frontend;

		do_action( 'cmwp_analytics_loaded' );
	}

	/**
	 * Define CM Teaser Constants
	 */
	private function define_constants() {
		$this->define( 'CMWP_ANALYTICS_PLUGIN_FILE', __FILE__ );
		$this->define( 'CMWP_ANALYTICS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

		$this->define( 'CMWP_ANALYTICS_DIR', plugin_dir_path( __FILE__ ) );
		$this->define( 'CMWP_ANALYTICS_URL', plugin_dir_url( __FILE__ ) );

		$this->define( 'CMWP_ANALYTICS_TEMPLATES', CMWP_ANALYTICS_DIR . '/templates' );

		$this->define( 'CMWP_ANALYTICS_VERSION', $this->version );
	}

	/**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		include_once( 'includes/class-cmwp-analytics-options.php' );
		include_once( 'includes/class-cmwp-analytics-frontend.php' );
	}

	/**
	 * Hook into actions and filters
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Init WooCommerce when WordPress Initialises.
	 */
	public function init() {
		// Before init action
		do_action( 'before_cmwp_analytics_init' );

		// Set up localisation
		$this->load_plugin_textdomain();

		// Init action
		do_action( 'cmwp_analytics_init' );
	}



	public function admin_init() {
		// Before init action
		do_action( 'before_cmwp_analytics_admin_init' );

		// Init action
		do_action( 'cmwp_analytics_init' );
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Frontend/global Locales found in:
	 * 	 	- WP_LANG_DIR/plugins/cmwp-teaser-LOCALE.mo
	 * 	 	- cmwp-teaser/i18n/languages/cmwp-teaser-LOCALE.mo (which if not found falls back to:)
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'cmwp-teaser' );

		load_textdomain( 'cmwp_analytics', WP_LANG_DIR . '/plugins/cmwp-teaser-' . $locale . '.mo' );
		load_plugin_textdomain( 'cmwp_analytics', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n/languages" );
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}


/**
 * Returns the main instance of CMWP_Analytics to prevent the need to use globals.
 *
 * @return WooCommerce
 */
function CMWP_Analytics() {
	return CMWP_Analytics::instance();
}

// Global for backwards compatibility.
$GLOBALS['CMWP_Analytics'] = CMWP_Analytics();
