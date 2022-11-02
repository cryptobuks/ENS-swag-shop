<?php
$lighthouse_start_time = ! empty( $lighthouse_start_time );
/**
 * Report.
 *
 * @var $lighthouse_report Smartcrawl_Lighthouse_Report
 */
$lighthouse_report = empty( $lighthouse_report ) ? false : $lighthouse_report;
if ( ! $lighthouse_report ) {
	return;
}

if ( $lighthouse_report->has_errors() ) {
	$this->render_view( 'lighthouse/lighthouse-error' );
} elseif ( ! $lighthouse_report->has_data() || $lighthouse_start_time ) {
	$this->render_view( 'lighthouse/lighthouse-no-data' );
} else {
	$this->render_view( 'lighthouse/lighthouse-report' );
}