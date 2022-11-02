<?php

/**
 * Set up crypto address
 *
 */
function set_crypto_address()
{

    $return_data = array();
    $return_data['status'] = 0;

    // Get post data
    $address = filter_var($_POST['address']);
    $enshash = filter_var($_POST['enshash']);

    if (trim($address) != '' && $address !== 'undefined') {

        $_SESSION['user_wallet_address'] = $address;
        $_SESSION['wa_hash'] = $enshash;

        global $wpdb;

        $query = $wpdb->get_results(
            "
                    SELECT * FROM wenp_ens_users
                    WHERE address='{$address}' LIMIT 1
        ");

        // Save address if it doesn't exist
        if (sizeof($query) == 0) {
            $data = [
                'address' => $address,
                'created_at' => date("Y-m-d H:i:s"),
            ];

            $format = [
                '%s',
                '%s',
            ];

            $result = $wpdb->insert('wenp_ens_users', $data, $format);
        }

        $return_data['status'] = 1;

    } else {
        $_SESSION['user_wallet_address'] = '';
        $return_data['status'] = 2;
    }

    echo json_encode($return_data);

}

add_action('wp_ajax_nopriv_set_crypto_address', 'set_crypto_address');
add_action('wp_ajax_set_crypto_address', 'set_crypto_address');