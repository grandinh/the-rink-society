<?php
/**
 * Sample Data Generator
 *
 * Creates sample hockey data for testing and development.
 * Access: yoursite.com/wp-content/plugins/the-rink-society/tests/sample-data.php
 *
 * @package TheRinkSociety
 */

// Load WordPress
$wp_load_path = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php';
if (file_exists($wp_load_path)) {
    require_once($wp_load_path);
} else {
    die('WordPress not found.');
}

// Only allow admins
if (!current_user_can('manage_options')) {
    die('You must be an administrator to run this script.');
}

header('Content-Type: text/plain');

echo "==============================================\n";
echo "GENERATING SAMPLE DATA\n";
echo "==============================================\n\n";

// Sample player names and data
$sample_players = array(
    array('first' => 'Connor', 'last' => 'McDavid', 'number' => '97', 'position' => 'forward', 'shoots' => 'left'),
    array('first' => 'Auston', 'last' => 'Matthews', 'number' => '34', 'position' => 'forward', 'shoots' => 'left'),
    array('first' => 'Nathan', 'last' => 'MacKinnon', 'number' => '29', 'position' => 'forward', 'shoots' => 'right'),
    array('first' => 'Sidney', 'last' => 'Crosby', 'number' => '87', 'position' => 'forward', 'shoots' => 'left'),
    array('first' => 'Alex', 'last' => 'Ovechkin', 'number' => '8', 'position' => 'forward', 'shoots' => 'right'),
    array('first' => 'Cale', 'last' => 'Makar', 'number' => '8', 'position' => 'defense', 'shoots' => 'right'),
    array('first' => 'Victor', 'last' => 'Hedman', 'number' => '77', 'position' => 'defense', 'shoots' => 'left'),
    array('first' => 'Roman', 'last' => 'Josi', 'number' => '59', 'position' => 'defense', 'shoots' => 'left'),
    array('first' => 'Igor', 'last' => 'Shesterkin', 'number' => '31', 'position' => 'goalie', 'shoots' => 'left'),
    array('first' => 'Andrei', 'last' => 'Vasilevskiy', 'number' => '88', 'position' => 'goalie', 'shoots' => 'left'),
    array('first' => 'Jack', 'last' => 'Hughes', 'number' => '86', 'position' => 'forward', 'shoots' => 'left'),
    array('first' => 'Elias', 'last' => 'Pettersson', 'number' => '40', 'position' => 'forward', 'shoots' => 'left'),
    array('first' => 'Mikko', 'last' => 'Rantanen', 'number' => '96', 'position' => 'forward', 'shoots' => 'left'),
    array('first' => 'Leon', 'last' => 'Draisaitl', 'number' => '29', 'position' => 'forward', 'shoots' => 'left'),
    array('first' => 'David', 'last' => 'Pastrnak', 'number' => '88', 'position' => 'forward', 'shoots' => 'right'),
);

$sample_teams = array(
    array('name' => 'Ice Dragons', 'color' => '#FF0000', 'secondary' => '#000000'),
    array('name' => 'Thunder Wolves', 'color' => '#0000FF', 'secondary' => '#FFFFFF'),
    array('name' => 'Steel Hawks', 'color' => '#FFD700', 'secondary' => '#000000'),
    array('name' => 'Frost Bears', 'color' => '#00FF00', 'secondary' => '#FFFFFF'),
);

// Create season
echo "Creating Season...\n";
$season_repo = new TRS_Season_Repository();
$season_id = $season_repo->create(array(
    'name' => 'Winter 2025',
    'start_date' => '2025-01-01',
    'end_date' => '2025-03-31',
    'status' => 'active',
));
echo "✓ Created season (ID: $season_id)\n\n";

// Create tournament
echo "Creating Tournament...\n";
$tournament_repo = new TRS_Tournament_Repository();
$tournament_id = $tournament_repo->create(array(
    'name' => 'Chiang Mai Cup 2025',
    'season_id' => $season_id,
    'start_date' => '2025-02-01',
    'end_date' => '2025-02-28',
    'format' => 'round_robin',
    'status' => 'active',
));
echo "✓ Created tournament (ID: $tournament_id)\n\n";

// Create teams
echo "Creating Teams...\n";
$team_repo = new TRS_Team_Repository();
$team_ids = array();

foreach ($sample_teams as $team_data) {
    $team_id = $team_repo->create(array(
        'name' => $team_data['name'],
        'primary_color' => $team_data['color'],
        'secondary_color' => $team_data['secondary'],
        'season_id' => $season_id,
    ));
    $team_ids[] = $team_id;
    echo "✓ Created team: {$team_data['name']} (ID: $team_id)\n";
}
echo "\n";

// Create players and assign to teams
echo "Creating Players...\n";
$player_repo = new TRS_Player_Repository();
$player_ids = array();

foreach ($sample_players as $i => $player_data) {
    // Create WordPress user
    $username = strtolower($player_data['first'] . '.' . $player_data['last']);
    $email = $username . '@rinksociety.test';

    $user_id = wp_create_user($username, wp_generate_password(), $email);

    if (is_wp_error($user_id)) {
        // User might exist, try to get it
        $user = get_user_by('login', $username);
        if ($user) {
            $user_id = $user->ID;
        } else {
            echo "✗ Could not create user: $username\n";
            continue;
        }
    } else {
        // Update display name
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => $player_data['first'] . ' ' . $player_data['last'],
            'first_name' => $player_data['first'],
            'last_name' => $player_data['last'],
        ));
    }

    // Create player profile
    $player_id = $player_repo->create(array(
        'user_id' => $user_id,
        'preferred_jersey_number' => $player_data['number'],
        'position' => $player_data['position'],
        'shoots' => $player_data['shoots'],
    ));

    if ($player_id) {
        $player_ids[] = $player_id;
        echo "✓ Created player: {$player_data['first']} {$player_data['last']} (#{$player_data['number']})\n";

        // Assign to a team (distribute evenly)
        $team_index = $i % count($team_ids);
        $team_id = $team_ids[$team_index];
        $team = $team_repo->get($team_id);

        $team->add_player($player_id, array(
            'season_id' => $season_id,
            'jersey_number' => $player_data['number'],
            'role' => ($i % 5 === 0) ? 'captain' : 'player',
        ));
    }
}
echo "\n";

// Create games
echo "Creating Games...\n";
$game_repo = new TRS_Game_Repository();
$game_ids = array();

// Round-robin: each team plays each other once
for ($i = 0; $i < count($team_ids); $i++) {
    for ($j = $i + 1; $j < count($team_ids); $j++) {
        $game_date = date('Y-m-d', strtotime('+' . (($i * count($team_ids) + $j)) . ' days'));

        $game_id = $game_repo->create(array(
            'tournament_id' => $tournament_id,
            'season_id' => $season_id,
            'game_date' => $game_date,
            'game_time' => '19:00:00',
            'venue' => 'The Rink Society Arena',
            'home_team_id' => $team_ids[$i],
            'away_team_id' => $team_ids[$j],
            'home_score' => rand(0, 8),
            'away_score' => rand(0, 8),
            'status' => 'final',
        ));

        if ($game_id) {
            $game_ids[] = $game_id;
            $home_team = $team_repo->get($team_ids[$i]);
            $away_team = $team_repo->get($team_ids[$j]);
            echo "✓ Created game: {$home_team->name} vs {$away_team->name}\n";
        }
    }
}
echo "\n";

// Add stats to games
echo "Adding Stats to Games...\n";
$stats_repo = new TRS_Stats_Repository();
$stat_types = array('goal', 'assist', 'penalty', 'shot');

foreach ($game_ids as $game_id) {
    $game = $game_repo->get($game_id);

    // Get players for both teams
    $home_players = $player_repo->get_by_team($game->home_team_id, $season_id);
    $away_players = $player_repo->get_by_team($game->away_team_id, $season_id);

    // Add random stats for home team players
    foreach ($home_players as $player) {
        if ($player->position === 'goalie') continue;

        $num_stats = rand(0, 3);
        for ($i = 0; $i < $num_stats; $i++) {
            $stat_type = $stat_types[array_rand($stat_types)];
            $stats_repo->create(array(
                'game_id' => $game_id,
                'team_id' => $game->home_team_id,
                'player_id' => $player->id,
                'stat_type' => $stat_type,
                'stat_value' => 1,
                'period' => (string)rand(1, 3),
            ));
        }
    }

    // Add random stats for away team players
    foreach ($away_players as $player) {
        if ($player->position === 'goalie') continue;

        $num_stats = rand(0, 3);
        for ($i = 0; $i < $num_stats; $i++) {
            $stat_type = $stat_types[array_rand($stat_types)];
            $stats_repo->create(array(
                'game_id' => $game_id,
                'team_id' => $game->away_team_id,
                'player_id' => $player->id,
                'stat_type' => $stat_type,
                'stat_value' => 1,
                'period' => (string)rand(1, 3),
            ));
        }
    }
}
echo "✓ Added stats to all games\n\n";

// Create some events
echo "Creating Events...\n";
$event_types = array('practice', 'meeting', 'social');

foreach ($team_ids as $team_id) {
    for ($i = 0; $i < 3; $i++) {
        $wpdb->insert(
            $wpdb->prefix . 'trs_events',
            array(
                'name' => ucfirst($event_types[$i]) . ' - Team Event',
                'event_type' => $event_types[$i],
                'event_date' => date('Y-m-d', strtotime('+' . ($i * 7) . ' days')),
                'event_time' => '18:00:00',
                'team_id' => $team_id,
                'season_id' => $season_id,
            )
        );
    }
}
echo "✓ Created events for all teams\n\n";

// Summary
echo "==============================================\n";
echo "SAMPLE DATA GENERATED SUCCESSFULLY!\n";
echo "==============================================\n\n";

echo "Summary:\n";
echo "- Season: 1\n";
echo "- Tournament: 1\n";
echo "- Teams: " . count($team_ids) . "\n";
echo "- Players: " . count($player_ids) . "\n";
echo "- Games: " . count($game_ids) . "\n";
echo "- Stats entries: ~" . (count($player_ids) * count($game_ids) / 2) . "\n";
echo "- Events: " . (count($team_ids) * 3) . "\n\n";

echo "You can now:\n";
echo "1. View data in your database tables\n";
echo "2. Test queries and leaderboards\n";
echo "3. Build admin interfaces on top of this data\n\n";

// Show a sample leaderboard
echo "Sample Leaderboard (Goals):\n";
echo "----------------------------\n";
$leaderboard = $stats_repo->get_leaderboard('goal', array('limit' => 5));

foreach ($leaderboard as $rank => $entry) {
    $player = $player_repo->get($entry->player_id);
    echo ($rank + 1) . ". {$player->get_name()} - {$entry->total} goals in {$entry->games_played} games\n";
}
