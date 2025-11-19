<?php if (!defined('ABSPATH')) exit;
$home_team = $game->get_home_team();
$away_team = $game->get_away_team();
?>
<div class="wrap">
    <h1><?php _e('Delete Game', 'the-rink-society'); ?></h1>
    <div class="notice notice-warning"><p><strong><?php _e('Warning:', 'the-rink-society'); ?></strong> <?php _e('Delete this game?', 'the-rink-society'); ?></p></div>
    
    <table class="form-table">
        <tr><th><?php _e('Matchup:', 'the-rink-society'); ?></th>
            <td><strong><?php echo esc_html($home_team->name) . ' vs ' . esc_html($away_team->name); ?></strong></td></tr>
        <tr><th><?php _e('Date:', 'the-rink-society'); ?></th>
            <td><?php echo trs_format_date($game->game_date); ?></td></tr>
        <tr><th><?php _e('Stats Recorded:', 'the-rink-society'); ?></th>
            <td><?php echo $stats_count; ?></td></tr>
    </table>

    <?php if ($stats_count > 0) : ?>
        <div class="notice notice-info">
            <p><?php _e('Note: Deleting this game will also delete all associated stats. This cannot be undone!', 'the-rink-society'); ?></p>
        </div>
    <?php endif; ?>

    <form method="post">
        <?php wp_nonce_field('trs_game_action'); ?>
        <input type="submit" name="trs_delete_game" class="button button-primary" value="<?php _e('Yes, Delete', 'the-rink-society'); ?>">
        <a href="<?php echo admin_url('admin.php?page=trs-games'); ?>" class="button"><?php _e('Cancel', 'the-rink-society'); ?></a>
    </form>
</div>
