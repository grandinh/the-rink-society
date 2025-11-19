<?php
/**
 * Players List View
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle success messages
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Players', 'the-rink-society'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=trs-players&action=new'); ?>" class="page-title-action">
        <?php _e('Add New', 'the-rink-society'); ?>
    </a>

    <?php if ($message === 'saved') : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Player saved successfully.', 'the-rink-society'); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($message === 'deleted') : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Player deleted successfully.', 'the-rink-society'); ?></p>
        </div>
    <?php endif; ?>

    <hr class="wp-header-end">

    <form method="get" action="">
        <input type="hidden" name="page" value="trs-players">
        <?php wp_nonce_field('trs_search_players', 'search_nonce'); ?>
        <p class="search-box">
            <label class="screen-reader-text" for="player-search-input"><?php _e('Search Players:', 'the-rink-society'); ?></label>
            <input type="search" id="player-search-input" name="s" value="<?php echo esc_attr($search); ?>">
            <input type="submit" class="button" value="<?php _e('Search Players', 'the-rink-society'); ?>">
        </p>
    </form>

    <?php if (empty($players)) : ?>
        <p><?php _e('No players found.', 'the-rink-society'); ?></p>
        <p>
            <a href="<?php echo admin_url('admin.php?page=trs-players&action=new'); ?>" class="button button-primary">
                <?php _e('Add Your First Player', 'the-rink-society'); ?>
            </a>
        </p>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'the-rink-society'); ?></th>
                    <th><?php _e('Name', 'the-rink-society'); ?></th>
                    <th><?php _e('Jersey #', 'the-rink-society'); ?></th>
                    <th><?php _e('Position', 'the-rink-society'); ?></th>
                    <th><?php _e('Shoots', 'the-rink-society'); ?></th>
                    <th><?php _e('Teams', 'the-rink-society'); ?></th>
                    <th><?php _e('Actions', 'the-rink-society'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players as $player) :
                    $teams = $player->get_teams();
                ?>
                    <tr>
                        <td><?php echo $player->id; ?></td>
                        <td>
                            <strong>
                                <a href="<?php echo admin_url('admin.php?page=trs-players&action=edit&id=' . $player->id); ?>">
                                    <?php echo esc_html($player->get_name()); ?>
                                </a>
                            </strong>
                        </td>
                        <td><?php echo esc_html($player->preferred_jersey_number ?: '-'); ?></td>
                        <td><?php echo $player->position ? trs_get_position_label($player->position) : '-'; ?></td>
                        <td><?php echo esc_html(ucfirst($player->shoots ?: '-')); ?></td>
                        <td>
                            <?php
                            if (!empty($teams)) {
                                $team_names = array_map(function($t) { return $t['name']; }, $teams);
                                echo esc_html(implode(', ', array_slice($team_names, 0, 3)));
                                if (count($team_names) > 3) {
                                    echo ' <small>(+' . (count($team_names) - 3) . ' more)</small>';
                                }
                            } else {
                                echo '<em>' . __('No teams', 'the-rink-society') . '</em>';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=trs-players&action=edit&id=' . $player->id); ?>" class="button button-small">
                                <?php _e('Edit', 'the-rink-society'); ?>
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=trs-players&action=delete&id=' . $player->id); ?>" class="button button-small button-link-delete">
                                <?php _e('Delete', 'the-rink-society'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="description">
            <?php printf(__('Showing %d player(s)', 'the-rink-society'), count($players)); ?>
        </p>
    <?php endif; ?>
</div>
