<?php
/**
 * Admin functionality
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('Hockey Manager', 'the-rink-society'),
            __('Hockey Manager', 'the-rink-society'),
            'manage_options',
            'trs-dashboard',
            array($this, 'dashboard_page'),
            'dashicons-awards',
            30
        );

        // Dashboard (duplicate of main menu for clean URL)
        add_submenu_page(
            'trs-dashboard',
            __('Dashboard', 'the-rink-society'),
            __('Dashboard', 'the-rink-society'),
            'manage_options',
            'trs-dashboard',
            array($this, 'dashboard_page')
        );

        // Players
        add_submenu_page(
            'trs-dashboard',
            __('Players', 'the-rink-society'),
            __('Players', 'the-rink-society'),
            'manage_options',
            'trs-players',
            array($this, 'players_page')
        );

        // Teams
        add_submenu_page(
            'trs-dashboard',
            __('Teams', 'the-rink-society'),
            __('Teams', 'the-rink-society'),
            'manage_options',
            'trs-teams',
            array($this, 'teams_page')
        );

        // Seasons
        add_submenu_page(
            'trs-dashboard',
            __('Seasons', 'the-rink-society'),
            __('Seasons', 'the-rink-society'),
            'manage_options',
            'trs-seasons',
            array($this, 'seasons_page')
        );

        // Tournaments
        add_submenu_page(
            'trs-dashboard',
            __('Tournaments', 'the-rink-society'),
            __('Tournaments', 'the-rink-society'),
            'manage_options',
            'trs-tournaments',
            array($this, 'tournaments_page')
        );

        // Games
        add_submenu_page(
            'trs-dashboard',
            __('Games', 'the-rink-society'),
            __('Games', 'the-rink-society'),
            'manage_options',
            'trs-games',
            array($this, 'games_page')
        );

        // Events
        add_submenu_page(
            'trs-dashboard',
            __('Events', 'the-rink-society'),
            __('Events', 'the-rink-society'),
            'manage_options',
            'trs-events',
            array($this, 'events_page')
        );

        // Settings
        add_submenu_page(
            'trs-dashboard',
            __('Settings', 'the-rink-society'),
            __('Settings', 'the-rink-society'),
            'manage_options',
            'trs-settings',
            array($this, 'settings_page')
        );

        // Stats (hidden page, accessed via Games)
        add_submenu_page(
            null, // Hidden from menu
            __('Game Stats', 'the-rink-society'),
            __('Game Stats', 'the-rink-society'),
            'manage_options',
            'trs-stats',
            array($this, 'stats_page')
        );
    }

    /**
     * Dashboard page
     */
    public function dashboard_page() {
        require_once TRS_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    /**
     * Players page
     */
    public function players_page() {
        require_once TRS_PLUGIN_DIR . 'admin/pages/class-trs-players-page.php';
        $page = new TRS_Players_Page();
        $page->render();
    }

    /**
     * Teams page
     */
    public function teams_page() {
        require_once TRS_PLUGIN_DIR . 'admin/pages/class-trs-teams-page.php';
        $page = new TRS_Teams_Page();
        $page->render();
    }

    /**
     * Seasons page
     */
    public function seasons_page() {
        require_once TRS_PLUGIN_DIR . 'admin/pages/class-trs-seasons-page.php';
        $page = new TRS_Seasons_Page();
        $page->render();
    }

    /**
     * Tournaments page
     */
    public function tournaments_page() {
        require_once TRS_PLUGIN_DIR . 'admin/pages/class-trs-tournaments-page.php';
        $page = new TRS_Tournaments_Page();
        $page->render();
    }

    /**
     * Games page
     */
    public function games_page() {
        require_once TRS_PLUGIN_DIR . 'admin/pages/class-trs-games-page.php';
        $page = new TRS_Games_Page();
        $page->render();
    }

    /**
     * Events page
     */
    public function events_page() {
        require_once TRS_PLUGIN_DIR . 'admin/pages/class-trs-events-page.php';
        $page = new TRS_Events_Page();
        $page->render();
    }

    /**
     * Settings page
     */
    public function settings_page() {
        require_once TRS_PLUGIN_DIR . 'admin/pages/class-trs-settings-page.php';
        $page = new TRS_Settings_Page();
        $page->render();
    }

    /**
     * Stats page (hidden, accessed from games)
     */
    public function stats_page() {
        require_once TRS_PLUGIN_DIR . 'admin/pages/class-trs-stats-page.php';
        $page = new TRS_Stats_Page();
        $page->render();
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('trs_settings', 'trs_date_format');
        register_setting('trs_settings', 'trs_time_format');
        register_setting('trs_settings', 'trs_jersey_validation');
        register_setting('trs_settings', 'trs_default_stat_types');
    }
}

// Initialize admin
if (is_admin()) {
    new TRS_Admin();
}
