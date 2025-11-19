<?php if (!defined('ABSPATH')) exit;
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Seasons', 'the-rink-society'); ?></h1>
    <a href="<?php echo admin_url('admin.php?page=trs-seasons&action=new'); ?>" class="page-title-action"><?php _e('Add New', 'the-rink-society'); ?></a>

    <?php if ($message === 'saved') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Season saved.', 'the-rink-society'); ?></p></div>
    <?php endif; ?>

    <hr class="wp-header-end">

    <?php if (empty($seasons)) : ?>
        <p><?php _e('No seasons found.', 'the-rink-society'); ?></p>
        <a href="<?php echo admin_url('admin.php?page=trs-seasons&action=new'); ?>" class="button button-primary"><?php _e('Create First Season', 'the-rink-society'); ?></a>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr>
                <th><?php _e('Name', 'the-rink-society'); ?></th>
                <th><?php _e('Status', 'the-rink-society'); ?></th>
                <th><?php _e('Dates', 'the-rink-society'); ?></th>
                <th><?php _e('Actions', 'the-rink-society'); ?></th>
            </tr></thead>
            <tbody>
                <?php foreach ($seasons as $season) : ?>
                    <tr>
                        <td><strong><a href="<?php echo admin_url('admin.php?page=trs-seasons&action=edit&id=' . $season->id); ?>"><?php echo esc_html($season->name); ?></a></strong></td>
                        <td><span class="trs-status-<?php echo $season->status; ?>"><?php echo ucfirst($season->status); ?></span></td>
                        <td><?php echo ($season->start_date && $season->end_date) ? trs_format_date($season->start_date) . ' - ' . trs_format_date($season->end_date) : '-'; ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=trs-seasons&action=edit&id=' . $season->id); ?>" class="button button-small"><?php _e('Edit', 'the-rink-society'); ?></a>
                            <a href="<?php echo admin_url('admin.php?page=trs-seasons&action=delete&id=' . $season->id); ?>" class="button button-small button-link-delete"><?php _e('Delete', 'the-rink-society'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
