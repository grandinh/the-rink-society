<?php
/**
 * Fired during plugin deactivation
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Deactivator {

    /**
     * Deactivate the plugin
     */
    public static function deactivate() {
        // Clear any scheduled cron jobs
        wp_clear_scheduled_hook('trs_daily_cleanup');

        // Clear transients
        self::clear_transients();

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Clear all plugin transients
     */
    private static function clear_transients() {
        global $wpdb;

        $wpdb->query(
            "DELETE FROM {$wpdb->options}
            WHERE option_name LIKE '_transient_trs_%'
            OR option_name LIKE '_transient_timeout_trs_%'"
        );
    }
}
