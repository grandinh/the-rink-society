<?php
/**
 * Admin Dashboard
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get stats
$player_repo = new TRS_Player_Repository();
$team_repo = new TRS_Team_Repository();
$season_repo = new TRS_Season_Repository();
$game_repo = new TRS_Game_Repository();

$total_players = $player_repo->count();
$total_teams = $team_repo->count();
$total_seasons = $season_repo->count();

$active_seasons = $season_repo->get_active();
$recent_games = $game_repo->get_all(array('limit' => 5, 'orderby' => 'game_date', 'order' => 'DESC'));

?>

<div class="wrap trs-admin-wrap">
    <h1><?php _e('Hockey Manager Dashboard', 'the-rink-society'); ?></h1>

    <div class="trs-dashboard-stats">
        <div class="trs-stat-box">
            <div class="trs-stat-number"><?php echo $total_players; ?></div>
            <div class="trs-stat-label"><?php _e('Total Players', 'the-rink-society'); ?></div>
            <a href="<?php echo admin_url('admin.php?page=trs-players'); ?>" class="trs-stat-link">
                <?php _e('Manage Players →', 'the-rink-society'); ?>
            </a>
        </div>

        <div class="trs-stat-box">
            <div class="trs-stat-number"><?php echo $total_teams; ?></div>
            <div class="trs-stat-label"><?php _e('Total Teams', 'the-rink-society'); ?></div>
            <a href="<?php echo admin_url('admin.php?page=trs-teams'); ?>" class="trs-stat-link">
                <?php _e('Manage Teams →', 'the-rink-society'); ?>
            </a>
        </div>

        <div class="trs-stat-box">
            <div class="trs-stat-number"><?php echo $total_seasons; ?></div>
            <div class="trs-stat-label"><?php _e('Total Seasons', 'the-rink-society'); ?></div>
            <a href="<?php echo admin_url('admin.php?page=trs-seasons'); ?>" class="trs-stat-link">
                <?php _e('Manage Seasons →', 'the-rink-society'); ?>
            </a>
        </div>

        <div class="trs-stat-box">
            <div class="trs-stat-number"><?php echo count($active_seasons); ?></div>
            <div class="trs-stat-label"><?php _e('Active Seasons', 'the-rink-society'); ?></div>
            <a href="<?php echo admin_url('admin.php?page=trs-tournaments'); ?>" class="trs-stat-link">
                <?php _e('View Tournaments →', 'the-rink-society'); ?>
            </a>
        </div>
    </div>

    <div class="trs-dashboard-content">
        <div class="trs-dashboard-main">
            <div class="trs-panel">
                <h2><?php _e('Recent Games', 'the-rink-society'); ?></h2>
                <?php if (!empty($recent_games)) : ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Date', 'the-rink-society'); ?></th>
                                <th><?php _e('Home Team', 'the-rink-society'); ?></th>
                                <th><?php _e('Away Team', 'the-rink-society'); ?></th>
                                <th><?php _e('Score', 'the-rink-society'); ?></th>
                                <th><?php _e('Status', 'the-rink-society'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_games as $game) :
                                $home_team = $team_repo->get($game->home_team_id);
                                $away_team = $team_repo->get($game->away_team_id);
                            ?>
                                <tr>
                                    <td><?php echo trs_format_date($game->game_date); ?></td>
                                    <td><?php echo esc_html($home_team->name); ?></td>
                                    <td><?php echo esc_html($away_team->name); ?></td>
                                    <td>
                                        <?php
                                        if ($game->home_score !== null && $game->away_score !== null) {
                                            echo $game->home_score . ' - ' . $game->away_score;
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="trs-status-<?php echo esc_attr($game->status); ?>">
                                            <?php echo trs_get_game_status_label($game->status); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p>
                        <a href="<?php echo admin_url('admin.php?page=trs-games'); ?>" class="button">
                            <?php _e('View All Games', 'the-rink-society'); ?>
                        </a>
                    </p>
                <?php else : ?>
                    <p><?php _e('No games found.', 'the-rink-society'); ?></p>
                    <a href="<?php echo admin_url('admin.php?page=trs-games&action=new'); ?>" class="button button-primary">
                        <?php _e('Create First Game', 'the-rink-society'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="trs-dashboard-sidebar">
            <div class="trs-panel">
                <h3><?php _e('Quick Actions', 'the-rink-society'); ?></h3>
                <ul class="trs-quick-actions">
                    <li><a href="<?php echo admin_url('admin.php?page=trs-players&action=new'); ?>" class="button">➕ <?php _e('Add Player', 'the-rink-society'); ?></a></li>
                    <li><a href="<?php echo admin_url('admin.php?page=trs-teams&action=new'); ?>" class="button">➕ <?php _e('Add Team', 'the-rink-society'); ?></a></li>
                    <li><a href="<?php echo admin_url('admin.php?page=trs-games&action=new'); ?>" class="button">➕ <?php _e('Add Game', 'the-rink-society'); ?></a></li>
                    <li><a href="<?php echo admin_url('admin.php?page=trs-events&action=new'); ?>" class="button">➕ <?php _e('Add Event', 'the-rink-society'); ?></a></li>
                </ul>
            </div>

            <?php if (!empty($active_seasons)) : ?>
            <div class="trs-panel">
                <h3><?php _e('Active Seasons', 'the-rink-society'); ?></h3>
                <ul>
                    <?php foreach ($active_seasons as $season) : ?>
                        <li>
                            <strong><?php echo esc_html($season->name); ?></strong><br>
                            <small>
                                <?php
                                if ($season->start_date && $season->end_date) {
                                    echo trs_format_date($season->start_date) . ' - ' . trs_format_date($season->end_date);
                                }
                                ?>
                            </small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="trs-panel">
                <h3><?php _e('Getting Started', 'the-rink-society'); ?></h3>
                <ol class="trs-setup-steps">
                    <li><?php echo $total_seasons > 0 ? '✓' : '○'; ?> <?php _e('Create a Season', 'the-rink-society'); ?></li>
                    <li><?php echo $total_teams > 0 ? '✓' : '○'; ?> <?php _e('Add Teams', 'the-rink-society'); ?></li>
                    <li><?php echo $total_players > 0 ? '✓' : '○'; ?> <?php _e('Add Players', 'the-rink-society'); ?></li>
                    <li>○ <?php _e('Assign Players to Teams', 'the-rink-society'); ?></li>
                    <li>○ <?php _e('Schedule Games', 'the-rink-society'); ?></li>
                    <li>○ <?php _e('Enter Stats', 'the-rink-society'); ?></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<style>
.trs-dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.trs-stat-box {
    background: white;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    text-align: center;
}

.trs-stat-number {
    font-size: 48px;
    font-weight: bold;
    color: #2271b1;
}

.trs-stat-label {
    font-size: 14px;
    color: #646970;
    margin: 10px 0;
}

.trs-stat-link {
    display: inline-block;
    margin-top: 10px;
    text-decoration: none;
}

.trs-dashboard-content {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    margin-top: 20px;
}

.trs-panel {
    background: white;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
}

.trs-panel h2, .trs-panel h3 {
    margin-top: 0;
}

.trs-quick-actions {
    list-style: none;
    padding: 0;
    margin: 0;
}

.trs-quick-actions li {
    margin-bottom: 10px;
}

.trs-quick-actions .button {
    width: 100%;
    text-align: left;
}

.trs-setup-steps {
    padding-left: 20px;
}

.trs-setup-steps li {
    margin-bottom: 8px;
}

.trs-status-scheduled { color: #996600; }
.trs-status-in_progress { color: #0073aa; }
.trs-status-final { color: #008a00; }
.trs-status-cancelled { color: #dc3232; }

@media (max-width: 1024px) {
    .trs-dashboard-content {
        grid-template-columns: 1fr;
    }
}
</style>
