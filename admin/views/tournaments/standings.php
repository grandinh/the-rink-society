<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap">
    <h1><?php echo esc_html($tournament->name); ?> - <?php _e('Standings', 'the-rink-society'); ?></h1>
    
    <p><a href="<?php echo admin_url('admin.php?page=trs-tournaments&action=edit&id=' . $tournament->id); ?>" class="button">&larr; <?php _e('Back to Tournament', 'the-rink-society'); ?></a></p>

    <?php if (empty($standings)) : ?>
        <p><?php _e('No standings data available. Teams need to play games first.', 'the-rink-society'); ?></p>
    <?php else : ?>
        <table class="wp-list-table widefat striped">
            <thead><tr>
                <th><?php _e('Rank', 'the-rink-society'); ?></th>
                <th><?php _e('Team', 'the-rink-society'); ?></th>
                <th><?php _e('W', 'the-rink-society'); ?></th>
                <th><?php _e('L', 'the-rink-society'); ?></th>
                <th><?php _e('T', 'the-rink-society'); ?></th>
                <th><?php _e('PTS', 'the-rink-society'); ?></th>
                <th><?php _e('GF', 'the-rink-society'); ?></th>
                <th><?php _e('GA', 'the-rink-society'); ?></th>
                <th><?php _e('DIFF', 'the-rink-society'); ?></th>
            </tr></thead>
            <tbody>
                <?php $rank = 1; ?>
                <?php foreach ($standings as $standing) : ?>
                    <tr>
                        <td><?php echo $rank++; ?></td>
                        <td><strong><?php echo esc_html($standing['team_name']); ?></strong></td>
                        <td><?php echo $standing['wins']; ?></td>
                        <td><?php echo $standing['losses']; ?></td>
                        <td><?php echo $standing['ties']; ?></td>
                        <td><strong><?php echo $standing['points']; ?></strong></td>
                        <td><?php echo $standing['goals_for']; ?></td>
                        <td><?php echo $standing['goals_against']; ?></td>
                        <td><?php 
                            $diff = $standing['goals_for'] - $standing['goals_against'];
                            echo $diff > 0 ? '+' . $diff : $diff;
                        ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="description"><?php _e('Points: 2 for win, 1 for tie, 0 for loss', 'the-rink-society'); ?></p>
    <?php endif; ?>
</div>
