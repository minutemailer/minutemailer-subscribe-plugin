<?php
/**
 * @wordpress-plugin
 * Plugin Name: Minutemailer Subscribe
 * Plugin URI: https://minutemailer.com
 * Description: Widget for signing up people to your Minutemailer contact list. Just add your API token from Minutemailer and add a widget to your sidebar to let people subscribe. Get your free Minutemailer account on minutemailer.com and start sending beautiful newsletters and plain emails.
 * Version: 1.2.0
 * Author: Minutemailer
 * Author URI: https://minutemailer.com
 * Text Domain: minutemailer-subscribe
 * Domain Path: /languages
 * License: GPL2
 */

 // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once('minutemailer-admin.php');


class MinuteMailer extends WP_Widget {

	// Constructor
	function __construct() {
        $widget_ops = array(
            'description' => 'Minutemailer subscribe form',
        );
		parent::__construct(false, $name = __('Minutemailer Subscribe', 'minutemailer_widget_plugin'), $widget_ops );
	}

	// Widget WP-admin
	function form($instance) {

        $headline = __('Subscribe to news', 'minutemailer-subscribe');
        $description = __('', 'minutemailer-subscribe');
        $name_label = __('Name', 'minutemailer-subscribe');
        $email_label = __('E-mail', 'minutemailer-subscribe');
        $signupbutton_text = __('Subscribe', 'minutemailer-subscribe');
        $list_id = '';
        $thank_you_message = __('Thank you for subscribing!', 'minutemailer-subscribe');

		if($instance) {
		     $headline = esc_attr($instance['headline']);
		     $description = esc_attr($instance['description']);
		     $name_label = esc_attr($instance['name_label']);
		     $email_label = esc_attr($instance['email_label']);
		     $signupbutton_text = esc_textarea($instance['signupbutton_text']);
             $list_id = esc_textarea($instance['list_id']);
		     $thank_you_message = esc_textarea($instance['thank_you_message']);
		}

		// Get lists
        $minutemailer_plugin_options = get_option( 'plugin_options' );
        $minutemailer_api_key = $minutemailer_plugin_options['minutemailer_api_key'];

        require_once('includes/curl.php');
        $url = 'https://api.minutemailer.com/v1/contactlists';
        $authorization = 'Authorization: Bearer ' . $minutemailer_api_key;
        $response = minutemailer_curl_send_request(NULL, $url, 'GET', $authorization);

        if (!$response) { ?>
            <p><?php _e('Something went wrong connecting to the Minutemailer API.', 'minutemailer-subscribe'); ?></p>
            <?php
        } ?>

		<p>
		<label for="<?php echo $this->get_field_id('headline'); ?>"><?php _e('Headline', 'minutemailer-subscribe'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('headline'); ?>" name="<?php echo $this->get_field_name('headline'); ?>" type="text" value="<?php echo $headline; ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description', 'minutemailer-subscribe'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>" type="text" value="<?php echo $description; ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id('name_label'); ?>"><?php _e('Name field', 'minutemailer-subscribe'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('name_label'); ?>" name="<?php echo $this->get_field_name('name_label'); ?>" type="text" value="<?php echo $name_label; ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id('email_label'); ?>"><?php _e('Email field', 'minutemailer-subscribe'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('email_label'); ?>" name="<?php echo $this->get_field_name('email_label'); ?>" type="text" value="<?php echo $email_label; ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id('signupbutton_text'); ?>"><?php _e('Signup button', 'minutemailer-subscribe'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('signupbutton_text'); ?>" name="<?php echo $this->get_field_name('signupbutton_text'); ?>" type="text" value="<?php echo $signupbutton_text; ?>" />
		</p>
        <p>
            <label for="<?php echo $this->get_field_id('list_id'); ?>"><?php _e('List', 'minutemailer-subscribe'); ?>:</label>
            <select id="<?php echo $this->get_field_id('list_id'); ?>" name="<?php echo $this->get_field_name('list_id'); ?>" class="widefat">
                <?php
                foreach ($response->contact_lists as $list_item) {
                    ?>
                    <option <?php selected( $list_id, $list_item->_id ); ?> value="<?php echo $list_item->_id; ?>"><?php echo $list_item->name; ?></option><?php
                } ?>
            </select>
        </p>

		<p>
		<label for="<?php echo $this->get_field_id('thank_you_message'); ?>"><?php _e('Thank you message', 'minutemailer-subscribe'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('thank_you_message'); ?>" name="<?php echo $this->get_field_name('thank_you_message'); ?>" type="text" value="<?php echo $thank_you_message; ?>" />
		</p>
		<?php
	}

	// Update widget
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['headline'] = strip_tags($new_instance['headline']);
		$instance['description'] = strip_tags($new_instance['description']);
		$instance['name_label'] = strip_tags($new_instance['name_label']);
		$instance['email_label'] = strip_tags($new_instance['email_label']);
		$instance['signupbutton_text'] = strip_tags($new_instance['signupbutton_text']);
        $instance['list_token'] = strip_tags($new_instance['list_token']);
        $instance['list_id'] = strip_tags($new_instance['list_id']);
		$instance['thank_you_message'] = strip_tags($new_instance['thank_you_message']);

		return $instance;
	}

	// Display widget
	function widget($args, $instance) {
		$headline = apply_filters('widget_title', $instance['headline']);
		$description = $instance['description'];
		$name_label = $instance['name_label'];
		$email_label = $instance['email_label'];
		$signupbutton_text = $instance['signupbutton_text'];
        $list_token = $instance['list_token'];
        $list_id = $instance['list_id'];
		$thank_you_message = $instance['thank_you_message'];

		echo $args['before_widget'];

		// Check if headline is set
		if ($headline) {
		    echo $args['before_title'] . $headline . $args['after_title'];
		}

		// Check if description is set
		if ($description) {
			echo '<p class="minutemailer-widget-description">'.$description.'</p>';
		}

		// Display form ?>
		<form class="minutemailer-widget-signup" action="https://subscribe.minutemailer.com/<?php echo $list_id; ?>" method="post">
			<p>
				<label for="minutemailer_signup_name">
					<span class="screen-reader-text"><?php echo $name_label; ?></span>
					<input id="minutemailer-signup-name" class="minutemailer-signup-name" type="text" name="name" placeholder="<?php echo $name_label; ?>" required>
				</label>
			</p>
			<p>
				<label for="minutemailer_signup_email">
					<span class="screen-reader-text"><?php echo $email_label; ?></span>
					<input id="minutemailer-signup-email" class="minutemailer-signup-email" type="email" name="email" placeholder="<?php echo $email_label; ?>" required>
				</label>
			</p>
			<p>
				<input class="minutemailer-hide-me" style="display: none;" value="" type="text" name="hide-me">
				<button type="submit" class="minutemailer-submit"><?php echo $signupbutton_text; ?></button>
			</p>
			<div class="minutemailer-submit-result" style="display: none;"><?php echo $thank_you_message; ?></div>
		</form>

		<?php
        echo $args['after_widget'];
	}
}

// Register widget
add_action ('widgets_init', 'minutemailer_init');
function minutemailer_init() {
	return register_widget('minutemailer');
}

// Load jQuery on init
function minutemailer_plugin_init_scripts(){
	wp_enqueue_script('jquery');
	wp_enqueue_script('minutemailer-js', plugins_url('/js/minutemailer.js', __FILE__), array('jquery'));
}
add_filter('wp_enqueue_scripts', 'minutemailer_plugin_init_scripts');

// Load plugin text domain
function minutemailer_plugin_init_textdomain() {
    load_plugin_textdomain('minutemailer-subscribe', false, basename(dirname( __FILE__ )) . '/languages');
}
add_action('init', 'minutemailer_plugin_init_textdomain');

// Redirect to settings page on Activation
function minutemailer_activation_redirect( $plugin ) {
    if($plugin == plugin_basename(__FILE__)) {
        exit(wp_redirect(admin_url('options-general.php?page=minutemailer_plugin')));
    }
}
add_action('activated_plugin', 'minutemailer_activation_redirect');
