<?php
/**
 * Game Repository
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Game_Repository {

    private $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'trs_games';
    }

    public function get($id) {
        global $wpdb;

        $data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE id = %d",
            $id
        ), ARRAY_A);

        return $data ? new TRS_Game($data) : null;
    }

    public function get_all($args = array()) {
        global $wpdb;

        $defaults = array(
            'tournament_id' => null,
            'season_id' => null,
            'team_id' => null,
            'status' => null,
            'date_from' => null,
            'date_to' => null,
            'orderby' => 'game_date',
            'order' => 'DESC',
            'limit' => null,
        );

        $args = wp_parse_args($args, $defaults);

        $query = "SELECT * FROM {$this->table}";
        $where = array();
        $params = array();

        if ($args['tournament_id']) {
            $where[] = "tournament_id = %d";
            $params[] = $args['tournament_id'];
        }

        if ($args['season_id']) {
            $where[] = "season_id = %d";
            $params[] = $args['season_id'];
        }

        if ($args['team_id']) {
            $where[] = "(home_team_id = %d OR away_team_id = %d)";
            $params[] = $args['team_id'];
            $params[] = $args['team_id'];
        }

        if ($args['status']) {
            $where[] = "status = %s";
            $params[] = $args['status'];
        }

        if ($args['date_from']) {
            $where[] = "game_date >= %s";
            $params[] = $args['date_from'];
        }

        if ($args['date_to']) {
            $where[] = "game_date <= %s";
            $params[] = $args['date_to'];
        }

        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $query .= " ORDER BY {$args['orderby']} {$args['order']}";

        if ($args['limit']) {
            $query .= " LIMIT {$args['limit']}";
        }

        if (!empty($params)) {
            $query = $wpdb->prepare($query, $params);
        }

        $results = $wpdb->get_results($query, ARRAY_A);
        $games = array();

        foreach ($results as $data) {
            $games[] = new TRS_Game($data);
        }

        return $games;
    }

    public function get_by_tournament($tournament_id) {
        return $this->get_all(array('tournament_id' => $tournament_id));
    }

    public function get_by_team($team_id, $args = array()) {
        $args['team_id'] = $team_id;
        return $this->get_all($args);
    }

    public function create($data) {
        global $wpdb;

        $defaults = array(
            'tournament_id' => null,
            'season_id' => null,
            'game_time' => null,
            'venue' => null,
            'home_score' => null,
            'away_score' => null,
            'status' => 'scheduled',
            'notes' => null,
        );

        $data = wp_parse_args($data, $defaults);

        if (empty($data['game_date']) || empty($data['home_team_id']) || empty($data['away_team_id'])) {
            return false;
        }

        $inserted = $wpdb->insert(
            $this->table,
            array(
                'tournament_id' => $data['tournament_id'],
                'season_id' => $data['season_id'],
                'game_date' => $data['game_date'],
                'game_time' => $data['game_time'],
                'venue' => $data['venue'],
                'home_team_id' => $data['home_team_id'],
                'away_team_id' => $data['away_team_id'],
                'home_score' => $data['home_score'],
                'away_score' => $data['away_score'],
                'status' => $data['status'],
                'notes' => $data['notes'],
            ),
            array('%d', '%d', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s')
        );

        return $inserted ? $wpdb->insert_id : false;
    }

    public function update($id, $data) {
        global $wpdb;

        $allowed_fields = array(
            'tournament_id' => '%d',
            'season_id' => '%d',
            'game_date' => '%s',
            'game_time' => '%s',
            'venue' => '%s',
            'home_team_id' => '%d',
            'away_team_id' => '%d',
            'home_score' => '%d',
            'away_score' => '%d',
            'status' => '%s',
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

    public function delete($id) {
        global $wpdb;

        return $wpdb->delete(
            $this->table,
            array('id' => $id),
            array('%d')
        );
    }
}
