<?php
/**
 * Player Repository - Data access layer for players
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Player_Repository {

    /**
     * Table name
     */
    private $table;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'trs_players';
    }

    /**
     * Get player by ID
     */
    public function get($id) {
        global $wpdb;

        $data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE id = %d",
            $id
        ), ARRAY_A);

        return $data ? new TRS_Player($data) : null;
    }

    /**
     * Get player by WordPress user ID
     */
    public function get_by_user_id($user_id) {
        global $wpdb;

        $data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE user_id = %d",
            $user_id
        ), ARRAY_A);

        return $data ? new TRS_Player($data) : null;
    }

    /**
     * Get all players
     */
    public function get_all($args = array()) {
        global $wpdb;

        $defaults = array(
            'orderby' => 'id',
            'order' => 'ASC',
            'limit' => null,
            'offset' => 0,
        );

        $args = wp_parse_args($args, $defaults);

        $query = "SELECT * FROM {$this->table}";
        $query .= " ORDER BY {$args['orderby']} {$args['order']}";

        if ($args['limit']) {
            $query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $args['limit'], $args['offset']);
        }

        $results = $wpdb->get_results($query, ARRAY_A);
        $players = array();

        foreach ($results as $data) {
            $players[] = new TRS_Player($data);
        }

        return $players;
    }

    /**
     * Search players by name
     */
    public function search($search_term, $limit = 20) {
        global $wpdb;

        // Join with WordPress users table to search by name
        $query = $wpdb->prepare(
            "SELECT p.* FROM {$this->table} p
            INNER JOIN {$wpdb->users} u ON p.user_id = u.ID
            WHERE u.display_name LIKE %s
            OR u.user_login LIKE %s
            OR u.user_email LIKE %s
            LIMIT %d",
            '%' . $wpdb->esc_like($search_term) . '%',
            '%' . $wpdb->esc_like($search_term) . '%',
            '%' . $wpdb->esc_like($search_term) . '%',
            $limit
        );

        $results = $wpdb->get_results($query, ARRAY_A);
        $players = array();

        foreach ($results as $data) {
            $players[] = new TRS_Player($data);
        }

        return $players;
    }

    /**
     * Get players by team
     */
    public function get_by_team($team_id, $season_id = null, $active_only = true) {
        global $wpdb;

        $query = "SELECT p.* FROM {$this->table} p
                  INNER JOIN {$wpdb->prefix}trs_team_players tp ON p.id = tp.player_id
                  WHERE tp.team_id = %d";

        $params = array($team_id);

        if ($active_only) {
            $query .= " AND tp.is_active = 1";
        }

        if ($season_id) {
            $query .= " AND tp.season_id = %d";
            $params[] = $season_id;
        }

        $query .= " ORDER BY tp.jersey_number ASC, p.id ASC";

        $results = $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
        $players = array();

        foreach ($results as $data) {
            $players[] = new TRS_Player($data);
        }

        return $players;
    }

    /**
     * Get teams for a player
     */
    public function get_player_teams($player_id, $season_id = null, $active_only = true) {
        global $wpdb;

        $query = "SELECT t.* FROM {$wpdb->prefix}trs_teams t
                  INNER JOIN {$wpdb->prefix}trs_team_players tp ON t.id = tp.team_id
                  WHERE tp.player_id = %d";

        $params = array($player_id);

        if ($active_only) {
            $query .= " AND tp.is_active = 1";
        }

        if ($season_id) {
            $query .= " AND tp.season_id = %d";
            $params[] = $season_id;
        }

        return $wpdb->get_results($wpdb->prepare($query, $params), ARRAY_A);
    }

    /**
     * Create new player
     */
    public function create($data) {
        global $wpdb;

        $defaults = array(
            'preferred_jersey_number' => null,
            'position' => null,
            'shoots' => null,
            'birth_date' => null,
        );

        $data = wp_parse_args($data, $defaults);

        // Validate required fields
        if (empty($data['user_id'])) {
            return false;
        }

        // Check if player already exists for this user
        if ($this->get_by_user_id($data['user_id'])) {
            return false;
        }

        $inserted = $wpdb->insert(
            $this->table,
            array(
                'user_id' => $data['user_id'],
                'preferred_jersey_number' => $data['preferred_jersey_number'],
                'position' => $data['position'],
                'shoots' => $data['shoots'],
                'birth_date' => $data['birth_date'],
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );

        return $inserted ? $wpdb->insert_id : false;
    }

    /**
     * Update player
     */
    public function update($id, $data) {
        global $wpdb;

        $allowed_fields = array(
            'preferred_jersey_number' => '%s',
            'position' => '%s',
            'shoots' => '%s',
            'birth_date' => '%s',
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
     * Delete player
     */
    public function delete($id) {
        global $wpdb;

        return $wpdb->delete(
            $this->table,
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * Get total count
     */
    public function count() {
        global $wpdb;
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table}");
    }
}
