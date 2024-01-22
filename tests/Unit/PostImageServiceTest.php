<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\PostImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostImageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleUploadedImage()
    {
        // Crear un usuario de prueba
        $user = User::create([
            'name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('image.jpg');

        Storage::fake('post-images');

        $service = new PostImageService();

        $filename = 'test_image.webp';
        $service->handleUploadedImage($file, $filename);

        // Agregar 'post-images/' al inicio de la ruta del archivo
        $filePath = 'post-images/' . $user->name . '_' . $user->last_name . '_' . $user->id . '/' . $filename;

        Storage::disk('post-images')->assertExists($filePath);
        $this->assertTrue(Storage::disk('post-images')->size($filePath) > 0);
    }
}
