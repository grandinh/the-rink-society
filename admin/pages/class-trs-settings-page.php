<?php
/**
 * Settings Admin Page
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Settings_Page {

    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings() {
        // General Settings
        register_setting('trs_general_settings', 'trs_facility_name');
        register_setting('trs_general_settings', 'trs_facility_location');
        register_setting('trs_general_settings', 'trs_default_location');
        register_setting('trs_general_settings', 'trs_timezone');

        // Display Settings
        register_setting('trs_display_settings', 'trs_date_format');
        register_setting('trs_display_settings', 'trs_time_format');
        register_setting('trs_display_settings', 'trs_items_per_page');
        register_setting('trs_display_settings', 'trs_show_player_photos');

        // Stats Settings
        register_setting('trs_stats_settings', 'trs_stats_enabled');
        register_setting('trs_stats_settings', 'trs_track_plus_minus');
        register_setting('trs_stats_settings', 'trs_track_pim');
        register_setting('trs_stats_settings', 'trs_track_shots');

        // Permissions Settings
        register_setting('trs_permissions_settings', 'trs_who_can_edit_rosters');
        register_setting('trs_permissions_settings', 'trs_who_can_enter_stats');
        register_setting('trs_permissions_settings', 'trs_who_can_manage_events');
    }

    public function render() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            check_admin_referer('trs_settings_action');

            if (isset($_POST['trs_save_general'])) {
                $this->save_general_settings();
            } elseif (isset($_POST['trs_save_display'])) {
                $this->save_display_settings();
            } elseif (isset($_POST['trs_save_stats'])) {
                $this->save_stats_settings();
            } elseif (isset($_POST['trs_save_permissions'])) {
                $this->save_permissions_settings();
            }
        }

        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';

        include TRS_PLUGIN_DIR . 'admin/views/settings.php';
    }

    private function save_general_settings() {
        update_option('trs_facility_name', sanitize_text_field($_POST['trs_facility_name']));
        update_option('trs_facility_location', sanitize_text_field($_POST['trs_facility_location']));
        update_option('trs_default_location', sanitize_text_field($_POST['trs_default_location']));
        update_option('trs_timezone', sanitize_text_field($_POST['trs_timezone']));

        wp_redirect(add_query_arg(array(
            'page' => 'trs-settings',
            'tab' => 'general',
            'message' => 'saved'
        ), admin_url('admin.php')));
        exit;
    }

    private function save_display_settings() {
        update_option('trs_date_format', sanitize_text_field($_POST['trs_date_format']));
        update_option('trs_time_format', sanitize_text_field($_POST['trs_time_format']));
        update_option('trs_items_per_page', intval($_POST['trs_items_per_page']));
        update_option('trs_show_player_photos', isset($_POST['trs_show_player_photos']) ? 1 : 0);

        wp_redirect(add_query_arg(array(
            'page' => 'trs-settings',
            'tab' => 'display',
            'message' => 'saved'
        ), admin_url('admin.php')));
        exit;
    }

    private function save_stats_settings() {
        update_option('trs_stats_enabled', isset($_POST['trs_stats_enabled']) ? 1 : 0);
        update_option('trs_track_plus_minus', isset($_POST['trs_track_plus_minus']) ? 1 : 0);
        update_option('trs_track_pim', isset($_POST['trs_track_pim']) ? 1 : 0);
        update_option('trs_track_shots', isset($_POST['trs_track_shots']) ? 1 : 0);

        wp_redirect(add_query_arg(array(
            'page' => 'trs-settings',
            'tab' => 'stats',
            'message' => 'saved'
        ), admin_url('admin.php')));
        exit;
    }

    private function save_permissions_settings() {
        update_option('trs_who_can_edit_rosters', sanitize_text_field($_POST['trs_who_can_edit_rosters']));
        update_option('trs_who_can_enter_stats', sanitize_text_field($_POST['trs_who_can_enter_stats']));
        update_option('trs_who_can_manage_events', sanitize_text_field($_POST['trs_who_can_manage_events']));

        wp_redirect(add_query_arg(array(
            'page' => 'trs-settings',
            'tab' => 'permissions',
            'message' => 'saved'
        ), admin_url('admin.php')));
        exit;
    }
}
