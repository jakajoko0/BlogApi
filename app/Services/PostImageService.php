<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class PostImageService
{
    // public function handleUploadedImage(UploadedFile $image, string $filename = null): string
    public function handleUploadedImage(UploadedFile $image, User $user, string $filename = null): string
    {
        //$user = Auth::user();
        $manager = ImageManager::withDriver(new GdDriver());
        $image = $manager->read($image);

        $currentWidth = $image->width();
        $currentHeight = $image->height();

        if ($currentWidth > 800) {
            $newWidth = 800;
            $newHeight = ($currentHeight / $currentWidth) * $newWidth;

            $image->resize($newWidth, $newHeight);
        }

        $webpImage = $image->toWebp(70);

        $name = $filename ?? now()->format('Ymd_His') . '.webp';

        $directoryName = $user->name . '_' . $user->last_name . '_' . $user->id;

        if (!Storage::disk('post-images')->exists('post-images/' . $directoryName)) {
            Storage::disk('post-images')->makeDirectory('post-images/' . $directoryName);
        }

        // Guardar la imagen en el disco
        Storage::disk('post-images')->put('post-images/' . $directoryName . '/' . $name, $webpImage->toFilePointer());

        return $name;
    }
}
