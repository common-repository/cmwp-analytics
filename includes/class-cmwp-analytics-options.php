<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * The CMWP_Analytics_Options class handles the teaser shortcodes.
 * You can add custom shortcodes with cmwp_add_teaser_shortcode().
 *
 * @package cmwp-analytics
 * @author conversionmedia Gmbh & Co. KG
 * @copyright 2017
 * @version 0.1
 */
class CMWP_Analytics_Options {

	/**
	 * @var array
	 */
	private $options;

	/**
	 * @var string
	 */
	public static $options_key = 'cmwp_analytics';

	/**
	 * @var string
	 */
	public static $page_slug = 'cmwp-analytics';

	/**
	 * CMWP_Analytics_Options Constructor.
	 */
	public function __construct() {
		$this->init_hooks();

		do_action( 'cmwp_analytics_options_loaded' );
	}

	/**
	 * Hook into actions and filters
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'load_options' ) );

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Load plugin options
	 */
	public function load_options() {
		// Load user options
		$options = get_option( static::$options_key, array() );

		// Default options
		$defaults = array(
			'tracking_id' => '',
			'tracking_code' => 1,
			'linker' => 0,
			'autolink' => null,
			'anonymizeip' => 1,
			'gaoptout' => 1,
		);

		// Overwrite default options with user options
		$this->options = wp_parse_args( $options, $defaults );
	}

	/**
	 * Add plugin options page
	 */
	public function admin_menu() {
		add_options_page(
			'CMWP Analytics',
			'CMWP Analytics',
			'manage_options',
			static::$page_slug,
			array($this, 'create_page')
		);
	}

	/**
	 * Load options page template
	 */
	public function create_page() {
		$options_group = static::$options_key . '_group';
		include( CMWP_ANALYTICS_TEMPLATES . '/admin/options-page.php' );
	}

	/**
	 * Define options page and options
	 */
	public function admin_init() {
		register_setting(
			static::$options_key.'_group',
			static::$options_key,
			array( $this, 'sanitize')
		);

		add_settings_section(
			'cmwp_analytics',
			'Google Analytics Universal',
			array($this, 'print_info'),
			'cmwp_analytics'
		);
		add_settings_field(
			'tracking_id',
			'Tracking-ID',
			array($this, 'print_tracking_id_field'), 
			'cmwp_analytics',
			'cmwp_analytics'
		);
		add_settings_field(
			'tracking_code',
			'Tracking Code aktivieren',
			array($this, 'print_tracking_code_field'), 
			'cmwp_analytics',
			'cmwp_analytics'
		);
		add_settings_field(
			'linker',
			'Cross-domain Tracking',
			array($this, 'print_linker_field'), 
			'cmwp_analytics',
			'cmwp_analytics'
		);
		add_settings_field(
			'autolink',
			'Cross-domain Liste',
			array($this, 'print_autolink_field'), 
			'cmwp_analytics',
			'cmwp_analytics'
		);
		add_settings_field(
			'anonymizeip',
			'IP-Anonymisierung',
			array($this, 'print_anonymizeip_field'), 
			'cmwp_analytics',
			'cmwp_analytics'
		);
		add_settings_field(
			'gaoptout',
			'Opt-Out Funktion',
			array($this, 'print_gaoptout_field'), 
			'cmwp_analytics',
			'cmwp_analytics'
		);

	}


	/**
	 * Print Options Fields
	 */
	public function print_info() {
		print '<p>Um den Tracking Code zu aktivieren, müssen Sie nur Ihre Tracking-ID eingeben.<p>';
		print '<p><u>Hinweis:</u> In Deutschland müssen Sie Ihre Besucher in den Datenschutzerklärungen über den Einsatz von Google Analytics Universal aufklären und ihnen dort die Möglichkeit bieten, dass sie das Tracking für sich deaktiveren können (Opt-Out). Einen Aufruf der Opt-Out Funktion können Sie mit dem Shortcode <strong>[cmwp-analytics-opt-out]</strong>, an entsprechender Stelle in Ihren Datenschutzerklärungen einbinden.<p>';
	}
	public function print_tracking_id_field( $args ) {
		printf(
			'<input type="text" id="tracking_id" name="%s[tracking_id]" value="%s" class="regular-text" />'.
			'<p class="description">Ihre Tracking-ID finden Sie in der Verwaltung ihres Google Analytics Kontos bei den "Property-Einstellungen".<br> Die Tracking ID hat folgendes Format: UA-xxxxxxxx-x</p>',
			static::$options_key,
			esc_attr( $this->options['tracking_id'] )
		);
	}
	public function print_tracking_code_field( $args ) {
		printf(
			'<input type="checkbox" id="tracking_code" name="%s[tracking_code]" value="1" %s />'.
			'<p for="pi_activation" class="description">Entfernen Sie den Haken, falls Sie die Ausgabe des Tracking Code vorübergehend deaktivieren wollen.</p>',
			static::$options_key,
			checked( $this->options['tracking_code'], 1, false )
		);
	}
	public function print_linker_field( $args ) {
		printf(
			'<input type="checkbox" id="linker" name="%s[linker]" value="1" %s />'.
			'<p for="linker_activation" class="description">Aktivieren Sie das Cross-domain Tracking.<br> Sie müssen dazu unten jedoch noch Ihre Domains angeben.</p>',
			static::$options_key,
			checked( $this->options['linker'], 1, false )
		);
	}
	public function print_autolink_field( $args ) {
		printf(
			'<textarea cols="50" rows="10" id="autolink" name="%s[autolink]">%s</textarea>'.
			'<p class="description">Geben Sie jeweils eine Domain pro Zeile für das Cross-domain Tracking ein.<br> Es werden nur die Hauptdomains benötigt, kein "www." und keine Subdomains.<br> Die Domains werden nur bei <u>aktivierter</u> Cross-domain Tracking Option verarbeitet.</p>',
			static::$options_key,
			esc_attr( $this->options['autolink'] )
		);
	}
	public function print_anonymizeip_field( $args ) {
		printf(
			'<input type="checkbox" id="anonymizeip" name="%s[anonymizeip]" value="1" %s />'.
			'<p for="ga_deactivation" class="description">In Deutschland ist für einen datenschutzkonformen Einsatz erforderlich, dass Sie Google anweisen die IP-Adressen Ihrer Besucher zu anonymisieren (anonymizeIp).<br> Standardmäßig wird die IP-Anonymisierung daher mit dem Tracking Code aktiviert.<br> Wenn Sie dies Funktion nicht wünschen, können Sie den Haken entfernen. </p>',
			static::$options_key,
			checked( $this->options['anonymizeip'], 1, false )
		);
	}
	public function print_gaoptout_field( $args ) {
		printf(
			'<input type="checkbox" id="gaoptout" name="%s[gaoptout]" value="1" %s />'.
			'<p for="ga_deactivation" class="description">In Deutschland ist für einen Datenschutzkonformen Einsatz erforderlich, dass Sie Ihren Besuchern eine Opt-Out Funktion anbieten.<br> Standardmäßig wird die Opt-Out Funktion daher mit dem Tracking Code ausgeliefert.<br> Wenn Sie dies Funktion nicht benötigten, können Sie den Haken entfernen.</p>',
			static::$options_key,
			checked( $this->options['gaoptout'], 1, false )
		);
	}


	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 *
	 */
	public function sanitize( $input ) {
		$new_input = array();

		// Default options
		$defaults = array(
			'tracking_id' => null,
			'tracking_code' => null,
			'linker' => null,
			'autolink' => null,
			'anonymizeip' => null,
			'gaoptout' => null,
		);

		// Overwrite default options with user options
		$this->options = wp_parse_args( $input, $defaults );

		// Sanitize user inputs
		$new_input['tracking_id'] = sanitize_text_field( $input['tracking_id'] );
		$new_input['tracking_code'] = intval( $input['tracking_code'] );
		$new_input['linker'] = intval( $input['linker'] );
		$new_input['autolink'] = sanitize_textarea_field( $input['autolink'] );
		$new_input['anonymizeip'] = intval( $input['anonymizeip'] );
		$new_input['gaoptout'] = intval( $input['gaoptout'] );

		// return sanitized input for saving
		return $new_input;
	}

}
