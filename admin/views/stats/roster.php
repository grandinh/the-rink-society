<?php if (!defined('ABSPATH')) exit;
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
?>
<div class="wrap">
    <h1><?php _e('Game Roster', 'the-rink-society'); ?></h1>
    
    <?php if ($message === 'roster_saved') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Roster saved.', 'the-rink-society'); ?></p></div>
    <?php endif; ?>

    <div class="card" style="max-width: 100%; margin-bottom: 20px;">
        <h2><?php echo esc_html($home_team->name) . ' vs ' . esc_html($away_team->name); ?></h2>
        <p><strong><?php _e('Date:', 'the-rink-society'); ?></strong> <?php echo trs_format_date($game->game_date); ?></p>
        <p><strong><?php _e('Location:', 'the-rink-society'); ?></strong> <?php echo esc_html($game->location); ?></p>
    </div>

    <p><?php _e('Select which players participated in this game and optionally override their jersey numbers.', 'the-rink-society'); ?></p>

    <form method="post">
        <?php wp_nonce_field('trs_stats_action'); ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Home Team -->
            <div class="card">
                <h2><?php echo esc_html($home_team->name); ?></h2>
                <?php if (empty($home_team_players)) : ?>
                    <p><?php _e('No players on this team.', 'the-rink-society'); ?></p>
                <?php else : ?>
                    <table class="widefat striped">
                        <thead><tr>
                            <th style="width: 40px;"><input type="checkbox" id="select_all_home"></th>
                            <th><?php _e('Player', 'the-rink-society'); ?></th>
                            <th style="width: 80px;"><?php _e('Jersey #', 'the-rink-society'); ?></th>
                        </tr></thead>
                        <tbody>
                            <?php 
                            $home_roster_ids = array_map(function($r) { return $r->player_id; }, $home_roster);
                            foreach ($home_team_players as $player) : 
                                $user = $player->get_user();
                                $in_roster = in_array($player->id, $home_roster_ids);
                                $roster_data = $in_roster ? array_values(array_filter($home_roster, function($r) use ($player) { return $r->player_id == $player->id; }))[0] : null;
                                $default_jersey = $player->get_jersey_number($home_team->id);
                            ?>
                                <tr>
                                    <td><input type="checkbox" name="home_players[]" value="<?php echo $player->id; ?>" <?php checked($in_roster); ?> class="home-player-check"></td>
                                    <td><?php echo esc_html($user->display_name); ?></td>
                                    <td><input type="text" name="home_jersey_<?php echo $player->id; ?>" value="<?php echo $roster_data && $roster_data->jersey_number ? esc_attr($roster_data->jersey_number) : esc_attr($default_jersey); ?>" style="width: 60px;" placeholder="<?php echo esc_attr($default_jersey); ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Away Team -->
            <div class="card">
                <h2><?php echo esc_html($away_team->name); ?></h2>
                <?php if (empty($away_team_players)) : ?>
                    <p><?php _e('No players on this team.', 'the-rink-society'); ?></p>
                <?php else : ?>
                    <table class="widefat striped">
                        <thead><tr>
                            <th style="width: 40px;"><input type="checkbox" id="select_all_away"></th>
                            <th><?php _e('Player', 'the-rink-society'); ?></th>
                            <th style="width: 80px;"><?php _e('Jersey #', 'the-rink-society'); ?></th>
                        </tr></thead>
                        <tbody>
                            <?php 
                            $away_roster_ids = array_map(function($r) { return $r->player_id; }, $away_roster);
                            foreach ($away_team_players as $player) : 
                                $user = $player->get_user();
                                $in_roster = in_array($player->id, $away_roster_ids);
                                $roster_data = $in_roster ? array_values(array_filter($away_roster, function($r) use ($player) { return $r->player_id == $player->id; }))[0] : null;
                                $default_jersey = $player->get_jersey_number($away_team->id);
                            ?>
                                <tr>
                                    <td><input type="checkbox" name="away_players[]" value="<?php echo $player->id; ?>" <?php checked($in_roster); ?> class="away-player-check"></td>
                                    <td><?php echo esc_html($user->display_name); ?></td>
                                    <td><input type="text" name="away_jersey_<?php echo $player->id; ?>" value="<?php echo $roster_data && $roster_data->jersey_number ? esc_attr($roster_data->jersey_number) : esc_attr($default_jersey); ?>" style="width: 60px;" placeholder="<?php echo esc_attr($default_jersey); ?>"></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <p class="submit">
            <input type="submit" name="trs_save_roster" class="button button-primary button-large" value="<?php _e('Save Roster & Continue to Stats', 'the-rink-society'); ?>">
            <a href="<?php echo admin_url('admin.php?page=trs-games&action=edit&id=' . $game->id); ?>" class="button"><?php _e('Back to Game', 'the-rink-society'); ?></a>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('#select_all_home').on('change', function() {
        $('.home-player-check').prop('checked', this.checked);
    });
    $('#select_all_away').on('change', function() {
        $('.away-player-check').prop('checked', this.checked);
    });
});
</script>
