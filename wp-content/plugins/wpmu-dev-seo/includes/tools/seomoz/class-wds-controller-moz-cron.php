<?php
/**
 * Class Smartcrawl_Controller_Moz_Cron
 *
 * @package    Smartcrawl
 * @subpackage Seomoz
 */

/**
 * Class Smartcrawl_Controller_Moz_Cron
 */
class Smartcrawl_Controller_Moz_Cron extends Smartcrawl_Base_Controller {

	use Smartcrawl_Singleton;

	const EVENT_HOOK = 'wds_daily_moz_data_hook';

	const OPTION_ID = 'wds-moz-data';

	/**
	 * Initialize the class.
	 *
	 * @return void
	 */
	protected function init() {
		add_action( 'admin_init', array( $this, 'schedule_moz_data_event' ) );
		add_action( self::EVENT_HOOK, array( $this, 'save_moz_data' ) );
	}

	/**
	 * Schedule cron event.
	 *
	 * @return void
	 */
	public function schedule_moz_data_event() {
		if ( ! wp_next_scheduled( self::EVENT_HOOK ) ) {
			wp_schedule_event( time(), 'daily', self::EVENT_HOOK );
		}
	}

	/**
	 * Save the moz data.
	 *
	 * @return void
	 */
	public function save_moz_data() {
		$access_id  = Smartcrawl_Settings::get_setting( 'access-id' );
		$secret_key = Smartcrawl_Settings::get_setting( 'secret-key' );

		if ( empty( $access_id ) || empty( $secret_key ) ) {
			return;
		}

		$target_url = preg_replace( '!http(s)?:\/\/!', '', home_url() );
		$api        = new Smartcrawl_Moz_API( $access_id, $secret_key );
		$urlmetrics = $api->urlmetrics( $target_url );

		$data           = get_option( self::OPTION_ID, array() );
		$data           = empty( $data ) || ! is_array( $data )
			? array()
			: $data;
		$data[ time() ] = $urlmetrics;
		update_option( self::OPTION_ID, $data );
	}
}