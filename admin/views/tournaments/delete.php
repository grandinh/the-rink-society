<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap">
    <h1><?php _e('Delete Tournament', 'the-rink-society'); ?></h1>
    <div class="notice notice-warning"><p><strong><?php _e('Warning:', 'the-rink-society'); ?></strong> <?php _e('Delete this tournament?', 'the-rink-society'); ?></p></div>
    
    <table class="form-table">
        <tr><th><?php _e('Tournament Name:', 'the-rink-society'); ?></th>
            <td><strong><?php echo esc_html($tournament->name); ?></strong></td></tr>
        <tr><th><?php _e('Teams:', 'the-rink-society'); ?></th>
            <td><?php echo $team_count; ?></td></tr>
        <tr><th><?php _e('Games:', 'the-rink-society'); ?></th>
            <td><?php echo $game_count; ?></td></tr>
    </table>

    <?php if ($game_count > 0) : ?>
        <div class="notice notice-info">
            <p><?php _e('Note: Deleting this tournament will also delete all associated games and stats. This cannot be undone!', 'the-rink-society'); ?></p>
        </div>
    <?php endif; ?>

    <form method="post">
        <?php wp_nonce_field('trs_tournament_action'); ?>
        <input type="submit" name="trs_delete_tournament" class="button button-primary" value="<?php _e('Yes, Delete', 'the-rink-society'); ?>">
        <a href="<?php echo admin_url('admin.php?page=trs-tournaments'); ?>" class="button"><?php _e('Cancel', 'the-rink-society'); ?></a>
    </form>
</div>
