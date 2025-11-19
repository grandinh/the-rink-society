# The Rink Society - Hockey Management Plugin

A comprehensive WordPress plugin for managing hockey leagues, tournaments, teams, and player statistics in community-driven hockey organizations.

**Live Demo**: [The Rink Society - Chiang Mai, Thailand](https://therinksociety.com)

## Features

### Core Management
- **Player Profiles**: Link WordPress users to player profiles with position, handedness, and stats
- **Team Rosters**: Many-to-many relationships - players can be on multiple teams simultaneously
- **Seasons & Tournaments**: Organize games and track standings
- **Game Scheduling**: Schedule games with date, time, location, and status tracking
- **Events**: Manage practices, meetings, fundraisers, and social events with attendance tracking

### Stats System
- **Context-Grounded Stats**: Every stat tied to specific game + team + player
- **Jersey Number Cascade**: Player default → team override → game override
- **Game Rosters**: Select which players participated in each game
- **Stat Entry**: Track goals, assists, and penalty minutes per player per game
- **Leaderboards**: Automatic rankings for points, goals, assists

### Admin Interface
- **Dashboard**: Overview with quick stats and recent games
- **Full CRUD**: Create, read, update, delete for all entities
- **Roster Management**: Assign players to teams with jersey numbers and roles
- **Tournament Standings**: Automatic win/loss/tie records and points
- **Settings**: Configure date formats, stats tracking, and permissions

### Public Display
- **6 Shortcodes**: Display rosters, schedules, stats on any page
- **Responsive Design**: Mobile-friendly tables and layouts
- **Filtering**: View stats by season, tournament, or team

## Philosophy

This plugin models the **real world as it is**, not as a perfectly organized league office thinks it should be.

- Players can be on multiple teams at once
- Teams can be thrown together for a single tournament
- Rosters can change constantly (soft deletes preserve history)
- Jersey numbers can vary by team and by game
- Stats are always tied to specific game context (no ambiguity)

The data model remains consistent and clean, but the rules are minimal and non-blocking.

## Installation

1. Upload the plugin files to `/wp-content/plugins/the-rink-society/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Navigate to **Hockey Manager** in the admin menu
4. Follow the Getting Started checklist on the Dashboard

## Quick Start Guide

### 1. Create a Season
Navigate to **Hockey Manager > Seasons** and create your first season (e.g., "Winter 2025").

### 2. Add Teams
Go to **Hockey Manager > Teams** and create teams with names and colors.

### 3. Add Players
Create WordPress user accounts first, then go to **Hockey Manager > Players** and link users to player profiles.

### 4. Build Rosters
From **Teams**, click "Roster" and assign players with their jersey numbers and roles (captain, etc.).

### 5. Schedule Games
Go to **Hockey Manager > Games** and schedule matchups between teams.

### 6. Enter Stats
After a game is played:
1. Click "Stats" from the game
2. Select which players participated (roster)
3. Enter goals, assists, penalties for each player
4. Save!

## Shortcodes

### Player Profile
Display a player's information:
```
[trs_player_profile id="1"]
```

### Team Roster
Show a team's full roster:
```
[trs_team_roster id="1"]
```

### Games Schedule
Display upcoming/recent games:
```
[trs_games_schedule limit="10" season_id="1"]
[trs_games_schedule team_id="2" status="scheduled"]
```

**Parameters**:
- `season_id` - Filter by season
- `tournament_id` - Filter by tournament
- `team_id` - Filter by team
- `limit` - Number of games (default: 10)
- `status` - scheduled, in-progress, completed, cancelled

### Leaderboard
Display stats leaders:
```
[trs_leaderboard type="points" limit="10"]
[trs_leaderboard type="goals" season_id="1"]
[trs_leaderboard type="assists" tournament_id="2"]
```

**Types**: `points`, `goals`, `assists`

### Tournament Standings
Show tournament standings table:
```
[trs_tournament_standings id="1"]
```

### Player Stats
Display a player's totals:
```
[trs_player_stats id="1"]
[trs_player_stats id="1" season_id="1"]
```

## Database Schema

The plugin creates 11 custom tables:

| Table | Purpose |
|-------|---------|
| `trs_players` | Player profiles (linked to WP users) |
| `trs_teams` | Teams |
| `trs_seasons` | Seasons |
| `trs_tournaments` | Tournaments |
| `trs_games` | Games |
| `trs_events` | Non-game events |
| `trs_team_players` | Player-team assignments (many-to-many) |
| `trs_tournament_players` | Player-tournament relationships |
| `trs_game_rosters` | Per-game rosters with jersey overrides |
| `trs_stats` | Game stats (game + team + player context) |
| `trs_event_attendance` | Event attendance tracking |

## Jersey Number System

The plugin implements a **3-level cascade** for jersey numbers:

1. **Player Default**: `trs_players.preferred_jersey_number`
2. **Team Override**: `trs_team_players.jersey_number` (if assigned to team)
3. **Game Override**: `trs_game_rosters.jersey_number` (if playing in specific game)

The system always uses the most specific number available.

## Stats Context

All stats are **context-grounded** - every stat row requires:
- `game_id` - Which game?
- `team_id` - Which team was the player on?
- `player_id` - Which player?

This eliminates ambiguity when players appear on multiple teams.

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Development Roadmap

**✅ Phase 1** - Core Foundation
- Database schema
- Models and repositories
- Plugin architecture

**✅ Phase 2** - Admin Interfaces
- Dashboard with stats overview
- Full CRUD for all entities
- Roster and tournament management

**✅ Phase 3** - Stats Entry
- Game roster selection
- Stats entry form
- Leaderboards

**✅ Phase 4** - Public Display
- 6 shortcodes
- Responsive templates
- Frontend styling

**🚧 Phase 5** - Polish & Advanced Features (Current)
- REST API
- Import/Export
- Documentation
- Advanced features

## File Structure

```
the-rink-society/
├── admin/
│   ├── class-trs-admin.php       # Admin menu & pages
│   ├── pages/                     # Page controllers
│   └── views/                     # Admin templates
├── includes/
│   ├── class-trs-core.php        # Core plugin class
│   ├── class-trs-activator.php   # Database setup
│   ├── models/                    # Entity models
│   ├── repositories/              # Data access layer
│   └── helpers/                   # Helper functions
├── public/
│   ├── class-trs-shortcodes.php  # Shortcode handlers
│   ├── templates/                 # Public templates
│   └── assets/                    # CSS/JS
└── the-rink-society.php          # Main plugin file
```

## Testing

Sample data generator included:
```
/wp-admin/ → Hockey Manager → Tests → Generate Sample Data
```

Creates:
- 15 players
- 4 teams
- 1 season & tournament
- 6 games with stats

## Support

- **GitHub**: [github.com/grandinh/the-rink-society](https://github.com/grandinh/the-rink-society)
- **Issues**: Report bugs on GitHub Issues
- **Website**: [therinksociety.com](https://therinksociety.com)

## License

GPL v2 or later

## Credits

Developed for The Rink Society - Chiang Mai, Thailand

Built with ❤️ and 🏒

---

**Version**: 0.5.0
**Last Updated**: January 2025
