<?php if (!defined('ABSPATH')) exit;
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Events', 'the-rink-society'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=trs-events&action=new'); ?>" class="page-title-action"><?php _e('Add New', 'the-rink-society'); ?></a>

    <?php if ($message === 'saved') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Event saved.', 'the-rink-society'); ?></p></div>
    <?php elseif ($message === 'deleted') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Event deleted.', 'the-rink-society'); ?></p></div>
    <?php endif; ?>

    <hr class="wp-header-end">

    <?php if (empty($events)) : ?>
        <p><?php _e('No events found.', 'the-rink-society'); ?></p>
        <a href="<?php echo admin_url('admin.php?page=trs-events&action=new'); ?>" class="button button-primary"><?php _e('Create First Event', 'the-rink-society'); ?></a>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr>
                <th><?php _e('Name', 'the-rink-society'); ?></th>
                <th><?php _e('Type', 'the-rink-society'); ?></th>
                <th><?php _e('Date/Time', 'the-rink-society'); ?></th>
                <th><?php _e('Location', 'the-rink-society'); ?></th>
                <th><?php _e('Attendance', 'the-rink-society'); ?></th>
                <th><?php _e('Actions', 'the-rink-society'); ?></th>
            </tr></thead>
            <tbody>
                <?php foreach ($events as $event) : 
                    $attendance_count = count($event->get_attendance());
                ?>
                    <tr>
                        <td><strong><a href="<?php echo admin_url('admin.php?page=trs-events&action=edit&id=' . $event->id); ?>"><?php echo esc_html($event->name); ?></a></strong></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $event->event_type)); ?></td>
                        <td><?php echo trs_format_date($event->event_date) . ($event->event_time ? ' ' . trs_format_time($event->event_time) : ''); ?></td>
                        <td><?php echo esc_html($event->location); ?></td>
                        <td><?php echo $attendance_count . ($event->max_attendees ? '/' . $event->max_attendees : ''); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=trs-events&action=edit&id=' . $event->id); ?>" class="button button-small"><?php _e('Edit', 'the-rink-society'); ?></a>
                            <a href="<?php echo admin_url('admin.php?page=trs-events&action=attendance&id=' . $event->id); ?>" class="button button-small"><?php _e('Attendance', 'the-rink-society'); ?></a>
                            <a href="<?php echo admin_url('admin.php?page=trs-events&action=delete&id=' . $event->id); ?>" class="button button-small button-link-delete"><?php _e('Delete', 'the-rink-society'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
