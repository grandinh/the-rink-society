<?php
/**
 * Games Admin Page
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Games_Page {

    private $game_repo;
    private $team_repo;
    private $season_repo;
    private $tournament_repo;
    private $stats_repo;

    public function __construct() {
        $this->game_repo = new TRS_Game_Repository();
        $this->team_repo = new TRS_Team_Repository();
        $this->season_repo = new TRS_Season_Repository();
        $this->tournament_repo = new TRS_Tournament_Repository();
        $this->stats_repo = new TRS_Stats_Repository();
    }

    public function render() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $game_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            check_admin_referer('trs_game_action');

            if (isset($_POST['trs_save_game'])) {
                $this->save_game();
            } elseif (isset($_POST['trs_delete_game'])) {
                $this->delete_game($game_id);
            } elseif (isset($_POST['trs_update_score'])) {
                $this->update_score($game_id);
            }
        }

        switch ($action) {
            case 'new':
                $this->render_edit_form();
                break;
            case 'edit':
                $this->render_edit_form($game_id);
                break;
            case 'delete':
                $this->render_delete_confirm($game_id);
                break;
            case 'score':
                $this->render_score_entry($game_id);
                break;
            default:
                $this->render_list();
                break;
        }
    }

    private function render_list() {
        // Filter options
        $season_id = isset($_GET['season_id']) ? intval($_GET['season_id']) : 0;
        $tournament_id = isset($_GET['tournament_id']) ? intval($_GET['tournament_id']) : 0;
        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

        $args = array();
        if ($season_id) $args['season_id'] = $season_id;
        if ($tournament_id) $args['tournament_id'] = $tournament_id;
        if ($status) $args['status'] = $status;

        $games = $this->game_repo->get_all($args);
        $seasons = $this->season_repo->get_all();
        $tournaments = $this->tournament_repo->get_all();

        include TRS_PLUGIN_DIR . 'admin/views/games/list.php';
    }

    private function render_edit_form($game_id = 0) {
        $game = null;
        $edit_mode = false;
        $teams = $this->team_repo->get_all();
        $seasons = $this->season_repo->get_all();
        $tournaments = $this->tournament_repo->get_all();

        if ($game_id) {
            $game = $this->game_repo->get($game_id);
            $edit_mode = true;
        }

        include TRS_PLUGIN_DIR . 'admin/views/games/edit.php';
    }

    private function render_delete_confirm($game_id) {
        $game = $this->game_repo->get($game_id);
        if (!$game) {
            wp_die(__('Game not found.', 'the-rink-society'));
        }

        $stats_count = count($this->stats_repo->get_by_game($game_id));

        include TRS_PLUGIN_DIR . 'admin/views/games/delete.php';
    }

    private function render_score_entry($game_id) {
        $game = $this->game_repo->get($game_id);
        if (!$game) {
            wp_die(__('Game not found.', 'the-rink-society'));
        }

        include TRS_PLUGIN_DIR . 'admin/views/games/score.php';
    }

    private function save_game() {
        $game_id = isset($_POST['game_id']) ? intval($_POST['game_id']) : 0;

        $data = array(
            'season_id' => isset($_POST['season_id']) && $_POST['season_id'] ? intval($_POST['season_id']) : null,
            'tournament_id' => isset($_POST['tournament_id']) && $_POST['tournament_id'] ? intval($_POST['tournament_id']) : null,
            'home_team_id' => intval($_POST['home_team_id']),
            'away_team_id' => intval($_POST['away_team_id']),
            'game_date' => sanitize_text_field($_POST['game_date']),
            'game_time' => isset($_POST['game_time']) ? sanitize_text_field($_POST['game_time']) : null,
            'location' => sanitize_text_field($_POST['location']),
            'status' => sanitize_text_field($_POST['status']),
            'home_score' => isset($_POST['home_score']) ? intval($_POST['home_score']) : null,
            'away_score' => isset($_POST['away_score']) ? intval($_POST['away_score']) : null,
        );

        // Validate teams are different
        if ($data['home_team_id'] === $data['away_team_id']) {
            wp_die(__('Home and away teams must be different.', 'the-rink-society'));
        }

        if ($game_id) {
            $this->game_repo->update($game_id, $data);
        } else {
            $game_id = $this->game_repo->create($data);
        }

        wp_redirect(add_query_arg(array(
            'page' => 'trs-games',
            'action' => 'edit',
            'id' => $game_id,
            'message' => 'saved'
        ), admin_url('admin.php')));
        exit;
    }

    private function delete_game($game_id) {
        $this->game_repo->delete($game_id);

        wp_redirect(add_query_arg(array(
            'page' => 'trs-games',
            'message' => 'deleted'
        ), admin_url('admin.php')));
        exit;
    }

    private function update_score($game_id) {
        $data = array(
            'home_score' => isset($_POST['home_score']) ? intval($_POST['home_score']) : null,
            'away_score' => isset($_POST['away_score']) ? intval($_POST['away_score']) : null,
            'status' => 'completed',
        );

        $this->game_repo->update($game_id, $data);

        wp_redirect(add_query_arg(array(
            'page' => 'trs-games',
            'action' => 'score',
            'id' => $game_id,
            'message' => 'score_updated'
        ), admin_url('admin.php')));
        exit;
    }
}
