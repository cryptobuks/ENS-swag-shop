<?php

include('vendor/autoload.php');

use Agustind\EthSignature;

/**
 * Validate signature
 *
 */
function validate_signature()
{
    $return_data = array();

    $return_data['status'] = 0;

    // Get post data
    $sign = filter_var($_POST['sign']);

    if (
        trim($sign) != '' && $sign !== 'undefined' &&
        isset($_SESSION['user_wallet_address']) &&
        isset($_SESSION['aw_hash'])
    ) {
        $_SESSION['aw_signature'] = $sign;

        $signature = new EthSignature();

        $is_valid = $signature->verify(
            $_SESSION['aw_hash'],
            $sign,
            $_SESSION['user_wallet_address']);

        if($is_valid){
            $return_data['status'] = 1;
        }else{
            $return_data['status'] = 2;
            $_SESSION['aw_signature'] = '';
        }

    } else {
        $return_data['status'] = 2;
        $_SESSION['aw_signature'] = '';
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($return_data);
    die();
}

add_action('wp_ajax_nopriv_validate_signature', 'validate_signature');
add_action('wp_ajax_validate_signature', 'validate_signature');