<?php
/**
 * Shortcodes for public display
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Shortcodes {

    private $player_repo;
    private $team_repo;
    private $game_repo;
    private $stats_repo;
    private $tournament_repo;
    private $season_repo;

    public function __construct() {
        $this->player_repo = new TRS_Player_Repository();
        $this->team_repo = new TRS_Team_Repository();
        $this->game_repo = new TRS_Game_Repository();
        $this->stats_repo = new TRS_Stats_Repository();
        $this->tournament_repo = new TRS_Tournament_Repository();
        $this->season_repo = new TRS_Season_Repository();

        $this->register_shortcodes();
    }

    private function register_shortcodes() {
        add_shortcode('trs_player_profile', array($this, 'player_profile'));
        add_shortcode('trs_team_roster', array($this, 'team_roster'));
        add_shortcode('trs_games_schedule', array($this, 'games_schedule'));
        add_shortcode('trs_leaderboard', array($this, 'leaderboard'));
        add_shortcode('trs_tournament_standings', array($this, 'tournament_standings'));
        add_shortcode('trs_player_stats', array($this, 'player_stats'));
    }

    /**
     * Player profile shortcode
     * Usage: [trs_player_profile id="1"]
     */
    public function player_profile($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts);

        $player = $this->player_repo->get(intval($atts['id']));
        if (!$player) {
            return '<p>Player not found.</p>';
        }

        ob_start();
        include TRS_PLUGIN_DIR . 'public/templates/player-profile.php';
        return ob_get_clean();
    }

    /**
     * Team roster shortcode
     * Usage: [trs_team_roster id="1"]
     */
    public function team_roster($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts);

        $team = $this->team_repo->get(intval($atts['id']));
        if (!$team) {
            return '<p>Team not found.</p>';
        }

        $players = $this->player_repo->get_by_team($team->id);

        ob_start();
        include TRS_PLUGIN_DIR . 'public/templates/team-roster.php';
        return ob_get_clean();
    }

    /**
     * Games schedule shortcode
     * Usage: [trs_games_schedule season_id="1" limit="10"]
     */
    public function games_schedule($atts) {
        $atts = shortcode_atts(array(
            'season_id' => 0,
            'tournament_id' => 0,
            'team_id' => 0,
            'limit' => 10,
            'status' => '',
        ), $atts);

        $args = array('limit' => intval($atts['limit']));
        if ($atts['season_id']) $args['season_id'] = intval($atts['season_id']);
        if ($atts['tournament_id']) $args['tournament_id'] = intval($atts['tournament_id']);
        if ($atts['team_id']) $args['team_id'] = intval($atts['team_id']);
        if ($atts['status']) $args['status'] = sanitize_text_field($atts['status']);

        $games = $this->game_repo->get_all($args);

        ob_start();
        include TRS_PLUGIN_DIR . 'public/templates/games-schedule.php';
        return ob_get_clean();
    }

    /**
     * Leaderboard shortcode
     * Usage: [trs_leaderboard type="points" limit="10"]
     */
    public function leaderboard($atts) {
        $atts = shortcode_atts(array(
            'type' => 'points',
            'season_id' => 0,
            'tournament_id' => 0,
            'limit' => 10,
        ), $atts);

        $args = array('limit' => intval($atts['limit']));
        if ($atts['season_id']) $args['season_id'] = intval($atts['season_id']);
        if ($atts['tournament_id']) $args['tournament_id'] = intval($atts['tournament_id']);

        $type = sanitize_text_field($atts['type']);

        if ($type === 'points') {
            $leaders = $this->stats_repo->get_points_leaders($args);
        } elseif ($type === 'goals') {
            $leaders = $this->stats_repo->get_leaderboard('goal', $args);
        } elseif ($type === 'assists') {
            $leaders = $this->stats_repo->get_leaderboard('assist', $args);
        } else {
            return '<p>Invalid leaderboard type.</p>';
        }

        ob_start();
        include TRS_PLUGIN_DIR . 'public/templates/leaderboard.php';
        return ob_get_clean();
    }

    /**
     * Tournament standings shortcode
     * Usage: [trs_tournament_standings id="1"]
     */
    public function tournament_standings($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts);

        $tournament = $this->tournament_repo->get(intval($atts['id']));
        if (!$tournament) {
            return '<p>Tournament not found.</p>';
        }

        $standings = $tournament->get_standings();

        ob_start();
        include TRS_PLUGIN_DIR . 'public/templates/tournament-standings.php';
        return ob_get_clean();
    }

    /**
     * Player stats shortcode
     * Usage: [trs_player_stats id="1"]
     */
    public function player_stats($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'season_id' => 0,
            'tournament_id' => 0,
        ), $atts);

        $player = $this->player_repo->get(intval($atts['id']));
        if (!$player) {
            return '<p>Player not found.</p>';
        }

        $args = array('player_id' => $player->id);
        if ($atts['season_id']) $args['season_id'] = intval($atts['season_id']);
        if ($atts['tournament_id']) $args['tournament_id'] = intval($atts['tournament_id']);

        $stats = $this->stats_repo->get_player_totals($player->id, $args);

        ob_start();
        include TRS_PLUGIN_DIR . 'public/templates/player-stats.php';
        return ob_get_clean();
    }
}

// Initialize shortcodes
new TRS_Shortcodes();
