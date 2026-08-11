<?php

namespace Tests\Feature;

use App\Models\Participante;
use App\Models\User;
use Database\Seeders\ImproveRolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ParticipanteManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('participant_media');
        $this->seed(ImproveRolesAndPermissionsSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    #[Test]
    public function manager_peut_creer_une_participante_avec_photo_privee(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $response = $this->actingAs($manager)->post(route('participantes.store'), [
            'first_name' => 'Aminata',
            'last_name' => 'Diallo',
            'phone' => '620000001',
            'email' => 'aminata@example.com',
            'address' => 'Ouagadougou',
            'photo' => UploadedFile::fake()->image('portrait.jpg', 300, 300)->size(500),
            'birthdate' => '1995-03-20',
            'status' => 'active',
            'has_cesarean' => '0',
            'registration_date' => '2026-08-11',
        ]);

        $participante = Participante::query()->where('phone', '620000001')->firstOrFail();

        $response->assertRedirect(route('participantes.show', $participante));
        $this->assertDatabaseHas('participantes', [
            'first_name' => 'Aminata',
            'last_name' => 'Diallo',
            'phone' => '620000001',
            'created_by' => $manager->id,
        ]);
        $this->assertNotNull($participante->photo_path);
        $this->assertStringStartsWith("participantes/{$participante->id}/profile/", $participante->photo_path);
        Storage::disk('participant_media')->assertExists($participante->photo_path);
    }

    #[Test]
    public function creation_participante_valide_nom_prenom_et_telephone(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $response = $this->actingAs($manager)->post(route('participantes.store'), [
            'first_name' => '',
            'last_name' => '',
            'phone' => '',
            'status' => 'active',
            'registration_date' => '2026-08-11',
        ]);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'phone']);
        $this->assertDatabaseCount('participantes', 0);
    }

    #[Test]
    public function coach_ne_peut_pas_creer_une_participante(): void
    {
        $coach = User::factory()->create();
        $coach->assignRole('coach');

        $this->actingAs($coach)->get(route('participantes.create'))->assertForbidden();

        $this->actingAs($coach)->post(route('participantes.store'), [
            'first_name' => 'Awa',
            'last_name' => 'Traore',
            'phone' => '620000002',
            'status' => 'active',
            'registration_date' => '2026-08-11',
        ])->assertForbidden();

        $this->assertDatabaseCount('participantes', 0);
    }

    #[Test]
    public function telephone_doublon_affiche_un_avertissement_sans_bloquer_apres_confirmation(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        Participante::factory()->create([
            'first_name' => 'Mariam',
            'last_name' => 'Kone',
            'phone' => '620000003',
        ]);

        $payload = [
            'first_name' => 'Fatou',
            'last_name' => 'Sow',
            'phone' => '620000003',
            'email' => 'fatou@example.com',
            'status' => 'active',
            'has_cesarean' => '0',
            'registration_date' => '2026-08-11',
        ];

        $this->actingAs($manager)
            ->post(route('participantes.store'), $payload)
            ->assertRedirect()
            ->assertSessionHas('warning');

        $this->assertDatabaseMissing('participantes', ['email' => 'fatou@example.com']);

        $this->actingAs($manager)
            ->post(route('participantes.store'), $payload + ['confirm_duplicate_phone' => '1'])
            ->assertRedirect();

        $this->assertDatabaseHas('participantes', ['email' => 'fatou@example.com']);
    }

    #[Test]
    public function photo_participante_est_servie_uniquement_aux_utilisateurs_autorises(): void
    {
        $participante = Participante::factory()->create([
            'photo_path' => 'participantes/1/profile/avatar.jpg',
        ]);
        Storage::disk('participant_media')->put($participante->photo_path, 'private-photo-content');

        $this->get(route('participantes.photo', $participante))->assertRedirect(route('login'));

        $userWithoutPermission = User::factory()->create();
        $this->actingAs($userWithoutPermission)
            ->get(route('participantes.photo', $participante))
            ->assertForbidden();

        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $response = $this->actingAs($manager)->get(route('participantes.photo', $participante));

        $response->assertOk();
        $this->assertSame('private-photo-content', $response->streamedContent());
    }
}
