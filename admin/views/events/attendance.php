<?php if (!defined('ABSPATH')) exit;
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$attending_ids = array_map(function($a) { return $a->player_id; }, $attendance);
?>
<div class="wrap">
    <h1><?php echo esc_html($event->name); ?> - <?php _e('Attendance', 'the-rink-society'); ?></h1>
    
    <?php if ($message === 'attendance_saved') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Attendance updated.', 'the-rink-society'); ?></p></div>
    <?php endif; ?>

    <p><a href="<?php echo admin_url('admin.php?page=trs-events&action=edit&id=' . $event->id); ?>" class="button">&larr; <?php _e('Back to Event', 'the-rink-society'); ?></a></p>

    <div class="card" style="max-width: 800px;">
        <p><strong><?php _e('Date:', 'the-rink-society'); ?></strong> <?php echo trs_format_date($event->event_date); ?></p>
        <p><strong><?php _e('Capacity:', 'the-rink-society'); ?></strong> <?php echo count($attending_ids) . ($event->max_attendees ? '/' . $event->max_attendees : ''); ?></p>

        <hr>

        <form method="post">
            <?php wp_nonce_field('trs_event_action'); ?>
            <h3><?php _e('Mark Attendance', 'the-rink-society'); ?></h3>
            
            <?php if (empty($all_players)) : ?>
                <p><?php _e('No players found. Please create players first.', 'the-rink-society'); ?></p>
            <?php else : ?>
                <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                    <?php foreach ($all_players as $player) : 
                        $user = $player->get_user();
                    ?>
                        <label style="display: block; padding: 5px;">
                            <input type="checkbox" name="attendees[]" value="<?php echo $player->id; ?>" <?php checked(in_array($player->id, $attending_ids)); ?>>
                            <?php echo esc_html($user->display_name); ?> 
                            <?php if ($player->preferred_jersey_number) : ?>
                                <span style="color: #666;">#<?php echo esc_html($player->preferred_jersey_number); ?></span>
                            <?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="submit">
                    <input type="submit" name="trs_mark_attendance" class="button button-primary" value="<?php _e('Save Attendance', 'the-rink-society'); ?>">
                </p>
            <?php endif; ?>
        </form>
    </div>
</div>
