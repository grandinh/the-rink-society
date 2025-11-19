# Testing Guide - The Rink Society Plugin

## Phase 1 Testing: Core Foundation

### Prerequisites

- WordPress 5.8+ installation
- PHP 7.4+
- MySQL 5.6+
- Access to WordPress admin panel
- Access to database (phpMyAdmin or similar)

### Installation Steps

1. **Upload Plugin**
   ```bash
   # Copy the plugin folder to WordPress plugins directory
   cp -r the-rink-society /path/to/wordpress/wp-content/plugins/
   ```

2. **Activate Plugin**
   - Log into WordPress admin
   - Navigate to Plugins → Installed Plugins
   - Find "The Rink Society"
   - Click "Activate"

3. **Verify Database Tables Created**

   After activation, check your database. You should see 11 new tables:

   ```sql
   SHOW TABLES LIKE 'wp_trs_%';
   ```

   Expected tables:
   - `wp_trs_players`
   - `wp_trs_teams`
   - `wp_trs_seasons`
   - `wp_trs_tournaments`
   - `wp_trs_games`
   - `wp_trs_events`
   - `wp_trs_team_players`
   - `wp_trs_tournament_players`
   - `wp_trs_game_rosters`
   - `wp_trs_stats`
   - `wp_trs_event_attendance`

4. **Verify Options Created**

   Check WordPress options:
   ```sql
   SELECT * FROM wp_options WHERE option_name LIKE 'trs_%';
   ```

   Expected options:
   - `trs_db_version` = "1.0"
   - `trs_activated_at` = timestamp
   - `trs_date_format` = "Y-m-d"
   - `trs_time_format` = "H:i"
   - `trs_jersey_validation` = "flexible"
   - `trs_default_stat_types` = array

### Manual Testing Checklist

#### Database Structure Tests

- [ ] All 11 tables exist
- [ ] Tables have correct character set and collation
- [ ] All foreign key columns have indexes
- [ ] Primary keys are auto-increment
- [ ] Timestamp columns default to CURRENT_TIMESTAMP

#### Model Tests (via WordPress debug or test script)

Run the verification script (see below) to test:

- [ ] Create a player linked to WordPress user
- [ ] Create a team
- [ ] Create a season
- [ ] Add player to team with jersey number
- [ ] Verify jersey number cascade works
- [ ] Create a game between two teams
- [ ] Add stats to a game
- [ ] Query stats by player
- [ ] Generate leaderboard

#### Edge Cases to Test

- [ ] Player on multiple teams simultaneously
- [ ] Player with different jersey numbers on different teams
- [ ] Game-specific jersey number override
- [ ] Stats aggregation across multiple teams
- [ ] Team record calculation
- [ ] Tournament standings calculation

### Running the Verification Script

1. Install the plugin and activate it
2. Navigate to: `yourdomain.com/wp-content/plugins/the-rink-society/tests/verify-phase1.php`
3. Review the output for any errors
4. Check the database to verify data was created correctly

### Common Issues

**Issue**: Tables not created
- **Solution**: Check WordPress debug log for errors
- **Check**: Database user has CREATE TABLE permissions
- **Try**: Deactivate and reactivate plugin

**Issue**: Autoloader not finding classes
- **Solution**: Verify file naming convention (class-trs-*.php)
- **Check**: Case sensitivity on Linux servers

**Issue**: Foreign key errors
- **Solution**: Ensure MySQL InnoDB engine is being used
- **Check**: Database engine settings

### Database Inspection Queries

```sql
-- Check table structure
DESCRIBE wp_trs_players;
DESCRIBE wp_trs_teams;
DESCRIBE wp_trs_stats;

-- Check indexes
SHOW INDEX FROM wp_trs_stats;

-- Count records (should be 0 after fresh install)
SELECT COUNT(*) FROM wp_trs_players;
SELECT COUNT(*) FROM wp_trs_teams;

-- Verify player-team relationship table structure
DESCRIBE wp_trs_team_players;
```

### Performance Testing

For Phase 1, basic performance metrics:

- Plugin activation should complete in < 5 seconds
- Each model CRUD operation should complete in < 100ms
- Database queries should use proper indexes (check with EXPLAIN)

### Next Phase Readiness

Before moving to Phase 2 (Admin Interfaces):

- [ ] All Phase 1 tests pass
- [ ] No PHP errors in debug log
- [ ] Database tables properly indexed
- [ ] Models can create and retrieve data
- [ ] Autoloader works for all classes
- [ ] Jersey number cascade logic works correctly

---

## Phase 2 Testing (Coming Soon)

Will include:
- Admin menu functionality
- Page rendering tests
- Form submission tests
- AJAX request tests
- User permission tests
