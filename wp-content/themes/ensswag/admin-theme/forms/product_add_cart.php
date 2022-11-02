<?php

/**
 * Get items that are already added to cart for current
 * productID
 *
 * @param $productID
 *
 * @return string
 */
function getProductAlreadyItemsInCartHtml($productID)
{

    $html = '';

    global $wpdb;

    $cart = WC()->cart->get_cart();

    if ($cart && sizeof($cart) > 0) {
        foreach ($cart as $key => $cartItem) {

            if ($cartItem['product_id'] == $productID) {

                $domainName = (isset($cartItem['ensName']) && $cartItem['ensName'] != '0') ? $cartItem['ensName'] : 'nick.eth';
                $domainAvatar = TEMPLATEDIR . '/images/default-avatar.svg';

                $domainData = $wpdb->get_row("
                    SELECT * FROM wenp_ens_domains
                    WHERE name='{$domainName}' LIMIT 1
                ");

                if (isset($domainData->id) && $domainData->id > 0) {
                    $domainAvatar = $domainData->avatar_url;
                }

                $html .= '
                    <div id="al-' . $key . '" class="already-in-cart my-2">
                        <div class="cart-domain-holder">
                            <img src="' . $domainAvatar . '" alt="">
                            <div class="cart-domain-name">' . $domainName . '</div>
                        </div>
                        <div class="cart-quantity"> x ' . $cartItem['quantity'] . '</div>
                        <a href="javascript:void(0);" onclick="removeProductFromCart(\'' . $key . '\', ' . $productID . ');" class="remove-item">' . __('Remove', 'template') . '</a>
                    </div>
                ';
            }
        }
    }

    return $html;

}

/**
 * PRODUCT ADD CART
 */
function product_add_cart()
{

    global $wpdb;

    $return_data = array();
    $return_data['status'] = 0;
    $return_data['total_cart'] = 0;
    $return_data['cart_already_html'] = '';

    //get post data
    $productID = filter_var($_POST['product_id']);
    $quantity = filter_var($_POST['quantity']);
    $domain = filter_var($_POST['domain']);

    if (filter_var($productID) > 0 && trim($domain) != '' && $quantity > 0) {

        // Check if domain is user domain
        $address = (isset($_SESSION['user_wallet_address']))? filter_var($_SESSION['user_wallet_address']) : '';
        $userID = 0;
        $query = $wpdb->get_row(
            "
                    SELECT * FROM wenp_ens_users
                    WHERE address='{$address}' LIMIT 1
        ");

        if (isset($query->id) && $query->id > 0) {
            $userID = $query->id;
        }

        $domainID = 0;
        $queryD = $wpdb->get_row(
        "
                SELECT * FROM wenp_ens_domains
                WHERE name='{$domain}' AND ens_user_id='{$userID}' LIMIT 1
        ");

        if (isset($queryD->id) && $queryD->id > 0) {
            $domainID = $queryD->id;
        }

        if( isset($_SESSION['user_wallet_address']) && $userID > 0 && $address != '' && $domainID > 0 ){
            $result = WC()->cart->add_to_cart($productID, $quantity, 0, [], ['ensName' => $domain, 'wAddress' => $_SESSION['user_wallet_address']]);

            $return_data['total_cart'] = WC()->cart->cart_contents_count;

            $return_data['cart_already_html'] = getProductAlreadyItemsInCartHtml($productID);

            $return_data['status'] = 1;
        }
        else{
            $return_data['status'] = 3;
        }

    } else {
        $return_data['status'] = 2;
    }

    echo json_encode($return_data);

}

add_action('wp_ajax_nopriv_product_add_cart', 'product_add_cart');
add_action('wp_ajax_product_add_cart', 'product_add_cart');