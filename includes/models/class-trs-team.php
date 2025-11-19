<?php
/**
 * Team Model
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Team {

    public $id;
    public $name;
    public $slug;
    public $logo_url;
    public $primary_color;
    public $secondary_color;
    public $season_id;
    public $created_at;
    public $updated_at;

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
        $fields = array('id', 'name', 'slug', 'logo_url', 'primary_color', 'secondary_color', 'season_id', 'created_at', 'updated_at');

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }
    }

    /**
     * Get team roster
     */
    public function get_roster($season_id = null, $active_only = true) {
        $repo = new TRS_Player_Repository();
        return $repo->get_by_team($this->id, $season_id, $active_only);
    }

    /**
     * Get roster count
     */
    public function get_roster_count($season_id = null) {
        global $wpdb;

        $query = "SELECT COUNT(*) FROM {$wpdb->prefix}trs_team_players
                  WHERE team_id = %d AND is_active = 1";
        $params = array($this->id);

        if ($season_id) {
            $query .= " AND season_id = %d";
            $params[] = $season_id;
        }

        return (int) $wpdb->get_var($wpdb->prepare($query, $params));
    }

    /**
     * Add player to team
     */
    public function add_player($player_id, $args = array()) {
        global $wpdb;

        $defaults = array(
            'season_id' => $this->season_id,
            'jersey_number' => null,
            'role' => 'player',
            'joined_date' => current_time('mysql', true),
            'is_active' => 1,
        );

        $args = wp_parse_args($args, $defaults);

        return $wpdb->insert(
            $wpdb->prefix . 'trs_team_players',
            array(
                'team_id' => $this->id,
                'player_id' => $player_id,
                'season_id' => $args['season_id'],
                'jersey_number' => $args['jersey_number'],
                'role' => $args['role'],
                'joined_date' => $args['joined_date'],
                'is_active' => $args['is_active'],
            ),
            array('%d', '%d', '%d', '%s', '%s', '%s', '%d')
        );
    }

    /**
     * Remove player from team
     */
    public function remove_player($player_id, $season_id = null) {
        global $wpdb;

        $where = array(
            'team_id' => $this->id,
            'player_id' => $player_id,
        );
        $formats = array('%d', '%d');

        if ($season_id) {
            $where['season_id'] = $season_id;
            $formats[] = '%d';
        }

        // Soft delete by setting is_active = 0 and left_date
        return $wpdb->update(
            $wpdb->prefix . 'trs_team_players',
            array(
                'is_active' => 0,
                'left_date' => current_time('mysql', true),
            ),
            $where,
            array('%d', '%s'),
            $formats
        );
    }

    /**
     * Update player jersey number on this team
     */
    public function update_player_jersey($player_id, $jersey_number, $season_id = null) {
        global $wpdb;

        $where = array(
            'team_id' => $this->id,
            'player_id' => $player_id,
            'is_active' => 1,
        );
        $formats = array('%d', '%d', '%d');

        if ($season_id) {
            $where['season_id'] = $season_id;
            $formats[] = '%d';
        }

        return $wpdb->update(
            $wpdb->prefix . 'trs_team_players',
            array('jersey_number' => $jersey_number),
            $where,
            array('%s'),
            $formats
        );
    }

    /**
     * Get team record (wins, losses, ties)
     */
    public function get_record($tournament_id = null, $season_id = null) {
        global $wpdb;

        $where = array("(g.home_team_id = %d OR g.away_team_id = %d)");
        $params = array($this->id, $this->id);

        if ($tournament_id) {
            $where[] = "g.tournament_id = %d";
            $params[] = $tournament_id;
        }

        if ($season_id) {
            $where[] = "g.season_id = %d";
            $params[] = $season_id;
        }

        $where[] = "g.status = 'final'";

        $query = "SELECT
                    SUM(CASE
                        WHEN (g.home_team_id = {$this->id} AND g.home_score > g.away_score)
                          OR (g.away_team_id = {$this->id} AND g.away_score > g.home_score)
                        THEN 1 ELSE 0 END) as wins,
                    SUM(CASE
                        WHEN (g.home_team_id = {$this->id} AND g.home_score < g.away_score)
                          OR (g.away_team_id = {$this->id} AND g.away_score < g.home_score)
                        THEN 1 ELSE 0 END) as losses,
                    SUM(CASE
                        WHEN g.home_score = g.away_score
                        THEN 1 ELSE 0 END) as ties
                  FROM {$wpdb->prefix}trs_games g
                  WHERE " . implode(' AND ', $where);

        $result = $wpdb->get_row($wpdb->prepare($query, $params));

        return array(
            'wins' => (int) ($result->wins ?? 0),
            'losses' => (int) ($result->losses ?? 0),
            'ties' => (int) ($result->ties ?? 0),
        );
    }

    /**
     * Convert to array
     */
    public function to_array() {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo_url' => $this->logo_url,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'season_id' => $this->season_id,
            'roster_count' => $this->get_roster_count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        );
    }
}
