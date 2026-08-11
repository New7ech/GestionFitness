<?php

namespace Tests\Feature;

use App\Enums\ChallengeStatus;
use App\Enums\PaymentMode;
use App\Enums\PaymentType;
use App\Models\Challenge;
use App\Models\ChallengeType;
use App\Models\Paiement;
use App\Models\Participante;
use App\Models\User;
use Database\Seeders\FitnessReferenceSeeder;
use Database\Seeders\ImproveRolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ChallengeManagementTest extends TestCase
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
    public function manager_peut_creer_un_challenge_avec_date_de_fin_calculee(): void
    {
        $manager = $this->manager();
        $participante = Participante::factory()->create();
        $challengeType = ChallengeType::query()->where('code', 'perte_de_poids')->firstOrFail();

        $response = $this->actingAs($manager)->post(route('challenges.store'), [
            'participante_id' => $participante->id,
            'challenge_type_id' => $challengeType->id,
            'start_date' => '2026-08-10',
            'duration_days' => 15,
            'status' => 'planifie',
            'price' => 30000,
            'goal_weight' => 70,
            'goal_waist' => 85,
        ]);

        $challenge = Challenge::query()->firstOrFail();

        $response->assertRedirect(route('challenges.show', $challenge));
        $this->assertSame('2026-08-25', $challenge->end_date->toDateString());
        $this->assertSame(ChallengeStatus::Planifie, $challenge->status);
        $this->assertSame($manager->id, $challenge->created_by);
    }

    #[Test]
    public function duree_du_challenge_est_validee_depuis_la_config(): void
    {
        $manager = $this->manager();
        $participante = Participante::factory()->create();
        $challengeType = ChallengeType::query()->where('code', 'perte_de_poids')->firstOrFail();

        $response = $this->actingAs($manager)->post(route('challenges.store'), [
            'participante_id' => $participante->id,
            'challenge_type_id' => $challengeType->id,
            'start_date' => '2026-08-10',
            'duration_days' => 14,
            'status' => 'planifie',
            'price' => 30000,
        ]);

        $response->assertSessionHasErrors('duration_days');
        $this->assertDatabaseCount('challenges', 0);
    }

    #[Test]
    public function coach_ne_peut_pas_creer_de_challenge(): void
    {
        $coach = User::factory()->create();
        $coach->assignRole('coach');

        $this->actingAs($coach)->get(route('challenges.create'))->assertForbidden();
        $this->actingAs($coach)->post(route('challenges.store'), [])->assertForbidden();
    }

    #[Test]
    public function participante_peut_afficher_plusieurs_challenges_dans_son_historique(): void
    {
        $manager = $this->manager();
        $participante = Participante::factory()->create([
            'first_name' => 'Aminata',
            'last_name' => 'Diallo',
        ]);
        $challengeType = ChallengeType::query()->where('code', 'diastasis')->firstOrFail();

        Challenge::factory()->create([
            'participante_id' => $participante->id,
            'challenge_type_id' => $challengeType->id,
            'start_date' => '2026-08-01',
            'duration_days' => 15,
        ]);
        Challenge::factory()->create([
            'participante_id' => $participante->id,
            'challenge_type_id' => $challengeType->id,
            'start_date' => '2026-09-01',
            'duration_days' => 30,
        ]);

        $response = $this->actingAs($manager)->get(route('participantes.show', $participante));

        $response->assertOk();
        $response->assertSee('15 jours');
        $response->assertSee('30 jours');
        $this->assertCount(2, $participante->fresh()->challenges);
    }

    #[Test]
    public function changement_de_planning_avec_historique_demande_confirmation(): void
    {
        $manager = $this->manager();
        $challenge = Challenge::factory()->create([
            'start_date' => '2026-08-10',
            'duration_days' => 15,
            'price' => 30000,
        ]);
        Paiement::query()->create([
            'challenge_id' => $challenge->id,
            'amount' => 10000,
            'type' => PaymentType::Paiement,
            'payment_date' => '2026-08-10',
            'payment_mode' => PaymentMode::Especes,
            'recorded_by' => $manager->id,
        ]);

        $payload = [
            'participante_id' => $challenge->participante_id,
            'challenge_type_id' => $challenge->challenge_type_id,
            'start_date' => '2026-08-10',
            'duration_days' => 30,
            'status' => $challenge->status->value,
            'price' => 30000,
        ];

        $this->actingAs($manager)
            ->put(route('challenges.update', $challenge), $payload)
            ->assertRedirect()
            ->assertSessionHas('warning');

        $this->assertSame('2026-08-25', $challenge->fresh()->end_date->toDateString());

        $this->actingAs($manager)
            ->put(route('challenges.update', $challenge), $payload + ['confirm_schedule_change' => '1'])
            ->assertRedirect(route('challenges.show', $challenge));

        $this->assertSame('2026-09-09', $challenge->fresh()->end_date->toDateString());
    }

    #[Test]
    public function manager_peut_changer_le_statut_du_challenge(): void
    {
        $manager = $this->manager();
        $challenge = Challenge::factory()->create([
            'status' => ChallengeStatus::Planifie,
        ]);

        $this->actingAs($manager)
            ->patch(route('challenges.status', $challenge), [
                'status' => 'en_cours',
            ])
            ->assertRedirect();

        $this->assertSame(ChallengeStatus::EnCours, $challenge->fresh()->status);
    }

    private function manager(): User
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        return $manager;
    }
}
