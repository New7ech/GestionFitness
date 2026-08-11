<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Models\Challenge;
use App\Models\Participante;
use App\Models\Presence;
use App\Models\User;
use Database\Seeders\FitnessReferenceSeeder;
use Database\Seeders\ImproveRolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PresenceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ImproveRolesAndPermissionsSeeder::class);
        $this->seed(FitnessReferenceSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    #[Test]
    public function manager_peut_enregistrer_une_presence(): void
    {
        $manager = $this->manager();
        $challenge = $this->challenge();

        $response = $this->actingAs($manager)->post(route('presences.store'), [
            'challenge_id' => $challenge->id,
            'attendance_date' => '2026-08-11',
            'status' => AttendanceStatus::Presente->value,
            'comment' => 'Séance complète.',
        ]);

        $presence = Presence::query()->firstOrFail();

        $response->assertRedirect(route('presences.show', $presence));
        $this->assertSame($challenge->id, $presence->challenge_id);
        $this->assertSame(AttendanceStatus::Presente, $presence->status);
        $this->assertSame($manager->id, $presence->recorded_by);
        $this->assertSame($manager->id, $presence->updated_by);
    }

    #[Test]
    public function creation_presence_valide_les_champs_obligatoires(): void
    {
        $manager = $this->manager();

        $response = $this->actingAs($manager)
            ->from(route('presences.create'))
            ->post(route('presences.store'), [
                'challenge_id' => '',
                'attendance_date' => '',
                'status' => '',
            ]);

        $response->assertRedirect(route('presences.create'));
        $response->assertSessionHasErrors(['challenge_id', 'attendance_date', 'status']);
        $this->assertDatabaseCount('presences', 0);
    }

    #[Test]
    public function une_seule_presence_est_autorisee_par_challenge_et_par_date(): void
    {
        $manager = $this->manager();
        $challenge = $this->challenge();

        Presence::factory()->create([
            'challenge_id' => $challenge->id,
            'attendance_date' => '2026-08-11',
            'status' => AttendanceStatus::Presente,
        ]);

        $response = $this->actingAs($manager)
            ->from(route('presences.create', ['challenge_id' => $challenge->id]))
            ->post(route('presences.store'), [
                'challenge_id' => $challenge->id,
                'attendance_date' => '2026-08-11',
                'status' => AttendanceStatus::Absente->value,
            ]);

        $response->assertRedirect(route('presences.create', ['challenge_id' => $challenge->id]));
        $response->assertSessionHasErrors('attendance_date');
        $this->assertSame(1, Presence::query()->where('challenge_id', $challenge->id)->count());
    }

    #[Test]
    public function date_presence_doit_rester_dans_la_periode_du_challenge(): void
    {
        $manager = $this->manager();
        $challenge = $this->challenge();

        $response = $this->actingAs($manager)
            ->from(route('presences.create', ['challenge_id' => $challenge->id]))
            ->post(route('presences.store'), [
                'challenge_id' => $challenge->id,
                'attendance_date' => '2026-09-15',
                'status' => AttendanceStatus::Presente->value,
            ]);

        $response->assertRedirect(route('presences.create', ['challenge_id' => $challenge->id]));
        $response->assertSessionHasErrors('attendance_date');
        $this->assertDatabaseCount('presences', 0);
    }

    #[Test]
    public function utilisateur_sans_permission_ne_peut_pas_enregistrer_une_presence(): void
    {
        $user = User::factory()->create();
        $challenge = $this->challenge();

        $this->actingAs($user)->get(route('presences.create'))->assertForbidden();

        $this->actingAs($user)->post(route('presences.store'), [
            'challenge_id' => $challenge->id,
            'attendance_date' => '2026-08-11',
            'status' => AttendanceStatus::Presente->value,
        ])->assertForbidden();

        $this->assertDatabaseCount('presences', 0);
    }

    #[Test]
    public function coach_peut_corriger_une_presence_et_l_audit_est_conserve(): void
    {
        $manager = $this->manager();
        $coach = User::factory()->create();
        $coach->assignRole('coach');
        $challenge = $this->challenge();
        $presence = Presence::factory()->create([
            'challenge_id' => $challenge->id,
            'attendance_date' => '2026-08-11',
            'status' => AttendanceStatus::Presente,
            'recorded_by' => $manager->id,
            'updated_by' => $manager->id,
        ]);

        $response = $this->actingAs($coach)->put(route('presences.update', $presence), [
            'challenge_id' => $challenge->id,
            'attendance_date' => '2026-08-11',
            'status' => AttendanceStatus::Absente->value,
            'comment' => 'Absence confirmée.',
        ]);

        $response->assertRedirect(route('presences.show', $presence));
        $presence->refresh();

        $this->assertSame(AttendanceStatus::Absente, $presence->status);
        $this->assertSame($manager->id, $presence->recorded_by);
        $this->assertSame($coach->id, $presence->updated_by);
        $this->assertSame('Absence confirmée.', $presence->comment);
    }

    #[Test]
    public function fiche_participante_affiche_l_historique_des_presences(): void
    {
        $manager = $this->manager();
        $participante = Participante::factory()->create();
        $challenge = $this->challenge($participante);

        Presence::factory()->create([
            'challenge_id' => $challenge->id,
            'attendance_date' => '2026-08-11',
            'status' => AttendanceStatus::Presente,
            'recorded_by' => $manager->id,
        ]);

        $response = $this->actingAs($manager)->get(route('participantes.show', $participante));

        $response->assertOk();
        $response->assertSee('11/08/2026');
        $response->assertSee(AttendanceStatus::Presente->label());
    }

    private function manager(): User
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        return $manager;
    }

    private function challenge(?Participante $participante = null): Challenge
    {
        return Challenge::factory()->create([
            'participante_id' => $participante?->id ?? Participante::factory(),
            'start_date' => '2026-08-01',
            'duration_days' => 30,
        ]);
    }
}
