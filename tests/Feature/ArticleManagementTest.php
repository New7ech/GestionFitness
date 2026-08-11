<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Categorie;
use App\Models\Fournisseur;
use App\Models\Emplacement;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class ArticleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // CrÃ©e un faux disque de stockage pour les tests
        // Les fichiers seront stockÃ©s dans storage/framework/testing/disks/public
        Storage::fake('public');
    }

    // MÃ©thode utilitaire pour crÃ©er et authentifier un utilisateur
    protected function authenticateUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    // Les mÃ©thodes de test seront ajoutÃ©es ici

    #[Test]
    public function test_peut_lister_articles()
    {
        $this->authenticateUser();
        Article::factory()->create(['name' => 'Article Alpha']);
        Article::factory()->create(['name' => 'Article Beta']);

        $response = $this->get(route('articles.index'));

        $response->assertStatus(200);
        $response->assertSee('Article Alpha');
        $response->assertSee('Article Beta');
    }

    #[Test]
    public function test_utilisateur_authentifie_peut_creer_article()
    {
        $user = $this->authenticateUser();

        $category = Categorie::factory()->create();
        $fournisseur = Fournisseur::factory()->create();
        $emplacement = Emplacement::factory()->create();

        $articleData = [
            'name' => 'New Awesome Article',
            'description' => 'This is a test description.',
            'prix' => 199.99,
            'quantite' => 25,
            'category_id' => $category->id,
            'fournisseur_id' => $fournisseur->id,
            'emplacement_id' => $emplacement->id,
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertRedirect(route('articles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('articles', [
            'name' => 'New Awesome Article',
            'description' => 'This is a test description.',
            'prix' => 199.99,
            'quantite' => 25,
            'category_id' => $category->id,
            'fournisseur_id' => $fournisseur->id,
            'emplacement_id' => $emplacement->id,
            'created_by' => $user->id,
            // On ne vÃ©rifie pas image_principale ici car le test original n'incluait pas l'upload
        ]);
    }

    #[Test]
    public function test_utilisateur_peut_creer_article_avec_image()
    {
        $user = $this->authenticateUser();
        $category = Categorie::factory()->create();
        $fournisseur = Fournisseur::factory()->create();
        $emplacement = Emplacement::factory()->create();

        $articleData = [
            'name' => 'Article With Image',
            'description' => 'Description for article with image.',
            'prix' => 99.99,
            'quantite' => 10,
            'category_id' => $category->id,
            'fournisseur_id' => $fournisseur->id,
            'emplacement_id' => $emplacement->id,
            'image_principale' => UploadedFile::fake()->image('article_image.jpg', 100, 100)->size(100), // kb
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertRedirect(route('articles.index'));
        $this->assertDatabaseHas('articles', ['name' => 'Article With Image']);

        $article = Article::where('name', 'Article With Image')->first();
        $this->assertNotNull($article->image_principale);
        Storage::disk('public')->assertExists($article->image_principale);
    }

    #[Test]
    public function test_article_creation_requires_name()
    {
        $this->authenticateUser();

        $articleData = [
            'description' => 'Test desc without name',
            'prix' => 100,
            'quantite' => 10,
        ];

        $response = $this->post(route('articles.store'), $articleData);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('articles', ['description' => 'Test desc without name']);
    }

    #[Test]
    public function test_peut_afficher_article()
    {
        $this->authenticateUser();
        $article = Article::factory()->create([
            'name' => 'Specific Article Name',
            'description' => 'Specific Article Description'
        ]);

        $response = $this->get(route('articles.show', $article));

        $response->assertStatus(200);
        $response->assertSee($article->name);
        $response->assertSee($article->description);
    }

    #[Test]
    public function test_utilisateur_authentifie_peut_modifier_article()
    {
        $user = $this->authenticateUser();
        $category = Categorie::factory()->create();
        $fournisseur = Fournisseur::factory()->create();
        $emplacement = Emplacement::factory()->create();

        $article = Article::factory()->create([
            'created_by' => $user->id,
            'category_id' => $category->id,
            'fournisseur_id' => $fournisseur->id,
            'emplacement_id' => $emplacement->id,
        ]);

        $newCategory = Categorie::factory()->create();
        $newFournisseur = Fournisseur::factory()->create();
        $newEmplacement = Emplacement::factory()->create();

        $updatedData = [
            'name' => 'Updated Article Name',
            'description' => 'Updated description for article.',
            'prix' => 150.75,
            'quantite' => 5,
            'category_id' => $newCategory->id,
            'fournisseur_id' => $newFournisseur->id,
            'emplacement_id' => $newEmplacement->id,
        ];

        $response = $this->put(route('articles.update', $article), $updatedData);

        $response->assertRedirect(route('articles.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('articles', array_merge(['id' => $article->id], $updatedData));
    }

    #[Test]
    public function test_utilisateur_peut_mettre_a_jour_article_avec_nouvelle_image()
    {
        $this->authenticateUser();
        $article = Article::factory()->create([
            'image_principale' => UploadedFile::fake()->image('old_image.jpg')->store('articles_images', 'public')
        ]);
        $oldImagePath = $article->image_principale;

        $updatedData = [
            'name' => 'Updated Name With New Image',
            'description' => $article->description, // garder les autres champs pour la validitÃ©
            'prix' => $article->prix,
            'quantite' => $article->quantite,
            'category_id' => $article->category_id,
            'fournisseur_id' => $article->fournisseur_id,
            'emplacement_id' => $article->emplacement_id,
            'image_principale' => UploadedFile::fake()->image('new_image.jpg', 120, 120)->size(150),
        ];

        $response = $this->put(route('articles.update', $article), $updatedData);

        $response->assertRedirect(route('articles.index'));
        $article->refresh();
        $this->assertNotNull($article->image_principale);
        $this->assertNotEquals($oldImagePath, $article->image_principale);
        Storage::disk('public')->assertMissing($oldImagePath);
        Storage::disk('public')->assertExists($article->image_principale);
    }

    #[Test]
    public function test_utilisateur_peut_supprimer_image_article_lors_mise_a_jour()
    {
        $this->authenticateUser();
        $article = Article::factory()->create([
            'image_principale' => UploadedFile::fake()->image('image_to_delete.jpg')->store('articles_images', 'public')
        ]);
        $imagePathToDelete = $article->image_principale;

        $updatedData = [
            'name' => 'Article With Image Deleted',
            'description' => $article->description,
            'prix' => $article->prix,
            'quantite' => $article->quantite,
            'category_id' => $article->category_id,
            'fournisseur_id' => $article->fournisseur_id,
            'emplacement_id' => $article->emplacement_id,
            'supprimer_image_principale' => '1', // Simule la case cochÃ©e
        ];

        $response = $this->put(route('articles.update', $article), $updatedData);

        $response->assertRedirect(route('articles.index'));
        $article->refresh();
        $this->assertNull($article->image_principale);
        Storage::disk('public')->assertMissing($imagePathToDelete);
    }


    #[Test]
    public function test_utilisateur_authentifie_peut_supprimer_article_et_son_image()
    {
        $user = $this->authenticateUser();
        // CrÃ©e un article avec une image
        $imagePath = UploadedFile::fake()->image('article_to_delete.jpg')->store('articles_images', 'public');
        $article = Article::factory()->create([
            'created_by' => $user->id,
            'image_principale' => $imagePath,
        ]);

        $this->assertTrue(Storage::disk('public')->exists($imagePath));

        $response = $this->delete(route('articles.destroy', $article));

        $response->assertRedirect(route('articles.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
        Storage::disk('public')->assertMissing($imagePath); // VÃ©rifie que l'image est supprimÃ©e du stockage
    }
}


