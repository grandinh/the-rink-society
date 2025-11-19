<?php
/**
 * Team Delete Confirmation
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

$roster_count = $team->get_roster_count();
?>

<div class="wrap">
    <h1><?php _e('Delete Team', 'the-rink-society'); ?></h1>

    <div class="notice notice-warning">
        <p>
            <strong><?php _e('Warning:', 'the-rink-society'); ?></strong>
            <?php _e('Are you sure you want to delete this team?', 'the-rink-society'); ?>
        </p>
    </div>

    <table class="form-table">
        <tr>
            <th><?php _e('Team Name:', 'the-rink-society'); ?></th>
            <td><strong><?php echo esc_html($team->name); ?></strong></td>
        </tr>
        <tr>
            <th><?php _e('Players on Roster:', 'the-rink-society'); ?></th>
            <td><?php echo $roster_count; ?></td>
        </tr>
    </table>

    <?php if ($roster_count > 0) : ?>
        <div class="notice notice-info">
            <p><?php _e('Note: Deleting this team will remove all players from the roster, but will not delete the player profiles themselves.', 'the-rink-society'); ?></p>
        </div>
    <?php endif; ?>

    <p><strong><?php _e('This action cannot be undone!', 'the-rink-society'); ?></strong></p>

    <form method="post" action="">
        <?php wp_nonce_field('trs_team_action'); ?>
        <p class="submit">
            <input type="submit"
                   name="trs_delete_team"
                   class="button button-primary"
                   value="<?php _e('Yes, Delete This Team', 'the-rink-society'); ?>"
                   onclick="return confirm('<?php _e('Are you absolutely sure?', 'the-rink-society'); ?>');">
            <a href="<?php echo admin_url('admin.php?page=trs-teams'); ?>" class="button">
                <?php _e('Cancel', 'the-rink-society'); ?>
            </a>
        </p>
    </form>
</div>
