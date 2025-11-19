<?php
/**
 * The Rink Society Hockey Management Plugin
 *
 * @package           TheRinkSociety
 * @author            The Rink Society
 * @copyright         2025 The Rink Society
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       The Rink Society
 * Plugin URI:        https://therinksociety.com
 * Description:       Flexible hockey league, tournament, and player stats management system for community-driven hockey organizations.
 * Version:           0.1.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            The Rink Society
 * Author URI:        https://therinksociety.com
 * Text Domain:       the-rink-society
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TRS_VERSION', '0.1.0');
define('TRS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TRS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TRS_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('TRS_DB_VERSION', '1.0');

/**
 * Activation hook
 */
function activate_the_rink_society() {
    require_once TRS_PLUGIN_DIR . 'includes/class-trs-activator.php';
    TRS_Activator::activate();
}
register_activation_hook(__FILE__, 'activate_the_rink_society');

/**
 * Deactivation hook
 */
function deactivate_the_rink_society() {
    require_once TRS_PLUGIN_DIR . 'includes/class-trs-deactivator.php';
    TRS_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_the_rink_society');

/**
 * Autoloader for plugin classes
 */
spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'TRS_';

    // Base directory for the namespace prefix
    $base_dir = TRS_PLUGIN_DIR . 'includes/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Convert class name to file path
    $file = $base_dir . 'class-' . str_replace('_', '-', strtolower($relative_class)) . '.php';

    // Check in models subdirectory
    if (!file_exists($file)) {
        $file = $base_dir . 'models/class-' . str_replace('_', '-', strtolower($relative_class)) . '.php';
    }

    // Check in repositories subdirectory
    if (!file_exists($file)) {
        $file = $base_dir . 'repositories/class-' . str_replace('_', '-', strtolower($relative_class)) . '.php';
    }

    // Check in helpers subdirectory
    if (!file_exists($file)) {
        $file = $base_dir . 'helpers/class-' . str_replace('_', '-', strtolower($relative_class)) . '.php';
    }

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Initialize the plugin
 */
function run_the_rink_society() {
    require_once TRS_PLUGIN_DIR . 'includes/class-trs-core.php';
    $plugin = new TRS_Core();
    $plugin->run();
}
run_the_rink_society();
