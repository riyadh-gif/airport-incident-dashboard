<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Flight;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Seed demo accounts and synthetic incident/flight data.
     *
     * Demo credentials (login is by NIP + password):
     *   - Admin    -> NIP: 10001, password: password
     *   - Operator -> NIP: 10002, password: password
     *
     * All names/data are synthetic (Faker id_ID). No real PII.
     */
    public function run(): void
    {
        // Demo admin account.
        User::updateOrCreate(
            ['nip' => '10001'],
            [
                'name' => 'Admin Demo',
                'role' => 'admin',
                'email' => 'admin.demo@example.com',
                'password' => Hash::make('password'),
            ],
        );

        // Demo operator account.
        User::updateOrCreate(
            ['nip' => '10002'],
            [
                'name' => 'Operator Demo',
                'role' => 'operator',
                'email' => 'operator.demo@example.com',
                'password' => Hash::make('password'),
            ],
        );

        // Synthetic domain data spread across the last ~30 days.
        Incident::factory()->count(60)->create();
        Flight::factory()->count(40)->create();
    }
}
