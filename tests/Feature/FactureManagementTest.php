<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Facture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FactureManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    #[Test]
    public function creation_facture_decremente_les_stocks(): void
    {
        $this->authenticateUser();

        $article = Article::factory()->create([
            'quantite' => 10,
            'prix' => 100,
        ]);

        $payload = [
            'client_nom' => 'Client Test',
            'client_prenom' => 'A',
            'client_adresse' => 'Adresse',
            'client_telephone' => '0123456789',
            'client_email' => 'client@example.com',
            'statut_paiement' => Facture::STATUS_IMPAYEE,
            'mode_paiement' => 'carte',
            'articles' => [
                ['article_id' => $article->id, 'quantity' => 3],
            ],
        ];

        $response = $this->post(route('factures.store'), $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('factures', ['client_nom' => 'Client Test']);

        $facture = Facture::query()->firstOrFail();

        $this->assertDatabaseHas('article_facture', [
            'facture_id' => $facture->id,
            'article_id' => $article->id,
            'quantite' => 3,
        ]);

        $this->assertSame(7, $article->fresh()->quantite);
    }

    #[Test]
    public function mise_a_jour_facture_recalcule_correctement_le_stock(): void
    {
        $this->authenticateUser();

        $article = Article::factory()->create([
            'quantite' => 10,
            'prix' => 100,
        ]);

        $this->post(route('factures.store'), [
            'client_nom' => 'Client Test',
            'client_prenom' => 'A',
            'client_adresse' => 'Adresse',
            'client_telephone' => '0123456789',
            'client_email' => 'client@example.com',
            'statut_paiement' => Facture::STATUS_IMPAYEE,
            'mode_paiement' => 'carte',
            'articles' => [
                ['article_id' => $article->id, 'quantity' => 3],
            ],
        ]);

        $facture = Facture::query()->firstOrFail();

        $response = $this->put(route('factures.update', $facture), [
            'client_nom' => 'Client Test',
            'client_prenom' => 'A',
            'client_adresse' => 'Adresse',
            'client_telephone' => '0123456789',
            'client_email' => 'client@example.com',
            'statut_paiement' => Facture::STATUS_PAYEE,
            'mode_paiement' => 'carte',
            'articles' => [
                ['article_id' => $article->id, 'quantity' => 1],
            ],
        ]);

        $response->assertRedirect(route('factures.index'));

        $this->assertSame(9, $article->fresh()->quantite);
        $this->assertDatabaseHas('article_facture', [
            'facture_id' => $facture->id,
            'article_id' => $article->id,
            'quantite' => 1,
        ]);
    }

    #[Test]
    public function suppression_facture_restitue_le_stock(): void
    {
        $this->authenticateUser();

        $article = Article::factory()->create([
            'quantite' => 10,
            'prix' => 100,
        ]);

        $this->post(route('factures.store'), [
            'client_nom' => 'Client Test',
            'client_prenom' => 'A',
            'client_adresse' => 'Adresse',
            'client_telephone' => '0123456789',
            'client_email' => 'client@example.com',
            'statut_paiement' => Facture::STATUS_IMPAYEE,
            'mode_paiement' => 'carte',
            'articles' => [
                ['article_id' => $article->id, 'quantity' => 2],
            ],
        ]);

        $facture = Facture::query()->firstOrFail();

        $response = $this->delete(route('factures.destroy', $facture));

        $response->assertRedirect(route('factures.index'));
        $this->assertSame(10, $article->fresh()->quantite);
        $this->assertDatabaseMissing('article_facture', ['facture_id' => $facture->id]);
        $this->assertSoftDeleted('factures', ['id' => $facture->id]);
    }
}
