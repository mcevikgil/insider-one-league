<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * 20 Premier Lig takımını veritabanına ekle.
     */
    public function run(): void
    {
        $teams = [
            ['name' => 'Manchester City', 'short_name' => 'MCI', 'strength' => 94, 'attack' => 95, 'defense' => 92, 'squad_depth' => 94, 'form' => 93],
            ['name' => 'Liverpool', 'short_name' => 'LIV', 'strength' => 87, 'attack' => 90, 'defense' => 84, 'squad_depth' => 86, 'form' => 85],
            ['name' => 'Arsenal', 'short_name' => 'ARS', 'strength' => 86, 'attack' => 88, 'defense' => 86, 'squad_depth' => 84, 'form' => 87],
            ['name' => 'Manchester United', 'short_name' => 'MUN', 'strength' => 80, 'attack' => 82, 'defense' => 78, 'squad_depth' => 80, 'form' => 79],
            ['name' => 'Chelsea', 'short_name' => 'CHE', 'strength' => 80, 'attack' => 80, 'defense' => 79, 'squad_depth' => 82, 'form' => 78],
            ['name' => 'Tottenham Hotspur', 'short_name' => 'TOT', 'strength' => 80, 'attack' => 84, 'defense' => 76, 'squad_depth' => 78, 'form' => 80],
            ['name' => 'Newcastle United', 'short_name' => 'NEW', 'strength' => 79, 'attack' => 78, 'defense' => 80, 'squad_depth' => 77, 'form' => 79],
            ['name' => 'Aston Villa', 'short_name' => 'AVL', 'strength' => 77, 'attack' => 77, 'defense' => 76, 'squad_depth' => 75, 'form' => 78],
            ['name' => 'West Ham United', 'short_name' => 'WHU', 'strength' => 74, 'attack' => 75, 'defense' => 74, 'squad_depth' => 73, 'form' => 74],
            ['name' => 'Brighton', 'short_name' => 'BHA', 'strength' => 74, 'attack' => 76, 'defense' => 72, 'squad_depth' => 72, 'form' => 75],
            ['name' => 'Crystal Palace', 'short_name' => 'CRY', 'strength' => 71, 'attack' => 70, 'defense' => 72, 'squad_depth' => 70, 'form' => 71],
            ['name' => 'Wolverhampton', 'short_name' => 'WOL', 'strength' => 70, 'attack' => 69, 'defense' => 71, 'squad_depth' => 69, 'form' => 70],
            ['name' => 'Fulham', 'short_name' => 'FUL', 'strength' => 69, 'attack' => 68, 'defense' => 69, 'squad_depth' => 68, 'form' => 69],
            ['name' => 'Brentford', 'short_name' => 'BRE', 'strength' => 68, 'attack' => 67, 'defense' => 68, 'squad_depth' => 66, 'form' => 68],
            ['name' => 'Everton', 'short_name' => 'EVE', 'strength' => 67, 'attack' => 66, 'defense' => 70, 'squad_depth' => 67, 'form' => 66],
            ['name' => 'Nottingham Forest', 'short_name' => 'NFO', 'strength' => 66, 'attack' => 65, 'defense' => 66, 'squad_depth' => 65, 'form' => 66],
            ['name' => 'Bournemouth', 'short_name' => 'BOU', 'strength' => 65, 'attack' => 64, 'defense' => 65, 'squad_depth' => 64, 'form' => 65],
            ['name' => 'Burnley', 'short_name' => 'BUR', 'strength' => 64, 'attack' => 63, 'defense' => 64, 'squad_depth' => 63, 'form' => 64],
            ['name' => 'Sheffield United', 'short_name' => 'SHU', 'strength' => 61, 'attack' => 60, 'defense' => 62, 'squad_depth' => 60, 'form' => 61],
            ['name' => 'Luton Town', 'short_name' => 'LUT', 'strength' => 59, 'attack' => 58, 'defense' => 60, 'squad_depth' => 58, 'form' => 59],
        ];

        foreach ($teams as $team) {
            Team::create([
                'name' => $team['name'],
                'short_name' => $team['short_name'],
                'strength' => $team['strength'],
                'attack' => $team['attack'],
                'defense' => $team['defense'],
                'squad_depth' => $team['squad_depth'],
                'form' => $team['form'],
                'is_selected' => false,
            ]);
        }
    }
}
