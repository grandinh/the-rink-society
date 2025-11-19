<?php
/**
 * Fired when the plugin is uninstalled
 *
 * @package TheRinkSociety
 */

// Exit if accessed directly or not uninstalling
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Remove all plugin data
 *
 * NOTE: This will DELETE all hockey data permanently!
 * Uncomment the sections below only if you want to completely remove all data.
 */

global $wpdb;

// Uncomment to delete all custom tables
/*
$tables = array(
    $wpdb->prefix . 'trs_players',
    $wpdb->prefix . 'trs_teams',
    $wpdb->prefix . 'trs_seasons',
    $wpdb->prefix . 'trs_tournaments',
    $wpdb->prefix . 'trs_games',
    $wpdb->prefix . 'trs_events',
    $wpdb->prefix . 'trs_team_players',
    $wpdb->prefix . 'trs_tournament_players',
    $wpdb->prefix . 'trs_game_rosters',
    $wpdb->prefix . 'trs_stats',
    $wpdb->prefix . 'trs_event_attendance',
);

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS $table");
}
*/

// Uncomment to delete all plugin options
/*
delete_option('trs_db_version');
delete_option('trs_activated_at');
delete_option('trs_date_format');
delete_option('trs_time_format');
delete_option('trs_jersey_validation');
delete_option('trs_default_stat_types');
*/

// Uncomment to clear all transients
/*
$wpdb->query(
    "DELETE FROM {$wpdb->options}
    WHERE option_name LIKE '_transient_trs_%'
    OR option_name LIKE '_transient_timeout_trs_%'"
);
*/
