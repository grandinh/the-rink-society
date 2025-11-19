<?php if (!defined('ABSPATH')) exit;
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Tournaments', 'the-rink-society'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=trs-tournaments&action=new'); ?>" class="page-title-action"><?php _e('Add New', 'the-rink-society'); ?></a>

    <?php if ($message === 'saved') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Tournament saved.', 'the-rink-society'); ?></p></div>
    <?php elseif ($message === 'deleted') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Tournament deleted.', 'the-rink-society'); ?></p></div>
    <?php endif; ?>

    <hr class="wp-header-end">

    <?php if (empty($tournaments)) : ?>
        <p><?php _e('No tournaments found.', 'the-rink-society'); ?></p>
        <a href="<?php echo admin_url('admin.php?page=trs-tournaments&action=new'); ?>" class="button button-primary"><?php _e('Create First Tournament', 'the-rink-society'); ?></a>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr>
                <th><?php _e('Name', 'the-rink-society'); ?></th>
                <th><?php _e('Season', 'the-rink-society'); ?></th>
                <th><?php _e('Format', 'the-rink-society'); ?></th>
                <th><?php _e('Status', 'the-rink-society'); ?></th>
                <th><?php _e('Dates', 'the-rink-society'); ?></th>
                <th><?php _e('Teams', 'the-rink-society'); ?></th>
                <th><?php _e('Actions', 'the-rink-society'); ?></th>
            </tr></thead>
            <tbody>
                <?php foreach ($tournaments as $tournament) : 
                    $season = $tournament->season_id ? $tournament->get_season() : null;
                    $team_count = count($tournament->get_teams());
                ?>
                    <tr>
                        <td><strong><a href="<?php echo admin_url('admin.php?page=trs-tournaments&action=edit&id=' . $tournament->id); ?>"><?php echo esc_html($tournament->name); ?></a></strong></td>
                        <td><?php echo $season ? esc_html($season->name) : '-'; ?></td>
                        <td><?php echo ucfirst($tournament->format); ?></td>
                        <td><span class="trs-status-<?php echo $tournament->status; ?>"><?php echo ucfirst($tournament->status); ?></span></td>
                        <td><?php echo ($tournament->start_date && $tournament->end_date) ? trs_format_date($tournament->start_date) . ' - ' . trs_format_date($tournament->end_date) : '-'; ?></td>
                        <td><?php echo $team_count; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=trs-tournaments&action=edit&id=' . $tournament->id); ?>" class="button button-small"><?php _e('Edit', 'the-rink-society'); ?></a>
                            <a href="<?php echo admin_url('admin.php?page=trs-tournaments&action=teams&id=' . $tournament->id); ?>" class="button button-small"><?php _e('Teams', 'the-rink-society'); ?></a>
                            <a href="<?php echo admin_url('admin.php?page=trs-tournaments&action=standings&id=' . $tournament->id); ?>" class="button button-small"><?php _e('Standings', 'the-rink-society'); ?></a>
                            <a href="<?php echo admin_url('admin.php?page=trs-tournaments&action=delete&id=' . $tournament->id); ?>" class="button button-small button-link-delete"><?php _e('Delete', 'the-rink-society'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
