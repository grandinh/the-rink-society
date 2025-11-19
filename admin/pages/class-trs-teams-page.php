<?php
/**
 * Teams Admin Page
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Teams_Page {

    private $team_repo;
    private $player_repo;
    private $season_repo;

    public function __construct() {
        $this->team_repo = new TRS_Team_Repository();
        $this->player_repo = new TRS_Player_Repository();
        $this->season_repo = new TRS_Season_Repository();
    }

    public function render() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $team_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            check_admin_referer('trs_team_action');

            if (isset($_POST['trs_save_team'])) {
                $this->save_team();
            } elseif (isset($_POST['trs_delete_team'])) {
                $this->delete_team($team_id);
            } elseif (isset($_POST['trs_add_player'])) {
                $this->add_player_to_team($team_id);
            } elseif (isset($_POST['trs_remove_player'])) {
                $this->remove_player_from_team($team_id);
            } elseif (isset($_POST['trs_update_roster'])) {
                $this->update_roster($team_id);
            }
        }

        switch ($action) {
            case 'new':
                $this->render_edit_form();
                break;
            case 'edit':
                $this->render_edit_form($team_id);
                break;
            case 'roster':
                $this->render_roster($team_id);
                break;
            case 'delete':
                $this->render_delete_confirm($team_id);
                break;
            default:
                $this->render_list();
                break;
        }
    }

    private function render_list() {
        $teams = $this->team_repo->get_all();
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

        include TRS_PLUGIN_DIR . 'admin/views/teams/list.php';
    }

    private function render_edit_form($team_id = 0) {
        $team = null;
        $edit_mode = false;

        if ($team_id) {
            $team = $this->team_repo->get($team_id);
            $edit_mode = true;
        }

        $seasons = $this->season_repo->get_all();

        include TRS_PLUGIN_DIR . 'admin/views/teams/edit.php';
    }

    private function render_roster($team_id) {
        $team = $this->team_repo->get($team_id);
        if (!$team) {
            wp_die(__('Team not found.', 'the-rink-society'));
        }

        $roster = $team->get_roster();
        $all_players = $this->player_repo->get_all();
        $available_players = array_filter($all_players, function($p) use ($team) {
            return !$p->is_on_team($team->id);
        });

        include TRS_PLUGIN_DIR . 'admin/views/teams/roster.php';
    }

    private function render_delete_confirm($team_id) {
        $team = $this->team_repo->get($team_id);
        if (!$team) {
            wp_die(__('Team not found.', 'the-rink-society'));
        }

        include TRS_PLUGIN_DIR . 'admin/views/teams/delete.php';
    }

    private function save_team() {
        $team_id = isset($_POST['team_id']) ? intval($_POST['team_id']) : 0;

        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'slug' => isset($_POST['slug']) ? sanitize_title($_POST['slug']) : '',
            'logo_url' => isset($_POST['logo_url']) ? esc_url_raw($_POST['logo_url']) : null,
            'primary_color' => isset($_POST['primary_color']) ? sanitize_hex_color($_POST['primary_color']) : null,
            'secondary_color' => isset($_POST['secondary_color']) ? sanitize_hex_color($_POST['secondary_color']) : null,
            'season_id' => isset($_POST['season_id']) ? intval($_POST['season_id']) : null,
        );

        if ($team_id) {
            $this->team_repo->update($team_id, $data);
        } else {
            $team_id = $this->team_repo->create($data);
        }

        wp_redirect(add_query_arg(array(
            'page' => 'trs-teams',
            'action' => 'edit',
            'id' => $team_id,
            'message' => 'saved'
        ), admin_url('admin.php')));
        exit;
    }

    private function delete_team($team_id) {
        $this->team_repo->delete($team_id);

        wp_redirect(add_query_arg(array(
            'page' => 'trs-teams',
            'message' => 'deleted'
        ), admin_url('admin.php')));
        exit;
    }

    private function add_player_to_team($team_id) {
        $team = $this->team_repo->get($team_id);
        $player_id = intval($_POST['player_id']);
        $jersey_number = sanitize_text_field($_POST['jersey_number']);
        $role = sanitize_text_field($_POST['role']);

        $team->add_player($player_id, array(
            'jersey_number' => $jersey_number,
            'role' => $role,
        ));

        wp_redirect(add_query_arg(array(
            'page' => 'trs-teams',
            'action' => 'roster',
            'id' => $team_id,
            'message' => 'player_added'
        ), admin_url('admin.php')));
        exit;
    }

    private function remove_player_from_team($team_id) {
        $team = $this->team_repo->get($team_id);
        $player_id = intval($_POST['player_id']);

        $team->remove_player($player_id);

        wp_redirect(add_query_arg(array(
            'page' => 'trs-teams',
            'action' => 'roster',
            'id' => $team_id,
            'message' => 'player_removed'
        ), admin_url('admin.php')));
        exit;
    }

    private function update_roster($team_id) {
        $team = $this->team_repo->get($team_id);

        if (isset($_POST['roster']) && is_array($_POST['roster'])) {
            foreach ($_POST['roster'] as $player_id => $data) {
                $player_id = intval($player_id);
                $team->update_player_jersey($player_id, sanitize_text_field($data['jersey_number']));
            }
        }

        wp_redirect(add_query_arg(array(
            'page' => 'trs-teams',
            'action' => 'roster',
            'id' => $team_id,
            'message' => 'roster_updated'
        ), admin_url('admin.php')));
        exit;
    }
}
