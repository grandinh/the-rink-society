<?php
/**
 * Players Admin Page
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Players_Page {

    private $player_repo;
    private $team_repo;

    public function __construct() {
        $this->player_repo = new TRS_Player_Repository();
        $this->team_repo = new TRS_Team_Repository();
    }

    public function render() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $player_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            check_admin_referer('trs_player_action');

            if (isset($_POST['trs_save_player'])) {
                $this->save_player();
            } elseif (isset($_POST['trs_delete_player'])) {
                $this->delete_player($player_id);
            }
        }

        // Render appropriate view
        switch ($action) {
            case 'new':
                $this->render_edit_form();
                break;
            case 'edit':
                $this->render_edit_form($player_id);
                break;
            case 'delete':
                $this->render_delete_confirm($player_id);
                break;
            default:
                $this->render_list();
                break;
        }
    }

    private function render_list() {
        $players = $this->player_repo->get_all(array('orderby' => 'id', 'order' => 'DESC'));
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

        if ($search) {
            $players = $this->player_repo->search($search);
        }

        include TRS_PLUGIN_DIR . 'admin/views/players/list.php';
    }

    private function render_edit_form($player_id = 0) {
        $player = null;
        $edit_mode = false;

        if ($player_id) {
            $player = $this->player_repo->get($player_id);
            $edit_mode = true;
        }

        // Get all WordPress users
        $wp_users = get_users(array('orderby' => 'display_name'));

        // Get users that don't have player profiles yet
        $available_users = array();
        foreach ($wp_users as $user) {
            $existing = $this->player_repo->get_by_user_id($user->ID);
            if (!$existing || ($player && $existing->id == $player->id)) {
                $available_users[] = $user;
            }
        }

        include TRS_PLUGIN_DIR . 'admin/views/players/edit.php';
    }

    private function render_delete_confirm($player_id) {
        $player = $this->player_repo->get($player_id);

        if (!$player) {
            wp_die(__('Player not found.', 'the-rink-society'));
        }

        include TRS_PLUGIN_DIR . 'admin/views/players/delete.php';
    }

    private function save_player() {
        $player_id = isset($_POST['player_id']) ? intval($_POST['player_id']) : 0;
        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

        $data = array(
            'user_id' => $user_id,
            'preferred_jersey_number' => isset($_POST['preferred_jersey_number']) ? sanitize_text_field($_POST['preferred_jersey_number']) : null,
            'position' => isset($_POST['position']) ? sanitize_text_field($_POST['position']) : null,
            'shoots' => isset($_POST['shoots']) ? sanitize_text_field($_POST['shoots']) : null,
            'birth_date' => isset($_POST['birth_date']) ? sanitize_text_field($_POST['birth_date']) : null,
        );

        if ($player_id) {
            // Update existing player
            $this->player_repo->update($player_id, $data);
            $message = __('Player updated successfully.', 'the-rink-society');
        } else {
            // Create new player
            $player_id = $this->player_repo->create($data);
            $message = __('Player created successfully.', 'the-rink-society');
        }

        wp_redirect(add_query_arg(array(
            'page' => 'trs-players',
            'action' => 'edit',
            'id' => $player_id,
            'message' => 'saved'
        ), admin_url('admin.php')));
        exit;
    }

    private function delete_player($player_id) {
        $this->player_repo->delete($player_id);

        wp_redirect(add_query_arg(array(
            'page' => 'trs-players',
            'message' => 'deleted'
        ), admin_url('admin.php')));
        exit;
    }
}
