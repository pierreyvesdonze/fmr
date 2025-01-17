<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ImageResizer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/product')]
final class ProductController extends AbstractController
{
    #[Route(name: 'product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ImageResizer $imageResizer): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            $imagePaths = [];
            if ($images) {
                foreach ($images as $image) {
                    $imagePaths[] = $imageResizer->resize($image, $this->getParameter('images_directory'));
                }
            }

            $product->setImages($imagePaths);
            $product->setUser($this->getUser());
            $product->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'product_edit')]
    public function edit(Product $product, Request $request, Filesystem $filesystem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            if ($images) {
                foreach ($images as $image) {
                    $newImageName = uniqid() . '.' . $image->guessExtension();
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newImageName
                    );
                    $product->addImage($newImageName);
                }
            }

            // Gestion de la suppression des images
            $imagesToRemove = [];
            foreach ($product->getImages() as $index => $image) {
                $removeImage = $form->get('remove_image_' . $index)->getData();
                if ($removeImage) {
                    $imagesToRemove[] = $image;
                }
            }

            // Retirer les images de la base de données
            $product->setImages(array_diff($product->getImages(), $imagesToRemove));

            // Supprimer les fichiers d'images du disque
            foreach ($imagesToRemove as $image) {
                $imagePath = $this->getParameter('images_directory') . '/' . $image;
                if ($filesystem->exists($imagePath)) {
                    $filesystem->remove($imagePath);
                }
            }

            // Enregistrer le produit (les nouvelles images sont déjà gérées par le form)
            $entityManager->flush();

            return $this->redirectToRoute('product_edit', ['id' => $product->getId()]);
        }

        return $this->render('product/edit.html.twig', [
            'form'    => $form->createView(),
            'product' => $product
        ]);
    }

    #[Route('/{id}', name: 'product_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Product $product,
        EntityManagerInterface $entityManager,
        Filesystem $filesystem
    ): Response {
        // Suppression des images associées au produit
        $images = $product->getImages();
        $uploadDir = $this->getParameter('images_directory');

        foreach ($images as $image) {
            $imagePath = $uploadDir . '/' . $image;
            if ($filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }
        }

        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }
}
