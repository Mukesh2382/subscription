<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://mukesh.com
 * @since             1.0.0
 * @package           Subs
 *
 * @wordpress-plugin
 * Plugin Name:       subs
 * Plugin URI:        https://https://subs.com
 * Description:       Demo of subscription model
 * Version:           1.0.0
 * Author:            Mukesh
 * Author URI:        https://https://mukesh.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       subs
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SUBS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-subs-activator.php
 */
function activate_subs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-subs-activator.php';
	Subs_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-subs-deactivator.php
 */
function deactivate_subs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-subs-deactivator.php';
	Subs_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_subs' );
register_deactivation_hook( __FILE__, 'deactivate_subs' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-subs.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
 function subs_add_settings_page()
{
    //added menu page
    add_menu_page(
        'Subscription Settings', //page title
        'Manage Subscriptions', //text to be displayed
        'manage_options', //manage options
        'subscription-settings', //slug
        'subscription_render_settings_page', // callback function
        'dashicons-admin-site-alt3',
        100 //position
    );

    add_submenu_page(
        'subscription-settings', // Parent slug
        'Users List', // Page title
        'Send Mails', // Menu title
        'manage_options', // Capability required to access page
        'send-mail-settings', // Menu slug
        'send_mail_cb' // Callback function that outputs the page HTML
    );
}
add_action('admin_menu', 'subs_add_settings_page');

//callback for submenu 
function send_mail_cb(){
    $email_array = get_option('subs_emails');
    echo '<table id="sm-table"><th>Subscribers List</th>';
    foreach($email_array as $email){
        echo '<tr><td>' . $email . '</td></tr>';
    }
    echo '</table>';
    ?>

    <form method="post">
        <input class="button button-primary" type="submit" name="send" id="send" value="Send Mail" />
    </form>

    <?php

    if (isset($_POST['send'])) {
        send_mail_to_all();
    }

}


function send_mail_to_all(){
    $subscribers_list = get_option('subs_emails');

    foreach ($subscribers_list as $mail) {
        $subject = 'Hello! We have something special for you';
        $summary = get_post_details();

        $message = "Our Latest articles (May Be Helpful to You)";
        $message .= "\n";
        foreach ($summary as $post_data) {
            $message .= 'Title: ' . $post_data['title'] . "\n";
            $message .= 'URL: ' . $post_data['url'] . "\n";
            $message .= "\n";
        }

        $headers = array(
            'From: mukesh.choudhari@wisdmlabs.com',
            'Content-Type: text/html; charset=UTF-8'
        );

        wp_mail($mail, $subject, $message, $headers);
    }
}
//callback for menu
function subscription_render_settings_page()
{
?>
    <div class="wrap">
        <h1>Subscription Settings</h1>
        <form method="post" action="options.php">
            <?php
            // Output the settings fields
            settings_fields('my_plugin_settings_group');
            do_settings_sections('my-plugin-settings');
            ?>
            <?php submit_button('Save Changes'); ?>
        </form>
    </div>
<?php
}


//function to be called on admin init
function setup_my_page()
{
    register_setting('my_plugin_settings_group', 'no_of_posts');
    add_settings_section('subs_settings', 'Notification Settings', '', 'my-plugin-settings');
    add_settings_field('no_of_posts','No of Posts','no_of_posts_cb', 'my-plugin-settings', 'subs_settings');
}
add_action('admin_init', 'setup_my_page');

function no_of_posts_cb()
{
?>
    <input type="text" name="no_of_posts" value="<?php echo esc_attr(get_option('no_of_posts')) ?>">
<?php
}



// ------------------ User Code Starts Here -----------------------



function handle_my_form()
{
    if (isset($_POST['email'])) {
        $email = sanitize_text_field($_POST['email']);
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if (preg_match($pattern, $email)) {
            if (isset($_POST['submit'])) {
                $subs_emails = get_option('subs_emails');
                if (!$subs_emails) {
                    $subs_emails = array();
                }
                if (in_array($email, $subs_emails)) {
                    echo '<script>alert("You are already subscribed!");</script>';
                } else {
                    $subs_emails[] = $email;
                    update_option('subs_emails', $subs_emails);

                    echo '<script>alert("You have been subscribed successfully!");</script>';
                    send_mail_to_user($email);
                }
            }
        } else {
            echo '<div class="error"><p>Invalid Email ID</p></div>';
        }
    }
}
add_action('init', 'handle_my_form');

function send_mail_to_user($to)
{
    $message = "You are subscribed to Daily Updates";
    $message .= "\n\n";
    $summary = get_post_details();
    foreach ($summary as $data) {
        $message .= $data['title'] . "\n";
        $message .= $data['url'] . "\n";
        $message .= "\n";
    }
    $headers = array(
        'From: mukesh.choudhari@wisdmlabs.com',
        'Content-Type: text/html; charset=UTF-8'
    );
    wp_mail($to, 'Welcome to Daily Updates', $message, $headers);
}


function get_post_details()
{
    $mailarray = array();
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => get_option('no_of_posts'),
        'post_status'    => 'publish'
    );

    $query = new WP_Query($args);

    foreach ($query->posts as $post) {
        $singlepost = array(
            'title' => $post->post_title,
            'url' => get_permalink($post->ID)
        );
        array_push($mailarray, $singlepost);
    }
    return $mailarray;
}
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
class Subscription_Widget extends WP_Widget
{

    // Constructor function
    public function __construct()
    {
        $widget_options = array(
            'classname' => 'subscription_widget',
            'description' => 'A widget for subscribing to our newsletter'
        );
        parent::__construct('subscription_widget', 'Subscription Widget', $widget_options);
    }

    // Output the widget content on the front-end
    public function widget($args, $instance)
    {
        // Code to output the widget HTML goes here
        subscribe_me_callback();
    }

    // Output the widget form in the admin area
    public function form($instance)
    {
        // Code to output the widget form HTML goes here
    }

    // Handle saving the widget options
    public function update($new_instance, $old_instance)
    {
        // Code to handle saving the widget options goes here
    }
}

function register_subscription_widget()
{
    register_widget('Subscription_Widget');
}
add_action('widgets_init', 'register_subscription_widget');

function run_subs() {

	$plugin = new Subs();
	$plugin->run();

}
run_subs();
