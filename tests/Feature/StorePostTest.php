<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorePostTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_store_method_creates_post()
    {
        // Crear un usuario autenticado
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear una imagen de prueba
        $image = UploadedFile::fake()->image('test_image.jpg');

        // Simular una solicitud con los datos necesarios
        $response = $this->postJson(route('posts.store'), [
            'title' => 'Test Post',
            'description' => 'This is a test post content.',
            'image' => $image,
            'user_id' => $user->id,
        ]);
        // Verificar la respuesta exitosa
        $response->assertStatus(201);

        // Verificar que el post se haya creado en la base de datos
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'description' => 'This is a test post content.',
            'user_id' => $user->id,
        ]);

        // Obtener el post creado
        $post = Post::first();

        // Agregar 'post-images/' al inicio de la ruta del archivo
        $filePath = 'post-images/' . $user->name . '_' . $user->last_name . '_' . $user->id . '/' . $post->image;

        // Verificar que se haya almacenado la imagen correctamente en el disco personalizado
        Storage::disk('post-images')->assertExists($filePath);
    }
}
