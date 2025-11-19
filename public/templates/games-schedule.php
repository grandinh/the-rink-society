<?php
/**
 * Games Schedule Template
 * 
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) exit;
?>

<div class="trs-games-schedule">
    <h2>Games Schedule</h2>
    
    <?php if (empty($games)) : ?>
        <p>No games scheduled.</p>
    <?php else : ?>
        <table class="trs-schedule-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Matchup</th>
                    <th>Score</th>
                    <th>Location</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($games as $game) : 
                    $home_team = $game->get_home_team();
                    $away_team = $game->get_away_team();
                ?>
                    <tr class="trs-game-<?php echo esc_attr($game->status); ?>">
                        <td><?php echo trs_format_date($game->game_date); ?></td>
                        <td><?php echo $game->game_time ? trs_format_time($game->game_time) : '-'; ?></td>
                        <td>
                            <strong><?php echo esc_html($home_team->name); ?></strong>
                            vs
                            <strong><?php echo esc_html($away_team->name); ?></strong>
                        </td>
                        <td>
                            <?php 
                            if ($game->home_score !== null && $game->away_score !== null) {
                                echo $game->home_score . ' - ' . $game->away_score;
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html($game->location); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
