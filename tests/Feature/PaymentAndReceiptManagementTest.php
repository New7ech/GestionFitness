<?php

namespace Tests\Feature;

use App\Enums\PaymentMode;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Challenge;
use App\Models\Paiement;
use App\Models\Recu;
use App\Models\User;
use App\Services\PaymentService;
use Carbon\Carbon;
use Database\Seeders\FitnessReferenceSeeder;
use Database\Seeders\ImproveRolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PaymentAndReceiptManagementTest extends TestCase
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
    public function creation_paiement_partiel_recalcule_le_statut_et_le_solde(): void
    {
        $manager = $this->manager();
        $challenge = Challenge::factory()->create(['price' => 30000]);

        $response = $this->actingAs($manager)->post(route('payments.store'), [
            'challenge_id' => $challenge->id,
            'amount' => 10000,
            'type' => PaymentType::Paiement->value,
            'payment_date' => '2026-08-11',
            'payment_mode' => PaymentMode::Especes->value,
        ]);

        $paiement = Paiement::query()->firstOrFail();

        $response->assertRedirect(route('payments.show', $paiement));
        $this->assertSame(PaymentStatus::PartiellementPaye, $challenge->fresh()->payment_status);
        $this->assertSame(20000.0, app(PaymentService::class)->remainingAmount($challenge->fresh()));
        $this->assertSame($manager->id, $paiement->recorded_by);
    }

    #[Test]
    public function statut_paiement_couvre_impaye_paye_et_rembourse(): void
    {
        $manager = $this->manager();
        $service = app(PaymentService::class);
        $challenge = Challenge::factory()->create(['price' => 30000]);

        $paiement = $service->create([
            'challenge_id' => $challenge->id,
            'amount' => 30000,
            'type' => PaymentType::Paiement->value,
            'payment_date' => '2026-08-11',
            'payment_mode' => PaymentMode::Carte->value,
        ], $manager->id);

        $this->assertSame(PaymentStatus::Paye, $challenge->fresh()->payment_status);

        $service->create([
            'challenge_id' => $challenge->id,
            'amount' => 5000,
            'type' => PaymentType::Remboursement->value,
            'payment_date' => '2026-08-12',
            'payment_mode' => PaymentMode::Carte->value,
        ], $manager->id);

        $this->assertSame(PaymentStatus::Rembourse, $challenge->fresh()->payment_status);

        $service->delete($paiement->fresh());
        $this->assertSame(PaymentStatus::Impaye, $challenge->fresh()->payment_status);
    }

    #[Test]
    public function generation_recu_est_sequentielle_et_idempotente_par_paiement(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-11 10:00:00'));

        $manager = $this->manager();
        $challenge = Challenge::factory()->create(['price' => 30000]);
        $paiement = app(PaymentService::class)->create([
            'challenge_id' => $challenge->id,
            'amount' => 10000,
            'type' => PaymentType::Paiement->value,
            'payment_date' => '2026-08-11',
            'payment_mode' => PaymentMode::MobileMoney->value,
        ], $manager->id);

        $this->actingAs($manager)
            ->post(route('payments.recu.store', $paiement))
            ->assertRedirect();

        $firstReceipt = Recu::query()->firstOrFail();

        $this->assertSame('REC-2026-0001', $firstReceipt->receipt_number);
        $this->assertSame('Mobile money', $firstReceipt->payment_mode);
        $this->assertSame(20000.0, (float) $firstReceipt->amount_remaining);
        $this->assertSame($manager->name, $firstReceipt->issued_by_name);

        $this->actingAs($manager)
            ->post(route('payments.recu.store', $paiement))
            ->assertRedirect(route('recus.show', $firstReceipt));

        $this->assertSame(1, Recu::query()->count());

        Carbon::setTestNow();
    }

    #[Test]
    public function recu_est_telechargeable_en_pdf(): void
    {
        $manager = $this->manager();
        $paiement = app(PaymentService::class)->create([
            'challenge_id' => Challenge::factory()->create(['price' => 30000])->id,
            'amount' => 30000,
            'type' => PaymentType::Paiement->value,
            'payment_date' => '2026-08-11',
            'payment_mode' => PaymentMode::Virement->value,
        ], $manager->id);

        $this->actingAs($manager)->post(route('payments.recu.store', $paiement));

        $recu = Recu::query()->firstOrFail();

        $response = $this->actingAs($manager)->get(route('recus.pdf', $recu));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString($recu->receipt_number, $response->headers->get('content-disposition'));
    }

    #[Test]
    public function coach_ne_peut_pas_acceder_aux_routes_paiements_et_recus(): void
    {
        $coach = User::factory()->create();
        $coach->assignRole('coach');
        $paiement = Paiement::factory()->create();
        $recu = Recu::query()->create([
            'payment_id' => $paiement->id,
            'receipt_number' => 'REC-2026-0099',
            'issued_at' => now(),
            'participante_full_name' => 'Test Coach',
            'challenge_type_label' => 'Perte de poids',
            'challenge_duration_days' => 15,
            'amount_paid' => 10000,
            'amount_remaining' => 0,
            'payment_mode' => 'Espèces',
            'issued_by_name' => 'Manager',
            'generated_by' => null,
        ]);

        $this->actingAs($coach)->get(route('payments.index'))->assertForbidden();
        $this->actingAs($coach)->get(route('payments.show', $paiement))->assertForbidden();
        $this->actingAs($coach)->post(route('payments.recu.store', $paiement))->assertForbidden();
        $this->actingAs($coach)->get(route('recus.show', $recu))->assertForbidden();
    }

    private function manager(): User
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        return $manager;
    }
}
