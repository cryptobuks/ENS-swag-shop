<?php

namespace WP_Defender\Behavior\Scan_Item;

use Calotes\Component\Behavior;
use WP_Defender\Component\Error_Code;
use WP_Defender\Model\Scan;
use WP_Defender\Model\Scan_Item;
use WP_Defender\Traits\Formats;

class Vuln_Result extends Behavior {
	use Formats;

	/**
	 * @return array
	 */
	public function to_array(): array {
		$data = $this->owner->raw_data;
		if ( isset( $data['name'], $data['version'], $data['bugs'] ) ) {
			return [
				'id' => $this->owner->id,
				'type' => Scan_Item::TYPE_VULNERABILITY,
				'file_name' => $data['name'],
				'short_desc' => sprintf( __( 'Vulnerability found in %s.', 'wpdef' ), $data['version'] ),
				'detail' => isset( $data['new_structure'] )
					? $this->get_details_as_array( $data )
					: $this->get_detail_as_string( $data ),
				// Need for all scan items for WP-CLI command. Full path = base slug for this item.
				'full_path' => $data['slug'],
				'new_structure' => isset( $data['slug'] ) ? 'yes' : 'no',
			];
		}

		return [];
	}

	/**
	 * @return array
	 */
	public function ignore(): array {
		$scan = Scan::get_last();
		$scan->ignore_issue( $this->owner->id );

		return [
			'message' => __( 'The suspicious file has been successfully ignored.', 'wpdef' ),
		];
	}

	/**
	 * @return array
	 */
	public function unignore(): array {
		$scan = Scan::get_last();
		$scan->unignore_issue( $this->owner->id );

		return [
			'message' => __( 'The suspicious file has been successfully restored.', 'wpdef' ),
		];
	}

	/**
	 * @return mixed
	 */
	public function resolve() {
		$data = $this->owner->raw_data;
		// No change to 'WordPress'.
		if ( 'wordpress' === $data['type'] ) {
			return [
				'url' => network_admin_url( 'wp-admin/update-core.php' ),
			];
		}

		if ( 'plugin' === $data['type'] ) {
			return $this->upgrade_plugin( $data['slug'] );
		} elseif ( 'theme' === $data['type'] ) {
			return $this->upgrade_theme( $data['base_slug'] );
		}

		// If type does not match.
		return new \WP_Error( Error_Code::INVALID, __( 'Please try again! We could not find the issue type.', 'wpdef' ) );
	}

	/**
	 * @param $slug
	 *
	 * @return array|bool|\WP_Error
	 */
	private function upgrade_theme( $slug ) {
		$skin = new Silent_Skin();
		$upgrader = new \Theme_Upgrader( $skin );
		$ret = $upgrader->upgrade( $slug );

		if ( true === $ret ) {
			$model = Scan::get_last();
			$model->remove_issue( $this->owner->id );

			return [
				'message' => __( 'This item has been resolved.', 'wpdef' ),
			];
		}

		// This is WP error.
		if ( is_wp_error( $ret ) ) {
			return $ret;
		}

		// Sometimes it returns false because of it could not complete the update process.
		return new \WP_Error( Error_Code::INVALID, __( "We couldn't update your theme. Please try updating with another method.", 'wpdef' ) );
	}

	/**
	 * @param string $slug
	 *
	 * @return array
	 * @since 2.8.1 Change Upgrade plugin logic.
	 */
	private function upgrade_plugin( $slug ): array {
		$skin = new Plugin_Skin();
		$upgrader = new \Plugin_Upgrader( $skin );
		$result = $upgrader->bulk_upgrade( [ $slug ] );

		if ( is_wp_error( $skin->result ) ) {
			return [
				'type_notice' => 'error',
				'message' => $skin->result->get_error_message(),
			];
		} elseif ( $skin->get_errors()->has_errors() ) {
			return [
				'type_notice' => 'error',
				'message' => $skin->get_error_messages(),
			];
		} elseif ( is_array( $result ) && ! empty( $result[ $slug ] ) ) {
			/*
			 * Plugin is already at the latest version.
			 *
			 * This may also be the return value if the `update_plugins` site transient is empty,
			 * e.g. when you update two plugins in quick succession before the transient repopulates.
			 *
			 * Preferably something can be done to ensure `update_plugins` isn't empty.
			 * For now, surface some sort of error here.
			 */
			if ( true === $result[ $slug ] ) {
				return [
					'type_notice' => 'error',
					'message' => $upgrader->strings['up_to_date'],
				];
			}
			$model = Scan::get_last();
			$model->remove_issue( $this->owner->id );

			return [
				'message' => __( 'This item has been resolved.', 'wpdef' ),
			];
		} elseif ( false === $result ) {
			return [
				'type_notice' => 'error',
				'message' => __( 'Unable to connect to the filesystem. Please confirm your credentials.', 'wpdef' ),
			];
		}

		return [
			'type_notice' => 'info',
			'message' => __( 'There is no update available for this plugin.', 'wpdef' ),
		];
	}

	/**
	 * @param array $data
	 *
	 * @return string
	 */
	protected function get_vulnerability_body( array $bug ): string {
		$text = '#' . $bug['title'] . PHP_EOL;
		$text .= '-' . __( 'Vulnerability type:', 'wpdef' ) . ' ' . $bug['vuln_type'] . PHP_EOL;
		if ( isset( $bug['fixed_in'] ) ) {
			$text .= '-' . __( 'This bug has been fixed in version:', 'wpdef' ) . ' ' . $bug['fixed_in'] . PHP_EOL;
		} else {
			$text .= __( 'No Update Available', 'wpdef' ) . PHP_EOL;
		}

		return $text;
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	public function get_detail_as_string( $data ): string {
		$strings = [];
		foreach ( $data['bugs'] as $bug ) {
			$strings[] = $this->get_vulnerability_body( $bug );
		}

		return implode( PHP_EOL, $strings );
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function get_details_as_array( $data ): array {
		$arr = [];
		foreach ( $data['bugs'] as $bug ) {
			$text = $this->get_vulnerability_body( $bug );
			$arr[ $bug['cvss_score'] ] = str_replace( PHP_EOL, '<br/>', $text );
		}

		return $arr;
	}
}

if ( ! class_exists( \WP_Upgrader::class ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
}
if ( ! class_exists( \Theme_Upgrader::class ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-theme-upgrader.php';
}

class Silent_Skin extends \Automatic_Upgrader_Skin {
	public function footer() {
		return;
	}

	public function header() {
		return;
	}

	public function feedback( $data, ...$args ) {
		return '';
	}
}

class Plugin_Skin extends \WP_Ajax_Upgrader_Skin {
	public function feedback( $data, ...$args ) {
		return '';
	}
}