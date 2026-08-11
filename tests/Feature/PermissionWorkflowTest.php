<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PermissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateAdmin(): User
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::query()->firstOrCreate(
            ['name' => 'super_admin'],
            ['guard_name' => 'web']
        );

        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        return $user;
    }

    #[Test]
    public function creation_permission_normalise_le_nom(): void
    {
        $this->authenticateAdmin();

        $response = $this->post(route('permissions.store'), [
            'name' => '  Utilisateurs Creer__Nouveau  ',
        ]);

        $response->assertRedirect(route('permissions.index'));

        $this->assertDatabaseHas('permissions', [
            'name' => 'utilisateurs-creer-nouveau',
            'guard_name' => 'web',
        ]);
    }

    #[Test]
    public function creation_permission_refuse_les_doublons_sur_guard_web(): void
    {
        $this->authenticateAdmin();

        Permission::query()->create([
            'name' => 'users-delete',
            'guard_name' => 'web',
        ]);

        $response = $this->from(route('permissions.create'))
            ->post(route('permissions.store'), [
                'name' => ' USERS_DELETE ',
            ]);

        $response->assertSessionHasErrors('name');

        $this->assertSame(1, Permission::query()->count());
    }

    #[Test]
    public function mise_a_jour_permission_accepte_le_meme_nom_normalise(): void
    {
        $this->authenticateAdmin();

        $permission = Permission::query()->create([
            'name' => 'articles.read',
            'guard_name' => 'web',
        ]);

        $response = $this->put(route('permissions.update', $permission), [
            'name' => '  ARTICLES.READ  ',
        ]);

        $response->assertRedirect(route('permissions.index'));

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'articles.read',
            'guard_name' => 'web',
        ]);
    }
}
