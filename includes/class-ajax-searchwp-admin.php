<?php

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

class Ajax_SearchWP_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'ajax_searchwp_add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function ajax_searchwp_add_admin_menu() {
        add_menu_page(
            __('Ajax SearchWP Settings', 'ajax-searchwp'), // Localize the menu title
            __('Ajax SearchWP', 'ajax-searchwp'), // Localize the menu label
            'manage_options',
            'ajax_searchwp',
            array($this, 'ajax_searchwp_settings_page'),
            'dashicons-search', // Optional: Add an icon
            100 // Optional: Menu position
        );
    }

    public function register_settings() {
        register_setting('ajax_searchwp_settings', 'ajax_searchwp_post_types', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_post_types'),
            'default' => array(),
        ));
        
        // Register the new settings
        register_setting('ajax_searchwp_settings', 'ajax_searchwp_search_placeholder');
        register_setting('ajax_searchwp_settings', 'ajax_searchwp_limit');
        register_setting('ajax_searchwp_settings', 'ajax_searchwp_no_results_text');
    
        add_settings_section(
            'ajax_searchwp_main_section',
            __('Main Settings', 'ajax-searchwp'),
            null,
            'ajax_searchwp_settings'
        );
    
        add_settings_field(
            'ajax_searchwp_post_types',
            __('Select Post Types to Include in Search', 'ajax-searchwp'),
            array($this, 'ajax_searchwp_post_types_render'),
            'ajax_searchwp_settings',
            'ajax_searchwp_main_section'
        );

        // Add new fields for placeholder, limit, and no results text
        add_settings_field(
            'ajax_searchwp_search_placeholder',
            __('Search Input Placeholder', 'ajax-searchwp'),
            array($this, 'ajax_searchwp_search_placeholder_render'),
            'ajax_searchwp_settings',
            'ajax_searchwp_main_section'
        );

        add_settings_field(
            'ajax_searchwp_limit',
            __('Limit', 'ajax-searchwp'),
            array($this, 'ajax_searchwp_limit_render'),
            'ajax_searchwp_settings',
            'ajax_searchwp_main_section'
        );

        add_settings_field(
            'ajax_searchwp_no_results_text',
            __('No Results Text', 'ajax-searchwp'),
            array($this, 'ajax_searchwp_no_results_text_render'),
            'ajax_searchwp_settings',
            'ajax_searchwp_main_section'
        );
    }

    public function sanitize_post_types($input) {
        if (!is_array($input)) {
            $input = array();
        }
        return array_map('sanitize_text_field', $input);
    }

    // Render post types checkboxes
    public function ajax_searchwp_post_types_render() {
        $selected_post_types = get_option('ajax_searchwp_post_types', array());
        $post_types = get_post_types(array('public' => true), 'objects');
    
        echo '<fieldset>';
        foreach ($post_types as $post_type) {
            $checked = in_array($post_type->name, $selected_post_types) ? 'checked="checked"' : '';
            echo '<label>';
            echo '<input type="checkbox" name="ajax_searchwp_post_types[]" value="' . esc_attr($post_type->name) . '" ' . esc_attr($checked) . ' />';
            echo esc_html($post_type->label);
            echo '</label><br>';
        }
        echo '</fieldset>';
    }

    // Render Search Input Placeholder field
    public function ajax_searchwp_search_placeholder_render() {
        $search_placeholder = get_option('ajax_searchwp_search_placeholder', __('Search here...', 'ajax-searchwp'));
        echo '<input type="text" name="ajax_searchwp_search_placeholder" value="' . esc_attr($search_placeholder) . '" />';
    }

    // Render Limit field
    public function ajax_searchwp_limit_render() {
        $limit = get_option('ajax_searchwp_limit', 5); // Default value 10
        echo '<input type="number" name="ajax_searchwp_limit" value="' . esc_attr($limit) . '" />';
    }

    // Render No Results Text field
    public function ajax_searchwp_no_results_text_render() {
        $no_results_text = get_option('ajax_searchwp_no_results_text', __('No results found', 'ajax-searchwp'));
        echo '<input type="text" name="ajax_searchwp_no_results_text" value="' . esc_attr($no_results_text) . '" />';
    }

    public function ajax_searchwp_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Ajax SearchWP Settings', 'ajax-searchwp'); ?></h1>
            <p><?php esc_html_e('Use the shortcode [ajax_searchwp] to add the search form to any page.', 'ajax-searchwp'); ?></p>
            <form action="options.php" method="post">
                <?php
                settings_fields('ajax_searchwp_settings');
                do_settings_sections('ajax_searchwp_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }    
}
?>
