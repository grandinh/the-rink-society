<?php
/**
 * Player Stats Template
 * 
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) exit;

$user = $player->get_user();
?>

<div class="trs-player-stats">
    <h2><?php echo esc_html($user->display_name); ?> - Stats</h2>
    
    <?php if (empty($stats)) : ?>
        <p>No stats recorded yet.</p>
    <?php else : ?>
        <table class="trs-stats-table">
            <thead>
                <tr>
                    <th>GP</th>
                    <th>G</th>
                    <th>A</th>
                    <th>PTS</th>
                    <th>PIM</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo isset($stats['games_played']) ? $stats['games_played'] : 0; ?></td>
                    <td><?php echo isset($stats['goals']) ? $stats['goals'] : 0; ?></td>
                    <td><?php echo isset($stats['assists']) ? $stats['assists'] : 0; ?></td>
                    <td><strong><?php echo (isset($stats['goals']) ? $stats['goals'] : 0) + (isset($stats['assists']) ? $stats['assists'] : 0); ?></strong></td>
                    <td><?php echo isset($stats['penalty_minutes']) ? $stats['penalty_minutes'] : 0; ?></td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
</div>
