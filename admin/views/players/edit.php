<?php
/**
 * Player Edit/Create Form
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

$page_title = $edit_mode ? __('Edit Player', 'the-rink-society') : __('Add New Player', 'the-rink-society');
?>

<div class="wrap">
    <h1><?php echo $page_title; ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('trs_player_action'); ?>
        <input type="hidden" name="player_id" value="<?php echo $player ? $player->id : 0; ?>">

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="user_id"><?php _e('WordPress User', 'the-rink-society'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <?php if ($edit_mode && $player) : ?>
                        <strong><?php echo esc_html($player->get_name()); ?></strong>
                        <input type="hidden" name="user_id" value="<?php echo $player->user_id; ?>">
                        <p class="description">
                            <?php _e('Cannot change WordPress user for existing player.', 'the-rink-society'); ?>
                        </p>
                    <?php else : ?>
                        <select name="user_id" id="user_id" required class="regular-text">
                            <option value=""><?php _e('Select a user...', 'the-rink-society'); ?></option>
                            <?php foreach ($available_users as $user) : ?>
                                <option value="<?php echo $user->ID; ?>">
                                    <?php echo esc_html($user->display_name); ?> (<?php echo esc_html($user->user_login); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">
                            <?php _e('Link this player to an existing WordPress user account.', 'the-rink-society'); ?>
                        </p>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="preferred_jersey_number"><?php _e('Preferred Jersey Number', 'the-rink-society'); ?></label>
                </th>
                <td>
                    <input type="text"
                           name="preferred_jersey_number"
                           id="preferred_jersey_number"
                           value="<?php echo $player ? esc_attr($player->preferred_jersey_number) : ''; ?>"
                           class="small-text">
                    <p class="description">
                        <?php _e('Default jersey number. Can be overridden per team or game.', 'the-rink-society'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="position"><?php _e('Position', 'the-rink-society'); ?></label>
                </th>
                <td>
                    <select name="position" id="position">
                        <option value=""><?php _e('Not specified', 'the-rink-society'); ?></option>
                        <option value="forward" <?php selected($player ? $player->position : '', 'forward'); ?>>
                            <?php _e('Forward', 'the-rink-society'); ?>
                        </option>
                        <option value="defense" <?php selected($player ? $player->position : '', 'defense'); ?>>
                            <?php _e('Defense', 'the-rink-society'); ?>
                        </option>
                        <option value="goalie" <?php selected($player ? $player->position : '', 'goalie'); ?>>
                            <?php _e('Goalie', 'the-rink-society'); ?>
                        </option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="shoots"><?php _e('Shoots', 'the-rink-society'); ?></label>
                </th>
                <td>
                    <select name="shoots" id="shoots">
                        <option value=""><?php _e('Not specified', 'the-rink-society'); ?></option>
                        <option value="left" <?php selected($player ? $player->shoots : '', 'left'); ?>>
                            <?php _e('Left', 'the-rink-society'); ?>
                        </option>
                        <option value="right" <?php selected($player ? $player->shoots : '', 'right'); ?>>
                            <?php _e('Right', 'the-rink-society'); ?>
                        </option>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="birth_date"><?php _e('Birth Date', 'the-rink-society'); ?></label>
                </th>
                <td>
                    <input type="date"
                           name="birth_date"
                           id="birth_date"
                           value="<?php echo $player ? esc_attr($player->birth_date) : ''; ?>">
                </td>
            </tr>
        </table>

        <?php if ($edit_mode && $player) : ?>
            <h2><?php _e('Teams', 'the-rink-society'); ?></h2>
            <?php
            $teams = $player->get_teams();
            if (!empty($teams)) :
            ?>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Team Name', 'the-rink-society'); ?></th>
                            <th><?php _e('Jersey Number', 'the-rink-society'); ?></th>
                            <th><?php _e('Role', 'the-rink-society'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teams as $team_data) : ?>
                            <tr>
                                <td><?php echo esc_html($team_data['name']); ?></td>
                                <td><?php echo esc_html($team_data['jersey_number'] ?: $player->preferred_jersey_number ?: '-'); ?></td>
                                <td><?php echo esc_html(ucfirst($team_data['role'] ?: 'player')); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><em><?php _e('This player is not on any teams yet.', 'the-rink-society'); ?></em></p>
            <?php endif; ?>
            <p>
                <em><?php _e('To manage team rosters, go to the Teams page.', 'the-rink-society'); ?></em>
            </p>
        <?php endif; ?>

        <p class="submit">
            <input type="submit"
                   name="trs_save_player"
                   class="button button-primary"
                   value="<?php echo $edit_mode ? __('Update Player', 'the-rink-society') : __('Create Player', 'the-rink-society'); ?>">
            <a href="<?php echo admin_url('admin.php?page=trs-players'); ?>" class="button">
                <?php _e('Cancel', 'the-rink-society'); ?>
            </a>
        </p>
    </form>
</div>

<style>
.required { color: #dc3232; }
</style>
