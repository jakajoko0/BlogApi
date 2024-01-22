<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\User;
use App\Services\PostImageService;

class PostController extends Controller
{
    public function store(StorePostRequest $request, PostImageService $postImageService)
    {
        $validated = $request->validated();

        //variable provisional
        $user = User::find($validated['user_id']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            //     $filename = $postImageService->handleUploadedImage($image);
            $filename = $postImageService->handleUploadedImage($image, $user);
        }

        $post = Post::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image' => $filename,
            //temporalmente vamos a asignar el usuario, pero normalmente se asigna el usuario autenticado
            'user_id' => $validated['user_id'],
            // 'user_id' => auth()->user()->id,
        ]);

        return response()->json($post, 201);
    }
}
