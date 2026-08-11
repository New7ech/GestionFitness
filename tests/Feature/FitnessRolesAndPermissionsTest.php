<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ImproveRolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class FitnessRolesAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function seeder_cree_les_roles_et_permissions_fitness_sans_doublon(): void
    {
        $this->seed(ImproveRolesAndPermissionsSeeder::class);
        $this->seed(ImproveRolesAndPermissionsSeeder::class);

        $this->assertSame(1, Role::query()->where('name', 'coach')->count());
        $this->assertSame(1, Permission::query()->where('name', 'show-participantes')->count());
        $this->assertSame(1, Permission::query()->where('name', 'generate-recus')->count());
    }

    #[Test]
    public function manager_et_super_admin_recoivent_toutes_les_permissions_fitness(): void
    {
        $this->seed(ImproveRolesAndPermissionsSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $manager = Role::query()->where('name', 'manager')->firstOrFail();
        $superAdmin = Role::query()->where('name', 'super_admin')->firstOrFail();

        $fitnessPermissions = [
            'show-participantes',
            'generate-recus',
            'delete-measurements',
            'view-activity-log',
        ];

        foreach ($fitnessPermissions as $permission) {
            $this->assertTrue($manager->hasPermissionTo($permission));
            $this->assertTrue($superAdmin->hasPermissionTo($permission));
        }
    }

    #[Test]
    public function coach_recoit_uniquement_les_permissions_operationnelles_autorisees(): void
    {
        $this->seed(ImproveRolesAndPermissionsSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $coach = Role::query()->where('name', 'coach')->firstOrFail();

        foreach ([
            'show-participantes',
            'view-participante-health-data',
            'show-challenges',
            'record-attendance',
            'edit-attendance',
            'record-measurements',
            'edit-measurements',
            'manage-media',
            'manage-comments',
            'view-bilan',
        ] as $permission) {
            $this->assertTrue($coach->hasPermissionTo($permission));
        }

        foreach ([
            'create-participantes',
            'show-payments',
            'create-payments',
            'delete-measurements',
            'generate-recus',
            'view-activity-log',
        ] as $permission) {
            $this->assertFalse($coach->hasPermissionTo($permission));
        }
    }

    #[Test]
    public function role_coach_apparait_dans_l_interface_roles_existante(): void
    {
        $this->seed(ImproveRolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $response = $this->actingAs($user)->get(route('roles.index'));

        $response->assertOk();
        $response->assertSee('coach');
    }
}
