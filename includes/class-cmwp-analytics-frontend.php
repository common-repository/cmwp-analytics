<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * The CMWP_Analytics_Frontend class handles the teaser shortcodes.
 * You can add custom shortcodes with cmwp_add_teaser_shortcode().
 *
 * @package cmwp-analytics
 * @author conversionmedia Gmbh & Co. KG
 * @copyright 2017
 * @version 0.1
 */
class CMWP_Analytics_Frontend {

	/**
	 * @var array
	 */
	private $options;

	/**
	 * @var string
	 */
	public static $options_key = 'cmwp_analytics';

	/**
	 * CMWP_Analytics_Options Constructor.
	 */
	public function __construct() {
		$this->init_hooks();

		do_action( 'cmwp_analytics_frontend_loaded' );
	}

	/**
	 * Hook into actions and filters
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'load_options' ) );
		add_action( 'init', array( $this, 'init' ) );

		add_shortcode( 'cmwp-analytics-opt-out', array($this, 'shortcode_gaoptout' ) );
	}

	public function load_options() {
		$this->options = get_option( static::$options_key );
	}

	public function init() {
		add_action( 'wp_head', array( $this, 'output' ) );
	}

	public function output() {
		$this->output_no_tracking_id();
		$this->output_no_tracking_code();
		$this->output_tracking_code();
	}

	public function output_no_tracking_id() {
		if ( ! empty( $this->options['tracking_id'] ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		printf( "<!-- CMWP Analytics: Es wurde noch keine Google Analytics Tracking-ID eingeben! (UA-xxxxxxxx-x) -->\n" );
	}

	public function output_no_tracking_code() {
		if ( ! empty( $this->options['tracking_code'] ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		printf( "<!-- CMWP Analytics: Die Ausgabe des Google Analytics Tracking Code wurde in den Einstellungen deaktiviert! -->\n" );
	}

	public function output_tracking_code() {
		if ( empty( $this->options['tracking_id'] ) ) {
			return;
		}
		if ( empty( $this->options['tracking_code'] ) ) {
			return;
		}

		extract( $this->options );

		$autolink = trim( $autolink );
		$autolink = str_replace( ',', "\n", $autolink );
		$autolink = str_replace( ';', "\n", $autolink );
		$autolink = str_replace( "\r", "", $autolink );
		$autolink = explode( "\n", $autolink );
		$autolink = array_unique( $autolink );
		$autolink = array_filter( $autolink );

		if( empty( $autolink ) ) {
			$linker = 0;
		}
		if ( $linker ) {
			$autolink = implode( "', '", $autolink );
			$autolink = "['" . $autolink . "']";
		}

		include( CMWP_ANALYTICS_TEMPLATES . '/tracking-snippet/google-analytics-universal.php');
	}

	/**
	 * Shortcode for Opt-Out Link
	 * @return string
	 */
	public function shortcode_gaoptout( $atts ) {
		if ( $this->options['gaoptout'] ) {
			return '<a href="javascript:gaOptOut();">Google Analytics deaktivieren</a>';
		}

		if ( current_user_can( 'manage_options' ) ) {
			return '<!-- Google Analytics OptOut ist deaktivert! -->';
		}

		return '';
	}

}
