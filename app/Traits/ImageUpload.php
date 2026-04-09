<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait ImageUpload
{
    function save_image($request_image, $folder_name, $oldImagePath = null)
    {
        try {
            if (!Storage::disk('public')->exists($folder_name)) {
                Storage::disk('public')->makeDirectory($folder_name);
            }

            if ($request_image) {
                // Strictly check if uploaded file is PNG
                if (strtolower($request_image->getClientOriginalExtension()) !== 'png' ||
                    $request_image->getMimeType() !== 'image/png') {
                    return response()->json([
                        'message' => 'Only PNG images are allowed.',
                        'status'  => false
                    ], 422);
                }

                $tempPath = $request_image->getPathname();
                $image = imagecreatefrompng($tempPath);

                $width = imagesx($image);
                $height = imagesy($image);

                $maxWidth = 1024;
                $newWidth = min($maxWidth, $width);
                $newHeight = ($newWidth / $width) * $height;

                $newImage = imagecreatetruecolor($newWidth, $newHeight);

                // Preserve PNG transparency
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);

                imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                $filename = uniqid() . '.png';
                $relativePath = $folder_name . '/' . $filename;

                // Save PNG with max compression (0 = none, 9 = max)
                imagepng($newImage, storage_path('app/public/' . $relativePath), 9);

                imagedestroy($image);
                imagedestroy($newImage);

                // Delete old image if exists
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }

                return $relativePath;
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "Something went wrong...! Contact your admin...",
                'status'  => false
            ], 500);
        }
    }
}
