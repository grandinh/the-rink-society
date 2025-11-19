<?php if (!defined('ABSPATH')) exit;
$message = isset($_GET['message']) ? sanitize_text_field($_GET['message']) : '';
$home_team = $game->get_home_team();
$away_team = $game->get_away_team();
?>
<div class="wrap">
    <h1><?php _e('Enter Game Score', 'the-rink-society'); ?></h1>
    
    <?php if ($message === 'score_updated') : ?>
        <div class="notice notice-success is-dismissible"><p><?php _e('Score updated.', 'the-rink-society'); ?></p></div>
    <?php endif; ?>

    <p><a href="<?php echo admin_url('admin.php?page=trs-games'); ?>" class="button">&larr; <?php _e('Back to Games', 'the-rink-society'); ?></a></p>

    <div class="card" style="max-width: 600px;">
        <h2><?php echo esc_html($home_team->name) . ' vs ' . esc_html($away_team->name); ?></h2>
        <p><strong><?php _e('Date:', 'the-rink-society'); ?></strong> <?php echo trs_format_date($game->game_date); ?></p>
        <p><strong><?php _e('Location:', 'the-rink-society'); ?></strong> <?php echo esc_html($game->location); ?></p>

        <hr>

        <form method="post">
            <?php wp_nonce_field('trs_game_action'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="home_score"><?php echo esc_html($home_team->name); ?> <?php _e('Score', 'the-rink-society'); ?></label></th>
                    <td><input type="number" name="home_score" id="home_score" value="<?php echo $game->home_score !== null ? esc_attr($game->home_score) : ''; ?>" min="0" style="width: 100px; font-size: 24px; text-align: center;" required></td>
                </tr>
                <tr>
                    <th><label for="away_score"><?php echo esc_html($away_team->name); ?> <?php _e('Score', 'the-rink-society'); ?></label></th>
                    <td><input type="number" name="away_score" id="away_score" value="<?php echo $game->away_score !== null ? esc_attr($game->away_score) : ''; ?>" min="0" style="width: 100px; font-size: 24px; text-align: center;" required></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="trs_update_score" class="button button-primary button-large" value="<?php _e('Update Score', 'the-rink-society'); ?>">
            </p>
        </form>

        <hr>

        <p><strong><?php _e('Next Steps:', 'the-rink-society'); ?></strong></p>
        <ul>
            <li><a href="<?php echo admin_url('admin.php?page=trs-stats&game_id=' . $game->id); ?>" class="button button-primary"><?php _e('Enter Player Stats', 'the-rink-society'); ?></a></li>
            <li><a href="<?php echo admin_url('admin.php?page=trs-games&action=edit&id=' . $game->id); ?>"><?php _e('Edit game details', 'the-rink-society'); ?></a></li>
        </ul>
    </div>
</div>
