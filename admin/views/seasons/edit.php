<?php if (!defined('ABSPATH')) exit;
$page_title = $edit_mode ? __('Edit Season', 'the-rink-society') : __('Add New Season', 'the-rink-society');
?>
<div class="wrap">
    <h1><?php echo $page_title; ?></h1>
    <form method="post" action="">
        <?php wp_nonce_field('trs_season_action'); ?>
        <input type="hidden" name="season_id" value="<?php echo $season ? $season->id : 0; ?>">
        <table class="form-table">
            <tr><th><label for="name"><?php _e('Season Name', 'the-rink-society'); ?> *</label></th>
                <td><input type="text" name="name" id="name" value="<?php echo $season ? esc_attr($season->name) : ''; ?>" class="regular-text" required></td></tr>
            <tr><th><label for="start_date"><?php _e('Start Date', 'the-rink-society'); ?></label></th>
                <td><input type="date" name="start_date" id="start_date" value="<?php echo $season ? esc_attr($season->start_date) : ''; ?>"></td></tr>
            <tr><th><label for="end_date"><?php _e('End Date', 'the-rink-society'); ?></label></th>
                <td><input type="date" name="end_date" id="end_date" value="<?php echo $season ? esc_attr($season->end_date) : ''; ?>"></td></tr>
            <tr><th><label for="status"><?php _e('Status', 'the-rink-society'); ?></label></th>
                <td><select name="status" id="status">
                    <option value="upcoming" <?php selected($season ? $season->status : 'upcoming', 'upcoming'); ?>><?php _e('Upcoming', 'the-rink-society'); ?></option>
                    <option value="active" <?php selected($season ? $season->status : '', 'active'); ?>><?php _e('Active', 'the-rink-society'); ?></option>
                    <option value="completed" <?php selected($season ? $season->status : '', 'completed'); ?>><?php _e('Completed', 'the-rink-society'); ?></option>
                </select></td></tr>
        </table>
        <p class="submit">
            <input type="submit" name="trs_save_season" class="button button-primary" value="<?php echo $edit_mode ? __('Update', 'the-rink-society') : __('Create', 'the-rink-society'); ?>">
            <a href="<?php echo admin_url('admin.php?page=trs-seasons'); ?>" class="button"><?php _e('Cancel', 'the-rink-society'); ?></a>
        </p>
    </form>
</div>
