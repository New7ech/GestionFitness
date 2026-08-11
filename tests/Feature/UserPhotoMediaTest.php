<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserPhotoMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    #[Test]
    public function photo_url_retourne_avatar_par_defaut_si_absent(): void
    {
        $user = User::factory()->create([
            'photo' => null,
        ]);

        $this->assertStringContainsString('/assets/img/profile.jpg', $user->photo_url);
    }

    #[Test]
    public function photo_url_utilise_la_route_media_si_photo_presente(): void
    {
        $user = User::factory()->create([
            'photo' => 'users/photos/avatar.jpg',
        ]);

        $this->assertSame(
            url('media/public/users/photos/avatar.jpg'),
            $user->photo_url
        );
    }

    #[Test]
    public function route_media_sert_le_fichier_public(): void
    {
        Storage::disk('public')->put('users/photos/avatar.txt', 'fake-image-content');

        $response = $this->get('/media/public/users/photos/avatar.txt');

        $response->assertOk();
        $this->assertSame('fake-image-content', $response->streamedContent());
    }

    #[Test]
    public function route_media_bloque_les_chemins_invalides(): void
    {
        $response = $this->get('/media/public/..%2F.env');

        $response->assertNotFound();
    }
}
