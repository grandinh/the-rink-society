<?php
/**
 * Team Roster Template
 * 
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) exit;
?>

<div class="trs-team-roster">
    <h2><?php echo esc_html($team->name); ?></h2>
    
    <?php if (empty($players)) : ?>
        <p>No players on this team yet.</p>
    <?php else : ?>
        <table class="trs-roster-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Player</th>
                    <th>Position</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players as $player) : 
                    $user = $player->get_user();
                    $jersey = $player->get_jersey_number($team->id);
                ?>
                    <tr>
                        <td><?php echo $jersey ?: '-'; ?></td>
                        <td><?php echo esc_html($user->display_name); ?></td>
                        <td><?php echo $player->position ? ucfirst($player->position) : '-'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
