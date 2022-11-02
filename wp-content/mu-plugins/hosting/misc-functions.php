<?php
/**
 * WPMU DEV Hosting Miscellaneous Functions
 */

/**
 * Disable the Delete button from Plugins list for WPMU DEV Dashboard.
 */
function wpmudev_hosting_plugin_actions( $actions, $plugin_file, $plugin_data, $context ) {
	// Remove deactivate link for important plugins.
	if ( array_key_exists( 'deactivate', $actions ) && in_array( $plugin_file, array( 'wpmudev-updates/update-notifications.php' ), true ) ) {
		unset( $actions['deactivate'] );
	}

	// Remove delete links for important plugins.
	if ( array_key_exists( 'delete', $actions ) && in_array( $plugin_file, array( 'wpmudev-updates/update-notifications.php' ), true ) ) {
		unset( $actions['delete'] );
	}

	return $actions;
}
add_filter( 'plugin_action_links', 'wpmudev_hosting_plugin_actions', 30, 4 );
add_filter( 'network_admin_plugin_action_links', 'wpmudev_hosting_plugin_actions', 30, 4 );

add_action(
	'delete_plugin',
	function( $plugin ) {
		if ( 'wpmudev-updates/update-notifications.php' === $plugin ) {
			if ( defined( 'DOING_AJAX' ) ) {
				$status = array(
					'delete'       => 'plugin',
					'slug'         => 'wpmu-dev-dashboard',
					'errorMessage' => __( 'Sorry, you are not allowed to delete this plugin.' ),
				);
				wp_send_json_error( $status );
			} elseif ( ( isset( $_POST['action'] ) && 'delete-selected' === $_POST['action'] ) || ( isset( $_POST['action2'] ) && 'delete-selected' === $_POST['action2'] ) ) {
				wp_redirect( self_admin_url( 'plugins.php' ) );
				exit;
			}
		}
	}
);

/**
 * Disable the Deactivate button from Plugins list for WPMU DEV Dashboard.
 */
add_action(
	'deactivate_plugin',
	function( $plugin ) {
		if ( ( ( isset( $_POST['action'] ) && 'deactivate-selected' === $_POST['action'] ) || ( isset( $_POST['action2'] ) && 'deactivate-selected' === $_POST['action2'] ) ) && 'wpmudev-updates/update-notifications.php' === $plugin ) {
			wp_redirect( self_admin_url( 'plugins.php' ) );
			exit;
		}
	}
);

function wpmudev_hosting_plugin_cap_filter( $allcaps, $cap, $args ) {
	// Bail out if we're not asking about deactivating a plugin.
	if ( 'deactivate_plugin' !== $args[0] ) {
		return $allcaps;
	}

	if ( 'wpmudev-updates/update-notifications.php' !== $args[2] ) {
		return $allcaps;
	}

	$allcaps[ $cap[0] ] = false;

	return $allcaps;
}
add_filter( 'user_has_cap', 'wpmudev_hosting_plugin_cap_filter', 10, 3 );

function wpmudev_hosting_plugin_cap_filter_super( $caps, $cap, $user_id, $args ) {
	if ( 'deactivate_plugin' === $cap && 'wpmudev-updates/update-notifications.php' === $args[0] ) {
		$caps[] = 'do_not_allow';
	}

	return $caps;
}
add_filter( 'map_meta_cap', 'wpmudev_hosting_plugin_cap_filter_super', 10, 4 );

/**
 * Customize the upgrade PHP link in Site Health.
 */
add_filter(
	'wp_update_php_url',
	function( $url ) {
		return 'https://premium.wpmudev.org/docs/hosting/tools-features/#php-versions';
	}
);

/**
 * Customize the direct upgrade PHP button in nag message.
 */
add_filter(
	'wp_direct_php_update_url',
	function( $url ) {
		return sprintf( 'https://premium.wpmudev.org/hub/hosting/?view=site&site_id=%s&tab=tools', WPMUDEV_HOSTING_SITE_ID );
	}
);

/**
 * Redirect to primary domain on single sites.
 * Fixes the cases of both temporary domain & primary domain being accessed directly.
 */
/**
 add_action(
	'plugins_loaded',
	function() {
		// Get the home url if exists.
		$home_url = get_home_url();
		if (
			defined( 'WP_CLI' ) && WP_CLI ||
			defined( 'WP_INSTALLING' ) && WP_INSTALLING ||
			'staging' === $_SERVER['WPMUDEV_HOSTING_ENV'] ||
			is_multisite() ||
			empty( $home_url )
		) {
			return;
		}

		// Set the temp domain regex.
		$temp_domain = "/\bwpmudev.host\b/i";

		// Find the primary domain.
		$primary_domain = parse_url( $home_url, PHP_URL_HOST );

		// Find the domain the request was made from.
		$request_domain = $_SERVER['HTTP_HOST'];

		// Find the URI of the request.
		$request_uri = $_SERVER['REQUEST_URI'];

		// If our primary domain is not a temporary domain.
		if ( ! preg_match( $temp_domain, $primary_domain ) ) {
			// If the request domain is a temporary domain or if it's not the same as the primary then redirect.
			if ( preg_match( $temp_domain, $request_domain ) || $primary_domain !== $request_domain ) {
				wp_safe_redirect( 'https://' . $primary_domain . $request_uri, 301, 'WordPress' );
				exit;
			}
		}
	}
);
*/

/**
 * WPMU DEV Hosting function for plugins to hook in and clear Static Server Cache on demand.
 */
function wpmudev_hosting_purge_static_cache( $path = '' ) {
	// Setup.
	$domain   = untrailingslashit( get_site_url( null, null, 'https' ) );
	$resolver = str_replace( array( 'http://', 'https://' ), '', $domain ) . ':443:127.0.0.1';

	// Default to purging all.
	$url = $domain . '/*';

	// Adjust the PURGE URI.
	if ( empty( $path ) ) {
		// Purge all.
		$url = $domain . '/*';
	} elseif ( '/' === $path ) {
		// Purge homepage.
		$url = $domain . '/$';
	} else {
		// Purge specific URI.
		$url = $domain . $path;
	}

	// Curl.
	$ch = curl_init();
	curl_setopt_array(
		$ch,
		array(
			CURLOPT_URL                  => $url,
			CURLOPT_RETURNTRANSFER       => true,
			CURLOPT_NOBODY               => false,
			CURLOPT_HEADER               => true,
			CURLOPT_CUSTOMREQUEST        => 'PURGE',
			CURLOPT_FOLLOWLOCATION       => true,
			CURLOPT_DNS_USE_GLOBAL_CACHE => false,
			CURLOPT_RESOLVE              => array(
				$resolver,
			),
		)
	);

	// Response.
	$response    = curl_exec( $ch );
	$header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
	$header      = substr( $response, 0, $header_size );
	$body        = substr( $response, $header_size );
	curl_close( $ch );

	if ( preg_match( '/^OK/', $body ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Quantum Hosting customizations
**/
if (  get_cfg_var('wpmudev.hosting_plan') == 'quantum' ) {
	class Quantum_Hosting {
		private $allowed_plugins_slugs = array(
			'akismet',
			'beaver-builder-lite-version',
			'beehive-analytics',
			'ultimate-branding',
			'classic-editor',
			'wp-defender',
			'elementor',
			'forminator',
			'forminator-stripe',
			'gutenberg',
			'wp-hummingbird',
			'hustle',
			'ocean-extra',
			'ocean-social-sharing',
			'wpmu-dev-seo',
			'wp-smush-pro',
			'astra-sites',
			'the-hub-client',
			'ultimate-addons-for-beaver-builder-lite',
			'wpmudev-updates',
			'wpmudev-videos',
			'branda-white-labeling',
			'broken-link-checker',
			'defender-security',
			'hummingbird-performance',
			'wordpress-popup',
			'smartcrawl-seo',
			'wp-smushit',
			'astra-widgets',
			'bb-bootstrap-alerts',
			'reset-astra-customizer',
			'astra-widgets',
			'header-footer-elementor',
			'fullwidth-templates',
			'bb-header-footer',
			'astra-import-export',
			'ultimate-addons-for-gutenberg',
			'timed-content-for-beaver-builder',
			'timeline-for-beaver-builder',
			'wpforms-lite',
			'essential-addons-for-elementor-lite',
			'elementskit-lite',
			'google-analytics-async'
		);

		private $allowed_themes_slugs = array(
			'hello-elementor',
			'oceanwp',
			'twentytwenty',
			'twentytwentyone',
			'twentytwentytwo',
			'astra'
		);

		function __construct() {
			add_filter( 'plugin_install_action_links', array( $this, 'install_action_links' ), PHP_INT_MAX, 2 );
			add_filter( 'plugin_action_links', array( $this, 'action_links' ), PHP_INT_MAX, 4 );
			add_action( 'admin_print_scripts', array( $this, 'plugin_install_scripts' ), PHP_INT_MAX );
			add_filter( 'upgrader_source_selection', array( $this, 'break_plugin_install' ), PHP_INT_MAX, 4 );
			add_action( 'after_setup_theme', array( $this, 'deactivate_plugins' ), PHP_INT_MAX );
			// Disable for now to allow all themes.
			// add_action( 'admin_print_scripts', array( $this, 'theme_install_scripts' ), PHP_INT_MAX );
		}

		private static function is_plugin_install_page() {
			$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

			if ( ! is_object( $current_screen ) ) {
				return false;
			}

			return isset( $current_screen->base ) && ( 'plugins' === $current_screen->base || 'plugin-install' === $current_screen->base );
		}

		private static function is_theme_install_page() {
			$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

			if ( ! is_object( $current_screen ) ) {
				return false;
			}

			return isset( $current_screen->base ) && ( 'themes' === $current_screen->base || 'theme-install' === $current_screen->base );
		}

		public function install_action_links( $actions, $plugin ) {
			if ( ! in_array( $plugin['slug'], $this->allowed_plugins_slugs, true ) ) {
				return array (
					'<button title="' . esc_html__( 'This plugin is not available on your current hosting plan. Please contact your hosting provider to upgrade your plan.', 'quantum-hosting' ) . '" type="button" class="plugin-banned button button-disabled" disabled="disabled">' . esc_html__( 'Not Available', 'quantum-hosting' ) . '</button>',
					'<a href="' . esc_url( esc_attr__( 'javascript:void(0)', 'quantum-hosting' ) ) . '">' . esc_html__( 'More Details', 'quantum-hosting' ) . '</a>'
				);
			}

			return $actions;
		}

		public function action_links( $actions, $plugin_file, $plugin_data, $context ) {
			preg_match( '#(.*)\/#', $plugin_file, $folder_slug );

			if (
				array_key_exists( 'activate', $actions ) &&
				! in_array( $folder_slug[1], $this->allowed_plugins_slugs, true )
			) {
				$actions['activate'] = esc_html__( 'Not Available', 'quantum-hosting' );
			}
			return $actions;
		}

		public function plugin_install_scripts() {
			if ( self::is_plugin_install_page() ) {
			?>
			<style>
				a.upload-view-toggle {
					display: none !important;
				}
			</style>
			<script>
				jQuery( function( $ ) {

					var pluginFilters = $( '#plugin-filter' ),
						observer;

					function removeLink() {
						$( 'button.plugin-banned' ).each( function() {
							var href      = $( this ).parent().parent().parent().siblings( '.name.column-name' ).children( 'h3' ).children( 'a' ),
								href_text = $( this ).parent().parent().parent().siblings( '.name.column-name' ).children( 'h3' ).children( 'a' ).html();

								href.replaceWith( '<span>' + href_text + '</span>' );
						} );

						$( 'a.upload-view-toggle, div.upload-plugin-wrap' ).remove();
					}

					if ( 'plugin-install-php' === adminpage && 1 === pluginFilters.length ) {
						try {
							observer = new MutationObserver( function( mutations ) {
								mutations.forEach( function( mutation ) {
									removeLink();
								});
							});

							observer.observe( pluginFilters.get( 0 ), {
								childList: true
							});

							removeLink();
						} catch ( error ) {
							console.warn( error );
						}
					}

					$( document ).ready( function() { removeLink(); } );
				} );
			</script>
			<?php
			}
		}

		public function theme_install_scripts() {
			if ( self::is_theme_install_page() ) {
			?>
			<style>
				button.upload-view-toggle {
					display: none !important;
				}
			</style>
			<script>
			jQuery( function( $ ) {
				var allowedThemes = [
					<?php
					foreach ( $this->allowed_themes_slugs as $slug ) {
						echo "'" . $slug . "',";
					}
					?>
				];

				var themeFilters = $( 'div.theme-browser' ),
					observer;

				function removeLink() {
					$( 'div.theme-browser div.theme' ).each( function() {
						var slug = $( this ).data( 'slug' ),
							skip = false;

						$.each( allowedThemes, function( i, v ) {
							if ( v === slug ) {
								skip = true;
							}
						} );

						$( this ).on( 'click', function() {
							$( 'body' ).find( 'div.theme-install-overlay' ).children().find( 'button.next-theme' ).remove();
							$( 'body' ).find( 'div.theme-install-overlay' ).children().find( 'button.previous-theme' ).remove();
						});

						if ( skip ) {
							return;
						}

						$( this ).on( 'click', function(e) {
							$( 'body' ).find( 'div.theme-install-overlay' ).children().find( 'a.theme-install' ).remove();
						});

						$( this ).children().find( 'a.theme-install' ).replaceWith( '<button type="button" class="plugin-banned button button-disabled" disabled="disabled">Not Available</button>');
						$( this ).children().find( 'button.preview' ).replaceWith( '<a class="button preview install-theme-preview" ref="#">More Details</a>' );
					} );

					$( 'button.upload-view-toggle, div.upload-theme' ).remove();
				}

				if ( 'theme-install-php' === adminpage && 1 === themeFilters.length ) {
					try {
						observer = new MutationObserver( function( mutations ) {
							mutations.forEach( function( mutation ) {
								removeLink();
							});
						});

						observer.observe( themeFilters.get( 0 ), {
							childList: true,
							subtree: true,
						});

						removeLink();
					} catch ( error ) {
						console.warn( error );
					}
				}

				$( document ).ready( function() { removeLink(); } );
			});
			</script>
			<?php
			}
		}

		public function break_plugin_install( $full_path, $path, $plugin_info, $type ) {
			if (
				array_key_exists( 'type', $type ) &&
				'plugin' === $type['type'] &&
				array_key_exists( 'action', $type ) &&
				'install' === $type['action']
			) {
				preg_match( '#.*\/([^\/]+)\/#', $full_path, $folder_slug );

				if ( ! in_array( $folder_slug[1], $this->allowed_plugins_slugs, true ) ) {
					global $wp_filesystem;

					if ( $wp_filesystem->is_dir( $full_path ) ) {
						$wp_filesystem->delete( $full_path, true, 'd' );
					}

					return new WP_Error( 'not_allowed', 'Not Available' );
				}
			}

			return $full_path;
		}

		public function deactivate_plugins() {
			$active_plugins = get_option( 'active_plugins' );

			foreach ( $active_plugins as $active_plugin ) {
				preg_match( '#(.*)\/#', $active_plugin, $folder_slug );

				if ( ! in_array( $folder_slug[1], $this->allowed_plugins_slugs, true ) ) {
					deactivate_plugins( $active_plugin, true );
				}
			}

		}
	}

	new Quantum_Hosting;

}