<?php
/**
 * Player Model
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Player {

    /**
     * Player ID
     */
    public $id;

    /**
     * WordPress user ID
     */
    public $user_id;

    /**
     * Preferred jersey number
     */
    public $preferred_jersey_number;

    /**
     * Position (forward, defense, goalie)
     */
    public $position;

    /**
     * Shoots (left, right)
     */
    public $shoots;

    /**
     * Birth date
     */
    public $birth_date;

    /**
     * Created timestamp
     */
    public $created_at;

    /**
     * Updated timestamp
     */
    public $updated_at;

    /**
     * WordPress user object (lazy loaded)
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct($data = array()) {
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    /**
     * Fill model with data
     */
    public function fill($data) {
        $fields = array('id', 'user_id', 'preferred_jersey_number', 'position', 'shoots', 'birth_date', 'created_at', 'updated_at');

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
    }

    /**
     * Get WordPress user
     */
    public function get_user() {
        if (!$this->user && $this->user_id) {
            $this->user = get_user_by('id', $this->user_id);
        }
        return $this->user;
    }

    /**
     * Get player display name
     */
    public function get_name() {
        $user = $this->get_user();
        return $user ? $user->display_name : '';
    }

    /**
     * Get player email
     */
    public function get_email() {
        $user = $this->get_user();
        return $user ? $user->user_email : '';
    }

    /**
     * Get player avatar URL
     */
    public function get_avatar_url($size = 96) {
        return get_avatar_url($this->user_id, array('size' => $size));
    }

    /**
     * Get jersey number for a specific context
     *
     * @param int $team_id Team ID (optional)
     * @param int $game_id Game ID (optional)
     * @return string Jersey number or null
     */
    public function get_jersey_number($team_id = null, $game_id = null) {
        global $wpdb;

        // Priority: game override > team override > player default

        // Check game roster override
        if ($game_id && $team_id) {
            $game_number = $wpdb->get_var($wpdb->prepare(
                "SELECT jersey_number FROM {$wpdb->prefix}trs_game_rosters
                WHERE game_id = %d AND team_id = %d AND player_id = %d AND jersey_number IS NOT NULL",
                $game_id, $team_id, $this->id
            ));
            if ($game_number !== null) {
                return $game_number;
            }
        }

        // Check team roster override
        if ($team_id) {
            $team_number = $wpdb->get_var($wpdb->prepare(
                "SELECT jersey_number FROM {$wpdb->prefix}trs_team_players
                WHERE team_id = %d AND player_id = %d AND is_active = 1 AND jersey_number IS NOT NULL",
                $team_id, $this->id
            ));
            if ($team_number !== null) {
                return $team_number;
            }
        }

        // Fall back to player default
        return $this->preferred_jersey_number;
    }

    /**
     * Check if player is on a team
     */
    public function is_on_team($team_id, $season_id = null) {
        global $wpdb;

        $query = "SELECT COUNT(*) FROM {$wpdb->prefix}trs_team_players
                  WHERE team_id = %d AND player_id = %d AND is_active = 1";
        $params = array($team_id, $this->id);

        if ($season_id) {
            $query .= " AND season_id = %d";
            $params[] = $season_id;
        }

        return $wpdb->get_var($wpdb->prepare($query, $params)) > 0;
    }

    /**
     * Get all teams for this player
     */
    public function get_teams($season_id = null, $active_only = true) {
        $repo = new TRS_Player_Repository();
        return $repo->get_player_teams($this->id, $season_id, $active_only);
    }

    /**
     * Convert to array
     */
    public function to_array() {
        return array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->get_name(),
            'email' => $this->get_email(),
            'preferred_jersey_number' => $this->preferred_jersey_number,
            'position' => $this->position,
            'shoots' => $this->shoots,
            'birth_date' => $this->birth_date,
            'avatar_url' => $this->get_avatar_url(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        );
    }
}
