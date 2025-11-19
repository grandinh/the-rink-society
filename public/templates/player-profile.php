<?php
/**
 * Player Profile Template
 * 
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) exit;

$user = $player->get_user();
$teams = $player->get_teams();
?>

<div class="trs-player-profile">
    <div class="trs-player-header">
        <h2><?php echo esc_html($user->display_name); ?></h2>
        <?php if ($player->preferred_jersey_number) : ?>
            <div class="trs-jersey-number">#<?php echo esc_html($player->preferred_jersey_number); ?></div>
        <?php endif; ?>
    </div>

    <div class="trs-player-info">
        <?php if ($player->position) : ?>
            <div class="trs-info-item">
                <strong>Position:</strong> <?php echo ucfirst($player->position); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($player->shoots) : ?>
            <div class="trs-info-item">
                <strong>Shoots:</strong> <?php echo ucfirst($player->shoots); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($teams)) : ?>
            <div class="trs-info-item">
                <strong>Teams:</strong>
                <?php 
                $team_names = array_map(function($t) { return esc_html($t->name); }, $teams);
                echo implode(', ', $team_names);
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>
