<?php
/**
 * Team Repository
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Team_Repository {

    private $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'trs_teams';
    }

    /**
     * Get team by ID
     */
    public function get($id) {
        global $wpdb;

        $data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE id = %d",
            $id
        ), ARRAY_A);

        return $data ? new TRS_Team($data) : null;
    }

    /**
     * Get team by slug
     */
    public function get_by_slug($slug) {
        global $wpdb;

        $data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE slug = %s",
            $slug
        ), ARRAY_A);

        return $data ? new TRS_Team($data) : null;
    }

    /**
     * Get all teams
     */
    public function get_all($args = array()) {
        global $wpdb;

        $defaults = array(
            'season_id' => null,
            'orderby' => 'name',
            'order' => 'ASC',
            'limit' => null,
            'offset' => 0,
        );

        $args = wp_parse_args($args, $defaults);

        $query = "SELECT * FROM {$this->table}";
        $where = array();
        $params = array();

        if ($args['season_id']) {
            $where[] = "season_id = %d";
            $params[] = $args['season_id'];
        }

        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $query .= " ORDER BY {$args['orderby']} {$args['order']}";

        if ($args['limit']) {
            $query .= " LIMIT {$args['limit']} OFFSET {$args['offset']}";
        }

        if (!empty($params)) {
            $query = $wpdb->prepare($query, $params);
        }

        $results = $wpdb->get_results($query, ARRAY_A);
        $teams = array();

        foreach ($results as $data) {
            $teams[] = new TRS_Team($data);
        }

        return $teams;
    }

    /**
     * Get teams by tournament
     */
    public function get_by_tournament($tournament_id) {
        global $wpdb;

        $query = "SELECT DISTINCT t.* FROM {$this->table} t
                  INNER JOIN {$wpdb->prefix}trs_games g
                  ON (t.id = g.home_team_id OR t.id = g.away_team_id)
                  WHERE g.tournament_id = %d
                  ORDER BY t.name ASC";

        $results = $wpdb->get_results($wpdb->prepare($query, $tournament_id), ARRAY_A);
        $teams = array();

        foreach ($results as $data) {
            $teams[] = new TRS_Team($data);
        }

        return $teams;
    }

    /**
     * Create team
     */
    public function create($data) {
        global $wpdb;

        $defaults = array(
            'logo_url' => null,
            'primary_color' => null,
            'secondary_color' => null,
            'season_id' => null,
        );

        $data = wp_parse_args($data, $defaults);

        if (empty($data['name'])) {
            return false;
        }

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = sanitize_title($data['name']);
        }

        // Ensure unique slug
        $slug = $data['slug'];
        $suffix = 1;
        while ($this->get_by_slug($slug)) {
            $slug = $data['slug'] . '-' . $suffix;
            $suffix++;
        }

        $inserted = $wpdb->insert(
            $this->table,
            array(
                'name' => $data['name'],
                'slug' => $slug,
                'logo_url' => $data['logo_url'],
                'primary_color' => $data['primary_color'],
                'secondary_color' => $data['secondary_color'],
                'season_id' => $data['season_id'],
            ),
            array('%s', '%s', '%s', '%s', '%s', '%d')
        );

        return $inserted ? $wpdb->insert_id : false;
    }

    /**
     * Update team
     */
    public function update($id, $data) {
        global $wpdb;

        $allowed_fields = array(
            'name' => '%s',
            'slug' => '%s',
            'logo_url' => '%s',
            'primary_color' => '%s',
            'secondary_color' => '%s',
            'season_id' => '%d',
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
     * Delete team
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
    public function count($season_id = null) {
        global $wpdb;

        if ($season_id) {
            return (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table} WHERE season_id = %d",
                $season_id
            ));
        }

        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table}");
    }
}
