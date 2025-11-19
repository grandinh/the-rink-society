<?php
/**
 * Stats Leaderboard View
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) exit;

$stats_repo = new TRS_Stats_Repository();
$season_repo = new TRS_Season_Repository();
$tournament_repo = new TRS_Tournament_Repository();

// Filters
$season_id = isset($_GET['season_id']) ? intval($_GET['season_id']) : 0;
$tournament_id = isset($_GET['tournament_id']) ? intval($_GET['tournament_id']) : 0;

$filter_args = array();
if ($season_id) $filter_args['season_id'] = $season_id;
if ($tournament_id) $filter_args['tournament_id'] = $tournament_id;

// Get leaderboards
$goals_leaders = $stats_repo->get_leaderboard('goal', array_merge($filter_args, array('limit' => 10)));
$assists_leaders = $stats_repo->get_leaderboard('assist', array_merge($filter_args, array('limit' => 10)));
$points_leaders = $stats_repo->get_points_leaders(array_merge($filter_args, array('limit' => 10)));

$seasons = $season_repo->get_all();
$tournaments = $tournament_repo->get_all();
?>
<div class="wrap">
    <h1><?php _e('Stats Leaderboards', 'the-rink-society'); ?></h1>

    <!-- Filters -->
    <div class="tablenav top">
        <form method="get">
            <input type="hidden" name="page" value="trs-dashboard">
            <input type="hidden" name="tab" value="stats">
            <select name="season_id">
                <option value=""><?php _e('All Seasons', 'the-rink-society'); ?></option>
                <?php foreach ($seasons as $season) : ?>
                    <option value="<?php echo $season->id; ?>" <?php selected($season_id, $season->id); ?>><?php echo esc_html($season->name); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="tournament_id">
                <option value=""><?php _e('All Tournaments', 'the-rink-society'); ?></option>
                <?php foreach ($tournaments as $tournament) : ?>
                    <option value="<?php echo $tournament->id; ?>" <?php selected($tournament_id, $tournament->id); ?>><?php echo esc_html($tournament->name); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" class="button" value="<?php _e('Filter', 'the-rink-society'); ?>">
        </form>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; margin-top: 20px;">
        <!-- Points Leaders -->
        <div class="card">
            <h2><?php _e('Points Leaders', 'the-rink-society'); ?></h2>
            <?php if (empty($points_leaders)) : ?>
                <p><?php _e('No stats recorded yet.', 'the-rink-society'); ?></p>
            <?php else : ?>
                <table class="widefat striped">
                    <thead><tr>
                        <th>#</th>
                        <th><?php _e('Player', 'the-rink-society'); ?></th>
                        <th><?php _e('G', 'the-rink-society'); ?></th>
                        <th><?php _e('A', 'the-rink-society'); ?></th>
                        <th><?php _e('PTS', 'the-rink-society'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php $rank = 1; foreach ($points_leaders as $leader) : ?>
                            <tr>
                                <td><?php echo $rank++; ?></td>
                                <td><?php echo esc_html($leader['player_name']); ?></td>
                                <td><?php echo $leader['goals']; ?></td>
                                <td><?php echo $leader['assists']; ?></td>
                                <td><strong><?php echo $leader['points']; ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Goals Leaders -->
        <div class="card">
            <h2><?php _e('Goals Leaders', 'the-rink-society'); ?></h2>
            <?php if (empty($goals_leaders)) : ?>
                <p><?php _e('No goals recorded yet.', 'the-rink-society'); ?></p>
            <?php else : ?>
                <table class="widefat striped">
                    <thead><tr>
                        <th>#</th>
                        <th><?php _e('Player', 'the-rink-society'); ?></th>
                        <th><?php _e('Goals', 'the-rink-society'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php $rank = 1; foreach ($goals_leaders as $leader) : ?>
                            <tr>
                                <td><?php echo $rank++; ?></td>
                                <td><?php echo esc_html($leader['player_name']); ?></td>
                                <td><strong><?php echo $leader['total']; ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Assists Leaders -->
        <div class="card">
            <h2><?php _e('Assists Leaders', 'the-rink-society'); ?></h2>
            <?php if (empty($assists_leaders)) : ?>
                <p><?php _e('No assists recorded yet.', 'the-rink-society'); ?></p>
            <?php else : ?>
                <table class="widefat striped">
                    <thead><tr>
                        <th>#</th>
                        <th><?php _e('Player', 'the-rink-society'); ?></th>
                        <th><?php _e('Assists', 'the-rink-society'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php $rank = 1; foreach ($assists_leaders as $leader) : ?>
                            <tr>
                                <td><?php echo $rank++; ?></td>
                                <td><?php echo esc_html($leader['player_name']); ?></td>
                                <td><strong><?php echo $leader['total']; ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
