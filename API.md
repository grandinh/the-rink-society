# REST API Documentation

The Rink Society provides a REST API for accessing player, team, game, and stats data.

**Base URL**: `/wp-json/trs/v1/`

## Authentication

All endpoints are currently public (read-only). Future versions may require authentication for write operations.

## Endpoints

### Players

#### Get All Players
```
GET /wp-json/trs/v1/players
```

**Parameters**:
- `team_id` (optional) - Filter by team

**Response**:
```json
[
  {
    "id": 1,
    "name": "John Doe",
    "jersey_number": "27",
    "position": "forward",
    "shoots": "left"
  }
]
```

#### Get Player
```
GET /wp-json/trs/v1/players/{id}
```

**Response**:
```json
{
  "id": 1,
  "name": "John Doe",
  "jersey_number": "27",
  "position": "forward",
  "shoots": "left",
  "birth_date": "1995-03-15",
  "teams": [
    {"id": 1, "name": "Ice Dragons"}
  ]
}
```

### Teams

#### Get All Teams
```
GET /wp-json/trs/v1/teams
```

**Response**:
```json
[
  {
    "id": 1,
    "name": "Ice Dragons",
    "primary_color": "#0033cc",
    "secondary_color": "#ffffff"
  }
]
```

#### Get Team
```
GET /wp-json/trs/v1/teams/{id}
```

**Response**:
```json
{
  "id": 1,
  "name": "Ice Dragons",
  "primary_color": "#0033cc",
  "secondary_color": "#ffffff",
  "record": {
    "wins": 5,
    "losses": 2,
    "ties": 1
  }
}
```

#### Get Team Roster
```
GET /wp-json/trs/v1/teams/{id}/roster
```

**Response**:
```json
[
  {
    "id": 1,
    "name": "John Doe",
    "jersey_number": "27",
    "position": "forward"
  }
]
```

### Games

#### Get All Games
```
GET /wp-json/trs/v1/games
```

**Parameters**:
- `season_id` (optional)
- `tournament_id` (optional)
- `team_id` (optional)
- `status` (optional) - scheduled, in-progress, completed, cancelled
- `limit` (optional) - default: all

**Response**:
```json
[
  {
    "id": 1,
    "date": "2025-01-20",
    "time": "19:00:00",
    "home_team": {
      "id": 1,
      "name": "Ice Dragons",
      "score": 5
    },
    "away_team": {
      "id": 2,
      "name": "Thunder Wolves",
      "score": 3
    },
    "location": "Main Rink",
    "status": "completed"
  }
]
```

#### Get Game
```
GET /wp-json/trs/v1/games/{id}
```

Returns single game with same structure as above.

### Stats

#### Get Leaderboard
```
GET /wp-json/trs/v1/stats/leaderboard
```

**Parameters**:
- `type` (optional) - points, goals, assists (default: points)
- `season_id` (optional)
- `tournament_id` (optional)
- `limit` (optional) - default: 10

**Response** (type=points):
```json
[
  {
    "player_id": 1,
    "player_name": "John Doe",
    "goals": 12,
    "assists": 15,
    "points": 27
  }
]
```

**Response** (type=goals or assists):
```json
[
  {
    "player_id": 1,
    "player_name": "John Doe",
    "total": 12
  }
]
```

#### Get Player Stats
```
GET /wp-json/trs/v1/stats/player/{id}
```

**Parameters**:
- `season_id` (optional)
- `tournament_id` (optional)

**Response**:
```json
{
  "games_played": 10,
  "goals": 12,
  "assists": 15,
  "penalty_minutes": 8
}
```

### Tournaments

#### Get Tournament Standings
```
GET /wp-json/trs/v1/tournaments/{id}/standings
```

**Response**:
```json
[
  {
    "team_id": 1,
    "team_name": "Ice Dragons",
    "wins": 5,
    "losses": 1,
    "ties": 0,
    "points": 10,
    "goals_for": 23,
    "goals_against": 12
  }
]
```

## Examples

### JavaScript (Fetch API)
```javascript
// Get top 5 scorers
fetch('/wp-json/trs/v1/stats/leaderboard?type=goals&limit=5')
  .then(response => response.json())
  .then(data => console.log(data));

// Get team roster
fetch('/wp-json/trs/v1/teams/1/roster')
  .then(response => response.json())
  .then(roster => console.log(roster));

// Get upcoming games for a team
fetch('/wp-json/trs/v1/games?team_id=1&status=scheduled&limit=5')
  .then(response => response.json())
  .then(games => console.log(games));
```

### cURL
```bash
# Get player stats
curl https://yoursite.com/wp-json/trs/v1/stats/player/1

# Get games for a tournament
curl "https://yoursite.com/wp-json/trs/v1/games?tournament_id=2"

# Get leaderboard
curl "https://yoursite.com/wp-json/trs/v1/stats/leaderboard?type=points&limit=10"
```

### jQuery
```javascript
// Display leaderboard
$.getJSON('/wp-json/trs/v1/stats/leaderboard?type=points&limit=10', function(data) {
  data.forEach(function(player) {
    console.log(`${player.player_name}: ${player.points} points`);
  });
});
```

## Error Responses

**404 Not Found**:
```json
{
  "code": "not_found",
  "message": "Player not found",
  "data": {"status": 404}
}
```

## Rate Limiting

Currently no rate limiting. Be respectful with requests.

## Future Features

- POST/PUT/DELETE endpoints for data modification
- Authentication via API keys
- Webhooks for real-time updates
- Batch operations
- Advanced filtering and sorting

## Support

Questions about the API? Open an issue on GitHub:
https://github.com/grandinh/the-rink-society/issues
