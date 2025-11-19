<?php
/**
 * Helper functions
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get formatted date
 */
function trs_format_date($date, $format = null) {
    if (!$format) {
        $format = get_option('trs_date_format', 'Y-m-d');
    }
    return date($format, strtotime($date));
}

/**
 * Get formatted time
 */
function trs_format_time($time, $format = null) {
    if (!$format) {
        $format = get_option('trs_time_format', 'H:i');
    }
    return date($format, strtotime($time));
}

/**
 * Get stat type label
 */
function trs_get_stat_type_label($stat_type) {
    $labels = array(
        'goal' => __('Goals', 'the-rink-society'),
        'assist' => __('Assists', 'the-rink-society'),
        'point' => __('Points', 'the-rink-society'),
        'penalty' => __('Penalty Minutes', 'the-rink-society'),
        'shot' => __('Shots', 'the-rink-society'),
        'save' => __('Saves', 'the-rink-society'),
        'plus_minus' => __('+/-', 'the-rink-society'),
    );

    return $labels[$stat_type] ?? ucfirst(str_replace('_', ' ', $stat_type));
}

/**
 * Get position label
 */
function trs_get_position_label($position) {
    $labels = array(
        'forward' => __('Forward', 'the-rink-society'),
        'defense' => __('Defense', 'the-rink-society'),
        'goalie' => __('Goalie', 'the-rink-society'),
    );

    return $labels[$position] ?? ucfirst($position);
}

/**
 * Get event type label
 */
function trs_get_event_type_label($event_type) {
    $labels = array(
        'practice' => __('Practice', 'the-rink-society'),
        'meeting' => __('Meeting', 'the-rink-society'),
        'draft' => __('Draft', 'the-rink-society'),
        'social' => __('Social Event', 'the-rink-society'),
        'other' => __('Other', 'the-rink-society'),
    );

    return $labels[$event_type] ?? ucfirst($event_type);
}

/**
 * Get game status label
 */
function trs_get_game_status_label($status) {
    $labels = array(
        'scheduled' => __('Scheduled', 'the-rink-society'),
        'in_progress' => __('In Progress', 'the-rink-society'),
        'final' => __('Final', 'the-rink-society'),
        'cancelled' => __('Cancelled', 'the-rink-society'),
    );

    return $labels[$status] ?? ucfirst($status);
}

/**
 * Calculate points (goals + assists)
 */
function trs_calculate_points($player_id, $args = array()) {
    $stats_repo = new TRS_Stats_Repository();

    $goals_args = array_merge($args, array('stat_type' => 'goal'));
    $assists_args = array_merge($args, array('stat_type' => 'assist'));

    $goals = $stats_repo->get_by_player($player_id, $goals_args);
    $assists = $stats_repo->get_by_player($player_id, $assists_args);

    $total_goals = array_sum(array_column($goals, 'stat_value'));
    $total_assists = array_sum(array_column($assists, 'stat_value'));

    return $total_goals + $total_assists;
}

/**
 * Check if current user can manage hockey data
 */
function trs_current_user_can_manage() {
    return current_user_can('manage_options') || current_user_can('edit_posts');
}
