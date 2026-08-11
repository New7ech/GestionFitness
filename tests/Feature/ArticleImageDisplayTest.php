<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ArticleImageDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    #[Test]
    public function image_url_utilise_endpoint_media_pour_un_chemin_local(): void
    {
        $article = Article::factory()->create([
            'image_principale' => 'articles_images/article.jpg',
        ]);

        $expected = url('media/public/articles_images/article.jpg');

        $this->assertSame($expected, $article->image_url);
        $this->assertSame($expected, $article->imageUrl);
    }

    #[Test]
    public function image_url_normalise_un_ancien_prefixe_storage(): void
    {
        $article = Article::factory()->create([
            'image_principale' => 'storage/articles_images/article.jpg',
        ]);

        $this->assertSame(
            url('media/public/articles_images/article.jpg'),
            $article->imageUrl
        );
    }

    #[Test]
    public function page_show_affiche_url_media_pour_image_locale(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::disk('public')->put('articles_images/article-show.jpg', 'img-content');

        $article = Article::factory()->create([
            'image_principale' => 'articles_images/article-show.jpg',
        ]);

        $response = $this->get(route('articles.show', $article));

        $response->assertOk();
        $response->assertSee('/media/public/articles_images/article-show.jpg');
    }

    #[Test]
    public function page_index_affiche_url_media_pour_image_locale(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Storage::disk('public')->put('articles_images/article-index.jpg', 'img-content');

        Article::factory()->create([
            'image_principale' => 'articles_images/article-index.jpg',
        ]);

        $response = $this->get(route('articles.index'));

        $response->assertOk();
        $response->assertSee('/media/public/articles_images/article-index.jpg');
    }
}
