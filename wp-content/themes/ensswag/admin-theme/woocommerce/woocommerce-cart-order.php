<?php


use Agustind\EthSignature;

add_action( 'woocommerce_checkout_create_order', 'ens_woocommerce_checkout_create_order_action', 10, 2 );

/**
 * Check wallet address signature before opening order
 *
 * @param  $order
 * @param  $data
 *
 * @return void
 */
function ens_woocommerce_checkout_create_order_action( $order, $data ){

    $orderSignError = false;

    if( !isset($_POST['wa_signature']) ){
        unset($_POST);
        $orderSignError = true;
    }
    else{
        if (
            isset($_SESSION['user_wallet_address'])
        ) {

            $signature = new EthSignature();

            $is_valid = $signature->verify(
                $_SESSION['wa_hash'],
                $_POST['wa_signature'],
                $_SESSION['user_wallet_address']);

            if(!$is_valid){
                $orderSignError = true;
            }

        }
        else{
            $orderSignError = true;
        }
    }

    // Check sign hash message from database if fail - Ledger problem
    if($orderSignError){
        $address = filter_var($_SESSION['user_wallet_address']);
        $enssign = filter_var($_POST['wa_signature']);

        global $wpdb;

        $query = $wpdb->get_row(
            "
                SELECT * FROM wenp_ens_user_logs
                WHERE user_address='{$address}' AND user_sign='{$enssign}' LIMIT 1
        ");

        if (isset($query->id) && $query->id > 0) {
            $dbSignature = $query->user_sign;

            $lastTwoCharacterSign = substr($dbSignature, -2);
            if($lastTwoCharacterSign == '00' || $lastTwoCharacterSign == '01') {

                $dbSignature = ($lastTwoCharacterSign == '00') ? substr($dbSignature, 0, -2) . '1B' : $dbSignature;
                $dbSignature = ($lastTwoCharacterSign == '01') ? substr($dbSignature, 0, -2) . '1C' : $dbSignature;

                $is_valid = $signature->verify(
                    $_SESSION['wa_hash'],
                    $dbSignature,
                    $_SESSION['user_wallet_address']
                );

                $return_data['drugi sign'] = $is_valid;

                if($is_valid){
                    $orderSignError = false;
                }
            }

        }
    }

    if($orderSignError){
        $_SESSION['wa_hash'] = '';
        $_SESSION['user_wallet_address'] = '';
        $_SESSION['user_ens_domains'] = [];

        $return_data['singature_order_check'] = 0;

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($return_data);

        die();
    }

}

add_action('woocommerce_checkout_order_processed', 'ens_checkout_processed', 1, 1);

add_action('woocommerce_checkout_order_processed', 'ens_checkout_processed', 1, 1);

/**
 * Manage cart processed.
 *
 * When order is paid or submitted we must send it to Printful,
 * check submission and make order in processed state on Printful
 *
 * @param $order_id
 *
 * @return void
 *
 * @throws Exception
 */
function ens_checkout_processed($order_id)
{
    // Check if order_id exist
    if ($order_id) {

        global $wpdb;

        $cartItems = WC()->cart->get_cart();

        $order = wc_get_order($order_id);
        $items = $order->get_items();

        // Save ENS name to order item metadata
        $checkAdded = [];
        foreach ($items as $key => $item) {
            $productID = $item['product_id'];
            $variationID = $item['variation_id'];

            foreach ($cartItems as $keyCarItem => $itemCart) {
                $cartItemVariationID = $itemCart['variation_id'];
                $ensName = (isset($itemCart['ensName']) && trim($itemCart['ensName']) != '') ? $itemCart['ensName'] : false;

                // Save ENS name to order metadata and check if order item already has some ENS name attached
                // Also check variation IDs, we can have similar product item we two different color, size or etc
                if ($ensName && $productID == $itemCart['product_id'] && $variationID == $cartItemVariationID && !in_array($ensName, $checkAdded)) {
                    wc_add_order_item_meta($key, 'ens_meta', $ensName);

                    // Save for checking entry
                    $checkAdded[] = $ensName;
                    break; // One order item can only have one ENS name
                }
            }

        }

        // Set up data for Printful
        $printfulOrderData = [
            "external_id" => $order_id,
            "shipping" => "STANDARD",
            "confirm" => true,
            "recipient" => [
                "name" => $order->get_formatted_shipping_full_name(),
                "address1" => $order->get_shipping_address_1(),
                "address2" => $order->get_shipping_address_2(),
                "city" => $order->get_shipping_city(),
                "state_code" => $order->get_shipping_state(),
                "state_name" => $order->get_shipping_state(),
                "country_code" => $order->get_shipping_country(),
                "country_name" => WC()->countries->countries[$order->get_shipping_country()],
                "zip" => $order->get_shipping_postcode(),
                "phone" => $order->get_billing_phone(),
                "email" => $order->get_billing_email(),
            ],
            "retail_costs" => [
                "currency" => $order->get_currency(),
                "subtotal" => $order->get_subtotal(),
                "discount" => "0",
                "shipping" => $order->get_shipping_total(),
                "digitization" => "0",
                "tax" => $order->get_cart_tax(),
                "vat" => "0",
                "total" => $order->get_total()

            ],
            "packing_slip" => []
        ];

        // Add Items
        $printfulOrderData['items'] = [];


        foreach ($items as $key => $item) {

            $productID = $item->get_product_id();

            // Get Printful data
            $variant_id_printful = 0;
            $product_id_prinful = 0;
            $catalog_id_prinful = 0;
            $postMetaData = $wpdb->get_row("
                SELECT * FROM wenp_ens_product_meta
                WHERE post_id='{$productID}' LIMIT 1
            ");

            if (isset($postMetaData->id) && $postMetaData->id > 0) {
                $variant_id_printful = $postMetaData->variant_id;
                $product_id_prinful = $postMetaData->product_id;
                $catalog_id_prinful = $postMetaData->catalog_id;
            }

            $product = wc_get_product($productID);
            $ensName = wc_get_order_item_meta($key, 'ens_meta');
            $ensName = ($ensName) ? $ensName : 'nick.eth';

            $thumbID = get_post_thumbnail_id($productID);
            $imageBig = wp_get_attachment_image_src($thumbID, 'full');

            $printfulOrderData['items'][] = [
                "id" => $product_id_prinful,
                "external_id" => $item->get_product_id() . '_' . $key,
                "variant_id" => $variant_id_printful,
                "quantity" => $item->get_quantity(),
                "price" => $product->get_price(),
                "retail_price" => $product->get_price(),
                "name" => $item['name'],
                "product" =>
                    [
                        "variant_id" => $variant_id_printful,
                        "product_id" => $catalog_id_prinful,
                        "image" => $imageBig[0],
                        "name" => $item['name']
                    ],
                "files" => [],
                "sku" => $product->get_sku(),
                "options" => []
            ];

            $itemsKey = array_key_last($printfulOrderData['items']);

            // Set first file
            if ($postMetaData->first_file_placement != '') {
                $printfulOrderData['items'][$itemsKey]['files'][] = [
                    "type" => $postMetaData->first_file_placement,
                    "url" =>
                        (filter_var($postMetaData->first_file_image_url, FILTER_VALIDATE_URL) === FALSE) ?
                            get_site_url() . '/createImage.php?name=' . $ensName :
                            $postMetaData->first_file_image_url,
                    "visible" => true,
                    "position" =>
                        [
                            "area_width" => $postMetaData->first_file_area_width,
                            "area_height" => $postMetaData->first_file_area_height,
                            "width" => $postMetaData->first_file_width,
                            "height" => $postMetaData->first_file_height,
                            "top" => $postMetaData->first_file_top,
                            "left" => $postMetaData->first_file_left,
                            "limit_to_print_area" => true
                        ],
                    "options" => [
                        [
                            "id" => "auto_thread_color",
                            "value" => true
                        ]
                    ]
                ];

                // Check options data
                if( trim($postMetaData->first_file_thread_position) != '' && trim($postMetaData->first_file_thread_position_colors) != '' ){
                    $threadColorsArray = explode(',', $postMetaData->first_file_thread_position_colors);
                    $printfulOrderData['items'][$itemsKey]['options'][] = [
                        "id" => trim($postMetaData->first_file_thread_position),
                        "value" => $threadColorsArray
                    ];
                }
            }

            // Set second file
            if ($postMetaData->second_file_placement != '') {
                $printfulOrderData['items'][$itemsKey]['files'][] = [
                    "type" => $postMetaData->second_file_placement,
                    "url" =>
                        (filter_var($postMetaData->second_file_image_url, FILTER_VALIDATE_URL) === FALSE) ?
                            get_site_url() . '/createImage.php?name=' . $ensName :
                            $postMetaData->second_file_image_url,
                    "visible" => true,
                    "position" =>
                        [
                            "area_width" => $postMetaData->second_file_area_width,
                            "area_height" => $postMetaData->second_file_area_height,
                            "width" => $postMetaData->second_file_width,
                            "height" => $postMetaData->second_file_height,
                            "top" => $postMetaData->second_file_top,
                            "left" => $postMetaData->second_file_left
                        ],
                    "options" => [
                        [
                            "id" => "auto_thread_color",
                            "value" => true
                        ]
                    ]
                ];

                // Check options data
                if( trim($postMetaData->second_file_thread_position) != '' && trim($postMetaData->second_file_thread_position_colors) != '' ){
                    $threadColorsArray = explode(',', $postMetaData->second_file_thread_position_colors);
                    $printfulOrderData['items'][$itemsKey]['options'][] = [
                        "id" => trim($postMetaData->second_file_thread_position),
                        "value" => $threadColorsArray
                    ];
                }
            }

            // Set third file
            if ($postMetaData->third_file_placement != '') {
                $printfulOrderData['items'][$itemsKey]['files'][] = [
                    "type" => $postMetaData->third_file_placement,
                    "url" =>
                        (filter_var($postMetaData->third_file_image_url, FILTER_VALIDATE_URL) === FALSE) ?
                            get_site_url() . '/createImage.php?name=' . $ensName :
                            $postMetaData->third_file_image_url,
                    "visible" => true,
                    "position" =>
                        [
                            "area_width" => $postMetaData->third_file_area_width,
                            "area_height" => $postMetaData->third_file_area_height,
                            "width" => $postMetaData->third_file_width,
                            "height" => $postMetaData->third_file_height,
                            "top" => $postMetaData->third_file_top,
                            "left" => $postMetaData->third_file_left
                        ],
                    "options" => [
                        [
                            "id" => "auto_thread_color",
                            "value" => true
                        ]
                    ]
                ];

                // Check options data
                if( trim($postMetaData->third_file_thread_position) != '' && trim($postMetaData->third_file_thread_position_colors) != '' ){
                    $threadColorsArray = explode(',', $postMetaData->third_file_thread_position_colors);
                    $printfulOrderData['items'][$itemsKey]['options'][] = [
                        "id" => trim($postMetaData->third_file_thread_position),
                        "value" => $threadColorsArray
                    ];
                }
            }

            // Check if we have files JSON object
            if(trim($postMetaData->files) !== ''){
                $jsonFiles = json_decode(str_replace('\"', '"', $postMetaData->files), TRUE);
                if(sizeof($jsonFiles) > 0){
                    $printfulOrderData['items'][$itemsKey]['files'] = $jsonFiles;
                }
            }

        }

        /* Submit order to Printful */

        // Paid with crypto
        if($order->get_payment_method() == 'cpwcw'){
            $data = curl_post_PF('https://api.printful.com/orders', $printfulOrderData);
        }

        // Paid with stripe/cart
        if($order->get_payment_method() == 'stripe'){
            $data = curl_post_PF('https://api.printful.com/orders?confirm=1', $printfulOrderData);
        }

        // Insert log data
        $data = [
            'order_id' => $order_id,
            'printful_data_sent' => json_encode($printfulOrderData),
            'order_data' => json_encode($order->get_data()),
            'printful_data' => json_encode($data),
            'created_at' => date("Y-m-d H:i:s"),
        ];

        $format = [
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
        ];

        $result = $wpdb->insert('wenp_ens_order_data_log', $data, $format);

        // Set up Order in WooCommerce to "processing" and also on Printful
        // if everything alright
        if ($data && isset($data->code) && $data->code == 200) {
            if (isset($data->result) && $data->result->id > 0) {

                // Set WocCoommerce order to processing
                $order->payment_complete();

            }
        }

    }

}

/**
 * Crypto payment done, change order status
 */
add_action('CryptoPay/WooCommerce/PaymentFinished', function($userId, $order, $paymentInfo) {
    if ($paymentInfo->status == 'verified') {
//        $order->payment_complete('processing');
        $order_id = $order->get_id();
        $data = curl_post_PF("https://api.printful.com/orders/@$order_id/confirm", ['id' => '@'.$order_id]);
    }
}, 10, 3);

/**
 * Check if crypto payment goes well
 * if it is make order to print on
 * Printful
 *
 */
function swag_status_completed($order_id, $old_status, $new_status)
{
    if($old_status == 'processing' && $new_status == 'on-hold'){
        $data = curl_post_PF("https://api.printful.com/orders/@$order_id/confirm", ['id' => '@'.$order_id]);
    }
    if($old_status == 'processing' && $new_status == 'completed'){
        $data = curl_post_PF("https://api.printful.com/orders/@$order_id/confirm", ['id' => '@'.$order_id]);
    }
    if($old_status == 'pending' && $new_status == 'processing'){
        $data = curl_post_PF("https://api.printful.com/orders/@$order_id/confirm", ['id' => '@'.$order_id]);
    }
}
add_action('woocommerce_order_status_changed', 'swag_status_completed', 10, 3);
