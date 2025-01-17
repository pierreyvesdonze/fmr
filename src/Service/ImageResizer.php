<?php

// src/Service/ImageResizer.php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageResizer
{
    public function resize(UploadedFile $file, string $destination): string
    {
        $image = imagecreatefromstring(file_get_contents($file->getPathname()));
        $width = imagesx($image);
        $height = imagesy($image);

        $maxWidth = 1200;

        if ($width > $maxWidth) {
            $ratio = $maxWidth / $width;
            $newWidth = $maxWidth;
            $newHeight = intval($height * $ratio);

            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            $newFilename = uniqid() . '.' . $file->guessExtension();
            $path = $destination . '/' . $newFilename;

            imagejpeg($resizedImage, $path, 90);
            imagedestroy($resizedImage);
        } else {
            $newFilename = uniqid() . '.' . $file->guessExtension();
            $file->move($destination, $newFilename);
        }

        imagedestroy($image);

        return $newFilename;
    }
}
