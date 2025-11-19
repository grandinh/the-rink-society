<?php
/**
 * Stats Admin Page
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Stats_Page {

    private $game_repo;
    private $player_repo;
    private $team_repo;
    private $stats_repo;

    public function __construct() {
        $this->game_repo = new TRS_Game_Repository();
        $this->player_repo = new TRS_Player_Repository();
        $this->team_repo = new TRS_Team_Repository();
        $this->stats_repo = new TRS_Stats_Repository();
    }

    public function render() {
        $game_id = isset($_GET['game_id']) ? intval($_GET['game_id']) : 0;

        if (!$game_id) {
            wp_die(__('Invalid game.', 'the-rink-society'));
        }

        $game = $this->game_repo->get($game_id);
        if (!$game) {
            wp_die(__('Game not found.', 'the-rink-society'));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            check_admin_referer('trs_stats_action');

            if (isset($_POST['trs_save_roster'])) {
                $this->save_roster($game_id);
            } elseif (isset($_POST['trs_save_stats'])) {
                $this->save_stats($game_id);
            }
        }

        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'roster';

        switch ($action) {
            case 'roster':
                $this->render_roster_management($game);
                break;
            case 'stats':
                $this->render_stats_entry($game);
                break;
            default:
                $this->render_roster_management($game);
                break;
        }
    }

    private function render_roster_management($game) {
        $home_team = $game->get_home_team();
        $away_team = $game->get_away_team();

        // Get current game roster
        $home_roster = $game->get_roster($home_team->id);
        $away_roster = $game->get_roster($away_team->id);

        // Get all team players
        $home_team_players = $this->player_repo->get_by_team($home_team->id);
        $away_team_players = $this->player_repo->get_by_team($away_team->id);

        include TRS_PLUGIN_DIR . 'admin/views/stats/roster.php';
    }

    private function render_stats_entry($game) {
        $home_team = $game->get_home_team();
        $away_team = $game->get_away_team();

        // Get game rosters
        $home_roster = $game->get_roster($home_team->id);
        $away_roster = $game->get_roster($away_team->id);

        if (empty($home_roster) || empty($away_roster)) {
            wp_die(__('Please set the game roster first.', 'the-rink-society') . ' <a href="' . admin_url('admin.php?page=trs-stats&game_id=' . $game->id . '&action=roster') . '">' . __('Manage Roster', 'the-rink-society') . '</a>');
        }

        // Get existing stats
        $existing_stats = array();
        foreach ($this->stats_repo->get_by_game($game->id) as $stat) {
            $key = $stat->team_id . '_' . $stat->player_id;
            if (!isset($existing_stats[$key])) {
                $existing_stats[$key] = array();
            }
            $existing_stats[$key][] = $stat;
        }

        include TRS_PLUGIN_DIR . 'admin/views/stats/entry.php';
    }

    private function save_roster($game_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'trs_game_rosters';

        $game = $this->game_repo->get($game_id);
        $home_team_id = $game->home_team_id;
        $away_team_id = $game->away_team_id;

        // Clear existing roster
        $wpdb->delete($table, array('game_id' => $game_id));

        // Save home team roster
        if (isset($_POST['home_players']) && is_array($_POST['home_players'])) {
            foreach ($_POST['home_players'] as $player_id) {
                $player_id = intval($player_id);
                $jersey = isset($_POST['home_jersey_' . $player_id]) ? sanitize_text_field($_POST['home_jersey_' . $player_id]) : null;

                $wpdb->insert($table, array(
                    'game_id' => $game_id,
                    'team_id' => $home_team_id,
                    'player_id' => $player_id,
                    'jersey_number' => $jersey,
                ));
            }
        }

        // Save away team roster
        if (isset($_POST['away_players']) && is_array($_POST['away_players'])) {
            foreach ($_POST['away_players'] as $player_id) {
                $player_id = intval($player_id);
                $jersey = isset($_POST['away_jersey_' . $player_id]) ? sanitize_text_field($_POST['away_jersey_' . $player_id]) : null;

                $wpdb->insert($table, array(
                    'game_id' => $game_id,
                    'team_id' => $away_team_id,
                    'player_id' => $player_id,
                    'jersey_number' => $jersey,
                ));
            }
        }

        wp_redirect(add_query_arg(array(
            'page' => 'trs-stats',
            'game_id' => $game_id,
            'action' => 'stats',
            'message' => 'roster_saved'
        ), admin_url('admin.php')));
        exit;
    }

    private function save_stats($game_id) {
        global $wpdb;
        $stats_table = $wpdb->prefix . 'trs_stats';

        $game = $this->game_repo->get($game_id);

        // Clear existing stats for this game
        $wpdb->delete($stats_table, array('game_id' => $game_id));

        // Process stats for each player
        if (isset($_POST['stats']) && is_array($_POST['stats'])) {
            foreach ($_POST['stats'] as $team_player_key => $stats_data) {
                list($team_id, $player_id) = explode('_', $team_player_key);
                $team_id = intval($team_id);
                $player_id = intval($player_id);

                // Goals
                $goals = isset($stats_data['goals']) ? intval($stats_data['goals']) : 0;
                for ($i = 0; $i < $goals; $i++) {
                    $wpdb->insert($stats_table, array(
                        'game_id' => $game_id,
                        'team_id' => $team_id,
                        'player_id' => $player_id,
                        'stat_type' => 'goal',
                        'value' => 1,
                    ));
                }

                // Assists
                $assists = isset($stats_data['assists']) ? intval($stats_data['assists']) : 0;
                for ($i = 0; $i < $assists; $i++) {
                    $wpdb->insert($stats_table, array(
                        'game_id' => $game_id,
                        'team_id' => $team_id,
                        'player_id' => $player_id,
                        'stat_type' => 'assist',
                        'value' => 1,
                    ));
                }

                // Penalties
                $penalties = isset($stats_data['pim']) ? intval($stats_data['pim']) : 0;
                if ($penalties > 0) {
                    $wpdb->insert($stats_table, array(
                        'game_id' => $game_id,
                        'team_id' => $team_id,
                        'player_id' => $player_id,
                        'stat_type' => 'penalty_minutes',
                        'value' => $penalties,
                    ));
                }
            }
        }

        wp_redirect(add_query_arg(array(
            'page' => 'trs-stats',
            'game_id' => $game_id,
            'action' => 'stats',
            'message' => 'stats_saved'
        ), admin_url('admin.php')));
        exit;
    }
}
