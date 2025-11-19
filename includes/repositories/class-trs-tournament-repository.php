<?php
/**
 * Tournament Repository
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Tournament_Repository {

    private $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'trs_tournaments';
    }

    public function get($id) {
        global $wpdb;

        $data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE id = %d",
            $id
        ), ARRAY_A);

        return $data ? new TRS_Tournament($data) : null;
    }

    public function get_by_slug($slug) {
        global $wpdb;

        $data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE slug = %s",
            $slug
        ), ARRAY_A);

        return $data ? new TRS_Tournament($data) : null;
    }

    public function get_all($args = array()) {
        global $wpdb;

        $defaults = array(
            'season_id' => null,
            'status' => null,
            'orderby' => 'start_date',
            'order' => 'DESC',
        );

        $args = wp_parse_args($args, $defaults);

        $query = "SELECT * FROM {$this->table}";
        $where = array();
        $params = array();

        if ($args['season_id']) {
            $where[] = "season_id = %d";
            $params[] = $args['season_id'];
        }

        if ($args['status']) {
            $where[] = "status = %s";
            $params[] = $args['status'];
        }

        if (!empty($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }

        $query .= " ORDER BY {$args['orderby']} {$args['order']}";

        if (!empty($params)) {
            $query = $wpdb->prepare($query, $params);
        }

        $results = $wpdb->get_results($query, ARRAY_A);
        $tournaments = array();

        foreach ($results as $data) {
            $tournaments[] = new TRS_Tournament($data);
        }

        return $tournaments;
    }

    public function get_by_season($season_id) {
        return $this->get_all(array('season_id' => $season_id));
    }

    public function create($data) {
        global $wpdb;

        $defaults = array(
            'season_id' => null,
            'start_date' => null,
            'end_date' => null,
            'format' => null,
            'description' => null,
            'status' => 'planning',
        );

        $data = wp_parse_args($data, $defaults);

        if (empty($data['name'])) {
            return false;
        }

        if (empty($data['slug'])) {
            $data['slug'] = sanitize_title($data['name']);
        }

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
                'season_id' => $data['season_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'format' => $data['format'],
                'description' => $data['description'],
                'status' => $data['status'],
            ),
            array('%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s')
        );

        return $inserted ? $wpdb->insert_id : false;
    }

    public function update($id, $data) {
        global $wpdb;

        $allowed_fields = array(
            'name' => '%s',
            'slug' => '%s',
            'season_id' => '%d',
            'start_date' => '%s',
            'end_date' => '%s',
            'format' => '%s',
            'description' => '%s',
            'status' => '%s',
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
