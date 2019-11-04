<?php

// Add settings page
add_action('admin_menu', 'minutemailer_plugin_admin_add_page');
function minutemailer_plugin_admin_add_page() {
    add_options_page('Minutemailer', 'Minutemailer', 'manage_options', 'minutemailer_plugin', 'minutemailer_plugin_options_page');
};


// Define settings for the plugin
add_action('admin_init', 'minutemailer_plugin_admin_init');
function minutemailer_plugin_admin_init(){
    register_setting( 'plugin_options', 'plugin_options', 'minutemailer_plugin_options_validate' );
    add_settings_section('plugin_main', 'Main Settings', 'minutemailer_plugin_section_text', 'plugin');
    add_settings_field('minuatemailer_plugin_api_key', 'Your Minutemailer API Key', 'minutemailer_plugin_setting_string', 'plugin', 'plugin_main');
}

// Plugin settings callback
function minutemailer_plugin_section_text() {
    //echo '<p>Main description of this section here.</p>';
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
