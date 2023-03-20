<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://mukesh.com
 * @since             1.0.0
 * @package           Subs
 *
 * @wordpress-plugin
 * Plugin Name:       subs
 * Plugin URI:        https://subs.com
 * Description:       Demo of subscription model    
 * Version:           1.0.0
 * Author:            Mukesh
 * Author URI:        https://mukesh.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       subs
 * Domain Path:       /languages
 */

function handle_my_form()
{
    global $wpdb;

    if (isset($_POST['email'])) {
        $email = sanitize_text_field($_POST['email']);
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if (preg_match($pattern, $email)) {
            if (isset($_POST['submit'])) {

                $subs_emails = get_option('subs_emails');
                if (!$subs_emails) {
                    $subs_emails = array();
                }
                $subs_emails[] = $_POST['email'];
                update_option('subs_emails', $subs_emails);

                // Display a success message
                echo '<div class="updated"><p>You are subscribed to Daily Updates!</p></div>';
            }
        } else {
            echo '<div class="error"><p>Invalid Email ID</p></div>';
        }
    }
}
add_action('init', 'handle_my_form');





add_action( 'wp_head', 'subscribe_me_callback' );


function subscribe_me_callback()
{
?>

    <!--Add Input fields on Schedule Content Page-->
    <div class="wrap">
        <form class="myform" method="post">
            <input type="hidden" name="action" value="cc_form">

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" /><br />

            <input type="submit" value="submit" name="submit" />
        </form>
    </div>

<?php
}
