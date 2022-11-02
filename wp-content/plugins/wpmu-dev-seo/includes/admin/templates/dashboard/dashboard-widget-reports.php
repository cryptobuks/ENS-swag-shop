<?php
$health_available  = is_main_site();
$crawler_available = Smartcrawl_Sitemap_Utils::crawler_available();
if ( ! $health_available && ! $crawler_available ) {
	return;
}
$service  = Smartcrawl_Service::get( Smartcrawl_Service::SERVICE_SITE );
$template = $service->is_member() ? 'dashboard/dashboard-reports-full' : 'dashboard/dashboard-reports-free';
$this->render_view( $template );