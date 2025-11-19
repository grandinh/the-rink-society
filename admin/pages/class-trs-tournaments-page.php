<?php
/**
 * Tournaments Admin Page
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Tournaments_Page {

    private $tournament_repo;
    private $season_repo;
    private $team_repo;

    public function __construct() {
        $this->tournament_repo = new TRS_Tournament_Repository();
        $this->season_repo = new TRS_Season_Repository();
        $this->team_repo = new TRS_Team_Repository();
    }

    public function render() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $tournament_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            check_admin_referer('trs_tournament_action');

            if (isset($_POST['trs_save_tournament'])) {
                $this->save_tournament();
            } elseif (isset($_POST['trs_delete_tournament'])) {
                $this->delete_tournament($tournament_id);
            } elseif (isset($_POST['trs_add_team'])) {
                $this->add_team_to_tournament($tournament_id);
            } elseif (isset($_POST['trs_remove_team'])) {
                $team_id = isset($_POST['team_id']) ? intval($_POST['team_id']) : 0;
                $this->remove_team_from_tournament($tournament_id, $team_id);
            }
        }

        switch ($action) {
            case 'new':
                $this->render_edit_form();
                break;
            case 'edit':
                $this->render_edit_form($tournament_id);
                break;
            case 'delete':
                $this->render_delete_confirm($tournament_id);
                break;
            case 'teams':
                $this->render_team_management($tournament_id);
                break;
            case 'standings':
                $this->render_standings($tournament_id);
                break;
            default:
                $this->render_list();
                break;
        }
    }

    private function render_list() {
        $tournaments = $this->tournament_repo->get_all();
        include TRS_PLUGIN_DIR . 'admin/views/tournaments/list.php';
    }

    private function render_edit_form($tournament_id = 0) {
        $tournament = null;
        $edit_mode = false;
        $seasons = $this->season_repo->get_all();

        if ($tournament_id) {
            $tournament = $this->tournament_repo->get($tournament_id);
            $edit_mode = true;
        }

        include TRS_PLUGIN_DIR . 'admin/views/tournaments/edit.php';
    }

    private function render_delete_confirm($tournament_id) {
        $tournament = $this->tournament_repo->get($tournament_id);
        if (!$tournament) {
            wp_die(__('Tournament not found.', 'the-rink-society'));
        }

        $team_count = count($tournament->get_teams());
        $game_count = count($tournament->get_games());

        include TRS_PLUGIN_DIR . 'admin/views/tournaments/delete.php';
    }

    private function render_team_management($tournament_id) {
        $tournament = $this->tournament_repo->get($tournament_id);
        if (!$tournament) {
            wp_die(__('Tournament not found.', 'the-rink-society'));
        }

        $current_teams = $tournament->get_teams();
        $all_teams = $this->team_repo->get_all();

        // Filter out teams already in tournament
        $current_team_ids = array_map(function($team) { return $team->id; }, $current_teams);
        $available_teams = array_filter($all_teams, function($team) use ($current_team_ids) {
            return !in_array($team->id, $current_team_ids);
        });

        include TRS_PLUGIN_DIR . 'admin/views/tournaments/teams.php';
    }

    private function render_standings($tournament_id) {
        $tournament = $this->tournament_repo->get($tournament_id);
        if (!$tournament) {
            wp_die(__('Tournament not found.', 'the-rink-society'));
        }

        $standings = $tournament->get_standings();
        include TRS_PLUGIN_DIR . 'admin/views/tournaments/standings.php';
    }

    private function save_tournament() {
        $tournament_id = isset($_POST['tournament_id']) ? intval($_POST['tournament_id']) : 0;

        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'slug' => isset($_POST['slug']) ? sanitize_title($_POST['slug']) : '',
            'season_id' => isset($_POST['season_id']) ? intval($_POST['season_id']) : null,
            'start_date' => isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : null,
            'end_date' => isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null,
            'format' => sanitize_text_field($_POST['format']),
            'status' => sanitize_text_field($_POST['status']),
            'description' => wp_kses_post($_POST['description']),
        );

        if ($tournament_id) {
            $this->tournament_repo->update($tournament_id, $data);
        } else {
            $tournament_id = $this->tournament_repo->create($data);
        }

        wp_redirect(add_query_arg(array(
            'page' => 'trs-tournaments',
            'action' => 'edit',
            'id' => $tournament_id,
            'message' => 'saved'
        ), admin_url('admin.php')));
        exit;
    }

    private function delete_tournament($tournament_id) {
        $this->tournament_repo->delete($tournament_id);

        wp_redirect(add_query_arg(array(
            'page' => 'trs-tournaments',
            'message' => 'deleted'
        ), admin_url('admin.php')));
        exit;
    }

    private function add_team_to_tournament($tournament_id) {
        $team_id = isset($_POST['team_id']) ? intval($_POST['team_id']) : 0;

        if ($team_id) {
            $tournament = $this->tournament_repo->get($tournament_id);
            $tournament->add_team($team_id);
        }

        wp_redirect(add_query_arg(array(
            'page' => 'trs-tournaments',
            'action' => 'teams',
            'id' => $tournament_id,
            'message' => 'team_added'
        ), admin_url('admin.php')));
        exit;
    }

    private function remove_team_from_tournament($tournament_id, $team_id) {
        $tournament = $this->tournament_repo->get($tournament_id);
        $tournament->remove_team($team_id);

        wp_redirect(add_query_arg(array(
            'page' => 'trs-tournaments',
            'action' => 'teams',
            'id' => $tournament_id,
            'message' => 'team_removed'
        ), admin_url('admin.php')));
        exit;
    }
}
