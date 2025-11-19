<?php
/**
 * Team Roster Management
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
?>

<div class="wrap">
    <h1><?php echo esc_html($team->name); ?> - <?php _e('Roster', 'the-rink-society'); ?></h1>

    <p>
        <a href="<?php echo admin_url('admin.php?page=trs-teams&action=edit&id=' . $team->id); ?>" class="button">
            ← <?php _e('Back to Team', 'the-rink-society'); ?>
        </a>
    </p>

    <?php if ($message === 'player_added') : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Player added to roster.', 'the-rink-society'); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($message === 'player_removed') : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Player removed from roster.', 'the-rink-society'); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($message === 'roster_updated') : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Roster updated successfully.', 'the-rink-society'); ?></p>
        </div>
    <?php endif; ?>

    <div class="trs-roster-container">
        <div class="trs-roster-main">
            <h2><?php _e('Current Roster', 'the-rink-society'); ?></h2>

            <?php if (empty($roster)) : ?>
                <p><em><?php _e('No players on this team yet.', 'the-rink-society'); ?></em></p>
            <?php else : ?>
                <form method="post" action="">
                    <?php wp_nonce_field('trs_team_action'); ?>
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th><?php _e('Player', 'the-rink-society'); ?></th>
                                <th><?php _e('Position', 'the-rink-society'); ?></th>
                                <th><?php _e('Jersey #', 'the-rink-society'); ?></th>
                                <th><?php _e('Role', 'the-rink-society'); ?></th>
                                <th><?php _e('Actions', 'the-rink-society'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roster as $player) : ?>
                                <tr>
                                    <td><strong><?php echo esc_html($player->get_name()); ?></strong></td>
                                    <td><?php echo $player->position ? trs_get_position_label($player->position) : '-'; ?></td>
                                    <td>
                                        <input type="text"
                                               name="roster[<?php echo $player->id; ?>][jersey_number]"
                                               value="<?php echo esc_attr($player->get_jersey_number($team->id) ?: ''); ?>"
                                               class="small-text">
                                    </td>
                                    <td><?php echo esc_html(ucfirst($player->role ?? 'player')); ?></td>
                                    <td>
                                        <button type="submit"
                                                name="trs_remove_player"
                                                value="<?php echo $player->id; ?>"
                                                class="button button-small"
                                                onclick="return confirm('<?php _e('Remove this player from the team?', 'the-rink-society'); ?>');">
                                            <?php _e('Remove', 'the-rink-society'); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <p class="submit">
                        <input type="submit"
                               name="trs_update_roster"
                               class="button button-primary"
                               value="<?php _e('Update Jersey Numbers', 'the-rink-society'); ?>">
                    </p>
                </form>
            <?php endif; ?>
        </div>

        <div class="trs-roster-sidebar">
            <div class="trs-panel">
                <h3><?php _e('Add Player to Roster', 'the-rink-society'); ?></h3>

                <?php if (empty($available_players)) : ?>
                    <p><em><?php _e('All players are already on this team.', 'the-rink-society'); ?></em></p>
                    <p>
                        <a href="<?php echo admin_url('admin.php?page=trs-players&action=new'); ?>" class="button">
                            <?php _e('Create New Player', 'the-rink-society'); ?>
                        </a>
                    </p>
                <?php else : ?>
                    <form method="post" action="">
                        <?php wp_nonce_field('trs_team_action'); ?>

                        <p>
                            <label for="player_id"><?php _e('Select Player:', 'the-rink-society'); ?></label>
                            <select name="player_id" id="player_id" class="widefat" required>
                                <option value=""><?php _e('Choose a player...', 'the-rink-society'); ?></option>
                                <?php foreach ($available_players as $player) : ?>
                                    <option value="<?php echo $player->id; ?>">
                                        <?php echo esc_html($player->get_name()); ?>
                                        <?php if ($player->position) : ?>
                                            (<?php echo trs_get_position_label($player->position); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </p>

                        <p>
                            <label for="jersey_number"><?php _e('Jersey Number:', 'the-rink-society'); ?></label>
                            <input type="text" name="jersey_number" id="jersey_number" class="widefat" placeholder="<?php _e('Leave blank for default', 'the-rink-society'); ?>">
                        </p>

                        <p>
                            <label for="role"><?php _e('Role:', 'the-rink-society'); ?></label>
                            <select name="role" id="role" class="widefat">
                                <option value="player"><?php _e('Player', 'the-rink-society'); ?></option>
                                <option value="captain"><?php _e('Captain', 'the-rink-society'); ?></option>
                                <option value="assistant_captain"><?php _e('Assistant Captain', 'the-rink-society'); ?></option>
                            </select>
                        </p>

                        <p>
                            <input type="submit"
                                   name="trs_add_player"
                                   class="button button-primary widefat"
                                   value="<?php _e('Add to Roster', 'the-rink-society'); ?>">
                        </p>
                    </form>
                <?php endif; ?>
            </div>

            <div class="trs-panel">
                <h3><?php _e('Roster Stats', 'the-rink-society'); ?></h3>
                <p><strong><?php _e('Total Players:', 'the-rink-society'); ?></strong> <?php echo count($roster); ?></p>
                <?php
                $positions = array('forward' => 0, 'defense' => 0, 'goalie' => 0);
                foreach ($roster as $player) {
                    if ($player->position && isset($positions[$player->position])) {
                        $positions[$player->position]++;
                    }
                }
                ?>
                <p><strong><?php _e('Forwards:', 'the-rink-society'); ?></strong> <?php echo $positions['forward']; ?></p>
                <p><strong><?php _e('Defense:', 'the-rink-society'); ?></strong> <?php echo $positions['defense']; ?></p>
                <p><strong><?php _e('Goalies:', 'the-rink-society'); ?></strong> <?php echo $positions['goalie']; ?></p>
            </div>
        </div>
    </div>
</div>

<style>
.trs-roster-container {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
    margin-top: 20px;
}

.trs-panel {
    background: white;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
}

.trs-panel h3 {
    margin-top: 0;
}

@media (max-width: 1024px) {
    .trs-roster-container {
        grid-template-columns: 1fr;
    }
}
</style>
