<?php
/**
 * Plugin Name: Minutemailer Subscribe
 * Plugin URI: https://minutemailer.com
 * Description: Widget for signing up people to your Minutemailer contact list. Just add the widget to your sidebar and enter the list token of the contact list you want to add people to. Get your free Minutemailer account on minutemailer.com and start sending emails and scheduling content to social media.
 * Version: 1.0.2
 * Author: Minutemailer
 * Author URI: https://minutemailer.com
 * Text Domain: minutemailer-subscribe
 * Domain Path: /languages
 * License: GPL2
 */

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
		if($instance) {
		     $headline = esc_attr($instance['headline']);
		     $name_label = esc_attr($instance['name_label']);
		     $email_label = esc_attr($instance['email_label']);
		     $signupbutton_text = esc_textarea($instance['signupbutton_text']);
		     $list_token = esc_textarea($instance['list_token']);
		     $thank_you_message = esc_textarea($instance['thank_you_message']);
		} else {
		     $headline = __('Subscribe to news', 'minutemailer-subscribe');
		     $name_label = __('Name', 'minutemailer-subscribe');
		     $email_label = __('E-mail', 'minutemailer-subscribe');
		     $signupbutton_text = __('Subscribe', 'minutemailer-subscribe');
		     $list_token = '';
		     $thank_you_message = __('Thank you for subscribing!', 'minutemailer-subscribe');
		}
		?>

		<p>
		<label for="<?php echo $this->get_field_id('headline'); ?>"><?php _e('Headline', 'minutemailer-subscribe'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('headline'); ?>" name="<?php echo $this->get_field_name('headline'); ?>" type="text" value="<?php echo $headline; ?>" />
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
		<label for="<?php echo $this->get_field_id('list_token'); ?>"><?php _e('List token', 'minutemailer-subscribe'); ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('list_token'); ?>" name="<?php echo $this->get_field_name('list_token'); ?>" type="text" value="<?php echo $list_token; ?>" />
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
		$instance['name_label'] = strip_tags($new_instance['name_label']);
		$instance['email_label'] = strip_tags($new_instance['email_label']);
		$instance['signupbutton_text'] = strip_tags($new_instance['signupbutton_text']);
		$instance['list_token'] = strip_tags($new_instance['list_token']);
		$instance['thank_you_message'] = strip_tags($new_instance['thank_you_message']);

		return $instance;
	}

	// Display widget
	function widget($args, $instance) {
		$headline = apply_filters('widget_title', $instance['headline']);
		$name_label = $instance['name_label'];
		$email_label = $instance['email_label'];
		$signupbutton_text = $instance['signupbutton_text'];
		$list_token = $instance['list_token'];
		$thank_you_message = $instance['thank_you_message'];

        echo $args['before_widget'];

		// Check if headline is set
		if ($headline) {
		    echo $args['before_title'] . $headline . $args['after_title'];
		}

		// Display form ?>
		<form class="minutemailer-signup" action="https://subscribe.minutemailer.com/<?php echo $list_token; ?>" method="post">
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
				<input type="submit" class="minutemailer-submit" value="<?php echo $signupbutton_text; ?>" />
			</p>
			<div class="minutemailer-submit-result" style="display: none;"><?php echo $thank_you_message; ?></div>
		</form>

		<?php
        echo $args['after_widget'];
	}
}

// Register widget
add_action('widgets_init', create_function('', 'return register_widget("minutemailer");'));


// Load jQuery on init
function minutemailer_plugin_init(){
	wp_enqueue_script('jquery');
	wp_enqueue_script('minutemailer-js', plugins_url('/js/minutemailer.js', __FILE__), array('jquery'));
}
add_filter('wp_enqueue_scripts', 'minutemailer_plugin_init');


// Load plugin text domain
function minutemailer_plugin_init_textdomain() {
    load_plugin_textdomain( 'minutemailer-subscribe', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action('init', 'minutemailer_plugin_init_textdomain');