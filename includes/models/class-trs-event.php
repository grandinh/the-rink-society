<?php
/**
 * Event Model
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Event {

    public $id;
    public $name;
    public $event_type;
    public $event_date;
    public $event_time;
    public $venue;
    public $team_id;
    public $tournament_id;
    public $season_id;
    public $description;
    public $created_at;
    public $updated_at;

    public function __construct($data = array()) {
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    public function fill($data) {
        $fields = array('id', 'name', 'event_type', 'event_date', 'event_time', 'venue', 'team_id', 'tournament_id', 'season_id', 'description', 'created_at', 'updated_at');

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
    }

    /**
     * Get attendance count
     */
    public function get_attendance_count() {
        global $wpdb;

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}trs_event_attendance WHERE event_id = %d AND status = 'attending'",
            $this->id
        ));
    }

    /**
     * Get all attendees
     */
    public function get_attendees($status = 'attending') {
        global $wpdb;

        $query = "SELECT p.*, ea.status as attendance_status
                  FROM {$wpdb->prefix}trs_event_attendance ea
                  INNER JOIN {$wpdb->prefix}trs_players p ON ea.player_id = p.id";

        if ($status) {
            $query .= " WHERE ea.event_id = %d AND ea.status = %s";
            return $wpdb->get_results($wpdb->prepare($query, $this->id, $status));
        } else {
            $query .= " WHERE ea.event_id = %d";
            return $wpdb->get_results($wpdb->prepare($query, $this->id));
        }
    }

    public function to_array() {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'event_type' => $this->event_type,
            'event_date' => $this->event_date,
            'event_time' => $this->event_time,
            'venue' => $this->venue,
            'team_id' => $this->team_id,
            'tournament_id' => $this->tournament_id,
            'season_id' => $this->season_id,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        );
    }
}
