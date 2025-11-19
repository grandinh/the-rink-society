<?php
/**
 * Player Delete Confirmation
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php _e('Delete Player', 'the-rink-society'); ?></h1>

    <div class="notice notice-warning">
        <p>
            <strong><?php _e('Warning:', 'the-rink-society'); ?></strong>
            <?php _e('Are you sure you want to delete this player?', 'the-rink-society'); ?>
        </p>
    </div>

    <table class="form-table">
        <tr>
            <th><?php _e('Player Name:', 'the-rink-society'); ?></th>
            <td><strong><?php echo esc_html($player->get_name()); ?></strong></td>
        </tr>
        <tr>
            <th><?php _e('Position:', 'the-rink-society'); ?></th>
            <td><?php echo $player->position ? trs_get_position_label($player->position) : '-'; ?></td>
        </tr>
        <tr>
            <th><?php _e('Jersey Number:', 'the-rink-society'); ?></th>
            <td><?php echo esc_html($player->preferred_jersey_number ?: '-'); ?></td>
        </tr>
    </table>

    <p><strong><?php _e('This action cannot be undone!', 'the-rink-society'); ?></strong></p>

    <form method="post" action="">
        <?php wp_nonce_field('trs_player_action'); ?>
        <p class="submit">
            <input type="submit"
                   name="trs_delete_player"
                   class="button button-primary"
                   value="<?php _e('Yes, Delete This Player', 'the-rink-society'); ?>"
                   onclick="return confirm('<?php _e('Are you absolutely sure?', 'the-rink-society'); ?>');">
            <a href="<?php echo admin_url('admin.php?page=trs-players'); ?>" class="button">
                <?php _e('Cancel', 'the-rink-society'); ?>
            </a>
        </p>
    </form>
</div>
