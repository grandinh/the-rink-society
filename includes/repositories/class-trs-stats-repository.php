<?php
/**
 * Stats Repository
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Stats_Repository {

    private $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'trs_stats';
    }

    public function get($id) {
        global $wpdb;

        $data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE id = %d",
            $id
        ), ARRAY_A);

        return $data ? new TRS_Stats($data) : null;
    }

    /**
     * Get stats by game
     */
    public function get_by_game($game_id, $team_id = null) {
        global $wpdb;

        $query = "SELECT * FROM {$this->table} WHERE game_id = %d";
        $params = array($game_id);

        if ($team_id) {
            $query .= " AND team_id = %d";
            $params[] = $team_id;
        }

        $query .= " ORDER BY period ASC, game_time ASC";

        $results = $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
        $stats = array();

        foreach ($results as $data) {
            $stats[] = new TRS_Stats($data);
        }

        return $stats;
    }

    /**
     * Get stats by player
     */
    public function get_by_player($player_id, $args = array()) {
        global $wpdb;

        $defaults = array(
            'season_id' => null,
            'tournament_id' => null,
            'team_id' => null,
            'stat_type' => null,
        );

        $args = wp_parse_args($args, $defaults);

        $query = "SELECT s.* FROM {$this->table} s";
        $joins = array();
        $where = array("s.player_id = %d");
        $params = array($player_id);

        if ($args['season_id'] || $args['tournament_id']) {
            $joins[] = "INNER JOIN {$wpdb->prefix}trs_games g ON s.game_id = g.id";
        }

        if ($args['season_id']) {
            $where[] = "g.season_id = %d";
            $params[] = $args['season_id'];
        }

        if ($args['tournament_id']) {
            $where[] = "g.tournament_id = %d";
            $params[] = $args['tournament_id'];
        }

        if ($args['team_id']) {
            $where[] = "s.team_id = %d";
            $params[] = $args['team_id'];
        }

        if ($args['stat_type']) {
            $where[] = "s.stat_type = %s";
            $params[] = $args['stat_type'];
        }

        if (!empty($joins)) {
            $query .= " " . implode(' ', $joins);
        }

        $query .= " WHERE " . implode(' AND ', $where);
        $query .= " ORDER BY s.created_at DESC";

        $results = $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
        $stats = array();

        foreach ($results as $data) {
            $stats[] = new TRS_Stats($data);
        }

        return $stats;
    }

    /**
     * Get aggregated stats for a player
     */
    public function get_player_totals($player_id, $args = array()) {
        global $wpdb;

        $defaults = array(
            'season_id' => null,
            'tournament_id' => null,
            'team_id' => null,
        );

        $args = wp_parse_args($args, $defaults);

        $query = "SELECT
                    s.stat_type,
                    SUM(s.stat_value) as total,
                    COUNT(DISTINCT s.game_id) as games_played
                  FROM {$this->table} s";

        $joins = array();
        $where = array("s.player_id = %d");
        $params = array($player_id);

        if ($args['season_id'] || $args['tournament_id']) {
            $joins[] = "INNER JOIN {$wpdb->prefix}trs_games g ON s.game_id = g.id";
        }

        if ($args['season_id']) {
            $where[] = "g.season_id = %d";
            $params[] = $args['season_id'];
        }

        if ($args['tournament_id']) {
            $where[] = "g.tournament_id = %d";
            $params[] = $args['tournament_id'];
        }

        if ($args['team_id']) {
            $where[] = "s.team_id = %d";
            $params[] = $args['team_id'];
        }

        if (!empty($joins)) {
            $query .= " " . implode(' ', $joins);
        }

        $query .= " WHERE " . implode(' AND ', $where);
        $query .= " GROUP BY s.stat_type";

        return $wpdb->get_results($wpdb->prepare($query, $params));
    }

    /**
     * Get leaderboard
     */
    public function get_leaderboard($stat_type, $args = array()) {
        global $wpdb;

        $defaults = array(
            'season_id' => null,
            'tournament_id' => null,
            'team_id' => null,
            'limit' => 10,
        );

        $args = wp_parse_args($args, $defaults);

        $query = "SELECT
                    s.player_id,
                    SUM(s.stat_value) as total,
                    COUNT(DISTINCT s.game_id) as games_played
                  FROM {$this->table} s";

        $joins = array();
        $where = array("s.stat_type = %s");
        $params = array($stat_type);

        if ($args['season_id'] || $args['tournament_id']) {
            $joins[] = "INNER JOIN {$wpdb->prefix}trs_games g ON s.game_id = g.id";
        }

        if ($args['season_id']) {
            $where[] = "g.season_id = %d";
            $params[] = $args['season_id'];
        }

        if ($args['tournament_id']) {
            $where[] = "g.tournament_id = %d";
            $params[] = $args['tournament_id'];
        }

        if ($args['team_id']) {
            $where[] = "s.team_id = %d";
            $params[] = $args['team_id'];
        }

        if (!empty($joins)) {
            $query .= " " . implode(' ', $joins);
        }

        $query .= " WHERE " . implode(' AND ', $where);
        $query .= " GROUP BY s.player_id";
        $query .= " ORDER BY total DESC";
        $query .= " LIMIT {$args['limit']}";

        return $wpdb->get_results($wpdb->prepare($query, $params));
    }

    /**
     * Create stat entry
     */
    public function create($data) {
        global $wpdb;

        $defaults = array(
            'stat_value' => 1,
            'period' => null,
            'game_time' => null,
            'notes' => null,
        );

        $data = wp_parse_args($data, $defaults);

        if (empty($data['game_id']) || empty($data['team_id']) || empty($data['player_id']) || empty($data['stat_type'])) {
            return false;
        }

        $inserted = $wpdb->insert(
            $this->table,
            array(
                'game_id' => $data['game_id'],
                'team_id' => $data['team_id'],
                'player_id' => $data['player_id'],
                'stat_type' => $data['stat_type'],
                'stat_value' => $data['stat_value'],
                'period' => $data['period'],
                'game_time' => $data['game_time'],
                'notes' => $data['notes'],
            ),
            array('%d', '%d', '%d', '%s', '%f', '%s', '%s', '%s')
        );

        return $inserted ? $wpdb->insert_id : false;
    }

    /**
     * Update stat
     */
    public function update($id, $data) {
        global $wpdb;

        $allowed_fields = array(
            'stat_value' => '%f',
            'period' => '%s',
            'game_time' => '%s',
            'notes' => '%s',
        );

        $update_data = array();
        $formats = array();

        foreach ($allowed_fields as $field => $format) {
            if (isset($data[$field])) {
                $update_data[$field] = $data[$field];
                $formats[] = $format;
            }
        }

        if (empty($update_data)) {
            return false;
        }

        return $wpdb->update(
            $this->table,
            $update_data,
            array('id' => $id),
            $formats,
            array('%d')
        );
    }

    /**
     * Delete stat
     */
    public function delete($id) {
        global $wpdb;

        return $wpdb->delete(
            $this->table,
            array('id' => $id),
            array('%d')
        );
    }
}
