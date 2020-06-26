<?php

 // If this file is called directly, abort.
 if ( ! defined( 'WPINC' ) ) {
	die;
}

// Add settings page
add_action('admin_menu', 'minutemailer_plugin_admin_add_page');
function minutemailer_plugin_admin_add_page() {
    add_options_page('Minutemailer', 'Minutemailer', 'manage_options', 'minutemailer_plugin', 'minutemailer_plugin_options_page');
};

// Define settings for the plugin
add_action('admin_init', 'minutemailer_plugin_admin_init');
function minutemailer_plugin_admin_init(){
    register_setting( 'plugin_options', 'plugin_options', 'minutemailer_plugin_options_validate' );
    add_settings_section('plugin_main', '', 'minutemailer_plugin_section_text', 'plugin');
}

// Plugin settings callback
function minutemailer_plugin_section_text() {
    echo '<p>'.__('<a href="https://minutemailer.com" target="_blank">Minutemailer</a> is an online email marketing tool for businesses. Create, schedule and send e-mail messages and newsletters for free. Use the plugin to easily get subscribers from your Wordpress blog.', 'minutemailer-subscribe').'</p>';
    echo '<p>'.__('To add the Minutemailer subscribe widget first you need to create an API token and paste it below.', 'minutemailer-subscribe').'</p>';
    echo '<p><a href="https://app.minutemailer.com/u/settings/api" target="_blank" type="submit" class="button-secondary">'.__('Create token', 'minutemailer-subscribe').'</a></p>';

    $options = get_option('plugin_options');
    if (!isset($options['minutemailer_api_key'])) {
        $options['minutemailer_api_key'] = '';
    }
    echo "<p><textarea id='minuatemailer_plugin_api_key' name='plugin_options[minutemailer_api_key]' cols='60' rows='18'>". $options['minutemailer_api_key'] . "</textarea></p>";

    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
       // add the function called
        // Get lists
        $minutemailer_plugin_options = get_option( 'plugin_options' );
        // print_r($my_options);
        $minutemailer_api_key = $minutemailer_plugin_options['minutemailer_api_key'];

        require_once('includes/curl.php');
        $url = 'https://api.minutemailer.com/v1/contactlists';
        $authorization = 'Authorization: Bearer ' . $minutemailer_api_key;
        $response = minutemailer_curl_send_request(NULL, $url, 'GET', $authorization);

        if (!$response || isset($response->error)) { 
            if ($response->error->code == 401) { ?>
                <p><?php echo $response->error->message; ?></p><?php
            } else { ?>
                <p><?php _e('Something went wrong connecting to the Minutemailer API.', 'minutemailer-subscribe'); ?></p><?php
            }
        } else { ?>
             <p><?php _e('Successfully connected to the Minutemailer API.', 'minutemailer-subscribe'); ?></p><?php
        }
    }
}

function minutemailer_plugin_setting_string() {
    $options = get_option('plugin_options');
    if (!isset($options['minutemailer_api_key'])) {
        $options['minutemailer_api_key'] = '';
    }
    echo "<textarea id='minuatemailer_plugin_api_key' name='plugin_options[minutemailer_api_key]' cols='60' rows='18'>". $options['minutemailer_api_key'] . "</textarea>";
}

// Validate input
function minutemailer_plugin_options_validate($input) {
    return $input;
}

// Display the admin options page
function minutemailer_plugin_options_page() {
?>
<div>
    <h2><?php _e('Minutemailer Subscribe Plugin', 'minutemailer_widget_plugin'); ?></h2>
    <form action="options.php" method="post">
        <?php settings_fields('plugin_options'); ?>
        <?php do_settings_sections('plugin'); ?>
        <input name="submit" type="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form>
</div><?php
}
