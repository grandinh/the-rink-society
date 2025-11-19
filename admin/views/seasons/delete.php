<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap">
    <h1><?php _e('Delete Season', 'the-rink-society'); ?></h1>
    <div class="notice notice-warning"><p><strong><?php _e('Warning:', 'the-rink-society'); ?></strong> <?php _e('Delete this season?', 'the-rink-society'); ?></p></div>
    <p><strong><?php echo esc_html($season->name); ?></strong></p>
    <form method="post"><<?php wp_nonce_field('trs_season_action'); ?>
        <input type="submit" name="trs_delete_season" class="button button-primary" value="<?php _e('Yes, Delete', 'the-rink-society'); ?>">
        <a href="<?php echo admin_url('admin.php?page=trs-seasons'); ?>" class="button"><?php _e('Cancel', 'the-rink-society'); ?></a>
    </form>
</div>
