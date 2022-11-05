<?php

/**
 * Set up ENS domains
 *
 */
function set_ens_domains()
{
    $defaultDomainImage = TEMPLATEDIR . '/images/default-avatar.svg';

    $return_data = array();
    $return_data['status'] = 0;
    $return_data['no_domains'] = 0;
    $return_data['ascii_status'] = '';
    $return_data['user_domains'] = [];
    $return_data['default_domain'] = [
        'value' => 'nick.eth',
        'name' => 'nick.eth',
        'image' => $defaultDomainImage
    ];

    // Get post data
    $address = filter_var($_POST['address']);
    $domains = ( isset($_POST['domains']) && is_array($_POST['domains']) )? filter_var_array($_POST['domains']) : [];

    if (trim($address) != '' && $address !== 'undefined' && $domains && sizeof($domains) > 0) {

        $_SESSION['user_wallet_address'] = $address;

        global $wpdb;

        // Get user ID
        $userID = 0;
        $query = $wpdb->get_row(
            "
                    SELECT * FROM wenp_ens_users
                    WHERE address='{$address}' LIMIT 1
        ");

        if (isset($query->id) && $query->id > 0) {
            $userID = $query->id;
        }

        // Save domains only if we have user ID
        if ($userID > 0) {

            $providedDomains = [];

            foreach ($domains as $domain) {
                if ($domain['__typename'] === 'Domain') {

                    $name = $providedDomains[] = filter_var($domain['name']);

                    // Check if domain exist
                    $query = $wpdb->get_row(
                        "
                                SELECT * FROM wenp_ens_domains
                                WHERE name='{$name}' AND ens_user_id='{$userID}' LIMIT 1
                    ");

                    // If domain has non-ASCII name or its name is longer than 13 characters
                    $active = 1;
                    $nameArray = explode('.', $name);

                    if(sizeof($nameArray) > 0 && strlen($nameArray[0]) > 13){
                        $return_data['ascii_status'] = $_SESSION['ascii_status'] = 'Only names shorter than 13 characters (excl. “.eth”) and ASCII characters supported.';
                        $active = 0;
                    }
                    elseif(sizeof($nameArray) > 0 && mb_detect_encoding($nameArray[0], 'ASCII', true) == FALSE){
                        $return_data['ascii_status'] = $_SESSION['ascii_status'] = 'Only names shorter than 13 characters (excl. “.eth”) and ASCII characters supported.';
                        $active = 0;
                    }

                    // Update domain data
                    if (isset($query->id) && $query->id > 0) {

                        $data = [
                            'ens_user_id' => $userID,
                            'domain_id' => filter_var($domain['id']),
                            'name' => filter_var($domain['name']),
                            'avatar_url' => (isset($domain['avatar_url']['linkage'][1]['content']))? filter_var($domain['avatar_url']['linkage'][1]['content']) : TEMPLATEDIR . '/images/default-avatar.svg',
                            'labelName' => filter_var($domain['labelName']),
                            'labelhash' => filter_var($domain['labelhash']),
                            'resolvedAddress' => filter_var($domain['resolvedAddress']['id']),
                            'active' => $active,
                        ];

                        $format = [
                            '%d',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%d',
                        ];

                        $data_where = ['id' => $query->id];
                        $where_format = ['%s'];

                        $result = $wpdb->update('wenp_ens_domains', $data, $data_where, $format, $where_format);

                    } // Add new one
                    else {

                        $data = [
                            'ens_user_id' => $userID,
                            'domain_id' => filter_var($domain['id']),
                            'name' => filter_var($domain['name']),
                            'avatar_url' => (isset($domain['avatar_url']['linkage'][1]['content']))? filter_var($domain['avatar_url']['linkage'][1]['content']) : TEMPLATEDIR . '/images/default-avatar.svg',
                            'labelName' => filter_var($domain['labelName']),
                            'labelhash' => filter_var($domain['labelhash']),
                            'resolvedAddress' => filter_var($domain['resolvedAddress']['id']),
                            'active' => $active,
                            'created_at' => date("Y-m-d H:i:s"),
                        ];

                        $format = [
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

                        $result = $wpdb->insert('wenp_ens_domains', $data, $format);

                    }

                }
            }

            // Check every user domain that exist in database
            // if not deactivate it
            $query = $wpdb->get_results(
                "
                    SELECT * FROM wenp_ens_domains
                    WHERE ens_user_id='{$userID}'
                "
            );

            if (sizeof($query) > 0) {
                foreach ($query as $item) {
                    if (!in_array($item->name, $providedDomains)) {
                        $data = [
                            'active' => 0,
                        ];

                        $format = [
                            '%d',
                        ];

                        $data_where = ['id' => $item->id];
                        $where_format = ['%s'];

                        $result = $wpdb->update('wenp_ens_domains', $data, $data_where, $format, $where_format);
                    }
                }
            }

            // Get active users domains
            $query = $wpdb->get_results(
                "
                    SELECT * FROM wenp_ens_domains
                    WHERE ens_user_id='{$userID}' AND active=1 ORDER BY name ASC
                "
            );

            if (sizeof($query) > 0) {
                $return_data['user_domains'] = $_SESSION['user_ens_domains'] = $query;
                $return_data['status'] = 1;
            }
            else{
                $return_data['status'] = 2;
            }

            // Check if user changed address here in cart items
            checkItemsInCartWithAddress($_SESSION['user_wallet_address']);

        } else {
            $return_data['status'] = 2;
        }

    } else {
        $return_data['status'] = 2;
    }

    // Reset session variables there isn't any domains from this account
    // This will also happen when user change her account and have no domains
    if ($return_data['status'] == 2) {
        $return_data['no_domains'] = 1;
        $_SESSION['ascii_status'] = '';
        removeAllCartItemsIfExist();
    }

    $return_data['total_cart'] = WC()->cart->cart_contents_count;

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($return_data);
    die();
}

add_action('wp_ajax_nopriv_set_ens_domains', 'set_ens_domains');
add_action('wp_ajax_set_ens_domains', 'set_ens_domains');

/**
 * Remove all items from cart
 *
 * @return boolean
 */
function removeAllCartItemsIfExist(){

    global $woocommerce;
    $woocommerce->cart->empty_cart();
    WC()->session->set('cart', array());
    WC()->session->forget_session();

    return true;
}

/**
 * Check if items in cart is related to provide wallet address
 * if item in cart has another wallet address remove it
 *
 * @param $user_wallet_address
 *
 * @return bool
 */
function checkItemsInCartWithAddress($user_wallet_address){

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if(!isset($cart_item['wAddress'])){
            WC()->cart->remove_cart_item( $cart_item_key );
        }
        elseif($cart_item['wAddress'] != $user_wallet_address){
            WC()->cart->remove_cart_item( $cart_item_key );
        }
    }

    return true;
}