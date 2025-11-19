<?php
/**
 * Core plugin class
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Core {

    /**
     * Plugin version
     */
    protected $version;

    /**
     * Constructor
     */
    public function __construct() {
        $this->version = TRS_VERSION;
        $this->load_dependencies();
        $this->define_hooks();
    }

    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Load helper functions
        if (file_exists(TRS_PLUGIN_DIR . 'includes/helpers/functions.php')) {
            require_once TRS_PLUGIN_DIR . 'includes/helpers/functions.php';
        }

        // Load admin class
        if (is_admin()) {
            require_once TRS_PLUGIN_DIR . 'admin/class-trs-admin.php';
        }

        // Load public shortcodes
        if (!is_admin()) {
            require_once TRS_PLUGIN_DIR . 'public/class-trs-shortcodes.php';
        }

        // Load REST API
        require_once TRS_PLUGIN_DIR . 'includes/class-trs-rest-api.php';
    }

    /**
     * Define WordPress hooks
     */
    private function define_hooks() {
        // Admin hooks
        if (is_admin()) {
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        }

        // Public hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));

        // Database version check and upgrade
        add_action('plugins_loaded', array($this, 'check_database_version'));
    }

    /**
     * Check database version and upgrade if needed
     */
    public function check_database_version() {
        $installed_version = get_option('trs_db_version', '0');

        if (version_compare($installed_version, TRS_DB_VERSION, '<')) {
            require_once TRS_PLUGIN_DIR . 'includes/class-trs-activator.php';
            TRS_Activator::activate();
        }
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'trs-') === false && strpos($hook, 'the-rink-society') === false) {
            return;
        }

        wp_enqueue_style(
            'trs-admin-css',
            TRS_PLUGIN_URL . 'admin/assets/css/trs-admin.css',
            array(),
            $this->version
        );

        wp_enqueue_script(
            'trs-admin-js',
            TRS_PLUGIN_URL . 'admin/assets/js/trs-admin.js',
            array('jquery'),
            $this->version,
            true
        );

        // Localize script with AJAX URL and nonce
        wp_localize_script('trs-admin-js', 'trsAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('trs-admin-nonce'),
            'strings' => array(
                'confirmDelete' => __('Are you sure you want to delete this item?', 'the-rink-society'),
                'error' => __('An error occurred. Please try again.', 'the-rink-society'),
            )
        ));
    }

    /**
     * Enqueue public assets
     */
    public function enqueue_public_assets() {
        wp_enqueue_style(
            'trs-public-css',
            TRS_PLUGIN_URL . 'public/assets/css/trs-public.css',
            array(),
            $this->version
        );

        wp_enqueue_script(
            'trs-public-js',
            TRS_PLUGIN_URL . 'public/assets/js/trs-public.js',
            array(),
            $this->version,
            true
        );

        wp_localize_script('trs-public-js', 'trsPublic', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('trs-public-nonce'),
        ));
    }

    /**
     * Run the plugin
     */
    public function run() {
        // Plugin is initialized via hooks
    }

    /**
     * Get plugin version
     */
    public function get_version() {
        return $this->version;
    }
}
