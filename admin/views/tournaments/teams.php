<?php if (!defined('ABSPATH')) exit;
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
?>
<div class="wrap">
    <h1><?php echo esc_html($tournament->name); ?> - <?php _e('Teams', 'the-rink-society'); ?></h1>
    
    <?php if ($message === 'team_added') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Team added to tournament.', 'the-rink-society'); ?></p></div>
    <?php elseif ($message === 'team_removed') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Team removed from tournament.', 'the-rink-society'); ?></p></div>
    <?php endif; ?>

    <p><a href="<?php echo admin_url('admin.php?page=trs-tournaments&action=edit&id=' . $tournament->id); ?>" class="button">&larr; <?php _e('Back to Tournament', 'the-rink-society'); ?></a></p>

    <div class="trs-two-column">
        <div class="trs-main-column">
            <h2><?php _e('Current Teams', 'the-rink-society'); ?></h2>
            <?php if (empty($current_teams)) : ?>
                <p><?php _e('No teams in this tournament yet.', 'the-rink-society'); ?></p>
            <?php else : ?>
                <table class="wp-list-table widefat striped">
                    <thead><tr>
                        <th><?php _e('Team', 'the-rink-society'); ?></th>
                        <th><?php _e('Record', 'the-rink-society'); ?></th>
                        <th><?php _e('Actions', 'the-rink-society'); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($current_teams as $team) : 
                            $record = $team->get_record($tournament->id);
                        ?>
                            <tr>
                                <td><strong><?php echo esc_html($team->name); ?></strong></td>
                                <td><?php echo $record['wins'] . '-' . $record['losses'] . '-' . $record['ties']; ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <?php wp_nonce_field('trs_tournament_action'); ?>
                                        <input type="hidden" name="team_id" value="<?php echo $team->id; ?>">
                                        <input type="submit" name="trs_remove_team" class="button button-small" value="<?php _e('Remove', 'the-rink-society'); ?>" onclick="return confirm('<?php _e('Remove this team from the tournament?', 'the-rink-society'); ?>');">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="trs-sidebar-column">
            <div class="postbox">
                <div class="postbox-header"><h2><?php _e('Add Team', 'the-rink-society'); ?></h2></div>
                <div class="inside">
                    <?php if (empty($available_teams)) : ?>
                        <p><?php _e('All teams are already in this tournament.', 'the-rink-society'); ?></p>
                    <?php else : ?>
                        <form method="post">
                            <?php wp_nonce_field('trs_tournament_action'); ?>
                            <p>
                                <label for="team_id"><?php _e('Select Team:', 'the-rink-society'); ?></label>
                                <select name="team_id" id="team_id" class="widefat" required>
                                    <option value=""><?php _e('- Select Team -', 'the-rink-society'); ?></option>
                                    <?php foreach ($available_teams as $team) : ?>
                                        <option value="<?php echo $team->id; ?>"><?php echo esc_html($team->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </p>
                            <p><input type="submit" name="trs_add_team" class="button button-primary" value="<?php _e('Add Team', 'the-rink-society'); ?>"></p>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.trs-two-column { display: flex; gap: 20px; }
.trs-main-column { flex: 1; }
.trs-sidebar-column { width: 300px; }
@media (max-width: 782px) {
    .trs-two-column { flex-direction: column; }
    .trs-sidebar-column { width: 100%; }
}
</style>
