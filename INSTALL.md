# Installation Guide - The Rink Society

## Quick Start

### Option 1: Manual Installation (Recommended for Development)

1. **Copy plugin to WordPress**
   ```bash
   cp -r the-rink-society /path/to/wordpress/wp-content/plugins/
   ```

2. **Set correct permissions**
   ```bash
   cd /path/to/wordpress/wp-content/plugins/the-rink-society
   chmod -R 755 .
   chown -R www-data:www-data .  # Adjust based on your server
   ```

3. **Activate the plugin**
   - Log into WordPress admin
   - Navigate to **Plugins → Installed Plugins**
   - Find "The Rink Society"
   - Click **Activate**

4. **Verify installation**
   - You should see a success message
   - Check that database tables were created (see TESTING.md)

### Option 2: Upload via WordPress Admin

1. **Create a ZIP file**
   ```bash
   zip -r the-rink-society.zip the-rink-society/
   ```

2. **Upload in WordPress**
   - Go to **Plugins → Add New → Upload Plugin**
   - Choose the ZIP file
   - Click **Install Now**
   - Click **Activate Plugin**

## Requirements

- **WordPress**: 5.8 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher
- **Disk Space**: ~1 MB (plugin files only, data usage varies)

## Post-Installation

### 1. Verify Database Tables

Run this SQL query in phpMyAdmin or your database client:

```sql
SHOW TABLES LIKE 'wp_trs_%';
```

You should see 11 tables:
- wp_trs_players
- wp_trs_teams
- wp_trs_seasons
- wp_trs_tournaments
- wp_trs_games
- wp_trs_events
- wp_trs_team_players
- wp_trs_tournament_players
- wp_trs_game_rosters
- wp_trs_stats
- wp_trs_event_attendance

### 2. Run Verification Script (Optional)

Access the verification script in your browser:
```
https://yoursite.com/wp-content/plugins/the-rink-society/tests/verify-phase1.php
```

This will test:
- Database structure
- Model creation
- CRUD operations
- Jersey number cascade logic
- Stats aggregation

### 3. Check WordPress Debug Log

If you have WP_DEBUG enabled, check for any errors:
```bash
tail -f /path/to/wordpress/wp-content/debug.log
```

## Configuration

### Default Settings

The plugin sets these default options on activation:

- **Date Format**: `Y-m-d` (2025-01-15)
- **Time Format**: `H:i` (14:30)
- **Jersey Validation**: `flexible` (allows any combination)
- **Default Stat Types**: goal, assist, penalty, shot, save

### Customizing Settings

You can modify these in WordPress options:

```php
// In your theme's functions.php or a custom plugin
update_option('trs_date_format', 'm/d/Y');  // US format
update_option('trs_jersey_validation', 'strict');  // Enforce unique jerseys per team
```

## Development Setup

### Enable WordPress Debug Mode

Add to `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Database Access

**Via WP-CLI:**
```bash
wp db query "SELECT * FROM wp_trs_players LIMIT 10"
```

**Via phpMyAdmin:**
- Access your database
- Look for tables prefixed with `wp_trs_`

## Troubleshooting

### Tables Not Created

**Symptom**: No `wp_trs_*` tables in database

**Solutions**:
1. Check database user permissions:
   ```sql
   SHOW GRANTS FOR 'your_db_user'@'localhost';
   ```
   Should include `CREATE, ALTER, DROP`

2. Manually run activator:
   ```bash
   wp eval 'require_once "wp-content/plugins/the-rink-society/includes/class-trs-activator.php"; TRS_Activator::activate();'
   ```

3. Check WordPress debug log for SQL errors

### Plugin Won't Activate

**Symptom**: Error message on activation

**Solutions**:
1. Check PHP version:
   ```bash
   php -v  # Should be 7.4+
   ```

2. Check for fatal errors:
   ```bash
   tail /path/to/wordpress/wp-content/debug.log
   ```

3. Verify file permissions:
   ```bash
   ls -la /path/to/wordpress/wp-content/plugins/the-rink-society/
   ```

### Autoloader Not Finding Classes

**Symptom**: "Class 'TRS_Player' not found"

**Solutions**:
1. Verify file naming:
   ```bash
   ls includes/models/class-trs-*.php
   ```
   Files must be named `class-trs-{name}.php`

2. Check case sensitivity (Linux):
   ```bash
   # Filenames must match exactly
   class-trs-player.php  # ✓ Correct
   Class-TRS-Player.php  # ✗ Wrong
   ```

3. Clear PHP opcache:
   ```php
   opcache_reset();
   ```

## Upgrading

### From Future Versions

When upgrading from a future version:

1. **Backup your database** first!
   ```bash
   wp db export backup-$(date +%Y%m%d).sql
   ```

2. **Deactivate the plugin** (do NOT delete)

3. **Replace plugin files** with new version

4. **Reactivate the plugin**
   - Database migrations will run automatically
   - Check version: `get_option('trs_db_version')`

## Uninstalling

### Keep Data (Deactivate Only)

1. Go to **Plugins → Installed Plugins**
2. Click **Deactivate** under "The Rink Society"
3. Data remains in database

### Remove Everything

1. **Deactivate** the plugin first
2. Click **Delete** under "The Rink Society"
3. Edit `uninstall.php` and uncomment the deletion sections
4. Delete the plugin via WordPress admin

**⚠️ Warning**: This will permanently delete ALL hockey data!

## Next Steps

After installation:

1. ✓ Verify database tables exist (TESTING.md)
2. ✓ Run verification script
3. → Proceed to Phase 2: Admin Interfaces
4. → Create your first season, teams, and players

## Support

For issues or questions:
- Check TESTING.md for verification steps
- Review WordPress debug log
- Ensure all requirements are met
