<?php
/**
 * Tournament Model
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Tournament {

    public $id;
    public $name;
    public $slug;
    public $season_id;
    public $start_date;
    public $end_date;
    public $format;
    public $description;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($data = array()) {
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    public function fill($data) {
        $fields = array('id', 'name', 'slug', 'season_id', 'start_date', 'end_date', 'format', 'description', 'status', 'created_at', 'updated_at');

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
    }

    /**
     * Get all games in this tournament
     */
    public function get_games() {
        $repo = new TRS_Game_Repository();
        return $repo->get_by_tournament($this->id);
    }

    /**
     * Get participating teams
     */
    public function get_teams() {
        $repo = new TRS_Team_Repository();
        return $repo->get_by_tournament($this->id);
    }

    /**
     * Get tournament standings
     */
    public function get_standings() {
        global $wpdb;

        $query = "SELECT
                    t.id, t.name,
                    COUNT(g.id) as games_played,
                    SUM(CASE WHEN (g.home_team_id = t.id AND g.home_score > g.away_score) OR (g.away_team_id = t.id AND g.away_score > g.home_score) THEN 1 ELSE 0 END) as wins,
                    SUM(CASE WHEN (g.home_team_id = t.id AND g.home_score < g.away_score) OR (g.away_team_id = t.id AND g.away_score < g.home_score) THEN 1 ELSE 0 END) as losses,
                    SUM(CASE WHEN g.home_score = g.away_score THEN 1 ELSE 0 END) as ties,
                    SUM(CASE WHEN g.home_team_id = t.id THEN g.home_score WHEN g.away_team_id = t.id THEN g.away_score ELSE 0 END) as goals_for,
                    SUM(CASE WHEN g.home_team_id = t.id THEN g.away_score WHEN g.away_team_id = t.id THEN g.home_score ELSE 0 END) as goals_against
                  FROM {$wpdb->prefix}trs_teams t
                  INNER JOIN {$wpdb->prefix}trs_games g ON (t.id = g.home_team_id OR t.id = g.away_team_id)
                  WHERE g.tournament_id = %d AND g.status = 'final'
                  GROUP BY t.id
                  ORDER BY wins DESC, goals_for DESC";

        return $wpdb->get_results($wpdb->prepare($query, $this->id));
    }

    public function to_array() {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'season_id' => $this->season_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'format' => $this->format,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        );
    }
}
