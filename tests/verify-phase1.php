<?php
/**
 * Phase 1 Verification Script
 *
 * This script tests the core foundation of the plugin.
 * Run this by accessing: yoursite.com/wp-content/plugins/the-rink-society/tests/verify-phase1.php
 *
 * @package TheRinkSociety
 */

// Load WordPress
$wp_load_path = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php';
if (file_exists($wp_load_path)) {
    require_once($wp_load_path);
} else {
    die('WordPress not found. Make sure this plugin is in wp-content/plugins/');
}

// Only allow admins to run this
if (!current_user_can('manage_options')) {
    die('You must be an administrator to run this verification script.');
}

header('Content-Type: text/plain');

echo "==============================================\n";
echo "THE RINK SOCIETY - PHASE 1 VERIFICATION\n";
echo "==============================================\n\n";

$errors = 0;
$warnings = 0;
$passes = 0;

function test_pass($message) {
    global $passes;
    echo "✓ PASS: $message\n";
    $passes++;
}

function test_fail($message) {
    global $errors;
    echo "✗ FAIL: $message\n";
    $errors++;
}

function test_warn($message) {
    global $warnings;
    echo "⚠ WARN: $message\n";
    $warnings++;
}

// Test 1: Check if plugin is active
echo "TEST 1: Plugin Activation\n";
echo "----------------------------\n";
if (defined('TRS_VERSION')) {
    test_pass("Plugin constants defined (version: " . TRS_VERSION . ")");
} else {
    test_fail("Plugin constants not defined");
}

// Test 2: Check database tables
echo "\nTEST 2: Database Tables\n";
echo "----------------------------\n";
global $wpdb;

$required_tables = array(
    'trs_players',
    'trs_teams',
    'trs_seasons',
    'trs_tournaments',
    'trs_games',
    'trs_events',
    'trs_team_players',
    'trs_tournament_players',
    'trs_game_rosters',
    'trs_stats',
    'trs_event_attendance',
);

foreach ($required_tables as $table) {
    $full_table_name = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table_name'");
    if ($exists) {
        test_pass("Table exists: $full_table_name");
    } else {
        test_fail("Table missing: $full_table_name");
    }
}

// Test 3: Check options
echo "\nTEST 3: Plugin Options\n";
echo "----------------------------\n";
$db_version = get_option('trs_db_version');
if ($db_version) {
    test_pass("Database version: $db_version");
} else {
    test_fail("Database version option not set");
}

$activated_at = get_option('trs_activated_at');
if ($activated_at) {
    test_pass("Activation timestamp: $activated_at");
} else {
    test_warn("Activation timestamp not set");
}

// Test 4: Test Model Creation
echo "\nTEST 4: Model CRUD Operations\n";
echo "----------------------------\n";

try {
    // Create a WordPress user for testing
    $test_user_id = wp_create_user('trs_test_player', wp_generate_password(), 'test@rinksociety.test');

    if (is_wp_error($test_user_id)) {
        // User might already exist
        $test_user = get_user_by('login', 'trs_test_player');
        if ($test_user) {
            $test_user_id = $test_user->ID;
            test_warn("Using existing test user (ID: $test_user_id)");
        } else {
            throw new Exception("Could not create test user");
        }
    } else {
        test_pass("Created test WordPress user (ID: $test_user_id)");
    }

    // Test Player creation
    $player_repo = new TRS_Player_Repository();
    $player_id = $player_repo->create(array(
        'user_id' => $test_user_id,
        'preferred_jersey_number' => '99',
        'position' => 'forward',
        'shoots' => 'left',
    ));

    if ($player_id) {
        test_pass("Created player (ID: $player_id)");

        // Test Player retrieval
        $player = $player_repo->get($player_id);
        if ($player && $player->preferred_jersey_number === '99') {
            test_pass("Retrieved player with correct data");
        } else {
            test_fail("Player data incorrect after retrieval");
        }
    } else {
        test_fail("Could not create player");
    }

    // Test Team creation
    $team_repo = new TRS_Team_Repository();
    $team_id = $team_repo->create(array(
        'name' => 'Test Hockey Team',
        'slug' => 'test-hockey-team',
    ));

    if ($team_id) {
        test_pass("Created team (ID: $team_id)");

        $team = $team_repo->get($team_id);
        if ($team && $team->name === 'Test Hockey Team') {
            test_pass("Retrieved team with correct data");
        } else {
            test_fail("Team data incorrect after retrieval");
        }
    } else {
        test_fail("Could not create team");
    }

    // Test Season creation
    $season_repo = new TRS_Season_Repository();
    $season_id = $season_repo->create(array(
        'name' => 'Test Season 2025',
        'status' => 'active',
    ));

    if ($season_id) {
        test_pass("Created season (ID: $season_id)");
    } else {
        test_fail("Could not create season");
    }

    // Test adding player to team
    if ($player_id && $team_id) {
        $team = $team_repo->get($team_id);
        $result = $team->add_player($player_id, array(
            'jersey_number' => '88',
            'role' => 'player',
        ));

        if ($result) {
            test_pass("Added player to team with jersey override");

            // Test jersey number cascade
            $player = $player_repo->get($player_id);
            $jersey = $player->get_jersey_number($team_id);
            if ($jersey === '88') {
                test_pass("Jersey number cascade works (team override: $jersey)");
            } else {
                test_fail("Jersey number cascade failed (expected 88, got $jersey)");
            }

            // Test without team context (should return default)
            $default_jersey = $player->get_jersey_number();
            if ($default_jersey === '99') {
                test_pass("Default jersey number works: $default_jersey");
            } else {
                test_fail("Default jersey number failed");
            }
        } else {
            test_fail("Could not add player to team");
        }
    }

    // Test Game creation
    if ($team_id) {
        $team2_id = $team_repo->create(array('name' => 'Test Team 2'));

        $game_repo = new TRS_Game_Repository();
        $game_id = $game_repo->create(array(
            'game_date' => date('Y-m-d'),
            'home_team_id' => $team_id,
            'away_team_id' => $team2_id,
            'season_id' => $season_id,
        ));

        if ($game_id) {
            test_pass("Created game (ID: $game_id)");

            // Update game score
            $result = $game_repo->update($game_id, array(
                'home_score' => 5,
                'away_score' => 3,
                'status' => 'final',
            ));

            if ($result !== false) {
                test_pass("Updated game score");
            } else {
                test_fail("Could not update game score");
            }
        } else {
            test_fail("Could not create game");
        }

        // Test Stats creation
        if ($game_id && $player_id) {
            $stats_repo = new TRS_Stats_Repository();
            $stat_id = $stats_repo->create(array(
                'game_id' => $game_id,
                'team_id' => $team_id,
                'player_id' => $player_id,
                'stat_type' => 'goal',
                'stat_value' => 2,
                'period' => '1',
            ));

            if ($stat_id) {
                test_pass("Created stat entry (ID: $stat_id)");

                // Test stats retrieval
                $stats = $stats_repo->get_by_player($player_id);
                if (count($stats) > 0) {
                    test_pass("Retrieved player stats (found " . count($stats) . " entries)");
                } else {
                    test_fail("Could not retrieve player stats");
                }

                // Test leaderboard
                $leaderboard = $stats_repo->get_leaderboard('goal', array('limit' => 10));
                if (count($leaderboard) > 0) {
                    test_pass("Generated leaderboard (" . count($leaderboard) . " entries)");
                } else {
                    test_warn("Leaderboard query returned no results");
                }
            } else {
                test_fail("Could not create stat entry");
            }
        }
    }

} catch (Exception $e) {
    test_fail("Exception during testing: " . $e->getMessage());
}

// Test 5: Check autoloader
echo "\nTEST 5: Class Autoloading\n";
echo "----------------------------\n";

$classes_to_check = array(
    'TRS_Player',
    'TRS_Team',
    'TRS_Season',
    'TRS_Tournament',
    'TRS_Game',
    'TRS_Event',
    'TRS_Stats',
    'TRS_Player_Repository',
    'TRS_Team_Repository',
    'TRS_Game_Repository',
    'TRS_Stats_Repository',
);

foreach ($classes_to_check as $class) {
    if (class_exists($class)) {
        test_pass("Class loaded: $class");
    } else {
        test_fail("Class not found: $class");
    }
}

// Summary
echo "\n==============================================\n";
echo "VERIFICATION SUMMARY\n";
echo "==============================================\n";
echo "Passes:   $passes\n";
echo "Failures: $errors\n";
echo "Warnings: $warnings\n";
echo "\n";

if ($errors === 0) {
    echo "✓ Phase 1 verification PASSED!\n";
    echo "Ready to proceed to Phase 2 (Admin Interfaces)\n";
} else {
    echo "✗ Phase 1 verification FAILED\n";
    echo "Please fix the errors above before proceeding.\n";
}

echo "\nNote: Test data has been created in the database.\n";
echo "You can manually inspect the tables to verify data integrity.\n";
