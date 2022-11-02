<?php
/**
 * Template Name: Webhook Printful
 */

$data = file_get_contents("php://input");

// Insert log data
$dataInsert = [
    'printful_data' => $data,
    'created_at' => date("Y-m-d H:i:s"),
];

$format = [
    '%s',
    '%s',
];

$result = $wpdb->insert('wenp_ens_order_data_log', $dataInsert, $format);

$json = json_decode($data);

if( $json ){

    // Order received mark as processing
    if($json->type == 'order_created' && isset($json->data->order->status) && $json->data->order->status != 'draft'){
        $order = wc_get_order(filter_var($json->data->order->external_id));
        if($order){
            $order->payment_complete();
        }
    }

    // Order shipped mark as completed
    if($json->type == 'package_shipped'){
        $order = wc_get_order(filter_var($json->data->order->external_id));
        if($order){
            $order->update_status( 'completed' );
        }
    }

    // Order on hold mark as on hold
    if($json->type == 'order_put_hold'){
        $order = wc_get_order(filter_var($json->data->order->external_id));
        if($order){
            $order->update_status( 'on-hold' );
        }
    }

    // Order returned or failed or canceled mark as canceled
    if($json->type == 'package_returned' || $json->type == 'order_failed' || $json->type == 'order_canceled' || $json->type == 'order_deleted'){
        $order = wc_get_order(filter_var($json->data->order->external_id));
        if($order){
            $order->update_status( 'cancelled' );
        }
    }

}