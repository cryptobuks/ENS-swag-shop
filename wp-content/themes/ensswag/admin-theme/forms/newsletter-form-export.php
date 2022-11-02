<?php

// This include gives us all the WordPress functionality
include_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );

global $wpdb;

$table = $wpdb->prefix.'ens_newsletter';

$query = $wpdb->get_results(
    "
      SELECT * FROM {$table}

    "
);

$separator  =   ";";    // Separator to be used in your file
$crlf       =   "\r\n";      // End of line
$content    =   '';

foreach ( $query as $value )
{
//    $content .= $value->name.$separator;
    $content .= $value->email.$separator;
    $content .= $crlf;
}

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Length: " . strlen($content));
header("Content-type: text/x-csv");

header("Content-Disposition: attachment; filename=csv-download.csv");
echo $content;
exit;