<?php
/**
 * REST API Endpoints
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_REST_API {

    private $namespace = 'trs/v1';

    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        // Players
        register_rest_route($this->namespace, '/players', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_players'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route($this->namespace, '/players/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_player'),
            'permission_callback' => '__return_true',
        ));

        // Teams
        register_rest_route($this->namespace, '/teams', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_teams'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route($this->namespace, '/teams/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_team'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route($this->namespace, '/teams/(?P<id>\d+)/roster', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_team_roster'),
            'permission_callback' => '__return_true',
        ));

        // Games
        register_rest_route($this->namespace, '/games', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_games'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route($this->namespace, '/games/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_game'),
            'permission_callback' => '__return_true',
        ));

        // Stats
        register_rest_route($this->namespace, '/stats/leaderboard', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_leaderboard'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route($this->namespace, '/stats/player/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_player_stats'),
            'permission_callback' => '__return_true',
        ));

        // Tournaments
        register_rest_route($this->namespace, '/tournaments/(?P<id>\d+)/standings', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_tournament_standings'),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * Get players list
     */
    public function get_players($request) {
        $player_repo = new TRS_Player_Repository();
        $team_id = $request->get_param('team_id');

        if ($team_id) {
            $players = $player_repo->get_by_team(intval($team_id));
        } else {
            $players = $player_repo->get_all();
        }

        $data = array_map(function($player) {
            $user = $player->get_user();
            return array(
                'id' => $player->id,
                'name' => $user->display_name,
                'jersey_number' => $player->preferred_jersey_number,
                'position' => $player->position,
                'shoots' => $player->shoots,
            );
        }, $players);

        return rest_ensure_response($data);
    }

    /**
     * Get single player
     */
    public function get_player($request) {
        $player_repo = new TRS_Player_Repository();
        $player = $player_repo->get(intval($request['id']));

        if (!$player) {
            return new WP_Error('not_found', 'Player not found', array('status' => 404));
        }

        $user = $player->get_user();
        $teams = $player->get_teams();

        $data = array(
            'id' => $player->id,
            'name' => $user->display_name,
            'jersey_number' => $player->preferred_jersey_number,
            'position' => $player->position,
            'shoots' => $player->shoots,
            'birth_date' => $player->birth_date,
            'teams' => array_map(function($team) {
                return array(
                    'id' => $team->id,
                    'name' => $team->name,
                );
            }, $teams),
        );

        return rest_ensure_response($data);
    }

    /**
     * Get teams list
     */
    public function get_teams($request) {
        $team_repo = new TRS_Team_Repository();
        $teams = $team_repo->get_all();

        $data = array_map(function($team) {
            return array(
                'id' => $team->id,
                'name' => $team->name,
                'primary_color' => $team->primary_color,
                'secondary_color' => $team->secondary_color,
            );
        }, $teams);

        return rest_ensure_response($data);
    }

    /**
     * Get single team
     */
    public function get_team($request) {
        $team_repo = new TRS_Team_Repository();
        $team = $team_repo->get(intval($request['id']));

        if (!$team) {
            return new WP_Error('not_found', 'Team not found', array('status' => 404));
        }

        $record = $team->get_record();

        $data = array(
            'id' => $team->id,
            'name' => $team->name,
            'primary_color' => $team->primary_color,
            'secondary_color' => $team->secondary_color,
            'record' => $record,
        );

        return rest_ensure_response($data);
    }

    /**
     * Get team roster
     */
    public function get_team_roster($request) {
        $player_repo = new TRS_Player_Repository();
        $team_id = intval($request['id']);
        $players = $player_repo->get_by_team($team_id);

        $data = array_map(function($player) use ($team_id) {
            $user = $player->get_user();
            return array(
                'id' => $player->id,
                'name' => $user->display_name,
                'jersey_number' => $player->get_jersey_number($team_id),
                'position' => $player->position,
            );
        }, $players);

        return rest_ensure_response($data);
    }

    /**
     * Get games list
     */
    public function get_games($request) {
        $game_repo = new TRS_Game_Repository();

        $args = array();
        if ($request->get_param('season_id')) {
            $args['season_id'] = intval($request->get_param('season_id'));
        }
        if ($request->get_param('tournament_id')) {
            $args['tournament_id'] = intval($request->get_param('tournament_id'));
        }
        if ($request->get_param('team_id')) {
            $args['team_id'] = intval($request->get_param('team_id'));
        }
        if ($request->get_param('status')) {
            $args['status'] = sanitize_text_field($request->get_param('status'));
        }
        if ($request->get_param('limit')) {
            $args['limit'] = intval($request->get_param('limit'));
        }

        $games = $game_repo->get_all($args);

        $data = array_map(function($game) {
            $home_team = $game->get_home_team();
            $away_team = $game->get_away_team();

            return array(
                'id' => $game->id,
                'date' => $game->game_date,
                'time' => $game->game_time,
                'home_team' => array(
                    'id' => $home_team->id,
                    'name' => $home_team->name,
                    'score' => $game->home_score,
                ),
                'away_team' => array(
                    'id' => $away_team->id,
                    'name' => $away_team->name,
                    'score' => $game->away_score,
                ),
                'location' => $game->location,
                'status' => $game->status,
            );
        }, $games);

        return rest_ensure_response($data);
    }

    /**
     * Get single game
     */
    public function get_game($request) {
        $game_repo = new TRS_Game_Repository();
        $game = $game_repo->get(intval($request['id']));

        if (!$game) {
            return new WP_Error('not_found', 'Game not found', array('status' => 404));
        }

        $home_team = $game->get_home_team();
        $away_team = $game->get_away_team();

        $data = array(
            'id' => $game->id,
            'date' => $game->game_date,
            'time' => $game->game_time,
            'home_team' => array(
                'id' => $home_team->id,
                'name' => $home_team->name,
                'score' => $game->home_score,
            ),
            'away_team' => array(
                'id' => $away_team->id,
                'name' => $away_team->name,
                'score' => $game->away_score,
            ),
            'location' => $game->location,
            'status' => $game->status,
        );

        return rest_ensure_response($data);
    }

    /**
     * Get leaderboard
     */
    public function get_leaderboard($request) {
        $stats_repo = new TRS_Stats_Repository();
        $type = sanitize_text_field($request->get_param('type') ?: 'points');

        $args = array();
        if ($request->get_param('season_id')) {
            $args['season_id'] = intval($request->get_param('season_id'));
        }
        if ($request->get_param('tournament_id')) {
            $args['tournament_id'] = intval($request->get_param('tournament_id'));
        }
        if ($request->get_param('limit')) {
            $args['limit'] = intval($request->get_param('limit'));
        } else {
            $args['limit'] = 10;
        }

        if ($type === 'points') {
            $leaders = $stats_repo->get_points_leaders($args);
        } else {
            $leaders = $stats_repo->get_leaderboard($type === 'assists' ? 'assist' : 'goal', $args);
        }

        return rest_ensure_response($leaders);
    }

    /**
     * Get player stats
     */
    public function get_player_stats($request) {
        $stats_repo = new TRS_Stats_Repository();
        $player_id = intval($request['id']);

        $args = array();
        if ($request->get_param('season_id')) {
            $args['season_id'] = intval($request->get_param('season_id'));
        }
        if ($request->get_param('tournament_id')) {
            $args['tournament_id'] = intval($request->get_param('tournament_id'));
        }

        $stats = $stats_repo->get_player_totals($player_id, $args);

        return rest_ensure_response($stats);
    }

    /**
     * Get tournament standings
     */
    public function get_tournament_standings($request) {
        $tournament_repo = new TRS_Tournament_Repository();
        $tournament = $tournament_repo->get(intval($request['id']));

        if (!$tournament) {
            return new WP_Error('not_found', 'Tournament not found', array('status' => 404));
        }

        $standings = $tournament->get_standings();

        return rest_ensure_response($standings);
    }
}

// Initialize REST API
new TRS_REST_API();
