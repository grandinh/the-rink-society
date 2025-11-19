<?php
/**
 * Fired during plugin activation
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

class TRS_Activator {

    /**
     * Activate the plugin
     */
    public static function activate() {
        self::create_tables();
        self::set_default_options();

        // Store database version
        update_option('trs_db_version', TRS_DB_VERSION);

        // Set activation timestamp
        if (!get_option('trs_activated_at')) {
            update_option('trs_activated_at', current_time('mysql'));
        }
    }

    /**
     * Create all custom database tables
     */
    private static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Table names with WordPress prefix
        $players_table = $wpdb->prefix . 'trs_players';
        $teams_table = $wpdb->prefix . 'trs_teams';
        $seasons_table = $wpdb->prefix . 'trs_seasons';
        $tournaments_table = $wpdb->prefix . 'trs_tournaments';
        $games_table = $wpdb->prefix . 'trs_games';
        $events_table = $wpdb->prefix . 'trs_events';
        $team_players_table = $wpdb->prefix . 'trs_team_players';
        $tournament_players_table = $wpdb->prefix . 'trs_tournament_players';
        $game_rosters_table = $wpdb->prefix . 'trs_game_rosters';
        $stats_table = $wpdb->prefix . 'trs_stats';
        $event_attendance_table = $wpdb->prefix . 'trs_event_attendance';

        // Players table (links to WordPress users)
        $sql_players = "CREATE TABLE $players_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            preferred_jersey_number varchar(10) DEFAULT NULL,
            position varchar(20) DEFAULT NULL,
            shoots varchar(10) DEFAULT NULL,
            birth_date date DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_id (user_id),
            KEY position (position)
        ) $charset_collate;";

        // Teams table
        $sql_teams = "CREATE TABLE $teams_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            logo_url varchar(500) DEFAULT NULL,
            primary_color varchar(7) DEFAULT NULL,
            secondary_color varchar(7) DEFAULT NULL,
            season_id bigint(20) UNSIGNED DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug),
            KEY season_id (season_id)
        ) $charset_collate;";

        // Seasons table
        $sql_seasons = "CREATE TABLE $seasons_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            start_date date DEFAULT NULL,
            end_date date DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'upcoming',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug),
            KEY status (status)
        ) $charset_collate;";

        // Tournaments table
        $sql_tournaments = "CREATE TABLE $tournaments_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            slug varchar(255) NOT NULL,
            season_id bigint(20) UNSIGNED DEFAULT NULL,
            start_date date DEFAULT NULL,
            end_date date DEFAULT NULL,
            format varchar(50) DEFAULT NULL,
            description text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'planning',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug),
            KEY season_id (season_id),
            KEY status (status)
        ) $charset_collate;";

        // Games table
        $sql_games = "CREATE TABLE $games_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            tournament_id bigint(20) UNSIGNED DEFAULT NULL,
            season_id bigint(20) UNSIGNED DEFAULT NULL,
            game_date date NOT NULL,
            game_time time DEFAULT NULL,
            venue varchar(255) DEFAULT NULL,
            home_team_id bigint(20) UNSIGNED NOT NULL,
            away_team_id bigint(20) UNSIGNED NOT NULL,
            home_score int DEFAULT NULL,
            away_score int DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'scheduled',
            notes text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY tournament_id (tournament_id),
            KEY season_id (season_id),
            KEY home_team_id (home_team_id),
            KEY away_team_id (away_team_id),
            KEY game_date (game_date),
            KEY status (status)
        ) $charset_collate;";

        // Events table (practices, meetings, etc.)
        $sql_events = "CREATE TABLE $events_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            event_type varchar(50) NOT NULL DEFAULT 'other',
            event_date date NOT NULL,
            event_time time DEFAULT NULL,
            venue varchar(255) DEFAULT NULL,
            team_id bigint(20) UNSIGNED DEFAULT NULL,
            tournament_id bigint(20) UNSIGNED DEFAULT NULL,
            season_id bigint(20) UNSIGNED DEFAULT NULL,
            description text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_type (event_type),
            KEY event_date (event_date),
            KEY team_id (team_id),
            KEY tournament_id (tournament_id),
            KEY season_id (season_id)
        ) $charset_collate;";

        // Team-Players relationship table (many-to-many)
        $sql_team_players = "CREATE TABLE $team_players_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            team_id bigint(20) UNSIGNED NOT NULL,
            player_id bigint(20) UNSIGNED NOT NULL,
            season_id bigint(20) UNSIGNED DEFAULT NULL,
            jersey_number varchar(10) DEFAULT NULL,
            role varchar(50) DEFAULT 'player',
            joined_date date DEFAULT NULL,
            left_date date DEFAULT NULL,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY team_id (team_id),
            KEY player_id (player_id),
            KEY season_id (season_id),
            KEY is_active (is_active)
        ) $charset_collate;";

        // Tournament-Players relationship table
        $sql_tournament_players = "CREATE TABLE $tournament_players_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            tournament_id bigint(20) UNSIGNED NOT NULL,
            player_id bigint(20) UNSIGNED NOT NULL,
            team_id bigint(20) UNSIGNED NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY tournament_player_team (tournament_id, player_id, team_id),
            KEY player_id (player_id),
            KEY team_id (team_id)
        ) $charset_collate;";

        // Game rosters table (who played in each game)
        $sql_game_rosters = "CREATE TABLE $game_rosters_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            game_id bigint(20) UNSIGNED NOT NULL,
            team_id bigint(20) UNSIGNED NOT NULL,
            player_id bigint(20) UNSIGNED NOT NULL,
            jersey_number varchar(10) DEFAULT NULL,
            position varchar(20) DEFAULT NULL,
            is_starter tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY game_team_player (game_id, team_id, player_id),
            KEY team_id (team_id),
            KEY player_id (player_id)
        ) $charset_collate;";

        // Stats table (game performance data)
        $sql_stats = "CREATE TABLE $stats_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            game_id bigint(20) UNSIGNED NOT NULL,
            team_id bigint(20) UNSIGNED NOT NULL,
            player_id bigint(20) UNSIGNED NOT NULL,
            stat_type varchar(50) NOT NULL,
            stat_value decimal(10,2) NOT NULL DEFAULT 0,
            period varchar(10) DEFAULT NULL,
            game_time varchar(10) DEFAULT NULL,
            notes text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY game_id (game_id),
            KEY team_id (team_id),
            KEY player_id (player_id),
            KEY stat_type (stat_type),
            KEY player_game (player_id, game_id),
            KEY game_team (game_id, team_id)
        ) $charset_collate;";

        // Event attendance table
        $sql_event_attendance = "CREATE TABLE $event_attendance_table (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            event_id bigint(20) UNSIGNED NOT NULL,
            player_id bigint(20) UNSIGNED NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'maybe',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY event_player (event_id, player_id),
            KEY player_id (player_id)
        ) $charset_collate;";

        // Execute table creation
        dbDelta($sql_players);
        dbDelta($sql_teams);
        dbDelta($sql_seasons);
        dbDelta($sql_tournaments);
        dbDelta($sql_games);
        dbDelta($sql_events);
        dbDelta($sql_team_players);
        dbDelta($sql_tournament_players);
        dbDelta($sql_game_rosters);
        dbDelta($sql_stats);
        dbDelta($sql_event_attendance);
    }

    /**
     * Set default plugin options
     */
    private static function set_default_options() {
        $defaults = array(
            'trs_date_format' => 'Y-m-d',
            'trs_time_format' => 'H:i',
            'trs_jersey_validation' => 'flexible', // 'strict' or 'flexible'
            'trs_default_stat_types' => array('goal', 'assist', 'penalty', 'shot', 'save'),
        );

        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
}
