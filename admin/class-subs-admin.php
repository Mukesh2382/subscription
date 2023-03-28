<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://mukesh.com
 * @since      1.0.0
 *
 * @package    Subs
 * @subpackage Subs/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Subs
 * @subpackage Subs/admin
 * @author     Mukesh <mukesh@mukesh.com>
 */
class Subs_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Subs_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Subs_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/subs-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Subs_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Subs_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/subs-admin.js', array('jquery'), $this->version, false);

		
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
		

		//callback for submenu 
		function send_mail_cb()
		{
			$email_array = get_option('subs_emails');
			echo '<table id="sm-table"><th>Subscribers List</th>';
			foreach ($email_array as $email) {
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


		function send_mail_to_all()
		{
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
			add_settings_field('no_of_posts', 'No of Posts', 'no_of_posts_cb', 'my-plugin-settings', 'subs_settings');
		}
		

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
		

		function register_subscription_widget()
		{
			register_widget('Subscription_Widget');
		}
		
	}
}
