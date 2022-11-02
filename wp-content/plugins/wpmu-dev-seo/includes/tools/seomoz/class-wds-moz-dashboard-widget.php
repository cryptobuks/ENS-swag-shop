<?php
/**
 * Class Smartcrawl_Moz_API
 *
 * @package    Smartcrawl
 * @subpackage Seomoz
 */

/**
 * Init WDS SEOMoz Dashboard Widget
 *
 * TODO: get rid of this widget and move the information it contains to an SC dashboard widget
 */
class Smartcrawl_Moz_Dashboard_Widget extends Smartcrawl_Base_Controller {

	use Smartcrawl_Singleton;

	/**
	 * Check if we can run the dashboard widget.
	 *
	 * @return bool
	 */
	public function should_run() {
		return (
			Smartcrawl_Settings_Admin::is_tab_allowed( Smartcrawl_Settings::TAB_AUTOLINKS )
			&& Smartcrawl_Settings::get_setting( 'access-id' )
			&& Smartcrawl_Settings::get_setting( 'secret-key' )
		);
	}

	/**
	 * Add a widget to WP Dashboard.
	 *
	 * @return void
	 */
	protected function init() {
		add_action( 'wp_dashboard_setup', array( &$this, 'dashboard_widget' ) );
	}

	/**
	 * Dashboard Widget callback.
	 *
	 * @return void
	 */
	public function dashboard_widget() {
		// Continue only if edit post capability is found.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		wp_add_dashboard_widget(
			'wds_seomoz_dashboard_widget',
			__( 'Moz - SmartCrawl', 'wds' ),
			array(
				&$this,
				'widget',
			)
		);
	}

	/**
	 * Render widget content.
	 *
	 * @return void
	 */
	public static function widget() {
		$renderer = Smartcrawl_Moz_Results_Renderer::get();
		?>
		<div class="<?php echo esc_attr( smartcrawl_sui_class() ); ?>">
			<div class="sui-wrap">
				<?php
				$renderer->render(
					get_bloginfo( 'url' ),
					'seomoz-dashboard-widget'
				);
				?>
			</div>
		</div>
		<?php
	}
}