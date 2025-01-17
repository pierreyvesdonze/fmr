<?php
namespace App\EventListener;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class ProductDeletionListener
{
    private Filesystem $filesystem;
    private string $uploadDir;

    public function __construct(string $uploadDir)
    {
        $this->filesystem = new Filesystem();
        $this->uploadDir = $uploadDir;
    }

    public function onProductDelete(Product $product): void
    {
        $images = $product->getImages();  // Récupère les images du produit

        foreach ($images as $image) {
            $imagePath = $this->uploadDir . '/' . $image;

            if ($this->filesystem->exists($imagePath)) {
                $this->filesystem->remove($imagePath);  // Supprime l'image du disque
            }
        }
    }
}
