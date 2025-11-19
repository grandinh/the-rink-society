# The Rink Society - Hockey Management Plugin

A flexible WordPress plugin for managing hockey leagues, tournaments, teams, and player statistics in community-driven hockey organizations.

## Features

- **Player Management**: Link WordPress users to player profiles with stats tracking
- **Team Rosters**: Flexible many-to-many relationships - players can be on multiple teams
- **Seasons & Tournaments**: Organize games into seasons and tournaments
- **Game Management**: Track scores, rosters, and stats for each game
- **Stats System**: Record goals, assists, penalties, saves, and more
- **Events**: Manage practices, meetings, drafts, and social events
- **Flexible Jersey Numbers**: Player default → team override → game override cascade

## Philosophy

This plugin models the **real world as it is**, not as a perfectly organized league office thinks it should be. Players can appear in weird combinations, teams can be thrown together for a single tournament, and rosters can change constantly. The data model remains consistent and clean, but the rules are minimal and non-blocking.

## Installation

1. Upload the plugin files to `/wp-content/plugins/the-rink-society/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Navigate to 'Hockey Manager' in the admin menu to get started

## Database Schema

The plugin creates 11 custom tables:

- `trs_players` - Player profiles (linked to WP users)
- `trs_teams` - Teams
- `trs_seasons` - Seasons
- `trs_tournaments` - Tournaments
- `trs_games` - Games
- `trs_events` - Non-game events (practices, meetings, etc.)
- `trs_team_players` - Player-team relationships (many-to-many)
- `trs_tournament_players` - Player-tournament relationships
- `trs_game_rosters` - Per-game rosters
- `trs_stats` - Game stats (always tied to game + team + player)
- `trs_event_attendance` - Event attendance tracking

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Version

**Current Version**: 0.1.0 (Phase 1 - Core Foundation)

## Development Status

**Phase 1** (Core Foundation) ✓ Complete:
- Database schema implemented
- All models and repositories created
- WordPress integration ready
- Plugin activation/deactivation hooks

**Phase 2** (Admin Interfaces) - Coming Next

## License

GPL v2 or later

## Credits

Developed for The Rink Society - Chiang Mai, Thailand
https://therinksociety.com
