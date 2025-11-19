/**
 * Public Frontend JavaScript
 * 
 * @package TheRinkSociety
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Add any public-facing JavaScript here
        
        // Example: Make tables responsive
        $('.trs-roster-table, .trs-schedule-table, .trs-leaderboard-table, .trs-standings-table, .trs-stats-table').wrap('<div style="overflow-x:auto;"></div>');
    });

})(jQuery);
