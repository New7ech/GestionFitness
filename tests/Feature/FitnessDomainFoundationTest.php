<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\ChallengeStatus;
use App\Enums\MeasurementStage;
use App\Enums\MediaType;
use App\Enums\ParticipantStatus;
use App\Enums\PaymentMode;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Challenge;
use App\Models\ChallengeType;
use App\Models\Commentaire;
use App\Models\Inscription;
use App\Models\MeasurementType;
use App\Models\MeasurementValue;
use App\Models\Media;
use App\Models\Mesure;
use App\Models\Paiement;
use App\Models\Participante;
use App\Models\Presence;
use App\Models\Recu;
use App\Models\User;
use Database\Seeders\FitnessReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FitnessDomainFoundationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function seeder_cree_les_referentiels_fitness_de_base(): void
    {
        $this->seed(FitnessReferenceSeeder::class);

        $this->assertDatabaseHas('challenge_types', [
            'code' => 'perte_de_poids',
            'label' => 'Perte de poids',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('challenge_types', [
            'code' => 'diastasis',
            'label' => 'Rééducation de la diastasie',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('measurement_types', [
            'code' => 'hanches',
            'label' => 'Hanches',
            'unit' => 'cm',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function challenge_calcule_automatiquement_sa_date_de_fin(): void
    {
        $challenge = Challenge::factory()->create([
            'start_date' => '2026-08-10',
            'duration_days' => 15,
            'end_date' => '2026-01-01',
        ]);

        $challenge->refresh();

        $this->assertSame('2026-08-25', $challenge->end_date->toDateString());
        $this->assertInstanceOf(ChallengeStatus::class, $challenge->status);
        $this->assertSame(ChallengeStatus::Planifie, $challenge->status);
    }

    #[Test]
    public function participante_peut_avoir_plusieurs_inscriptions(): void
    {
        $participante = Participante::factory()->create();
        $challengeType = ChallengeType::factory()->create();

        $firstChallenge = Challenge::factory()->create([
            'challenge_type_id' => $challengeType->id,
            'duration_days' => 15,
        ]);

        $secondChallenge = Challenge::factory()->create([
            'challenge_type_id' => $challengeType->id,
            'duration_days' => 30,
        ]);

        $firstInscription = Inscription::factory()->create([
            'participante_id' => $participante->id,
            'challenge_id' => $firstChallenge->id,
        ]);

        $secondInscription = Inscription::factory()->create([
            'participante_id' => $participante->id,
            'challenge_id' => $secondChallenge->id,
        ]);

        $participante->load('inscriptions');

        $this->assertCount(2, $participante->inscriptions);
        $this->assertTrue($firstInscription->participante->is($participante));
        $this->assertTrue($secondInscription->participante->is($participante));
    }

    #[Test]
    public function factories_creent_les_modeles_fitness_avec_les_casts_attendus(): void
    {
        $participante = Participante::factory()->create();
        $measurementType = MeasurementType::factory()->create();
        $challenge = Challenge::factory()->create();
        $inscription = Inscription::factory()->create([
            'participante_id' => $participante->id,
            'challenge_id' => $challenge->id,
        ]);

        $this->assertInstanceOf(ParticipantStatus::class, $participante->status);
        $this->assertSame(ParticipantStatus::Active, $participante->status);
        $this->assertTrue($measurementType->is_active);
        $this->assertNotNull($challenge->challengeType);
        $this->assertNotNull($challenge->end_date);
        $this->assertInstanceOf(PaymentStatus::class, $inscription->payment_status);
    }

    #[Test]
    public function relations_historiques_fitness_sont_operationnelles(): void
    {
        $user = User::factory()->create();
        $participante = Participante::factory()->create(['created_by' => $user->id]);
        $challenge = Challenge::factory()->create([
            'created_by' => $user->id,
        ]);
        $inscription = Inscription::factory()->create([
            'participante_id' => $participante->id,
            'challenge_id' => $challenge->id,
            'created_by' => $user->id,
        ]);

        $paiement = Paiement::query()->create([
            'inscription_id' => $inscription->id,
            'amount' => 10000,
            'type' => PaymentType::Paiement,
            'payment_date' => '2026-08-10',
            'payment_mode' => PaymentMode::Especes,
            'recorded_by' => $user->id,
        ]);

        $recu = Recu::query()->create([
            'payment_id' => $paiement->id,
            'receipt_number' => 'REC-2026-0001',
            'issued_at' => now(),
            'participante_full_name' => $participante->full_name,
            'challenge_type_label' => $challenge->challengeType->label,
            'challenge_duration_days' => $challenge->duration_days,
            'amount_paid' => 10000,
            'amount_remaining' => 0,
            'payment_mode' => PaymentMode::Especes->value,
            'issued_by_name' => $user->name,
            'generated_by' => $user->id,
        ]);

        $mesure = Mesure::query()->create([
            'inscription_id' => $inscription->id,
            'measured_at' => '2026-08-10',
            'stage' => MeasurementStage::Initiale,
            'weight' => 80,
            'waist' => 95,
            'recorded_by' => $user->id,
        ]);

        $measurementType = MeasurementType::factory()->create();
        $measurementValue = MeasurementValue::query()->create([
            'mesure_id' => $mesure->id,
            'measurement_type_id' => $measurementType->id,
            'value' => 101,
        ]);

        $presence = Presence::query()->create([
            'inscription_id' => $inscription->id,
            'attendance_date' => '2026-08-10',
            'status' => AttendanceStatus::Presente,
            'recorded_by' => $user->id,
        ]);

        $media = Media::query()->create([
            'mediable_type' => Mesure::class,
            'mediable_id' => $mesure->id,
            'type' => MediaType::Photo,
            'stage' => MeasurementStage::Initiale,
            'disk_path' => 'participantes/1/challenges/1/photo.jpg',
            'original_filename' => 'photo.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => 1024,
            'uploaded_by' => $user->id,
        ]);

        $commentaire = Commentaire::query()->create([
            'commentable_type' => Participante::class,
            'commentable_id' => $participante->id,
            'body' => 'Suivi initial.',
            'created_by' => $user->id,
        ]);

        $this->assertTrue($paiement->inscription->is($inscription));
        $this->assertTrue($paiement->recu->is($recu));
        $this->assertTrue($recu->paiement->is($paiement));
        $this->assertTrue($mesure->inscription->is($inscription));
        $this->assertTrue($mesure->values->contains($measurementValue));
        $this->assertTrue($measurementValue->measurementType->is($measurementType));
        $this->assertTrue($presence->inscription->is($inscription));
        $this->assertTrue($media->mediable->is($mesure));
        $this->assertTrue($commentaire->commentable->is($participante));
        $this->assertTrue($inscription->recus->contains($recu));
    }
}
