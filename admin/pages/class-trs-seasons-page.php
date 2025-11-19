<?php
/**
 * Seasons Admin Page
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Seasons_Page {

    private $season_repo;

    public function __construct() {
        $this->season_repo = new TRS_Season_Repository();
    }

    public function render() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $season_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            check_admin_referer('trs_season_action');

            if (isset($_POST['trs_save_season'])) {
                $this->save_season();
            } elseif (isset($_POST['trs_delete_season'])) {
                $this->delete_season($season_id);
            }
        }

        switch ($action) {
            case 'new':
                $this->render_edit_form();
                break;
            case 'edit':
                $this->render_edit_form($season_id);
                break;
            case 'delete':
                $this->render_delete_confirm($season_id);
                break;
            default:
                $this->render_list();
                break;
        }
    }

    private function render_list() {
        $seasons = $this->season_repo->get_all();
        include TRS_PLUGIN_DIR . 'admin/views/seasons/list.php';
    }

    private function render_edit_form($season_id = 0) {
        $season = null;
        $edit_mode = false;

        if ($season_id) {
            $season = $this->season_repo->get($season_id);
            $edit_mode = true;
        }

        include TRS_PLUGIN_DIR . 'admin/views/seasons/edit.php';
    }

    private function render_delete_confirm($season_id) {
        $season = $this->season_repo->get($season_id);
        if (!$season) {
            wp_die(__('Season not found.', 'the-rink-society'));
        }
        include TRS_PLUGIN_DIR . 'admin/views/seasons/delete.php';
    }

    private function save_season() {
        $season_id = isset($_POST['season_id']) ? intval($_POST['season_id']) : 0;

        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'slug' => isset($_POST['slug']) ? sanitize_title($_POST['slug']) : '',
            'start_date' => isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : null,
            'end_date' => isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null,
            'status' => sanitize_text_field($_POST['status']),
        );

        if ($season_id) {
            $this->season_repo->update($season_id, $data);
        } else {
            $season_id = $this->season_repo->create($data);
        }

        wp_redirect(add_query_arg(array(
            'page' => 'trs-seasons',
            'action' => 'edit',
            'id' => $season_id,
            'message' => 'saved'
        ), admin_url('admin.php')));
        exit;
    }

    private function delete_season($season_id) {
        $this->season_repo->delete($season_id);

        wp_redirect(add_query_arg(array(
            'page' => 'trs-seasons',
            'message' => 'deleted'
        ), admin_url('admin.php')));
        exit;
    }
}
