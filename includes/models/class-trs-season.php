<?php
/**
 * Season Model
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Season {

    public $id;
    public $name;
    public $slug;
    public $start_date;
    public $end_date;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($data = array()) {
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    public function fill($data) {
        $fields = array('id', 'name', 'slug', 'start_date', 'end_date', 'status', 'created_at', 'updated_at');

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
    }

    /**
     * Check if season is active
     */
    public function is_active() {
        return $this->status === 'active';
    }

    /**
     * Get all teams in this season
     */
    public function get_teams() {
        $repo = new TRS_Team_Repository();
        return $repo->get_all(array('season_id' => $this->id));
    }

    /**
     * Get all tournaments in this season
     */
    public function get_tournaments() {
        $repo = new TRS_Tournament_Repository();
        return $repo->get_by_season($this->id);
    }

    public function to_array() {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        );
    }
}
