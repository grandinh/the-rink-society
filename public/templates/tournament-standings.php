<?php
/**
 * Tournament Standings Template
 * 
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) exit;
?>

<div class="trs-tournament-standings">
    <h2><?php echo esc_html($tournament->name); ?> - Standings</h2>
    
    <?php if (empty($standings)) : ?>
        <p>No standings data available yet.</p>
    <?php else : ?>
        <table class="trs-standings-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Team</th>
                    <th>W</th>
                    <th>L</th>
                    <th>T</th>
                    <th>PTS</th>
                    <th>GF</th>
                    <th>GA</th>
                    <th>DIFF</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($standings as $standing) : ?>
                    <tr>
                        <td><?php echo $rank++; ?></td>
                        <td><strong><?php echo esc_html($standing['team_name']); ?></strong></td>
                        <td><?php echo $standing['wins']; ?></td>
                        <td><?php echo $standing['losses']; ?></td>
                        <td><?php echo $standing['ties']; ?></td>
                        <td><strong><?php echo $standing['points']; ?></strong></td>
                        <td><?php echo $standing['goals_for']; ?></td>
                        <td><?php echo $standing['goals_against']; ?></td>
                        <td>
                            <?php 
                            $diff = $standing['goals_for'] - $standing['goals_against'];
                            echo $diff > 0 ? '+' . $diff : $diff;
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
