<?php if (!defined('ABSPATH')) exit;
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Games', 'the-rink-society'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=trs-games&action=new'); ?>" class="page-title-action"><?php _e('Add New', 'the-rink-society'); ?></a>

    <?php if ($message === 'saved') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Game saved.', 'the-rink-society'); ?></p></div>
    <?php elseif ($message === 'deleted') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Game deleted.', 'the-rink-society'); ?></p></div>
    <?php endif; ?>

    <hr class="wp-header-end">

    <!-- Filters -->
    <div class="tablenav top">
        <form method="get">
            <input type="hidden" name="page" value="trs-games">
            <select name="season_id">
                <option value=""><?php _e('All Seasons', 'the-rink-society'); ?></option>
                <?php foreach ($seasons as $season) : ?>
                    <option value="<?php echo $season->id; ?>" <?php selected($_GET['season_id'] ?? '', $season->id); ?>><?php echo esc_html($season->name); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="tournament_id">
                <option value=""><?php _e('All Tournaments', 'the-rink-society'); ?></option>
                <?php foreach ($tournaments as $tournament) : ?>
                    <option value="<?php echo $tournament->id; ?>" <?php selected($_GET['tournament_id'] ?? '', $tournament->id); ?>><?php echo esc_html($tournament->name); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status">
                <option value=""><?php _e('All Statuses', 'the-rink-society'); ?></option>
                <option value="scheduled" <?php selected($_GET['status'] ?? '', 'scheduled'); ?>><?php _e('Scheduled', 'the-rink-society'); ?></option>
                <option value="in-progress" <?php selected($_GET['status'] ?? '', 'in-progress'); ?>><?php _e('In Progress', 'the-rink-society'); ?></option>
                <option value="completed" <?php selected($_GET['status'] ?? '', 'completed'); ?>><?php _e('Completed', 'the-rink-society'); ?></option>
                <option value="cancelled" <?php selected($_GET['status'] ?? '', 'cancelled'); ?>><?php _e('Cancelled', 'the-rink-society'); ?></option>
            </select>
            <input type="submit" class="button" value="<?php _e('Filter', 'the-rink-society'); ?>">
        </form>
    </div>

    <?php if (empty($games)) : ?>
        <p><?php _e('No games found.', 'the-rink-society'); ?></p>
        <a href="<?php echo admin_url('admin.php?page=trs-games&action=new'); ?>" class="button button-primary"><?php _e('Create First Game', 'the-rink-society'); ?></a>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr>
                <th><?php _e('Date/Time', 'the-rink-society'); ?></th>
                <th><?php _e('Matchup', 'the-rink-society'); ?></th>
                <th><?php _e('Score', 'the-rink-society'); ?></th>
                <th><?php _e('Season/Tournament', 'the-rink-society'); ?></th>
                <th><?php _e('Location', 'the-rink-society'); ?></th>
                <th><?php _e('Status', 'the-rink-society'); ?></th>
                <th><?php _e('Actions', 'the-rink-society'); ?></th>
            </tr></thead>
            <tbody>
                <?php foreach ($games as $game) : 
                    $home_team = $game->get_home_team();
                    $away_team = $game->get_away_team();
                    $season = $game->season_id ? $game->get_season() : null;
                    $tournament = $game->tournament_id ? $game->get_tournament() : null;
                ?>
                    <tr>
                        <td><?php echo trs_format_date($game->game_date) . ($game->game_time ? ' ' . trs_format_time($game->game_time) : ''); ?></td>
                        <td><strong><?php echo esc_html($home_team->name) . ' vs ' . esc_html($away_team->name); ?></strong></td>
                        <td><?php echo ($game->home_score !== null && $game->away_score !== null) ? $game->home_score . '-' . $game->away_score : '-'; ?></td>
                        <td><?php 
                            if ($tournament) echo esc_html($tournament->name);
                            elseif ($season) echo esc_html($season->name);
                            else echo '-';
                        ?></td>
                        <td><?php echo esc_html($game->location); ?></td>
                        <td><span class="trs-status-<?php echo $game->status; ?>"><?php echo ucfirst($game->status); ?></span></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=trs-games&action=edit&id=' . $game->id); ?>" class="button button-small"><?php _e('Edit', 'the-rink-society'); ?></a>
                            <a href="<?php echo admin_url('admin.php?page=trs-games&action=score&id=' . $game->id); ?>" class="button button-small"><?php _e('Score', 'the-rink-society'); ?></a>
                            <a href="<?php echo admin_url('admin.php?page=trs-games&action=delete&id=' . $game->id); ?>" class="button button-small button-link-delete"><?php _e('Delete', 'the-rink-society'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
