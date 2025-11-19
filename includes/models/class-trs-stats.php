<?php
/**
 * Stats Model
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Stats {

    public $id;
    public $game_id;
    public $team_id;
    public $player_id;
    public $stat_type;
    public $stat_value;
    public $period;
    public $game_time;
    public $notes;
    public $created_at;
    public $updated_at;

    public function __construct($data = array()) {
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    public function fill($data) {
        $fields = array('id', 'game_id', 'team_id', 'player_id', 'stat_type', 'stat_value', 'period', 'game_time', 'notes', 'created_at', 'updated_at');

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
    }

    /**
     * Get player object
     */
    public function get_player() {
        $repo = new TRS_Player_Repository();
        return $repo->get($this->player_id);
    }

    /**
     * Get game object
     */
    public function get_game() {
        $repo = new TRS_Game_Repository();
        return $repo->get($this->game_id);
    }

    /**
     * Get team object
     */
    public function get_team() {
        $repo = new TRS_Team_Repository();
        return $repo->get($this->team_id);
    }

    public function to_array() {
        return array(
            'id' => $this->id,
            'game_id' => $this->game_id,
            'team_id' => $this->team_id,
            'player_id' => $this->player_id,
            'stat_type' => $this->stat_type,
            'stat_value' => $this->stat_value,
            'period' => $this->period,
            'game_time' => $this->game_time,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        );
    }
}
