<?php
// File Security Check
if (!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    die ('You do not have sufficient permissions to access this page');
}

/**
 * Printful API cURL post/get
 *
 * @param $link
 * @param $postData
 * @return mixed
 */
function curl_post_PF($link, $postData = [])
{
    $token = get_option('wedevs_basics')['printful_api_key'];

    // Define headers
    $headers = array(
        'Authorization: Bearer ' . $token,
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json',
    );

    $ch = curl_init($link);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);// VERY IMPORTANT!!!

    if ($postData && sizeof($postData) > 0) {
        $curlpostdata = json_encode($postData);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlpostdata);
    }

    $response = curl_exec($ch);

    $curl_response_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

    curl_close($ch);

    $responseJSON = json_decode($response);

    return json_decode($response);
}

/**
 * Get task key
 *
 * This function will return task keys that is related
 * to the demanded mockups
 *
 * @param string $name
 * @param id $postID
 *
 * @return false|void
 */
function get_task_key($name, $postID)
{
    global $wpdb;

    $postContentData = get_post($postID);

    // Get domain id
    $postMetaData = $wpdb->get_row(
        "
                SELECT * FROM wenp_ens_product_meta
                WHERE post_id='{$postID}' LIMIT 1
        ");
    if (isset($postMetaData->id) && $postMetaData->id == 0) {
        return false;
    }

    $postData = [
        "variant_ids" => [$postMetaData->variant_id],
        "format" => "png",
        "files" => []
    ];

    // Set first file
    if ($postMetaData->first_file_placement != '') {
        $postData['files'][] = [
            "placement" => $postMetaData->first_file_placement,
            "image_url" =>
                (filter_var($postMetaData->first_file_image_url, FILTER_VALIDATE_URL) === FALSE) ?
                    get_site_url() . '/createImage.php?name=' . $name . '&width=' . $postMetaData->first_file_width . '&height=' . $postMetaData->first_file_height :
                    $postMetaData->first_file_image_url,
            "position" =>
                [
                    "area_width" => $postMetaData->first_file_area_width,
                    "area_height" => $postMetaData->first_file_area_height,
                    "width" => $postMetaData->first_file_width,
                    "height" => $postMetaData->first_file_height,
                    "top" => $postMetaData->first_file_top,
                    "left" => $postMetaData->first_file_left
                ]
        ];
    }

    // Set second file
    if ($postMetaData->second_file_placement != '') {
        $postData['files'][] = [
            "placement" => $postMetaData->second_file_placement,
            "image_url" =>
                (filter_var($postMetaData->second_file_image_url, FILTER_VALIDATE_URL) === FALSE) ?
                    get_site_url() . '/createImage.php?name=' . $name . '&width=' . $postMetaData->second_file_width . '&height=' . $postMetaData->second_file_height :
                    $postMetaData->second_file_image_url,
            "position" =>
                [
                    "area_width" => $postMetaData->second_file_area_width,
                    "area_height" => $postMetaData->second_file_area_height,
                    "width" => $postMetaData->second_file_width,
                    "height" => $postMetaData->second_file_height,
                    "top" => $postMetaData->second_file_top,
                    "left" => $postMetaData->second_file_left
                ]
        ];
    }

    // Set third file
    if ($postMetaData->third_file_placement != '') {
        $postData['files'][] = [
            "placement" => $postMetaData->third_file_placement,
            "image_url" =>
                (filter_var($postMetaData->third_file_image_url, FILTER_VALIDATE_URL) === FALSE) ?
                    get_site_url() . '/createImage.php?name=' . $name . '&width=' . $postMetaData->third_file_width . '&height=' . $postMetaData->third_file_height :
                    $postMetaData->third_file_image_url,
            "position" =>
                [
                    "area_width" => $postMetaData->third_file_area_width,
                    "area_height" => $postMetaData->third_file_area_height,
                    "width" => $postMetaData->third_file_width,
                    "height" => $postMetaData->third_file_height,
                    "top" => $postMetaData->third_file_top,
                    "left" => $postMetaData->third_file_left
                ]
        ];
    }

    $data = curl_post_PF('https://api.printful.com/mockup-generator/create-task/' . $postMetaData->catalog_id, $postData);

    if (!isset($data->error) && isset($data->code) && $data->code === 200) {
        return $data->result->task_key;
    } elseif (isset($data->error) && isset($data->code) && $data->code === 429) {
        sleep(40);
        return get_task_key($name);
    } else {
        return false;
    }

}

/**
 * Get task mockups
 *
 * @param string $taskKey
 *
 * @return false|void
 */
function get_task_mockups($taskKey)
{
    $data = curl_post_PF('https://api.printful.com/mockup-generator/task?task_key=' . $taskKey);

    if (!isset($data->error) && isset($data->code) && $data->code === 200 && isset($data->result->status) && $data->result->status == 'pending') {
        sleep(7);
        return get_task_mockups($taskKey);
    } elseif (!isset($data->error) && isset($data->code) && $data->code === 200) {
        return $data->result;
    } elseif (isset($data->error) && isset($data->code) && $data->code === 429) {

        echo '<h1>' . $data->result . '</h1>';
    } else {
        return false;
    }
}

/**
 * Save mockup image to ENS mockups
 *
 * @param string $imageURL
 * @param int $postID
 * @param int $ens_user_id
 * @param int $ens_domain_id
 * @param int $image_order
 *
 * @return array
 */
function saveMockupImage($imageURL, $postID = 0, $ens_user_id = 0, $ens_domain_id = 0, $image_order = 0)
{
    $returnData = [];

    if (trim($imageURL) != '' && $postID > 0) {

        // Parsed path
        $path = parse_url($imageURL, PHP_URL_PATH);
        $filename = basename($path);
        $WP_upload_directory = wp_upload_dir();

        // Set up file name
        $uploadFilename = $WP_upload_directory["path"] . "/" . $filename;
        $returnData['image_url'] = $WP_upload_directory["url"] . "/" . $filename;

        // Get file and save to WP uploads directory
        $contents = file_get_contents($imageURL);
        $savedFile = fopen($uploadFilename, "w");
        fwrite($savedFile, $contents);
        fclose($savedFile);

        $image = wp_get_image_editor($uploadFilename);

        if (!is_wp_error($image)) {

            // Save 712x712
            $image->resize(712, 712, true);
            $report712 = $image->save();
            $returnData['image_big_url'] = $WP_upload_directory["url"] . "/" . $report712['file'];

            // Save 210x210
            $image->resize(210, 210, true);
            $report210 = $image->save();
            $returnData['image_small_url'] = $WP_upload_directory["url"] . "/" . $report210['file'];

        }

        // Save image data to database
        $data = [
            'ens_user_id' => $ens_user_id,
            'ens_domain_id' => $ens_domain_id,
            'post_id' => $postID,
            'image_url' => $returnData['image_url'],
            'image_name' => $filename,
            'image_big_url' => (isset($returnData['image_big_url'])) ? $returnData['image_big_url'] : '',
            'image_big_name' => (isset($report712['file'])) ? $report712['file'] : '',
            'image_small_url' => (isset($returnData['image_small_url'])) ? $returnData['image_small_url'] : '',
            'image_small_name' => (isset($report210['file'])) ? $report210['file'] : '',
            'image_order' => $image_order,
            'created_at' => date("Y-m-d H:i:s"),
        ];

        $format = [
            '%d',
            '%d',
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%s',
        ];

        global $wpdb;

        $result = $wpdb->insert('wenp_ens_mockups', $data, $format);

    }

    return $returnData;
}

/**
 * Get shipping rate for specific product
 *
 * @param $variant_id
 * @param $address
 * @param $country_code
 * @param $state_code
 * @param $zip
 *
 * @return array|mixed
 */
function getShippingRatesForProduct($variant_id = 7854, $address = '19749 Dearborn St', $country_code = "US", $state_code = "CA", $zip = 91311)
{

    $postData = [
        "recipient" =>
            [
                "address1" => $address,
//        "city" => "Chatsworth",
                "country_code" => $country_code,
                "state_code" => $state_code,
                "zip" => $zip,
            ],
        "items" =>
            [
                [
                    "variant_id" => $variant_id,
                    "quantity" => 1,
                ]
            ],
        "currency" => "USD",
        "locale" => "en_US"
    ];

    $data = curl_post_PF('https://api.printful.com/shipping/rates', $postData);

    if(isset($data->code) && $data->code == 200){
        return $data->result[0];
    }

    return [];

}

/**
 * Calculate cart shipping cost by Printful data
 *
 * @return mixed|string|void
 */
function getCartShippingCost(){

    $postData = [
        "recipient" =>
            [
                "address1" => WC()->customer->get_shipping_address(),
                "city" => WC()->customer->get_shipping_city(),
                "country_code" => WC()->customer->get_shipping_country(),
                "state_code" => WC()->customer->get_shipping_state(),
                "zip" => WC()->customer->get_shipping_postcode(),
            ],
        "items" => [],
        "currency" => "USD",
        "locale" => "en_US"
    ];

    global $wpdb;

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        // Get variant id
        $variantID = $cart_item['product_id'];

        // Check only if it is printful product
        if( has_term(26, 'product_cat', $variantID) ){
            $postMetaData = $wpdb->get_results(
                "
                    SELECT id, variant_id FROM wenp_ens_product_meta
                    WHERE post_id='{$variantID}' LIMIT 1
            ");
            if (isset($postMetaData->id) && $postMetaData->id == 0) {
                $variantID = $postMetaData->variant_id;
            }

            $postData["items"][] = [
                "variant_id" => $variantID,
                "quantity" => $cart_item['quantity'],
            ];
        }
    }

    $data = curl_post_PF('https://api.printful.com/shipping/rates', $postData);

    if(isset($data->code) && $data->code == 200){
        return $data->result[0]->rate;
    }

    return '10.00';

}