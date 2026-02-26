<?php

namespace ISC\Pro\Admin;

/**
 * Handle add-on licenses
 */
class License {
	/**
	 * Instance of this class.
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Store URL
	 *
	 * @var string
	 */
	protected $store_url = null;

	/**
	 * Customer account URL
	 *
	 * @var string
	 */
	protected $account_url = null;

	/**
	 * License key
	 *
	 * @var string
	 */
	protected $license_key = null;

	/**
	 * ISC_Pro_Admin_License constructor.
	 */
	public function __construct() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			add_action( 'load-plugins.php', [ $this, 'check_plugin_licenses' ] );
		}
		add_action( 'plugins_loaded', [ $this, 'wp_plugins_loaded' ] );

		// callbacks for AJAX requests
		add_action( 'wp_ajax_isc-activate-license', [ $this, 'activate_license_ajax' ] );
		add_action( 'wp_ajax_isc-deactivate-license', [ $this, 'deactivate_license_ajax' ] );
	}

	/**
	 * Get store URL
	 *
	 * @param string $license_key license key.
	 * @return string store URL
	 */
	private function get_store_url( $license_key ) {
		if ( $this->store_url ) {
			return $this->store_url;
		}

		if ( substr( $license_key, 0, 2 ) === 'DE' ) {
			$this->store_url = 'https://shop.imagesourcecontrol.de/';
		} else {
			$this->store_url = 'https://shop.imagesourcecontrol.com/';
		}

		return $this->store_url;
	}

	/**
	 * Get API endpoint
	 *
	 * @param string $license_key license key.
	 * @return string license check URL
	 */
	private function get_api_endpoint( $license_key = null ) {
		if ( ! $license_key ) {
			$license_key = $this->get_license();
		}

		if ( substr( $license_key, 0, 2 ) === 'DE' ) {
			return 'https://shop.imagesourcecontrol.de/license-api/';
		} else {
			return 'https://shop.imagesourcecontrol.com/license-api/';
		}
	}

	/**
	 * Get account URL
	 *
	 * @param string $license_key license key.
	 * @return string account URL
	 */
	public function get_account_url( $license_key ) {
		if ( $this->account_url ) {
			return $this->account_url;
		}

		if ( substr( $license_key, 0, 2 ) === 'DE' ) {
			$this->account_url = 'https://shop.imagesourcecontrol.de/';
		} else {
			$this->account_url = 'https://shop.imagesourcecontrol.com/';
		}

		return $this->account_url;
	}

	/**
	 * Get license key
	 *
	 * @return string license key
	 */
	private function get_license_key() {
		if ( $this->license_key ) {
			return $this->license_key;
		}

		$this->license_key = get_option( 'isc_license', '' );

		return $this->license_key;
	}

	/**
	 * Actions and filter available after all plugins are initialized
	 */
	public function wp_plugins_loaded() {

		// check for add-on updates.
		add_action( 'init', [ $this, 'updater' ], 1 );
		// register license settings
		add_action( 'admin_init', [ $this, 'register_settings' ], 1 );
		// react on API update checks
		add_action( 'http_api_debug', [ $this, 'update_license_after_version_info' ], 10, 5 );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return self   object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register license settings section
	 */
	public function register_settings() {
		add_settings_section( 'isc_settings_section_license', __( 'License', 'image-source-control-isc' ), [ $this, 'render_section_license' ], 'isc_settings_page' );
		add_settings_field( 'license', __( 'License', 'image-source-control-isc' ), [ $this, 'renderfield_license' ], 'isc_settings_page', 'isc_settings_section_license' );
	}

	/**
	 * Render the License settings section
	 */
	public function render_section_license() {
		require_once ISCPATH . '/pro/admin/templates/settings/license.php';
	}

	/**
	 * Render License key option
	 */
	public function renderfield_license() {
		$expiry_date    = $this->get_license_expires();
		$license_key    = $this->get_license_key();
		$license_status = $this->get_license_status();
		$expired        = false;
		$error_text     = '';

		ob_start();
		?>
		<button type="button" class="button-secondary license-activate"
				name="license_activate"><?php esc_html_e( 'Update license', 'image-source-control-isc' ); ?></button>
		<?php
		$update_button = ob_get_clean();

		$expired_error = __( 'Your license expired.', 'image-source-control-isc' )
						. ' ' . sprintf(
						// $translators: %1$s is HTML of a button, %2$s a starting a tag, %3$s the closing a tag
						 // phpcs:ignore
							 __( 'Click on %1$s if you renewed it or have a subscription or %2$srenew your license%3$s.', 'image-source-control-isc' ),
							$update_button,
							'<a href="' . esc_url( $this->get_store_url( $license_key ) ) . 'checkout/?edd_license_key=' . esc_attr( $license_key ) . '#utm_source=isc-settings&utm_medium=link&utm_campaign=settings-renew-license" id="renewal-link" target="_blank">',
							'</a>'
						);

		if ( 'lifetime' !== $expiry_date ) {
			$expires_time = strtotime( $expiry_date );
			$days_left    = ( $expires_time - time() ) / DAY_IN_SECONDS;
			if ( $expiry_date && $days_left <= 0 ) {
				$error_text = __( 'Your license expired.', 'image-source-control-isc' )
								. ' '
							. sprintf(
								/* translators: "it" is the license key. %1$s is a starting link tag, %2$s is the closing one. */
								__( 'You can extend it in %1$syour account%2$s.', 'image-source-control-isc' ),
								'<a href="' . $this->get_account_url( $license_key ) . '#utm_source=isc-settings&utm_medium=link&utm_campaign=license-expired" target="_blank">',
								'</a>'
							);
				$expired = true;
			} elseif ( 0 < $days_left && 31 > $days_left ) {
				$error_text = sprintf(
				// translators: %d is a number of days.
					esc_html__( '(%d days left)', 'image-source-control-isc' ),
					$days_left
				);
			}
		}

		require_once ISCPATH . '/pro/admin/templates/settings/license-key.php';
	}

	/**
	 * Initiate plugin checks
	 */
	public function check_plugin_licenses() {
		// check license status.
		if ( $this->get_license_status() !== 'valid' ) {
			// register warning.
			add_action( 'after_plugin_row_' . ISCBASE, [ $this, 'add_plugin_list_license_notice' ], 10, 2 );
		}
	}

	/**
	 * Add a warning about an invalid license on the plugin list
	 *
	 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
	 * @param array  $plugin_data An array of plugin data.
	 */
	public function add_plugin_list_license_notice( $plugin_file, $plugin_data ) {
		static $cols;
		if ( is_null( $cols ) ) {
			$cols = count( _get_list_table( 'WP_Plugins_List_Table' )->get_columns() );
		}
		printf(
			'<tr class="isc-plugin-update-tr plugin-update-tr active"><td class="plugin-update colspanchange" colspan="%d"><div class="update-message notice inline notice-warning notice-alt"><p>%s</p></div></td></tr>',
			esc_attr( $cols ),
			wp_kses_post(
				sprintf(
				/* Translators: 1: add-on name 2: admin URL to license page */
					__( 'There might be a new version of %1$s. Please <strong>provide a valid license key</strong> in order to receive updates and support <a href="%2$s">on this page</a>.', 'image-source-control-isc' ),
					$plugin_data['Title'],
					esc_url( add_query_arg( 'page', 'isc-settings', get_admin_url() . 'options-general.php' ) )
				)
			)
		);
	}

	/**
	 * Activate license using AJAX
	 */
	public function activate_license_ajax() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// check nonce.
		check_ajax_referer( 'isc_ajax_license_nonce', 'security' );

		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->activate_license( $_POST['license'] );
		// phpcs:enable

		die();
	}

	/**
	 * Deactivate license using AJAX
	 */
	public function deactivate_license_ajax() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// check nonce.
		check_ajax_referer( 'isc_ajax_license_nonce', 'security' );

		echo esc_html( $this->deactivate_license() );

		die();
	}

	/**
	 * Save license key
	 *
	 * @param string $license_key license key.
	 *
	 * @return string
	 */
	public function activate_license( $license_key = '' ) {
		$license_key = esc_attr( trim( $license_key ) );
		if ( '' === $license_key ) {
			return esc_html__( 'Please enter a valid license key', 'image-source-control-isc' );
		}

		/**
		 * We need to remove the mltlngg_get_url_translated filter added by Multilanguage by BestWebSoft, https://wordpress.org/plugins/multilanguage/
		 * it causes the URL to look different from its original
		 * we are adding it again later
		 */
		remove_filter( 'home_url', 'mltlngg_get_url_translated' );

		$api_params = [
			'edd_action' => 'activate_license',
			'license'    => $license_key,
			'item_name'  => rawurlencode( ISCNAME ),
			'item_id'    => 18, // only one product and it is identical in DE and EN
			'url'        => home_url(),
		];

		/**
		 * Re-add the filter removed from above
		 */
		if ( function_exists( 'mltlngg_get_url_translated' ) ) {
			add_filter( 'home_url', 'mltlngg_get_url_translated' );
		}

		// Call the custom API.
		$response = wp_remote_post(
			$this->get_api_endpoint( $license_key ),
			[
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			]
		);

		if ( is_wp_error( $response ) ) {
			$body = wp_remote_retrieve_body( $response );
			if ( $body ) {
				return $body;
			} else {
				$curl = curl_version();

				return esc_html__( 'License couldn’t be activated. Please try again later.', 'image-source-control-isc' ) . " (cURL {$curl['version']})";
			}
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		// save license status.
		if ( ! empty( $license_data->license ) ) {
			update_option( 'isc_license_status', $license_data->license, false );
		}
		if ( ! empty( $license_data->expires ) ) {
			update_option( 'isc_license_expires', $license_data->expires, false );
		}

		// display activation problem.
		if ( ! empty( $license_data->error ) ) {
			// user friendly texts for errors.
			$errors = [
				'item_name_mismatch'  => __( 'This is not the correct key for this plugin.', 'image-source-control-isc' ),
				'no_activations_left' => __( 'There are no activations left.', 'image-source-control-isc' )
										. '&nbsp;'
										. sprintf(
										/* translators: %1$s is a starting link tag, %2$s is the closing one. */
											__( 'You can manage activations in %1$syour account%2$s.', 'image-source-control-isc' ),
											'<a href="' . $this->get_account_url( $license_key ) . '#utm_source=isc-settings&utm_medium=link&utm_campaign=license-no-activations-left" target="_blank">',
											'</a>'
										) . '&nbsp;'
										. sprintf(
										/* translators: %1$s is a starting link tag, %2$s is the closing one. */
											__( '%1$sUpgrade%2$s for more activations.', 'image-source-control-isc' ),
											'<a href="' . $this->get_account_url( $license_key ) . 'upgrades/#utm_source=isc-settings&utm_medium=link&utm_campaign=license-no-activations-left-upgrade" target="_blank">',
											'</a>'
										),
				'expired'             => __( 'Your license expired.', 'image-source-control-isc' )
										. '&nbsp;'
										. sprintf(
										/* translators: "is" is the license key. %1$s is a starting link tag, %2$s is the closing one. */
											__( 'You can extend it in %1$syour account%2$s.', 'image-source-control-isc' ),
											'<a href="' . $this->get_account_url( $license_key ) . '#utm_source=isc-settings&utm_medium=link&utm_campaign=license-expired" target="_blank">',
											'</a>'
										),
			];
			$error  = isset( $errors[ $license_data->error ] ) ? $errors[ $license_data->error ] : $license_data->error;
			if ( isset( $errors[ $license_data->error ] ) ) {
				return $error;
			} else {
				return sprintf(
				// translators: %s is a string containing information about the issue.
					__( 'License is invalid. Reason: %s', 'image-source-control-isc' ),
					$error
				);
			}
		} else {
			// save license key.
			$this->save_license( $license_key );
		}

		return 1;
	}

	/**
	 * Deactivate license key
	 *
	 * @return string
	 */
	public function deactivate_license() {
		$license_key = $this->get_license();

		$api_params = [
			'edd_action' => 'deactivate_license',
			'license'    => $license_key,
			'item_name'  => rawurlencode( ISCNAME ),
		];
		// send the remote request.
		$response = wp_remote_post(
			$this->get_api_endpoint( $license_key ),
			[
				'body'      => $api_params,
				'timeout'   => 15,
				'sslverify' => false,
			]
		);

		if ( is_wp_error( $response ) ) {
			$body = wp_remote_retrieve_body( $response );
			if ( $body ) {
				return $body;
			} else {
				return __( 'License couldn’t be deactivated. Please try again later.', 'image-source-control-isc' );
			}
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// remove data.
		if ( 'deactivated' === $license_data->license ) {
			delete_option( 'isc_license_status' );
			delete_option( 'isc_license_expires' );
		} elseif ( 'failed' === $license_data->license ) {
			update_option( 'isc_license_expires', $license_data->expires, false );
			update_option( 'isc_license_status', $license_data->license, false );

			return __( 'License couldn’t be deactivated. Please try again later.', 'image-source-control-isc' );
		} else {
			return __( 'License couldn’t be deactivated. Please try again later.', 'image-source-control-isc' );
		}

		return 1;
	}

	/**
	 * Get license key
	 *
	 * @return string
	 */
	public function get_license() {
		return get_option( 'isc_license', '' );
	}

	/**
	 * Save license key
	 *
	 * @param string $license license key.
	 */
	public function save_license( $license = null ) {
		update_option( 'isc_license', $license, false );
	}

	/**
	 * Get license status
	 *
	 * @return string $status license status, e.g. "valid" or "invalid"
	 */
	public function get_license_status() {
		return get_option( 'isc_license_status', false );
	}

	/**
	 * Get license expired value
	 *
	 * @return string $date expiry date, empty string if no option exists
	 */
	public function get_license_expires() {
		return get_option( 'isc_license_expires', '' );
	}


	/**
	 * Register the Updater class
	 */
	public function updater() {
		$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
		if ( ! current_user_can( 'manage_options' ) && ! $doing_cron && ( ! is_multisite() || is_main_site() ) ) {
			return;
		}

		$license_key = get_option( 'isc_license', '' );

		// check if a license expired over time.
		$expiry_date = $this->get_license_expires();
		$now         = time();
		if ( $expiry_date && 'lifetime' !== $expiry_date && strtotime( $expiry_date ) < $now ) {
			// remove license status.
			delete_option( 'isc_license_status' );
		}

		// by default, EDD looks every 3 hours for updates. The following code block changes that to 24 hours. set_expiration_of_update_option delivers that value.
		$option_key = 'pre_update_option_edd_sl_' . md5( serialize( 'isc' . $license_key ) );
		add_filter( $option_key, [ $this, 'set_expiration_of_update_option' ] );

		new \ISC_SL_Plugin_Updater(
			$this->get_api_endpoint( $license_key ),
			ISCPATH . 'isc.php',
			[
				'version'   => ISCVERSION,
				'license'   => $license_key,
				'item_name' => ISCNAME,
				'author'    => ISCNAME,
			]
		);
	}

	/**
	 * Set the expiration of the updater transient key to 1 day instead of 1 hour to prevent too many update checks
	 *
	 * @param array $value value array.
	 *
	 * @return array
	 */
	public function set_expiration_of_update_option( $value ) {
		$value['timeout'] = time() + 86400;

		return $value;
	}

	/**
	 * Update the license status based on information retrieved from the version info check
	 *
	 * @param array|\WP_Error $response    HTTP response or WP_Error object.
	 * @param string          $context     Context under which the hook is fired.
	 * @param string          $class       HTTP transport used.
	 * @param array           $parsed_args HTTP request arguments.
	 * @param string          $url         The request URL.
	 * @return array|\WP_Error
	 */
	public function update_license_after_version_info( $response, $context, $class, $parsed_args, $url ) {

		// bail if this call is not from our version check or returns an issue
		if ( $url !== $this->get_api_endpoint()
			|| (
				empty( $parsed_args['body']['edd_action'] )
				|| 'get_version' !== $parsed_args['body']['edd_action']
			)
			|| is_wp_error( $response )
		) {
			return $response;
		}

		$params = json_decode( wp_remote_retrieve_body( $response ) );
		// return if no name is given to identify the plugin that needs update
		if ( empty( $params->name ) ) {
			return $response;
		}

		$new_license_status = null;
		$new_expiry_date    = null;

		// Some conditions could happen at the same time, though due to different conditions in EDD we are safer to have multiple checks
		if ( isset( $params->valid_until ) ) {
			if ( 'invalid' === $params->valid_until ) {
				$new_license_status = 'invalid';
			}
			if ( 'lifetime' === $params->valid_until ) {
				$new_license_status = 'valid';
				$new_expiry_date    = 'lifetime';
			}
			// license is timestamp
			if ( is_int( $params->valid_until ) ) {
				$new_expiry_date = (int) $params->valid_until;
				if ( time() < $params->valid_until ) {
					$new_license_status = 'valid';
				}
			}
		} elseif ( empty( $params->download_link ) || empty( $params->package ) || isset( $params->msg ) ) {
			// if either of these two parameters is missing then the user does not have a valid license according to our store
			// if there is a "msg" parameter then the license did also not work for another reason
			$new_license_status = 'invalid';
		}

		if ( ! $new_license_status && ! $new_expiry_date ) {
			return $response;
		}

		// identify the returned plugin name differs
		if ( $params->name !== ISCNAME ) {
			return $response;
		}

		if ( $new_license_status ) {
			update_option( 'isc_license_status', $new_license_status, false );
		}
		if ( $new_expiry_date ) {
			if ( 'lifetime' !== $new_expiry_date ) {
				$new_expiry_date = gmdate( 'Y-m-d 23:59:49', $new_expiry_date );
			}
			update_option( 'isc_license_expires', $new_expiry_date, false );
		}

		return $response;
	}

	/**
	 * Return true if the license is valid
	 *
	 * @return bool
	 */
	public static function is_valid(): bool {
		$license = self::get_instance();
		return $license->get_license_status() === 'valid';
	}

	/**
	 * Render a string about the license not being valid, if that is the case
	 */
	public static function maybe_render_license_not_valid() {
		if ( ! self::is_valid() ) {
			printf(
			// translators: %s marks the opening and closing link tag to the settings page
				esc_html__( 'Please %1$sactivate your license%2$s to use this feature.', 'image-source-control-isc' ),
				'<a href="' . esc_url( admin_url( 'options-general.php?page=isc-settings' ) ) . '">',
				'</a>'
			);
		}
	}
}
