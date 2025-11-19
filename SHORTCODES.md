# Shortcode Usage Guide

Complete guide to using The Rink Society shortcodes in your WordPress pages and posts.

## Quick Reference

| Shortcode | Purpose |
|-----------|---------|
| `[trs_player_profile]` | Player details and info |
| `[trs_team_roster]` | Team roster with positions |
| `[trs_games_schedule]` | Games list with filters |
| `[trs_leaderboard]` | Stats leaders |
| `[trs_tournament_standings]` | Tournament table |
| `[trs_player_stats]` | Player stat summary |

---

## Player Profile

Display a player's profile information including position, shooting hand, and teams.

### Basic Usage
```
[trs_player_profile id="1"]
```

### Parameters
- `id` **(required)** - Player ID

### Example Output
```
John Doe #27
Position: Forward
Shoots: Left
Teams: Ice Dragons, Thunder Wolves
```

### Use Cases
- Player bio pages
- Team member spotlights
- Player directory

---

## Team Roster

Show a complete team roster with jersey numbers and positions.

### Basic Usage
```
[trs_team_roster id="1"]
```

### Parameters
- `id` **(required)** - Team ID

### Example Output
```
Ice Dragons

#  Player         Position
27 John Doe       Forward
15 Jane Smith     Defense
1  Mike Johnson   Goalie
```

### Use Cases
- Team pages
- Roster announcements
- Print programs

---

## Games Schedule

Display a list of scheduled, in-progress, or completed games with filtering.

### Basic Usage
```
[trs_games_schedule]
```

### All Parameters
```
[trs_games_schedule
  season_id="1"
  tournament_id="2"
  team_id="3"
  limit="10"
  status="scheduled"]
```

### Parameters
- `season_id` - Filter by season ID
- `tournament_id` - Filter by tournament ID
- `team_id` - Filter by team ID (shows games where team is home or away)
- `limit` - Number of games to show (default: 10)
- `status` - Filter by status: `scheduled`, `in-progress`, `completed`, `cancelled`

### Examples

**Show next 5 games for a team:**
```
[trs_games_schedule team_id="1" limit="5" status="scheduled"]
```

**Show all games from a tournament:**
```
[trs_games_schedule tournament_id="3"]
```

**Show recent completed games:**
```
[trs_games_schedule status="completed" limit="10"]
```

### Use Cases
- Home page schedule widget
- Team-specific schedules
- Tournament brackets
- Season calendar

---

## Leaderboard

Display top scorers, goal leaders, or assist leaders with optional filtering.

### Basic Usage
```
[trs_leaderboard type="points"]
```

### All Parameters
```
[trs_leaderboard
  type="points"
  season_id="1"
  tournament_id="2"
  limit="10"]
```

### Parameters
- `type` **(required)** - `points`, `goals`, or `assists`
- `season_id` - Filter by season
- `tournament_id` - Filter by tournament
- `limit` - Number of leaders to show (default: 10)

### Examples

**Top 5 goal scorers this season:**
```
[trs_leaderboard type="goals" season_id="1" limit="5"]
```

**Points leaders for a tournament:**
```
[trs_leaderboard type="points" tournament_id="2"]
```

**Assists leaders (all-time):**
```
[trs_leaderboard type="assists" limit="20"]
```

### Output Columns
- **Points**: Rank, Player, G, A, PTS
- **Goals**: Rank, Player, Goals
- **Assists**: Rank, Player, Assists

### Use Cases
- Sidebar widgets
- Season highlights
- Tournament awards
- Player comparisons

---

## Tournament Standings

Display tournament standings with wins, losses, ties, and points.

### Basic Usage
```
[trs_tournament_standings id="1"]
```

### Parameters
- `id` **(required)** - Tournament ID

### Example Output
```
Winter Championship - Standings

Rank Team           W  L  T  PTS  GF  GA  DIFF
1    Ice Dragons    5  1  0  10   23  12  +11
2    Thunder Wolves 4  2  0  8    19  15  +4
3    Steel Hawks    2  3  1  5    14  18  -4
4    Frost Bears    0  6  0  0    8   19  -11
```

### Columns Explained
- **W/L/T** - Wins, Losses, Ties
- **PTS** - Points (2 for win, 1 for tie, 0 for loss)
- **GF/GA** - Goals For / Goals Against
- **DIFF** - Goal Differential

### Use Cases
- Tournament pages
- Live standings during events
- Historical tournament results

---

## Player Stats

Display a player's cumulative statistics with optional filtering.

### Basic Usage
```
[trs_player_stats id="1"]
```

### All Parameters
```
[trs_player_stats
  id="1"
  season_id="2"
  tournament_id="3"]
```

### Parameters
- `id` **(required)** - Player ID
- `season_id` - Filter stats to specific season
- `tournament_id` - Filter stats to specific tournament

### Examples

**Player's season stats:**
```
[trs_player_stats id="5" season_id="1"]
```

**Player's tournament performance:**
```
[trs_player_stats id="5" tournament_id="2"]
```

**Player's all-time stats:**
```
[trs_player_stats id="5"]
```

### Output Columns
- **GP** - Games Played
- **G** - Goals
- **A** - Assists
- **PTS** - Points (G + A)
- **PIM** - Penalty Minutes

### Use Cases
- Player profile pages
- Season recap
- Award nominations
- Contract negotiations 😄

---

## Advanced Examples

### Complete Team Page

Combine multiple shortcodes for a comprehensive team page:

```
<h1>Ice Dragons</h1>

<h2>Current Roster</h2>
[trs_team_roster id="1"]

<h2>Upcoming Games</h2>
[trs_games_schedule team_id="1" status="scheduled" limit="5"]

<h2>Recent Results</h2>
[trs_games_schedule team_id="1" status="completed" limit="5"]
```

### Season Leaderboards Page

Show multiple leaderboards side-by-side:

```html
<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
  <div>
    <h3>Points Leaders</h3>
    [trs_leaderboard type="points" season_id="1" limit="10"]
  </div>
  <div>
    <h3>Goal Scorers</h3>
    [trs_leaderboard type="goals" season_id="1" limit="10"]
  </div>
  <div>
    <h3>Playmakers</h3>
    [trs_leaderboard type="assists" season_id="1" limit="10"]
  </div>
</div>
```

### Player Spotlight Page

Create a complete player profile:

```
<h1>Player Spotlight: John Doe</h1>

[trs_player_profile id="5"]

<h2>Season Stats</h2>
[trs_player_stats id="5" season_id="1"]

<h2>All-Time Stats</h2>
[trs_player_stats id="5"]
```

---

## Styling

All shortcodes include default responsive styling via `trs-public.css`. You can override styles in your theme:

```css
/* Custom table styling */
.trs-leaderboard-table {
  border: 2px solid #333;
}

.trs-leaderboard-table th {
  background: #your-team-color;
}

/* Mobile adjustments */
@media (max-width: 768px) {
  .trs-schedule-table {
    font-size: 12px;
  }
}
```

---

## Finding IDs

To find the ID numbers for shortcode parameters:

1. **Player ID**: Go to **Hockey Manager > Players** and edit a player. The ID is in the URL: `?page=trs-players&action=edit&id=5`
2. **Team ID**: Similar - check URL when editing a team
3. **Season/Tournament ID**: Same process

Or use the WordPress admin to hover over items and check the browser status bar for the ID.

---

## Troubleshooting

**Shortcode displays as text instead of rendering:**
- Make sure you're using the exact shortcode name (copy/paste recommended)
- Check that the plugin is activated

**"Not found" message:**
- Verify the ID exists (check admin panel)
- Make sure you're using the correct ID parameter name

**Formatting issues:**
- Check for theme CSS conflicts
- Disable other plugins temporarily to test
- Add custom CSS to your theme

**No data showing:**
- Confirm you've entered data in the admin panel
- Check that filters (season_id, etc.) match existing data

---

## Support

For more help:
- **GitHub**: [github.com/grandinh/the-rink-society](https://github.com/grandinh/the-rink-society)
- **Documentation**: See README.md
- **Issues**: Report bugs on GitHub
