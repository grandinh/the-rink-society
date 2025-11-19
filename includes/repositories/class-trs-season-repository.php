<?php
/**
 * Season Repository
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Season_Repository {

    private $table;

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'trs_seasons';
    }

    public function get($id) {
        global $wpdb;

        $data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE id = %d",
            $id
        ), ARRAY_A);

        return $data ? new TRS_Season($data) : null;
    }

    public function get_by_slug($slug) {
        global $wpdb;

        $data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE slug = %s",
            $slug
        ), ARRAY_A);

        return $data ? new TRS_Season($data) : null;
    }

    public function get_all($args = array()) {
        global $wpdb;

        $defaults = array(
            'status' => null,
            'orderby' => 'start_date',
            'order' => 'DESC',
        );

        $args = wp_parse_args($args, $defaults);

        $query = "SELECT * FROM {$this->table}";
        $where = array();
        $params = array();

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
        $seasons = array();

        foreach ($results as $data) {
            $seasons[] = new TRS_Season($data);
        }

        return $seasons;
    }

    public function get_active() {
        return $this->get_all(array('status' => 'active'));
    }

    public function create($data) {
        global $wpdb;

        $defaults = array(
            'start_date' => null,
            'end_date' => null,
            'status' => 'upcoming',
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
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => $data['status'],
            ),
            array('%s', '%s', '%s', '%s', '%s')
        );

        return $inserted ? $wpdb->insert_id : false;
    }

    public function update($id, $data) {
        global $wpdb;

        $allowed_fields = array(
            'name' => '%s',
            'slug' => '%s',
            'start_date' => '%s',
            'end_date' => '%s',
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

    public function count() {
        global $wpdb;
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$this->table}");
    }
}
