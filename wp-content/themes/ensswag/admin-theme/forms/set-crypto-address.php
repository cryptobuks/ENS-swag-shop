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
    $enssign = filter_var($_POST['enssign']);
    $wagmiwallet = filter_var($_POST['wagmiwallet']);

    if(trim($address) != '' && $address !== 'undefined' && trim($enshash) != ''){
        saveUserAddressLog($address, $enshash, $enssign, $wagmiwallet);
    }

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


/**
 * Save all data related to user address
 *
 * @param $address
 * @param $enshash
 * @param $enssign
 * @param $wagmiwallet
 * @return bool
 */
function saveUserAddressLog($address, $enshash, $enssign, $wagmiwallet){

    global $wpdb;

    $query = $wpdb->get_row(
        "
                SELECT id FROM wenp_ens_user_logs
                WHERE user_address='{$address}' AND  user_hash='{$enshash}' LIMIT 1
        ");

    // Update log
    if (isset($query->id) && $query->id > 0) {
        $data = [
            'user_address' => $address,
            'user_hash' => $enshash,
            'user_sign' => $enssign,
            'user_wagmi_wallet' => str_replace('"', '', $wagmiwallet),
            'user_server' => json_encode($_SERVER),
            'user_session' => json_encode($_SESSION),
        ];

        $format = [
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
        ];

        $data_where = ['id' => $query->id];
        $where_format = ['%s'];

        $result = $wpdb->update('wenp_ens_user_logs', $data, $data_where, $format, $where_format);
    }
    // Insert log
    else{
        $data = [
            'user_address' => $address,
            'user_hash' => $enshash,
            'user_sign' => $enssign,
            'user_wagmi_wallet' => str_replace('"', '', $wagmiwallet),
            'user_server' => json_encode($_SERVER),
            'user_session' => json_encode($_SESSION),
            'created_at' => date("Y-m-d H:i:s"),
        ];

        $format = [
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
        ];

        $result = $wpdb->insert('wenp_ens_user_logs', $data, $format);

    }

    return true;
}