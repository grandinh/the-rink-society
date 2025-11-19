<?php if (!defined('ABSPATH')) exit;
$page_title = $edit_mode ? __('Edit Event', 'the-rink-society') : __('Add New Event', 'the-rink-society');
?>
<div class="wrap">
    <h1><?php echo $page_title; ?></h1>
    <form method="post" action="">
        <?php wp_nonce_field('trs_event_action'); ?>
        <input type="hidden" name="event_id" value="<?php echo $event ? $event->id : 0; ?>">
        <table class="form-table">
            <tr><th><label for="name"><?php _e('Event Name', 'the-rink-society'); ?> *</label></th>
                <td><input type="text" name="name" id="name" value="<?php echo $event ? esc_attr($event->name) : ''; ?>" class="regular-text" required></td></tr>
            <tr><th><label for="event_type"><?php _e('Event Type', 'the-rink-society'); ?></label></th>
                <td><select name="event_type" id="event_type">
                    <option value="practice" <?php selected($event ? $event->event_type : 'practice', 'practice'); ?>><?php _e('Practice', 'the-rink-society'); ?></option>
                    <option value="social" <?php selected($event ? $event->event_type : '', 'social'); ?>><?php _e('Social', 'the-rink-society'); ?></option>
                    <option value="fundraiser" <?php selected($event ? $event->event_type : '', 'fundraiser'); ?>><?php _e('Fundraiser', 'the-rink-society'); ?></option>
                    <option value="meeting" <?php selected($event ? $event->event_type : '', 'meeting'); ?>><?php _e('Meeting', 'the-rink-society'); ?></option>
                    <option value="other" <?php selected($event ? $event->event_type : '', 'other'); ?>><?php _e('Other', 'the-rink-society'); ?></option>
                </select></td></tr>
            <tr><th><label for="event_date"><?php _e('Event Date', 'the-rink-society'); ?> *</label></th>
                <td><input type="date" name="event_date" id="event_date" value="<?php echo $event ? esc_attr($event->event_date) : ''; ?>" required></td></tr>
            <tr><th><label for="event_time"><?php _e('Event Time', 'the-rink-society'); ?></label></th>
                <td><input type="time" name="event_time" id="event_time" value="<?php echo $event ? esc_attr($event->event_time) : ''; ?>"></td></tr>
            <tr><th><label for="location"><?php _e('Location', 'the-rink-society'); ?> *</label></th>
                <td><input type="text" name="location" id="location" value="<?php echo $event ? esc_attr($event->location) : ''; ?>" class="regular-text" required></td></tr>
            <tr><th><label for="max_attendees"><?php _e('Max Attendees', 'the-rink-society'); ?></label></th>
                <td><input type="number" name="max_attendees" id="max_attendees" value="<?php echo $event && $event->max_attendees ? esc_attr($event->max_attendees) : ''; ?>" min="1" style="width: 100px;">
                    <p class="description"><?php _e('Leave blank for unlimited', 'the-rink-society'); ?></p></td></tr>
            <tr><th><label for="description"><?php _e('Description', 'the-rink-society'); ?></label></th>
                <td><textarea name="description" id="description" rows="5" class="large-text"><?php echo $event ? esc_textarea($event->description) : ''; ?></textarea></td></tr>
        </table>
        <p class="submit">
            <input type="submit" name="trs_save_event" class="button button-primary" value="<?php echo $edit_mode ? __('Update', 'the-rink-society') : __('Create', 'the-rink-society'); ?>">
            <a href="<?php echo admin_url('admin.php?page=trs-events'); ?>" class="button"><?php _e('Cancel', 'the-rink-society'); ?></a>
        </p>
    </form>

    <?php if ($edit_mode) : ?>
        <hr>
        <h2><?php _e('Quick Actions', 'the-rink-society'); ?></h2>
        <p><a href="<?php echo admin_url('admin.php?page=trs-events&action=attendance&id=' . $event->id); ?>" class="button"><?php _e('Manage Attendance', 'the-rink-society'); ?></a></p>
    <?php endif; ?>
</div>
