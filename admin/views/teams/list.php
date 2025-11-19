<?php
/**
 * Teams List View
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Teams', 'the-rink-society'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=trs-teams&action=new'); ?>" class="page-title-action">
        <?php _e('Add New', 'the-rink-society'); ?>
    </a>

    <?php if ($message === 'saved') : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Team saved successfully.', 'the-rink-society'); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($message === 'deleted') : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Team deleted successfully.', 'the-rink-society'); ?></p>
        </div>
    <?php endif; ?>

    <hr class="wp-header-end">

    <?php if (empty($teams)) : ?>
        <p><?php _e('No teams found.', 'the-rink-society'); ?></p>
        <p>
            <a href="<?php echo admin_url('admin.php?page=trs-teams&action=new'); ?>" class="button button-primary">
                <?php _e('Create Your First Team', 'the-rink-society'); ?>
            </a>
        </p>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 50px;"><?php _e('ID', 'the-rink-society'); ?></th>
                    <th><?php _e('Team Name', 'the-rink-society'); ?></th>
                    <th><?php _e('Colors', 'the-rink-society'); ?></th>
                    <th><?php _e('Season', 'the-rink-society'); ?></th>
                    <th><?php _e('Players', 'the-rink-society'); ?></th>
                    <th><?php _e('Record', 'the-rink-society'); ?></th>
                    <th><?php _e('Actions', 'the-rink-society'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $team) :
                    $roster_count = $team->get_roster_count();
                    $record = $team->get_record();
                    $season_repo = new TRS_Season_Repository();
                    $season = $team->season_id ? $season_repo->get($team->season_id) : null;
                ?>
                    <tr>
                        <td><?php echo $team->id; ?></td>
                        <td>
                            <strong>
                                <a href="<?php echo admin_url('admin.php?page=trs-teams&action=edit&id=' . $team->id); ?>">
                                    <?php echo esc_html($team->name); ?>
                                </a>
                            </strong>
                        </td>
                        <td>
                            <?php if ($team->primary_color) : ?>
                                <span style="display:inline-block; width:20px; height:20px; background:<?php echo esc_attr($team->primary_color); ?>; border:1px solid #ccc; border-radius:3px;"></span>
                            <?php endif; ?>
                            <?php if ($team->secondary_color) : ?>
                                <span style="display:inline-block; width:20px; height:20px; background:<?php echo esc_attr($team->secondary_color); ?>; border:1px solid #ccc; border-radius:3px;"></span>
                            <?php endif; ?>
                            <?php if (!$team->primary_color && !$team->secondary_color) : ?>
                                <span class="description"><?php _e('None', 'the-rink-society'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo $season ? esc_html($season->name) : '<span class="description">' . __('No season', 'the-rink-society') . '</span>'; ?>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=trs-teams&action=roster&id=' . $team->id); ?>">
                                <?php echo $roster_count; ?> <?php _e('players', 'the-rink-society'); ?>
                            </a>
                        </td>
                        <td>
                            <?php
                            $total_games = $record['wins'] + $record['losses'] + $record['ties'];
                            if ($total_games > 0) {
                                echo sprintf('%d-%d-%d', $record['wins'], $record['losses'], $record['ties']);
                            } else {
                                echo '<span class="description">' . __('No games', 'the-rink-society') . '</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=trs-teams&action=edit&id=' . $team->id); ?>" class="button button-small">
                                <?php _e('Edit', 'the-rink-society'); ?>
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=trs-teams&action=roster&id=' . $team->id); ?>" class="button button-small">
                                <?php _e('Roster', 'the-rink-society'); ?>
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=trs-teams&action=delete&id=' . $team->id); ?>" class="button button-small button-link-delete">
                                <?php _e('Delete', 'the-rink-society'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="description">
            <?php printf(__('Showing %d team(s)', 'the-rink-society'), count($teams)); ?>
        </p>
    <?php endif; ?>
</div>
