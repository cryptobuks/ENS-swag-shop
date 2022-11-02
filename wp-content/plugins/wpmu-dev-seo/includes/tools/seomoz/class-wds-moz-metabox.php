<?php
/**
 * Class Smartcrawl_Moz_Metabox
 *
 * @package    Smartcrawl
 * @subpackage Seomoz
 */

/**
 * Init WDS SEOMoz Results
 */
class Smartcrawl_Moz_Metabox extends Smartcrawl_Base_Controller {

	use Smartcrawl_Singleton;

	/**
	 * Can we add meta box.
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
	 * Init the class.
	 *
	 * @return  void
	 */
	protected function init() {
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
	}

	/**
	 * Adds a box to the main column on the Post and Page edit screens.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		$show = user_can_see_urlmetrics_metabox();

		foreach ( get_post_types() as $post_type ) {
			if ( $show ) {
				add_meta_box(
					'wds_seomoz_urlmetrics',
					__( 'Moz URL Metrics - SmartCrawl', 'wds' ),
					array( &$this, 'urlmetrics_box' ),
					$post_type,
					'normal',
					'high'
				);
			}
		}
	}

	/**
	 * Prints the box content.
	 *
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function urlmetrics_box( $post ) {
		$renderer = Smartcrawl_Moz_Results_Renderer::get();
		?>
		<div class="<?php echo esc_attr( smartcrawl_sui_class() ); ?>">
			<div class="<?php smartcrawl_wrap_class( 'wds-metabox' ); ?>">
				<div class="wds-metabox-section">
					<?php
					$renderer->render(
						get_permalink( $post->ID ),
						'urlmetrics-metabox'
					);
					?>
				</div>
			</div>
		</div>
		<?php
	}
}