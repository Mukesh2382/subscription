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

function create_my_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'email_data';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		emailid varchar(255) NOT NULL
	  ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'create_my_table');


function handle_my_form()
{
    global $wpdb;
    if (isset($_POST['email'])) {
        $table_name = $wpdb->prefix . 'email_data';
        $email = sanitize_text_field($_POST['email']);
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if (preg_match($pattern, $email)) {
            $wpdb->insert(
                $table_name,
                array(
                    'emailid' => $email
                )
            );
        }
        else{
            echo "<script>alert('Invalid Email ID');</script>";
        }
    }
}
add_action('init', 'handle_my_form');



function my_add_menu_pages()
{
    add_menu_page(
        'Subscription Page',
        'Subscription Details',
        'manage_options',
        'subs',
        'subscribe_me_callback',
        'dashicons-email',
        6
    );
}
add_action('admin_menu', 'my_add_menu_pages');

function subscribe_me_callback()
{
?>

    <!--Add Input fields on Schedule Content Page-->
    <div class="wrap">
        <h1>Subscribe Me..!!</h1>


        <form class="myform" method="post">
            <input type="hidden" name="action" value="cc_form">

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" /><br />

            <?php submit_button('Subscribe'); ?>

        </form>
    </div>

<?php
}
