# Insider One Champions League

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?logo=vue.js&logoColor=white)](https://vuejs.org)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

A football league simulation application where you can select 4 teams from 20 Premier League clubs and simulate a complete double round-robin tournament with realistic match outcomes.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Usage](#usage)
- [API Documentation](#api-documentation)
- [Project Structure](#project-structure)
- [Algorithms](#algorithms)

---

## Features

| Feature | Description |
|---------|-------------|
| Team Selection | Choose 4 teams from 20 Premier League clubs |
| Fixture Generation | Automatic round-robin fixture creation (6 weeks, 12 matches) |
| Match Simulation | Realistic score generation using Poisson distribution |
| Live Standings | Real-time league table with points and goal difference |
| Championship Predictions | Monte Carlo simulation for title probability (week 4+) |
| Manual Score Edit | Click any match to manually adjust the result |
| Week Navigation | Browse through all 6 match weeks |

---

## Tech Stack

### Backend
| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 8.2+ | Server-side language |
| Laravel | 12 | PHP Framework |
| SQLite | 3 | Database |

### Frontend
| Technology | Version | Purpose |
|------------|---------|---------|
| Vue.js | 3 | Frontend framework (Composition API) |
| Tailwind CSS | 4 | Utility-first CSS |
| Vite | 6 | Build tool |

---

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- Git

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/mcevikgil/insider-one-league.git
cd insider-one-league

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Environment setup
cp .env.example .env
php artisan key:generate

# 5. Database setup
touch database/database.sqlite
php artisan migrate --seed

# 6. Build assets
npm run build

# 7. Start the server
php artisan serve
```

Open your browser and visit: `http://localhost:8000`

---

## Usage

### Quick Start

1. **Select Teams**: Click on 4 teams from the team grid
2. **Generate Fixtures**: Click "Generate Fixtures" button
3. **Start Simulation**: Click "Start Simulate" to begin
4. **Play Matches**: Use "Play Week" or "Play All Weeks"
5. **View Results**: Check standings and championship predictions

### Development Mode

```bash
# Terminal 1: Start Vite dev server (hot reload)
npm run dev

# Terminal 2: Start Laravel server
php artisan serve
```

---

## Testing

```bash
# Run all tests
php artisan test

# Run only unit tests
php artisan test --testsuite=Unit
```

### Test Coverage

| Test File | Tests | Description |
|-----------|-------|-------------|
| `SimulationServiceTest` | 5 | Poisson distribution, score calculation, home advantage |
| `PredictionServiceTest` | 6 | Monte Carlo simulation, team strength effects |
| `LeagueServiceTest` | 7 | Round-robin fixture generation algorithm |
| `StandingCalculationTest` | 8 | Points calculation, goal difference, sorting |

---

## API Documentation

### Teams

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/teams` | List all teams |
| `POST` | `/api/teams/{id}/toggle-select` | Toggle team selection |

### League

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/league/generate-fixtures` | Generate fixtures |
| `GET` | `/api/league/matches` | Get all matches |
| `GET` | `/api/league/current-week` | Get current week number |
| `POST` | `/api/league/simulate-week` | Simulate current week |
| `POST` | `/api/league/simulate-all` | Simulate all remaining matches |
| `PUT` | `/api/league/matches/{id}` | Update match score |
| `POST` | `/api/league/reset` | Reset league |

### Standings & Predictions

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/standings` | Get league standings |
| `GET` | `/api/predictions` | Get championship odds |

---

## Project Structure

```
insider-one-league/
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── LeagueController.php      # Fixtures, simulation, reset
│   │   ├── TeamController.php        # Team listing, selection
│   │   ├── StandingController.php    # League standings
│   │   └── PredictionController.php  # Championship odds
│   ├── Models/
│   │   ├── Team.php                  # Team model
│   │   └── Game.php                  # Match model
│   └── Services/
│       ├── LeagueService.php         # Fixture generation
│       ├── SimulationService.php     # Match simulation (Poisson)
│       ├── StandingService.php       # Standings calculation
│       └── PredictionService.php     # Monte Carlo predictions
├── resources/js/
│   ├── app.vue                       # Main component
│   ├── composables/useApi.js         # API layer
│   └── components/
│       ├── TeamSelection.vue         # Team selection grid
│       ├── FixtureDisplay.vue        # Fixture list
│       ├── LeagueTable.vue           # Standings table
│       ├── PredictionPanel.vue       # Championship predictions
│       ├── ControlButtons.vue        # Simulation controls
│       └── MatchEditModal.vue        # Score edit modal
├── tests/Unit/
│   ├── SimulationServiceTest.php
│   ├── PredictionServiceTest.php
│   ├── LeagueServiceTest.php
│   └── StandingCalculationTest.php
└── database/
    ├── migrations/
    └── seeders/TeamSeeder.php        # Premier League teams
```

---

## Algorithms

### Match Simulation (Poisson Distribution)

Each team has 4 attributes (0-100):

| Attribute | Weight | Description |
|-----------|--------|-------------|
| Attack | 60% | Offensive capability |
| Defense | 60% | Defensive strength |
| Form | 25% | Current performance |
| Squad Depth | 15% | Team depth quality |

**Expected Goals Formula:**

```
homeExpected = (homeOffense - awayDefense + 50 + HOME_ADVANTAGE) / 50
awayExpected = (awayOffense - homeDefense + 50) / 50
```

Goals are generated using **Poisson random distribution** with the calculated lambda values.

### Championship Prediction (Monte Carlo)

After week 4, the system runs **1000 simulations** of remaining matches:

1. For each simulation, play all unplayed matches
2. Calculate final standings (points, goal difference)
3. Determine the champion
4. Count championships per team
5. Calculate probability: `(team_wins / 1000) * 100%`