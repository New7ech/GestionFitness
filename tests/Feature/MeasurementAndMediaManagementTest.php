<?php

namespace Tests\Feature;

use App\Enums\MeasurementStage;
use App\Enums\MediaType;
use App\Models\Challenge;
use App\Models\Inscription;
use App\Models\MeasurementType;
use App\Models\Media;
use App\Models\Mesure;
use App\Models\User;
use Database\Seeders\FitnessReferenceSeeder;
use Database\Seeders\ImproveRolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class MeasurementAndMediaManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('participant_media');
        $this->seed(ImproveRolesAndPermissionsSeeder::class);
        $this->seed(FitnessReferenceSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    #[Test]
    public function manager_peut_creer_une_mesure_avec_valeurs_complementaires(): void
    {
        $manager = $this->manager();
        $challenge = Challenge::factory()->create();
        $inscription = Inscription::factory()->create([
            'challenge_id' => $challenge->id,
        ]);
        $measurementType = MeasurementType::query()->where('code', 'hanches')->firstOrFail();

        $response = $this->actingAs($manager)->post(route('mesures.store'), [
            'inscription_id' => $inscription->id,
            'measured_at' => '2026-08-11',
            'stage' => MeasurementStage::Initiale->value,
            'weight' => 82.5,
            'waist' => 96.25,
            'measurement_values' => [
                $measurementType->id => 105.75,
            ],
            'comment' => 'Mesure initiale.',
        ]);

        $mesure = Mesure::query()->firstOrFail();
        $value = $mesure->values()->firstOrFail();

        $response->assertRedirect(route('mesures.show', $mesure));
        $this->assertSame($inscription->id, $mesure->inscription_id);
        $this->assertSame($manager->id, $mesure->recorded_by);
        $this->assertSame(82.5, (float) $mesure->weight);
        $this->assertSame($measurementType->id, $value->measurement_type_id);
        $this->assertSame(105.75, (float) $value->value);
    }

    #[Test]
    public function creation_mesure_valide_les_champs_obligatoires(): void
    {
        $manager = $this->manager();

        $response = $this->actingAs($manager)
            ->from(route('mesures.create'))
            ->post(route('mesures.store'), [
                'inscription_id' => '',
                'measured_at' => '',
                'stage' => '',
                'weight' => '',
            ]);

        $response->assertRedirect(route('mesures.create'));
        $response->assertSessionHasErrors(['inscription_id', 'measured_at', 'stage', 'weight']);
        $this->assertDatabaseCount('mesures', 0);
    }

    #[Test]
    public function utilisateur_sans_permission_ne_peut_pas_creer_de_mesure(): void
    {
        $user = User::factory()->create();
        $challenge = Challenge::factory()->create();
        $inscription = Inscription::factory()->create([
            'challenge_id' => $challenge->id,
        ]);

        $this->actingAs($user)->get(route('mesures.create'))->assertForbidden();

        $this->actingAs($user)->post(route('mesures.store'), [
            'inscription_id' => $inscription->id,
            'measured_at' => '2026-08-11',
            'stage' => MeasurementStage::Initiale->value,
            'weight' => 82.5,
        ])->assertForbidden();

        $this->assertDatabaseCount('mesures', 0);
    }

    #[Test]
    public function trois_mesures_successives_restent_toutes_conservees(): void
    {
        $manager = $this->manager();
        $challenge = Challenge::factory()->create();
        $inscription = Inscription::factory()->create([
            'challenge_id' => $challenge->id,
        ]);

        foreach ([82.5, 81.2, 79.9] as $index => $weight) {
            $this->actingAs($manager)->post(route('mesures.store'), [
                'inscription_id' => $inscription->id,
                'measured_at' => now()->addDays($index)->toDateString(),
                'stage' => MeasurementStage::Intermediaire->value,
                'weight' => $weight,
            ])->assertRedirect();
        }

        $weights = Mesure::query()
            ->where('inscription_id', $inscription->id)
            ->orderBy('id')
            ->pluck('weight')
            ->map(fn ($weight): float => (float) $weight)
            ->all();

        $this->assertSame([82.5, 81.2, 79.9], $weights);
    }

    #[Test]
    public function correction_mesure_cree_une_nouvelle_ligne_sans_ecraser_l_originale(): void
    {
        $manager = $this->manager();
        $challenge = Challenge::factory()->create();
        $inscription = Inscription::factory()->create([
            'challenge_id' => $challenge->id,
        ]);
        $mesure = Mesure::factory()->create([
            'inscription_id' => $inscription->id,
            'measured_at' => '2026-08-11',
            'stage' => MeasurementStage::Initiale,
            'weight' => 82.5,
            'waist' => 96,
            'recorded_by' => $manager->id,
        ]);

        $response = $this->actingAs($manager)->put(route('mesures.update', $mesure), [
            'inscription_id' => $inscription->id,
            'measured_at' => '2026-08-12',
            'stage' => MeasurementStage::Intermediaire->value,
            'weight' => 81.4,
            'waist' => 94.5,
        ]);

        $newMesure = Mesure::query()->whereKeyNot($mesure->id)->firstOrFail();

        $response->assertRedirect(route('mesures.show', $newMesure));
        $this->assertSame(2, Mesure::query()->where('inscription_id', $inscription->id)->count());
        $this->assertSame(82.5, (float) $mesure->fresh()->weight);
        $this->assertSame(81.4, (float) $newMesure->weight);
    }

    #[Test]
    public function manager_peut_uploader_un_media_sur_disque_prive(): void
    {
        $manager = $this->manager();
        $challenge = Challenge::factory()->create();
        $inscription = Inscription::factory()->create([
            'challenge_id' => $challenge->id,
        ]);

        $response = $this->actingAs($manager)->post(route('challenges.media.store', $challenge), [
            'type' => MediaType::Photo->value,
            'stage' => MeasurementStage::Initiale->value,
            'media' => UploadedFile::fake()->image('avant.jpg', 400, 400)->size(600),
        ]);

        $media = Media::query()->firstOrFail();

        $response->assertRedirect();
        $this->assertSame($manager->id, $media->uploaded_by);
        $this->assertSame(MediaType::Photo, $media->type);
        $this->assertStringStartsWith("participantes/{$inscription->participante_id}/challenges/{$inscription->challenge_id}/media/photo/", $media->disk_path);
        Storage::disk('participant_media')->assertExists($media->disk_path);
    }

    #[Test]
    public function upload_media_rejette_un_type_de_fichier_invalide(): void
    {
        $manager = $this->manager();
        $challenge = Challenge::factory()->create();
        $inscription = Inscription::factory()->create([
            'challenge_id' => $challenge->id,
        ]);

        $response = $this->actingAs($manager)
            ->from(route('challenges.show', $challenge))
            ->post(route('challenges.media.store', $challenge), [
                'type' => MediaType::Photo->value,
                'stage' => MeasurementStage::Initiale->value,
                'media' => UploadedFile::fake()->create('notes.txt', 1, 'text/plain'),
            ]);

        $response->assertRedirect(route('challenges.show', $challenge));
        $response->assertSessionHasErrors('media');
        $this->assertDatabaseCount('media', 0);
    }

    #[Test]
    public function media_participante_est_servi_uniquement_aux_utilisateurs_autorises(): void
    {
        $challenge = Challenge::factory()->create();
        $inscription = Inscription::factory()->create([
            'challenge_id' => $challenge->id,
        ]);
        $path = "participantes/{$inscription->participante_id}/challenges/{$inscription->challenge_id}/media/photo/private.jpg";
        Storage::disk('participant_media')->put($path, 'private-media-content');

        $media = Media::factory()->create([
            'mediable_type' => Inscription::class,
            'mediable_id' => $inscription->id,
            'disk_path' => $path,
        ]);

        $this->get(route('participant-media.show', $media))->assertRedirect(route('login'));

        $userWithoutPermission = User::factory()->create();
        $this->actingAs($userWithoutPermission)
            ->get(route('participant-media.show', $media))
            ->assertForbidden();

        $manager = $this->manager();
        $response = $this->actingAs($manager)->get(route('participant-media.show', $media));

        $response->assertOk();
        $this->assertSame('private-media-content', $response->streamedContent());
    }

    private function manager(): User
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        return $manager;
    }
}
