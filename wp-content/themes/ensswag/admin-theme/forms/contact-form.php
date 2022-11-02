<?php

/**
 * Footer CONTACT FORM
 *
 * Send mail to client - AJAX function - contact form
 */
function contact_form(){

    $return_data = array();
    $return_data['status']	= 0;

    //get post data
    $name       =	filter_var($_POST['contact-name']);
    $email		=	filter_var($_POST['contact-email']);
    $message    =	filter_var($_POST['contact-message']);

    $nonce 		=	( wp_verify_nonce( $_POST['contact-nonce'], "contact_form_check" ) )? true : false;

    if( filter_var($email, FILTER_VALIDATE_EMAIL) && trim($name) != '' && $nonce ){


        //set up to
        $contact_email = 'ensswag@kkatusic.com';

        $options = get_option('wedevs_basics');
        if( isset($options['contact_form_email']) && $options['contact_form_email'] != '' ){
            $contact_email = $options['contact_form_email'];
        }

        $headers[] = 'From: ENS Swag Shop <info@ensmerchshop.xyz>';
        $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';

        $message = '<p>
                        <strong>Name:</strong> ' . $name . '<br>
                        <strong>Email:</strong> ' . $email . '<br>
                        <strong>Message:</strong><br>
                        ' . $message . '
                    </p>
                    ';

        add_filter( 'wp_mail_content_type', 'set_html_content_type' );
        $check = wp_mail( $contact_email, 'Message from website ensmerchshop.xyz', $message, $headers );
        remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

        $return_data['status'] = 1;

    }
    else{
        $return_data['status'] = 2;
    }

    echo json_encode($return_data);

}

add_action( 'wp_ajax_nopriv_contact_form', 'contact_form' );
add_action( 'wp_ajax_contact_form', 'contact_form' );