<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap">
    <h1><?php _e('Delete Event', 'the-rink-society'); ?></h1>
    <div class="notice notice-warning"><p><strong><?php _e('Warning:', 'the-rink-society'); ?></strong> <?php _e('Delete this event?', 'the-rink-society'); ?></p></div>
    
    <table class="form-table">
        <tr><th><?php _e('Event Name:', 'the-rink-society'); ?></th>
            <td><strong><?php echo esc_html($event->name); ?></strong></td></tr>
        <tr><th><?php _e('Date:', 'the-rink-society'); ?></th>
            <td><?php echo trs_format_date($event->event_date); ?></td></tr>
        <tr><th><?php _e('Attendance Records:', 'the-rink-society'); ?></th>
            <td><?php echo $attendance_count; ?></td></tr>
    </table>

    <?php if ($attendance_count > 0) : ?>
        <div class="notice notice-info">
            <p><?php _e('Note: Deleting this event will also delete all attendance records.', 'the-rink-society'); ?></p>
        </div>
    <?php endif; ?>

    <form method="post">
        <?php wp_nonce_field('trs_event_action'); ?>
        <input type="submit" name="trs_delete_event" class="button button-primary" value="<?php _e('Yes, Delete', 'the-rink-society'); ?>">
        <a href="<?php echo admin_url('admin.php?page=trs-events'); ?>" class="button"><?php _e('Cancel', 'the-rink-society'); ?></a>
    </form>
</div>
