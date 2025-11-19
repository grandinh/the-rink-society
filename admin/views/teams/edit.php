<?php
/**
 * Team Edit/Create Form
 *
 * @package TheRinkSociety
 */

if (!defined('ABSPATH')) {
    exit;
}

$page_title = $edit_mode ? __('Edit Team', 'the-rink-society') : __('Add New Team', 'the-rink-society');
?>

<div class="wrap">
    <h1><?php echo $page_title; ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('trs_team_action'); ?>
        <input type="hidden" name="team_id" value="<?php echo $team ? $team->id : 0; ?>">

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="name"><?php _e('Team Name', 'the-rink-society'); ?> <span class="required">*</span></label>
                </th>
                <td>
                    <input type="text"
                           name="name"
                           id="name"
                           value="<?php echo $team ? esc_attr($team->name) : ''; ?>"
                           class="regular-text"
                           required>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="slug"><?php _e('Slug', 'the-rink-society'); ?></label>
                </th>
                <td>
                    <input type="text"
                           name="slug"
                           id="slug"
                           value="<?php echo $team ? esc_attr($team->slug) : ''; ?>"
                           class="regular-text">
                    <p class="description">
                        <?php _e('URL-friendly version of the name. Leave blank to auto-generate.', 'the-rink-society'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="season_id"><?php _e('Season', 'the-rink-society'); ?></label>
                </th>
                <td>
                    <select name="season_id" id="season_id">
                        <option value=""><?php _e('No season', 'the-rink-society'); ?></option>
                        <?php foreach ($seasons as $season) : ?>
                            <option value="<?php echo $season->id; ?>" <?php selected($team ? $team->season_id : 0, $season->id); ?>>
                                <?php echo esc_html($season->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="primary_color"><?php _e('Primary Color', 'the-rink-society'); ?></label>
                </th>
                <td>
                    <input type="color"
                           name="primary_color"
                           id="primary_color"
                           value="<?php echo $team && $team->primary_color ? esc_attr($team->primary_color) : '#000000'; ?>">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="secondary_color"><?php _e('Secondary Color', 'the-rink-society'); ?></label>
                </th>
                <td>
                    <input type="color"
                           name="secondary_color"
                           id="secondary_color"
                           value="<?php echo $team && $team->secondary_color ? esc_attr($team->secondary_color) : '#FFFFFF'; ?>">
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="logo_url"><?php _e('Logo URL', 'the-rink-society'); ?></label>
                </th>
                <td>
                    <input type="url"
                           name="logo_url"
                           id="logo_url"
                           value="<?php echo $team ? esc_attr($team->logo_url) : ''; ?>"
                           class="regular-text">
                    <p class="description">
                        <?php _e('Full URL to team logo image.', 'the-rink-society'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit"
                   name="trs_save_team"
                   class="button button-primary"
                   value="<?php echo $edit_mode ? __('Update Team', 'the-rink-society') : __('Create Team', 'the-rink-society'); ?>">
            <?php if ($edit_mode && $team) : ?>
                <a href="<?php echo admin_url('admin.php?page=trs-teams&action=roster&id=' . $team->id); ?>" class="button">
                    <?php _e('Manage Roster', 'the-rink-society'); ?>
                </a>
            <?php endif; ?>
            <a href="<?php echo admin_url('admin.php?page=trs-teams'); ?>" class="button">
                <?php _e('Cancel', 'the-rink-society'); ?>
            </a>
        </p>
    </form>
</div>

<style>
.required { color: #dc3232; }
</style>
