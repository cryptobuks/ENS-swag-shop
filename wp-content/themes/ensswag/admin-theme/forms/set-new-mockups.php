<?php

/**
 * Set new mockups
 *
 */
function set_new_mockups()
{

    $return_data = array();
    $return_data['status'] = 0;
    $return_data['images'] = [];

    $postID = filter_var($_POST['postID']);
    $domain = filter_var($_POST['domain']);

    if (
        trim($postID) != '' && $postID !== 'undefined' && $postID > 0 &&
        trim($domain) != '' && $domain != '0' &&
        isset($_SESSION['user_wallet_address']) && $_SESSION['user_wallet_address'] != ''
    ) {

        global $wpdb;

        $user_address = filter_var($_SESSION['user_wallet_address']);
        $ens_user_id = 0;
        $ens_domain_id = 0;

        // Get user id
        $query = $wpdb->get_row(
            "
                SELECT id FROM wenp_ens_users
                WHERE address='{$user_address}' LIMIT 1
        ");
        if (isset($query->id) && $query->id > 0) {
            $ens_user_id = $query->id;
        }

        // Get domain id
        $query = $wpdb->get_row(
            "
                SELECT id FROM wenp_ens_domains
                WHERE ens_user_id='{$ens_user_id}' AND name='{$domain}' LIMIT 1
        ");
        if (isset($query->id) && $query->id > 0) {
            $ens_domain_id = $query->id;
        }

        // We have inital data
        if($ens_user_id > 0 && $ens_domain_id > 0){

            // Check if we have images
            $query = $wpdb->get_results(
                "
                    SELECT * FROM wenp_ens_mockups
                    WHERE ens_user_id='{$ens_user_id}' AND  ens_domain_id='{$ens_domain_id}' AND post_id='{$postID}' ORDER BY image_order ASC
            ");

            // Return existing images
            if (sizeof($query) > 0) {
                foreach ($query AS $image){
                    $return_data['images'][] = $image;
                }

                $return_data['status'] = 1;
            }
            // Create new ones - new domain mockups
            else {

                // First get task key
                $images = [];
                $getTaskKey = get_task_key($domain, $postID);
                if ($getTaskKey) {

                    // Second get mockup images
                    $mockupImages = get_task_mockups($getTaskKey);

                    if (isset($mockupImages->mockups)) {
                        foreach ($mockupImages->mockups as $mockup) {
                            $images[] = $mockup->mockup_url;
                        }
                    }
                }

                if( sizeof($images) > 0 ){

                    // Save images to WP directory and get all dimensions urls
                    $imageCounter = 1;
                    foreach ($images AS $key => $image){
                        $return_data['images'][] = saveMockupImage($image, $postID, $ens_user_id, $ens_domain_id, $imageCounter);
                        $imageCounter++;
                    }

                    $return_data['status'] = 1;
                }
                // we didn't get images
                else{
                    $return_data['status'] = 4;
                }

            }

        }
        // we don't have user and domain
        else{
            $return_data['status'] = 3;
        }

    }
    // domain haven't provided or it is default domain name
    elseif ($domain == 0){
        $return_data['status'] = 5;
    }
    else {
        $_SESSION['user_wallet_address'] = '';
        $return_data['status'] = 2;
    }

    echo json_encode($return_data);

}

add_action('wp_ajax_nopriv_set_new_mockups', 'set_new_mockups');
add_action('wp_ajax_set_new_mockups', 'set_new_mockups');