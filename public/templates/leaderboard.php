<?php
/**
 * Leaderboard Template
 * 
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) exit;
?>

<div class="trs-leaderboard">
    <h2><?php echo ucfirst($type); ?> Leaders</h2>
    
    <?php if (empty($leaders)) : ?>
        <p>No stats recorded yet.</p>
    <?php else : ?>
        <table class="trs-leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Player</th>
                    <?php if ($type === 'points') : ?>
                        <th>G</th>
                        <th>A</th>
                        <th>PTS</th>
                    <?php else : ?>
                        <th><?php echo ucfirst($type); ?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($leaders as $leader) : ?>
                    <tr>
                        <td><?php echo $rank++; ?></td>
                        <td><?php echo esc_html($leader['player_name']); ?></td>
                        <?php if ($type === 'points') : ?>
                            <td><?php echo $leader['goals']; ?></td>
                            <td><?php echo $leader['assists']; ?></td>
                            <td><strong><?php echo $leader['points']; ?></strong></td>
                        <?php else : ?>
                            <td><strong><?php echo $leader['total']; ?></strong></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
