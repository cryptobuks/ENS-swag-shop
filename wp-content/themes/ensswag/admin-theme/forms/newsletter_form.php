<?php

/**
 * Newsletter
 */
function newsletter_form()
{

    $return_data = array();
    $return_data['status'] = 0;

    // Get post data
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $newfrch_check = filter_var($_POST['newfrch-check']);

    $nonce = (wp_verify_nonce($_POST['newsletter-form-nonce'], "newsletter_form_nonce")) ? true : false;

    if (
        trim($email) != '' &&
        $newfrch_check == 1 && $nonce
    ) {
        global $wpdb;

        // Check if mail exist, if not add it
        $query = $wpdb->get_row(
        "
            SELECT id FROM wenp_ens_newsletter
            WHERE email='{$email}' LIMIT 1
       ");

        if(!$query){
            $data = [
                'email' => $email,
                'created_at' => date("Y-m-d H:i:s"),
            ];

            $format = [
                '%s',
                '%s',
            ];

            $result = $wpdb->insert('wenp_ens_newsletter', $data, $format);
        }

        $return_data['status'] = 1;

    } else {
        $return_data['status'] = 2;
    }

    echo json_encode($return_data);

}

add_action('wp_ajax_nopriv_newsletter_form', 'newsletter_form');
add_action('wp_ajax_newsletter_form', 'newsletter_form');

/**
 * Show mailing list main admin page
 */
function show_newsletter_mailing_list(){
    echo '
				<div id="theme-options-wrap" class="widefat">
					<div class="icon32" id="icon-tools"><br /></div>
					<h2>Mailing list</h2>
					<p>Click generate CSV File and file would be loaded for you.</p>

					<ul>
					    <li>
					        <a href="'.get_template_directory_uri().'/admin-theme/forms/newsletter-form-export.php?w=1">Generate CSV File</a>
                        </li>
                        <li>
                            <br><br>
					        <a href="admin.php?page=mailing-list-newsletter-reset" onclick="return confirm(\'Are you sure you want to reset newsletter list?\');">RESET LIST</a>
                        </li>
                    </ul>

				</div>
			';
}

/**
 * Function that define main link in menu links navigation and sub pages links
 */
function admin_menu_newsletter_links () {

    add_utility_page( 'Newsletter Mailing list', 'Newsletter Mailing list', 'administrator', 'mailing-list-newsletter', 'show_newsletter_mailing_list' );

    add_submenu_page( 'mailing-list-newsletter-aaa', 'Reset List', 'Reset List', 'administrator', 'mailing-list-newsletter-reset', 'reset_newsletter_mailing_list' );
}

add_action( 'admin_menu', 'admin_menu_newsletter_links' );

/**
 * reset mailing list
 */
function reset_newsletter_mailing_list(){

    global $wpdb;

    $table = $wpdb->prefix.'newsletter_subscribers';

    $query = $wpdb->get_results(
        "
					DELETE FROM {$table} WHERE 1=1
				"
    );

    echo '
				<div id="theme-options-wrap" class="widefat">
					<div class="icon32" id="icon-tools"><br /></div>
					<h2>List has been reset!</h2>
				</div>
			';

}