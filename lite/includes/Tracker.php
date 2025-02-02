<?php

namespace KaizenCoders\UpdateURLS;

/**
 * Tracker
 *
 * @class       Tracker
 * @since       1.2
 *
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @author      KaizenCoders <hello@kaizencoders.com>
 */
class Tracker {

	/**
	 * Get Active, Inactive or all plugins info
	 *
	 * @since 1.2
	 * @return array
	 *
	 */
	public static function get_plugins( $status = 'all', $details = false ) {

		$plugins = [
			'active_plugins'   => [],
			'inactive_plugins' => [],
		];

		// Check if get_plugins() function exists. This is required on the front end of the
		// site, since it is in a file that is normally only loaded in the admin.
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins    = get_plugins();
		$active_plugins = get_option( 'active_plugins', [] );

		if ( is_multisite() ) {
			$sitewide_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', [] ) );
			$active_plugins             = ! empty( $active_plugins ) ? array_merge( $sitewide_activated_plugins,
				$active_plugins ) : $sitewide_activated_plugins;
		}

		foreach ( $all_plugins as $plugin_path => $plugin ) {
			// If the plugin isn't active, don't show it.
			if ( in_array( $plugin_path, $active_plugins ) ) {
				$slug      = 'active_plugins';
				$is_active = 1;
			} else {
				$slug      = 'inactive_plugins';
				$is_active = 0;
			}

			if ( $details ) {

				$plugin_data = [
					'name'       => $plugin['Name'],
					'version'    => $plugin['Version'],
					'author'     => $plugin['Author'],
					'author_uri' => $plugin['AuthorURI'],
					'plugin_uri' => $plugin['PluginURI'],
					'is_active'  => $is_active,
				];

				$plugins[ $slug ][ $plugin_path ] = $plugin_data;
			} else {
				$plugins[ $slug ][] = $plugin_path;
			}
		}

		if ( 'active' === $status ) {
			return $plugins['active_plugins'];
		} elseif ( 'inactive' === $status ) {
			return $plugins['inactive_plugins'];
		} else {
			return array_merge( $plugins['active_plugins'], $plugins['inactive_plugins'] );
		}
	}

	/**
	 * Get Active Plugins
	 *
	 * @since 1.2
	 *
	 * @param  bool  $details
	 *
	 * @return array
	 *
	 */
	public static function get_active_plugins( $details = false ) {
		return self::get_plugins( 'active', $details );
	}

	/**
	 * Get Inactive plugins
	 *
	 * @since 1.2
	 *
	 * @param  bool  $details
	 *
	 * @return array
	 *
	 */
	public static function get_inactive_plugins( $details = false ) {
		return self::get_plugins( 'inactive', $details );
	}

	/**
	 * Check whether plugin is active or not.
	 *
	 * @since 1.2
	 *
	 * @param  string  $plugin
	 *
	 * @return bool
	 *
	 */
	public static function is_plugin_activated( $plugin = '' ) {
		if ( empty( $plugin ) ) {
			return false;
		}

		$active_plugins = self::get_active_plugins();

		if ( count( $active_plugins ) == 0 ) {
			return false;
		}

		if ( in_array( $plugin, $active_plugins ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Is plugin installed?
	 *
	 * @since 1.2
	 *
	 * @param  string  $plugin
	 *
	 * @return bool
	 *
	 */
	public static function is_plugin_installed( $plugin = '' ) {
		if ( empty( $plugin ) ) {
			return false;
		}

		$all_plugins = self::get_plugins();

		if ( count( $all_plugins ) == 0 ) {
			return false;
		}

		if ( in_array( $plugin, $all_plugins ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get Current Theme Info
	 *
	 * @since 1.2
	 * @return array
	 *
	 */
	public static function get_current_theme_info() {
		$current_theme = [];
		if ( function_exists( 'wp_get_theme' ) ) {
			$theme_data    = wp_get_theme();
			$current_theme = [
				'name'       => $theme_data->get( 'Name' ),
				'version'    => $theme_data->get( 'Version' ),
				'author'     => $theme_data->get( 'Author' ),
				'author_uri' => $theme_data->get( 'AuthorURI' ),
			];
		} elseif ( function_exists( 'get_theme_data' ) ) {
			$theme_data    = get_theme_data( get_stylesheet_directory() . '/style.css' );
			$current_theme = [
				'name'       => $theme_data['Name'],
				'version'    => $theme_data['Version'],
				'author'     => $theme_data['Author'],
				'author_uri' => $theme_data['AuthorURI'],
			];
		}

		return $current_theme;
	}

	/**
	 * Get server info
	 *
	 * @since 1.2
	 * @return array
	 *
	 */
	public static function get_server_info() {
		global $wpdb;

		$server_info = [
			'php_version'                  => PHP_VERSION,
			'mysql_version'                => $wpdb->db_version(),
			'web_server_info'              => $_SERVER['SERVER_SOFTWARE'],
			'user_agent'                   => $_SERVER['HTTP_USER_AGENT'],
			'php_memory_limit'             => ini_get( 'memory_limit' ),
			'php_post_max_size'            => ini_get( 'post_max_size' ),
			'php_upload_max_file_size'     => ini_get( 'upload_max_filesize' ),
			'php_max_execution_time'       => ini_get( 'max_execution_time' ),
			'session'                      => isset( $_SESSION ) ? 'enabled' : 'disabled',
			'session_name'                 => esc_html( ini_get( 'session.name' ) ),
			'cookie_path'                  => esc_html( ini_get( 'session.cookie_path' ) ),
			'session_save_path'            => esc_html( ini_get( 'session.save_path' ) ),
			'use_cookies'                  => ini_get( 'session.use_cookies' ) ? 'on' : 'off',
			'use_only_cookies'             => ini_get( 'session.use_only_cookies' ) ? 'on' : 'off',
			'ssl_support_extension_loaded' => extension_loaded( 'openssl' ) ? 'yes' : 'no',
			'mb_string_extension_loaded'   => extension_loaded( 'mbstring' ) ? 'yes' : 'no',
		];

		return $server_info;
	}

	/**
	 * Get WordPress information
	 *
	 * @since 1.2
	 * @return array
	 *
	 */
	public static function get_wp_info() {
		global $wpdb;

		$wp_info = [
			'site_url'              => site_url(),
			'home_url'              => home_url(),
			'wp_version'            => get_bloginfo( 'version' ),
			'permalink_structure'   => get_option( 'permalink_structure' ),
			'multisite'             => is_multisite() ? 'yes' : 'no',
			'wp_debug'              => defined( 'WP_DEBUG' ) ? ( WP_DEBUG ? 'enabled' : 'disabled' ) : '',
			'display_errors'        => ( ini_get( 'display_errors' ) ) ? 'on' : 'off',
			'wp_table_prefix'       => $wpdb->prefix,
			'wp_db_charset_Collate' => $wpdb->get_charset_collate(),
			'wp_memory_limit'       => ( size_format( (int) WP_MEMORY_LIMIT * 1048576 ) ),
			'wp_upload_size'        => ( size_format( wp_max_upload_size() ) ),
			'filesystem_method'     => get_filesystem_method(),
		];

		return $wp_info;
	}

	/**
	 * Get the system info.
	 *
	 * @since 1.2.8
	 * @return string
	 *
	 */
	public static function get_system_info() {
		global $wpdb;

		$return = '### Begin System Info ###' . "\n\n";

		// Basic site info
		$return .= '-- WordPress Configuration' . "\n\n";
		$return .= 'Site URL:                 ' . site_url() . "\n";
		$return .= 'Home URL:                 ' . home_url() . "\n";
		$return .= 'Multisite:                ' . ( is_multisite() ? 'Yes' : 'No' ) . "\n";
		$return .= 'Version:                  ' . get_bloginfo( 'version' ) . "\n";
		$return .= 'Language:                 ' . get_locale() . "\n";
		$return .= 'Table Prefix:             ' . 'Length: ' . strlen( $wpdb->prefix ) . "\n";
		$return .= 'WP_DEBUG:                 ' . ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n";
		$return .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";

		// Plugin Configuration
		$return .= "\n" . '-- Update URLS Configuration' . "\n\n";
		$return .= 'Plugin Version:           ' . KC_UU_PLUGIN_VERSION. "\n";

		// Server Configuration
		$return .= "\n" . '-- Server Configuration' . "\n\n";
		$os     = self::get_os();
		$return .= 'Operating System:         ' . $os['name'] . "\n";
		$return .= 'PHP Version:              ' . PHP_VERSION . "\n";
		$return .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";

		$return .= 'Server Software:          ' . $_SERVER['SERVER_SOFTWARE'] . "\n";

		// PHP configs... now we're getting to the important stuff
		$return .= "\n" . '-- PHP Configuration' . "\n\n";
		$return .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . "\n";
		$return .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . "\n";
		$return .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . "\n";
		$return .= 'Time Limit:               ' . ini_get( 'max_execution_time' ) . "\n";
		$return .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . "\n";
		$return .= 'Display Errors:           ' . ( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ) . "\n";

		// WordPress active plugins
		$return         .= "\n" . '-- WordPress Active Plugins' . "\n\n";
		$plugins        = get_plugins();
		$active_plugins = get_option( 'active_plugins', [] );
		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( ! in_array( $plugin_path, $active_plugins ) ) {
				continue;
			}
			$return .= $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
		}

		// WordPress inactive plugins
		$return .= "\n" . '-- WordPress Inactive Plugins' . "\n\n";
		foreach ( $plugins as $plugin_path => $plugin ) {
			if ( in_array( $plugin_path, $active_plugins ) ) {
				continue;
			}
			$return .= $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
		}

		if ( is_multisite() ) {
			// WordPress Multisite active plugins
			$return         .= "\n" . '-- Network Active Plugins' . "\n\n";
			$plugins        = wp_get_active_network_plugins();
			$active_plugins = get_site_option( 'active_sitewide_plugins', [] );
			foreach ( $plugins as $plugin_path ) {
				$plugin_base = plugin_basename( $plugin_path );
				if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
					continue;
				}
				$plugin = get_plugin_data( $plugin_path );
				$return .= $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
			}
		}

		$return .= "\n" . '### End System Info ###';

		return $return;
	}

	/**
	 * Determines the current operating system.
	 * @access public
	 * @since 1.2.8
	 * @return array
	 */
	public static function get_os() {
		$os         = [];
		$uname      = php_uname( 's' );
		$os['code'] = strtoupper( substr( $uname, 0, 3 ) );
		$os['name'] = $uname;

		return $os;
	}

}