<?php
/**
 * Game Model
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Game {

    public $id;
    public $tournament_id;
    public $season_id;
    public $game_date;
    public $game_time;
    public $venue;
    public $home_team_id;
    public $away_team_id;
    public $home_score;
    public $away_score;
    public $status;
    public $notes;
    public $created_at;
    public $updated_at;

    public function __construct($data = array()) {
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    public function fill($data) {
        $fields = array('id', 'tournament_id', 'season_id', 'game_date', 'game_time', 'venue', 'home_team_id', 'away_team_id', 'home_score', 'away_score', 'status', 'notes', 'created_at', 'updated_at');

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
    }

    /**
     * Get home team
     */
    public function get_home_team() {
        $repo = new TRS_Team_Repository();
        return $repo->get($this->home_team_id);
    }

    /**
     * Get away team
     */
    public function get_away_team() {
        $repo = new TRS_Team_Repository();
        return $repo->get($this->away_team_id);
    }

    /**
     * Get game roster for a team
     */
    public function get_roster($team_id) {
        global $wpdb;

        $query = "SELECT gr.*, p.*
                  FROM {$wpdb->prefix}trs_game_rosters gr
                  INNER JOIN {$wpdb->prefix}trs_players p ON gr.player_id = p.id
                  WHERE gr.game_id = %d AND gr.team_id = %d
                  ORDER BY gr.jersey_number ASC";

        return $wpdb->get_results($wpdb->prepare($query, $this->id, $team_id));
    }

    /**
     * Get all stats for this game
     */
    public function get_stats($team_id = null) {
        $repo = new TRS_Stats_Repository();
        return $repo->get_by_game($this->id, $team_id);
    }

    /**
     * Get winner team ID
     */
    public function get_winner() {
        if ($this->status !== 'final' || $this->home_score === null || $this->away_score === null) {
            return null;
        }

        if ($this->home_score > $this->away_score) {
            return $this->home_team_id;
        } elseif ($this->away_score > $this->home_score) {
            return $this->away_team_id;
        }

        return null; // Tie
    }

    /**
     * Check if game is final
     */
    public function is_final() {
        return $this->status === 'final';
    }

    public function to_array() {
        return array(
            'id' => $this->id,
            'tournament_id' => $this->tournament_id,
            'season_id' => $this->season_id,
            'game_date' => $this->game_date,
            'game_time' => $this->game_time,
            'venue' => $this->venue,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'home_score' => $this->home_score,
            'away_score' => $this->away_score,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        );
    }
}
