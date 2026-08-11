<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les rôles et permissions en premier
        $this->call(ImproveRolesAndPermissionsSeeder::class);
        $this->call(FitnessReferenceSeeder::class);

        // Administrateur principal
        $admin = User::firstOrCreate(
            ['email' => 'admin@gestion.local'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('Admin@1234'),
                'status' => true,
                'is_admin' => true,
            ]
        );
        $admin->syncRoles(['super_admin']);

        // Gestionnaire de démonstration
        $manager = User::firstOrCreate(
            ['email' => 'manager@gestion.local'],
            [
                'name' => 'Gestionnaire Demo',
                'password' => Hash::make('Manager@1234'),
                'status' => true,
                'is_admin' => false,
            ]
        );
        $manager->syncRoles(['manager']);

        $this->command->info('');
        $this->command->info('✅ Comptes créés :');
        $this->command->info('   Admin    → admin@gestion.local    / Admin@1234');
        $this->command->info('   Manager  → manager@gestion.local  / Manager@1234');
        $this->command->info('');
    }
}
