<?php if (!defined('ABSPATH')) exit;
$page_title = $edit_mode ? __('Edit Game', 'the-rink-society') : __('Add New Game', 'the-rink-society');
?>
<div class="wrap">
    <h1><?php echo $page_title; ?></h1>
    <form method="post" action="">
        <?php wp_nonce_field('trs_game_action'); ?>
        <input type="hidden" name="game_id" value="<?php echo $game ? $game->id : 0; ?>">
        <table class="form-table">
            <tr><th><label for="season_id"><?php _e('Season', 'the-rink-society'); ?></label></th>
                <td><select name="season_id" id="season_id">
                    <option value=""><?php _e('- No Season -', 'the-rink-society'); ?></option>
                    <?php foreach ($seasons as $season) : ?>
                        <option value="<?php echo $season->id; ?>" <?php selected($game ? $game->season_id : '', $season->id); ?>><?php echo esc_html($season->name); ?></option>
                    <?php endforeach; ?>
                </select></td></tr>
            <tr><th><label for="tournament_id"><?php _e('Tournament', 'the-rink-society'); ?></label></th>
                <td><select name="tournament_id" id="tournament_id">
                    <option value=""><?php _e('- No Tournament -', 'the-rink-society'); ?></option>
                    <?php foreach ($tournaments as $tournament) : ?>
                        <option value="<?php echo $tournament->id; ?>" <?php selected($game ? $game->tournament_id : '', $tournament->id); ?>><?php echo esc_html($tournament->name); ?></option>
                    <?php endforeach; ?>
                </select></td></tr>
            <tr><th><label for="home_team_id"><?php _e('Home Team', 'the-rink-society'); ?> *</label></th>
                <td><select name="home_team_id" id="home_team_id" required>
                    <option value=""><?php _e('- Select Team -', 'the-rink-society'); ?></option>
                    <?php foreach ($teams as $team) : ?>
                        <option value="<?php echo $team->id; ?>" <?php selected($game ? $game->home_team_id : '', $team->id); ?>><?php echo esc_html($team->name); ?></option>
                    <?php endforeach; ?>
                </select></td></tr>
            <tr><th><label for="away_team_id"><?php _e('Away Team', 'the-rink-society'); ?> *</label></th>
                <td><select name="away_team_id" id="away_team_id" required>
                    <option value=""><?php _e('- Select Team -', 'the-rink-society'); ?></option>
                    <?php foreach ($teams as $team) : ?>
                        <option value="<?php echo $team->id; ?>" <?php selected($game ? $game->away_team_id : '', $team->id); ?>><?php echo esc_html($team->name); ?></option>
                    <?php endforeach; ?>
                </select></td></tr>
            <tr><th><label for="game_date"><?php _e('Game Date', 'the-rink-society'); ?> *</label></th>
                <td><input type="date" name="game_date" id="game_date" value="<?php echo $game ? esc_attr($game->game_date) : ''; ?>" required></td></tr>
            <tr><th><label for="game_time"><?php _e('Game Time', 'the-rink-society'); ?></label></th>
                <td><input type="time" name="game_time" id="game_time" value="<?php echo $game ? esc_attr($game->game_time) : ''; ?>"></td></tr>
            <tr><th><label for="location"><?php _e('Location', 'the-rink-society'); ?> *</label></th>
                <td><input type="text" name="location" id="location" value="<?php echo $game ? esc_attr($game->location) : ''; ?>" class="regular-text" required></td></tr>
            <tr><th><label for="status"><?php _e('Status', 'the-rink-society'); ?></label></th>
                <td><select name="status" id="status">
                    <option value="scheduled" <?php selected($game ? $game->status : 'scheduled', 'scheduled'); ?>><?php _e('Scheduled', 'the-rink-society'); ?></option>
                    <option value="in-progress" <?php selected($game ? $game->status : '', 'in-progress'); ?>><?php _e('In Progress', 'the-rink-society'); ?></option>
                    <option value="completed" <?php selected($game ? $game->status : '', 'completed'); ?>><?php _e('Completed', 'the-rink-society'); ?></option>
                    <option value="cancelled" <?php selected($game ? $game->status : '', 'cancelled'); ?>><?php _e('Cancelled', 'the-rink-society'); ?></option>
                </select></td></tr>
            <tr><th><label for="home_score"><?php _e('Home Score', 'the-rink-society'); ?></label></th>
                <td><input type="number" name="home_score" id="home_score" value="<?php echo $game && $game->home_score !== null ? esc_attr($game->home_score) : ''; ?>" min="0" style="width: 100px;"></td></tr>
            <tr><th><label for="away_score"><?php _e('Away Score', 'the-rink-society'); ?></label></th>
                <td><input type="number" name="away_score" id="away_score" value="<?php echo $game && $game->away_score !== null ? esc_attr($game->away_score) : ''; ?>" min="0" style="width: 100px;"></td></tr>
        </table>
        <p class="submit">
            <input type="submit" name="trs_save_game" class="button button-primary" value="<?php echo $edit_mode ? __('Update', 'the-rink-society') : __('Create', 'the-rink-society'); ?>">
            <a href="<?php echo admin_url('admin.php?page=trs-games'); ?>" class="button"><?php _e('Cancel', 'the-rink-society'); ?></a>
        </p>
    </form>

    <?php if ($edit_mode) : ?>
        <hr>
        <h2><?php _e('Quick Actions', 'the-rink-society'); ?></h2>
        <p>
            <a href="<?php echo admin_url('admin.php?page=trs-games&action=score&id=' . $game->id); ?>" class="button"><?php _e('Update Score', 'the-rink-society'); ?></a>
            <a href="<?php echo admin_url('admin.php?page=trs-stats&game_id=' . $game->id); ?>" class="button button-primary"><?php _e('Enter Player Stats', 'the-rink-society'); ?></a>
        </p>
    <?php endif; ?>
</div>
