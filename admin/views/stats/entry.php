<?php if (!defined('ABSPATH')) exit;
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
?>
<div class="wrap">
    <h1><?php _e('Enter Player Stats', 'the-rink-society'); ?></h1>
    
    <?php if ($message === 'stats_saved') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Stats saved successfully!', 'the-rink-society'); ?></p></div>
    <?php endif; ?>

    <div class="card" style="max-width: 100%; margin-bottom: 20px;">
        <h2><?php echo esc_html($home_team->name) . ' vs ' . esc_html($away_team->name); ?></h2>
        <p>
            <strong><?php _e('Date:', 'the-rink-society'); ?></strong> <?php echo trs_format_date($game->game_date); ?> &nbsp;|&nbsp;
            <strong><?php _e('Score:', 'the-rink-society'); ?></strong> 
            <?php echo ($game->home_score !== null ? $game->home_score : '-') . ' - ' . ($game->away_score !== null ? $game->away_score : '-'); ?>
        </p>
    </div>

    <p>
        <a href="<?php echo admin_url('admin.php?page=trs-stats&game_id=' . $game->id . '&action=roster'); ?>" class="button"><?php _e('Edit Roster', 'the-rink-society'); ?></a>
    </p>

    <form method="post">
        <?php wp_nonce_field('trs_stats_action'); ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Home Team Stats -->
            <div class="card">
                <h2><?php echo esc_html($home_team->name); ?></h2>
                <table class="widefat striped">
                    <thead><tr>
                        <th><?php _e('Player', 'the-rink-society'); ?></th>
                        <th><?php _e('#', 'the-rink-society'); ?></th>
                        <th style="width: 60px;"><?php _e('G', 'the-rink-society'); ?></th>
                        <th style="width: 60px;"><?php _e('A', 'the-rink-society'); ?></th>
                        <th style="width: 60px;"><?php _e('PIM', 'the-rink-society'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($home_roster as $roster_entry) : 
                            $player = $this->player_repo->get($roster_entry->player_id);
                            $user = $player->get_user();
                            $key = $home_team->id . '_' . $player->id;
                            
                            // Get existing stats
                            $goals = 0;
                            $assists = 0;
                            $pim = 0;
                            if (isset($existing_stats[$key])) {
                                foreach ($existing_stats[$key] as $stat) {
                                    if ($stat->stat_type === 'goal') $goals++;
                                    if ($stat->stat_type === 'assist') $assists++;
                                    if ($stat->stat_type === 'penalty_minutes') $pim += $stat->value;
                                }
                            }
                        ?>
                            <tr>
                                <td><?php echo esc_html($user->display_name); ?></td>
                                <td><?php echo esc_html($roster_entry->jersey_number ?: '-'); ?></td>
                                <td><input type="number" name="stats[<?php echo $key; ?>][goals]" value="<?php echo $goals; ?>" min="0" style="width: 50px;"></td>
                                <td><input type="number" name="stats[<?php echo $key; ?>][assists]" value="<?php echo $assists; ?>" min="0" style="width: 50px;"></td>
                                <td><input type="number" name="stats[<?php echo $key; ?>][pim]" value="<?php echo $pim; ?>" min="0" style="width: 50px;"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Away Team Stats -->
            <div class="card">
                <h2><?php echo esc_html($away_team->name); ?></h2>
                <table class="widefat striped">
                    <thead><tr>
                        <th><?php _e('Player', 'the-rink-society'); ?></th>
                        <th><?php _e('#', 'the-rink-society'); ?></th>
                        <th style="width: 60px;"><?php _e('G', 'the-rink-society'); ?></th>
                        <th style="width: 60px;"><?php _e('A', 'the-rink-society'); ?></th>
                        <th style="width: 60px;"><?php _e('PIM', 'the-rink-society'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($away_roster as $roster_entry) : 
                            $player = $this->player_repo->get($roster_entry->player_id);
                            $user = $player->get_user();
                            $key = $away_team->id . '_' . $player->id;
                            
                            // Get existing stats
                            $goals = 0;
                            $assists = 0;
                            $pim = 0;
                            if (isset($existing_stats[$key])) {
                                foreach ($existing_stats[$key] as $stat) {
                                    if ($stat->stat_type === 'goal') $goals++;
                                    if ($stat->stat_type === 'assist') $assists++;
                                    if ($stat->stat_type === 'penalty_minutes') $pim += $stat->value;
                                }
                            }
                        ?>
                            <tr>
                                <td><?php echo esc_html($user->display_name); ?></td>
                                <td><?php echo esc_html($roster_entry->jersey_number ?: '-'); ?></td>
                                <td><input type="number" name="stats[<?php echo $key; ?>][goals]" value="<?php echo $goals; ?>" min="0" style="width: 50px;"></td>
                                <td><input type="number" name="stats[<?php echo $key; ?>][assists]" value="<?php echo $assists; ?>" min="0" style="width: 50px;"></td>
                                <td><input type="number" name="stats[<?php echo $key; ?>][pim]" value="<?php echo $pim; ?>" min="0" style="width: 50px;"></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="submit">
            <input type="submit" name="trs_save_stats" class="button button-primary button-large" value="<?php _e('Save Stats', 'the-rink-society'); ?>">
            <a href="<?php echo admin_url('admin.php?page=trs-games'); ?>" class="button"><?php _e('Back to Games', 'the-rink-society'); ?></a>
        </p>
    </form>
</div>
