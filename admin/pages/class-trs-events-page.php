<?php
/**
 * Events Admin Page
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Events_Page {

    private $event_repo;
    private $player_repo;

    public function __construct() {
        $this->event_repo = new TRS_Event_Repository();
        $this->player_repo = new TRS_Player_Repository();
    }

    public function render() {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
        $event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            check_admin_referer('trs_event_action');

            if (isset($_POST['trs_save_event'])) {
                $this->save_event();
            } elseif (isset($_POST['trs_delete_event'])) {
                $this->delete_event($event_id);
            } elseif (isset($_POST['trs_mark_attendance'])) {
                $this->mark_attendance($event_id);
            }
        }

        switch ($action) {
            case 'new':
                $this->render_edit_form();
                break;
            case 'edit':
                $this->render_edit_form($event_id);
                break;
            case 'delete':
                $this->render_delete_confirm($event_id);
                break;
            case 'attendance':
                $this->render_attendance($event_id);
                break;
            default:
                $this->render_list();
                break;
        }
    }

    private function render_list() {
        $events = $this->event_repo->get_all();
        include TRS_PLUGIN_DIR . 'admin/views/events/list.php';
    }

    private function render_edit_form($event_id = 0) {
        $event = null;
        $edit_mode = false;

        if ($event_id) {
            $event = $this->event_repo->get($event_id);
            $edit_mode = true;
        }

        include TRS_PLUGIN_DIR . 'admin/views/events/edit.php';
    }

    private function render_delete_confirm($event_id) {
        $event = $this->event_repo->get($event_id);
        if (!$event) {
            wp_die(__('Event not found.', 'the-rink-society'));
        }

        $attendance_count = count($event->get_attendance());

        include TRS_PLUGIN_DIR . 'admin/views/events/delete.php';
    }

    private function render_attendance($event_id) {
        $event = $this->event_repo->get($event_id);
        if (!$event) {
            wp_die(__('Event not found.', 'the-rink-society'));
        }

        $attendance = $event->get_attendance();
        $all_players = $this->player_repo->get_all();

        include TRS_PLUGIN_DIR . 'admin/views/events/attendance.php';
    }

    private function save_event() {
        $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;

        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'slug' => isset($_POST['slug']) ? sanitize_title($_POST['slug']) : '',
            'event_type' => sanitize_text_field($_POST['event_type']),
            'event_date' => sanitize_text_field($_POST['event_date']),
            'event_time' => isset($_POST['event_time']) ? sanitize_text_field($_POST['event_time']) : null,
            'location' => sanitize_text_field($_POST['location']),
            'description' => wp_kses_post($_POST['description']),
            'max_attendees' => isset($_POST['max_attendees']) && $_POST['max_attendees'] ? intval($_POST['max_attendees']) : null,
        );

        if ($event_id) {
            $this->event_repo->update($event_id, $data);
        } else {
            $event_id = $this->event_repo->create($data);
        }

        wp_redirect(add_query_arg(array(
            'page' => 'trs-events',
            'action' => 'edit',
            'id' => $event_id,
            'message' => 'saved'
        ), admin_url('admin.php')));
        exit;
    }

    private function delete_event($event_id) {
        $this->event_repo->delete($event_id);

        wp_redirect(add_query_arg(array(
            'page' => 'trs-events',
            'message' => 'deleted'
        ), admin_url('admin.php')));
        exit;
    }

    private function mark_attendance($event_id) {
        $player_ids = isset($_POST['attendees']) ? array_map('intval', $_POST['attendees']) : array();

        $event = $this->event_repo->get($event_id);
        $event->mark_attendance($player_ids);

        wp_redirect(add_query_arg(array(
            'page' => 'trs-events',
            'action' => 'attendance',
            'id' => $event_id,
            'message' => 'attendance_saved'
        ), admin_url('admin.php')));
        exit;
    }
}
