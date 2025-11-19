<?php if (!defined('ABSPATH')) exit;
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
?>
<div class="wrap">
    <h1><?php _e('Hockey Manager Settings', 'the-rink-society'); ?></h1>

    <?php if ($message === 'saved') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Settings saved.', 'the-rink-society'); ?></p></div>
    <?php endif; ?>

    <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url('admin.php?page=trs-settings&tab=general'); ?>" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('General', 'the-rink-society'); ?></a>
        <a href="<?php echo admin_url('admin.php?page=trs-settings&tab=display'); ?>" class="nav-tab <?php echo $active_tab === 'display' ? 'nav-tab-active' : ''; ?>"><?php _e('Display', 'the-rink-society'); ?></a>
        <a href="<?php echo admin_url('admin.php?page=trs-settings&tab=stats'); ?>" class="nav-tab <?php echo $active_tab === 'stats' ? 'nav-tab-active' : ''; ?>"><?php _e('Stats', 'the-rink-society'); ?></a>
        <a href="<?php echo admin_url('admin.php?page=trs-settings&tab=permissions'); ?>" class="nav-tab <?php echo $active_tab === 'permissions' ? 'nav-tab-active' : ''; ?>"><?php _e('Permissions', 'the-rink-society'); ?></a>
    </h2>

    <form method="post">
        <?php wp_nonce_field('trs_settings_action'); ?>

        <?php if ($active_tab === 'general') : ?>
            <table class="form-table">
                <tr><th><label for="trs_facility_name"><?php _e('Facility Name', 'the-rink-society'); ?></label></th>
                    <td><input type="text" name="trs_facility_name" id="trs_facility_name" value="<?php echo esc_attr(get_option('trs_facility_name', 'The Rink Society')); ?>" class="regular-text"></td></tr>
                <tr><th><label for="trs_facility_location"><?php _e('Facility Location', 'the-rink-society'); ?></label></th>
                    <td><input type="text" name="trs_facility_location" id="trs_facility_location" value="<?php echo esc_attr(get_option('trs_facility_location', 'Chiang Mai, Thailand')); ?>" class="regular-text"></td></tr>
                <tr><th><label for="trs_default_location"><?php _e('Default Game Location', 'the-rink-society'); ?></label></th>
                    <td><input type="text" name="trs_default_location" id="trs_default_location" value="<?php echo esc_attr(get_option('trs_default_location', 'Main Rink')); ?>" class="regular-text"></td></tr>
                <tr><th><label for="trs_timezone"><?php _e('Timezone', 'the-rink-society'); ?></label></th>
                    <td><select name="trs_timezone" id="trs_timezone">
                        <?php $selected_tz = get_option('trs_timezone', 'Asia/Bangkok'); ?>
                        <option value="Asia/Bangkok" <?php selected($selected_tz, 'Asia/Bangkok'); ?>><?php _e('Bangkok (UTC+7)', 'the-rink-society'); ?></option>
                        <option value="UTC" <?php selected($selected_tz, 'UTC'); ?>><?php _e('UTC', 'the-rink-society'); ?></option>
                        <option value="America/New_York" <?php selected($selected_tz, 'America/New_York'); ?>><?php _e('New York (UTC-5)', 'the-rink-society'); ?></option>
                        <option value="America/Los_Angeles" <?php selected($selected_tz, 'America/Los_Angeles'); ?>><?php _e('Los Angeles (UTC-8)', 'the-rink-society'); ?></option>
                    </select></td></tr>
            </table>
            <p class="submit"><input type="submit" name="trs_save_general" class="button button-primary" value="<?php _e('Save General Settings', 'the-rink-society'); ?>"></p>

        <?php elseif ($active_tab === 'display') : ?>
            <table class="form-table">
                <tr><th><label for="trs_date_format"><?php _e('Date Format', 'the-rink-society'); ?></label></th>
                    <td><select name="trs_date_format" id="trs_date_format">
                        <?php $date_format = get_option('trs_date_format', 'M j, Y'); ?>
                        <option value="M j, Y" <?php selected($date_format, 'M j, Y'); ?>><?php echo date('M j, Y'); ?></option>
                        <option value="F j, Y" <?php selected($date_format, 'F j, Y'); ?>><?php echo date('F j, Y'); ?></option>
                        <option value="Y-m-d" <?php selected($date_format, 'Y-m-d'); ?>><?php echo date('Y-m-d'); ?></option>
                        <option value="d/m/Y" <?php selected($date_format, 'd/m/Y'); ?>><?php echo date('d/m/Y'); ?></option>
                    </select></td></tr>
                <tr><th><label for="trs_time_format"><?php _e('Time Format', 'the-rink-society'); ?></label></th>
                    <td><select name="trs_time_format" id="trs_time_format">
                        <?php $time_format = get_option('trs_time_format', 'g:i A'); ?>
                        <option value="g:i A" <?php selected($time_format, 'g:i A'); ?>><?php echo date('g:i A'); ?> (12-hour)</option>
                        <option value="H:i" <?php selected($time_format, 'H:i'); ?>><?php echo date('H:i'); ?> (24-hour)</option>
                    </select></td></tr>
                <tr><th><label for="trs_items_per_page"><?php _e('Items Per Page', 'the-rink-society'); ?></label></th>
                    <td><input type="number" name="trs_items_per_page" id="trs_items_per_page" value="<?php echo esc_attr(get_option('trs_items_per_page', 20)); ?>" min="5" max="100" style="width: 100px;"></td></tr>
                <tr><th><label for="trs_show_player_photos"><?php _e('Show Player Photos', 'the-rink-society'); ?></label></th>
                    <td><input type="checkbox" name="trs_show_player_photos" id="trs_show_player_photos" value="1" <?php checked(get_option('trs_show_player_photos', 0), 1); ?>></td></tr>
            </table>
            <p class="submit"><input type="submit" name="trs_save_display" class="button button-primary" value="<?php _e('Save Display Settings', 'the-rink-society'); ?>"></p>

        <?php elseif ($active_tab === 'stats') : ?>
            <table class="form-table">
                <tr><th><label for="trs_stats_enabled"><?php _e('Enable Stats Tracking', 'the-rink-society'); ?></label></th>
                    <td><input type="checkbox" name="trs_stats_enabled" id="trs_stats_enabled" value="1" <?php checked(get_option('trs_stats_enabled', 1), 1); ?>></td></tr>
                <tr><th><label for="trs_track_plus_minus"><?php _e('Track Plus/Minus', 'the-rink-society'); ?></label></th>
                    <td><input type="checkbox" name="trs_track_plus_minus" id="trs_track_plus_minus" value="1" <?php checked(get_option('trs_track_plus_minus', 0), 1); ?>></td></tr>
                <tr><th><label for="trs_track_pim"><?php _e('Track Penalty Minutes', 'the-rink-society'); ?></label></th>
                    <td><input type="checkbox" name="trs_track_pim" id="trs_track_pim" value="1" <?php checked(get_option('trs_track_pim', 1), 1); ?>></td></tr>
                <tr><th><label for="trs_track_shots"><?php _e('Track Shots on Goal', 'the-rink-society'); ?></label></th>
                    <td><input type="checkbox" name="trs_track_shots" id="trs_track_shots" value="1" <?php checked(get_option('trs_track_shots', 0), 1); ?>></td></tr>
            </table>
            <p class="submit"><input type="submit" name="trs_save_stats" class="button button-primary" value="<?php _e('Save Stats Settings', 'the-rink-society'); ?>"></p>

        <?php elseif ($active_tab === 'permissions') : ?>
            <table class="form-table">
                <tr><th><label for="trs_who_can_edit_rosters"><?php _e('Who Can Edit Rosters', 'the-rink-society'); ?></label></th>
                    <td><select name="trs_who_can_edit_rosters" id="trs_who_can_edit_rosters">
                        <?php $who_rosters = get_option('trs_who_can_edit_rosters', 'manage_options'); ?>
                        <option value="manage_options" <?php selected($who_rosters, 'manage_options'); ?>><?php _e('Administrators Only', 'the-rink-society'); ?></option>
                        <option value="edit_others_posts" <?php selected($who_rosters, 'edit_others_posts'); ?>><?php _e('Editors and Above', 'the-rink-society'); ?></option>
                        <option value="edit_posts" <?php selected($who_rosters, 'edit_posts'); ?>><?php _e('Authors and Above', 'the-rink-society'); ?></option>
                    </select></td></tr>
                <tr><th><label for="trs_who_can_enter_stats"><?php _e('Who Can Enter Stats', 'the-rink-society'); ?></label></th>
                    <td><select name="trs_who_can_enter_stats" id="trs_who_can_enter_stats">
                        <?php $who_stats = get_option('trs_who_can_enter_stats', 'edit_posts'); ?>
                        <option value="manage_options" <?php selected($who_stats, 'manage_options'); ?>><?php _e('Administrators Only', 'the-rink-society'); ?></option>
                        <option value="edit_others_posts" <?php selected($who_stats, 'edit_others_posts'); ?>><?php _e('Editors and Above', 'the-rink-society'); ?></option>
                        <option value="edit_posts" <?php selected($who_stats, 'edit_posts'); ?>><?php _e('Authors and Above', 'the-rink-society'); ?></option>
                    </select></td></tr>
                <tr><th><label for="trs_who_can_manage_events"><?php _e('Who Can Manage Events', 'the-rink-society'); ?></label></th>
                    <td><select name="trs_who_can_manage_events" id="trs_who_can_manage_events">
                        <?php $who_events = get_option('trs_who_can_manage_events', 'edit_posts'); ?>
                        <option value="manage_options" <?php selected($who_events, 'manage_options'); ?>><?php _e('Administrators Only', 'the-rink-society'); ?></option>
                        <option value="edit_others_posts" <?php selected($who_events, 'edit_others_posts'); ?>><?php _e('Editors and Above', 'the-rink-society'); ?></option>
                        <option value="edit_posts" <?php selected($who_events, 'edit_posts'); ?>><?php _e('Authors and Above', 'the-rink-society'); ?></option>
                    </select></td></tr>
            </table>
            <p class="submit"><input type="submit" name="trs_save_permissions" class="button button-primary" value="<?php _e('Save Permission Settings', 'the-rink-society'); ?>"></p>
        <?php endif; ?>
    </form>
</div>
