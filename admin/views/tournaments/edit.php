<?php if (!defined('ABSPATH')) exit;
$page_title = $edit_mode ? __('Edit Tournament', 'the-rink-society') : __('Add New Tournament', 'the-rink-society');
?>
<div class="wrap">
    <h1><?php echo $page_title; ?></h1>
    <form method="post" action="">
        <?php wp_nonce_field('trs_tournament_action'); ?>
        <input type="hidden" name="tournament_id" value="<?php echo $tournament ? $tournament->id : 0; ?>">
        <table class="form-table">
            <tr><th><label for="name"><?php _e('Tournament Name', 'the-rink-society'); ?> *</label></th>
                <td><input type="text" name="name" id="name" value="<?php echo $tournament ? esc_attr($tournament->name) : ''; ?>" class="regular-text" required></td></tr>
            <tr><th><label for="season_id"><?php _e('Season', 'the-rink-society'); ?></label></th>
                <td><select name="season_id" id="season_id">
                    <option value=""><?php _e('- No Season -', 'the-rink-society'); ?></option>
                    <?php foreach ($seasons as $season) : ?>
                        <option value="<?php echo $season->id; ?>" <?php selected($tournament ? $tournament->season_id : '', $season->id); ?>><?php echo esc_html($season->name); ?></option>
                    <?php endforeach; ?>
                </select></td></tr>
            <tr><th><label for="format"><?php _e('Format', 'the-rink-society'); ?></label></th>
                <td><select name="format" id="format">
                    <option value="round-robin" <?php selected($tournament ? $tournament->format : 'round-robin', 'round-robin'); ?>><?php _e('Round Robin', 'the-rink-society'); ?></option>
                    <option value="single-elimination" <?php selected($tournament ? $tournament->format : '', 'single-elimination'); ?>><?php _e('Single Elimination', 'the-rink-society'); ?></option>
                    <option value="double-elimination" <?php selected($tournament ? $tournament->format : '', 'double-elimination'); ?>><?php _e('Double Elimination', 'the-rink-society'); ?></option>
                    <option value="swiss" <?php selected($tournament ? $tournament->format : '', 'swiss'); ?>><?php _e('Swiss', 'the-rink-society'); ?></option>
                </select></td></tr>
            <tr><th><label for="start_date"><?php _e('Start Date', 'the-rink-society'); ?></label></th>
                <td><input type="date" name="start_date" id="start_date" value="<?php echo $tournament ? esc_attr($tournament->start_date) : ''; ?>"></td></tr>
            <tr><th><label for="end_date"><?php _e('End Date', 'the-rink-society'); ?></label></th>
                <td><input type="date" name="end_date" id="end_date" value="<?php echo $tournament ? esc_attr($tournament->end_date) : ''; ?>"></td></tr>
            <tr><th><label for="status"><?php _e('Status', 'the-rink-society'); ?></label></th>
                <td><select name="status" id="status">
                    <option value="upcoming" <?php selected($tournament ? $tournament->status : 'upcoming', 'upcoming'); ?>><?php _e('Upcoming', 'the-rink-society'); ?></option>
                    <option value="active" <?php selected($tournament ? $tournament->status : '', 'active'); ?>><?php _e('Active', 'the-rink-society'); ?></option>
                    <option value="completed" <?php selected($tournament ? $tournament->status : '', 'completed'); ?>><?php _e('Completed', 'the-rink-society'); ?></option>
                </select></td></tr>
            <tr><th><label for="description"><?php _e('Description', 'the-rink-society'); ?></label></th>
                <td><textarea name="description" id="description" rows="5" class="large-text"><?php echo $tournament ? esc_textarea($tournament->description) : ''; ?></textarea></td></tr>
        </table>
        <p class="submit">
            <input type="submit" name="trs_save_tournament" class="button button-primary" value="<?php echo $edit_mode ? __('Update', 'the-rink-society') : __('Create', 'the-rink-society'); ?>">
            <a href="<?php echo admin_url('admin.php?page=trs-tournaments'); ?>" class="button"><?php _e('Cancel', 'the-rink-society'); ?></a>
        </p>
    </form>

    <?php if ($edit_mode) : ?>
        <hr>
        <h2><?php _e('Quick Links', 'the-rink-society'); ?></h2>
        <p>
            <a href="<?php echo admin_url('admin.php?page=trs-tournaments&action=teams&id=' . $tournament->id); ?>" class="button"><?php _e('Manage Teams', 'the-rink-society'); ?></a>
            <a href="<?php echo admin_url('admin.php?page=trs-tournaments&action=standings&id=' . $tournament->id); ?>" class="button"><?php _e('View Standings', 'the-rink-society'); ?></a>
        </p>
    <?php endif; ?>
</div>
