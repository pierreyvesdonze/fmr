<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\FilterProductType;
use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use App\Repository\ColorRepository;
use App\Repository\GenderCategoryRepository;
use App\Repository\MainCategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SizeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ImageResizer;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/article')]
final class ProductController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager) {}
    
    #[Route('s/utilisateur/{userId}', name: 'product_user_index', methods: ['GET'])]
    #[Route('s/{mainCategory}/{genderCategory}', name: 'product_index', methods: ['GET', 'POST'])]
    #[Route('/recherche', name: 'product_search', methods: ['GET', 'POST'])]
    public function index(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        SizeRepository $sizeRepository,
        BrandRepository $brandRepository,
        ColorRepository $colorRepository,
        MainCategoryRepository $mainCategoryRepository,
        GenderCategoryRepository $genderCategoryRepository,
        PaginatorInterface $paginator,
        Request $request,
        string $mainCategory = 'tout',
        string $genderCategory = 'tout',
        ?int $userId = null
    ): Response {

        $form = $this->createForm(FilterProductType::class, null, [
            'categories'       => $categoryRepository->findAll(),
            'sizes'            => $sizeRepository->findAll(),
            'brands'           => $brandRepository->findAll(),
            'colors'           => $colorRepository->findAll(),
            'mainCategories'   => $mainCategoryRepository->findAll(),
            'genderCategories' => $genderCategoryRepository->findAll(),
        ]);

        $form->handleRequest($request);

        // Récupérer le mot-clé de la recherche (si il existe)
        $keyword = $request->query->get('q', '');

        if ($userId !== null) {
            $products = $productRepository->findByUserId($userId);
        } elseif ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $products = $productRepository->findFilteredProducts($data);
        } else {
            if ($mainCategory !== 'tout') {
                if ($genderCategory === 'tout') {
                    $products = $productRepository->findByMainCategory($mainCategory);
                } else {
                    $products = $productRepository->findByMainCategoryAndGender($mainCategory, $genderCategory);
                }
            } else {
                $products = $productRepository->findRandomAllProducts();
            }
        }

        if ($keyword) {
            // Recherche par nom, catégorie, marque, couleur, etc.
            $products = array_filter($products, function ($product) use ($keyword) {
                return stripos($product->getName(), $keyword) !== false ||
                       stripos($product->getCategory()?->getName(), $keyword) !== false ||
                       stripos($product->getBrand()?->getName(), $keyword) !== false || // Recherche dans la marque (évite null)
                       stripos($product->getColor()?->getName(), $keyword) !== false || // Recherche dans la couleur
                       stripos($product->getSize()?->getName(), $keyword) !== false;    // Recherche dans la taille
            });
        }

        // Pagination avec limite de 21 articles par page
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            21
        );

        return $this->render('product/index.html.twig', [
            'products'       => $pagination,
            'genderCategory' => $genderCategory,
            'form'           => $form->createView()
        ]);
    }

    #[Route('/nouveau', name: 'product_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ImageResizer $imageResizer
    ): Response {
        if (!$this->getUser()) {
            $this->addFlash('success', 'Vous devez être connecté(e) pour ajouter un article');
            return $this->redirectToRoute('product_index');
        }

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mainImage = $form->get('mainImage')->getData();
            $mainImagePath = null;

            if ($mainImage) {
                $mainImagePath = $imageResizer->resize($mainImage, $this->getParameter('images_directory'));
            }

            $images = $form->get('images')->getData();
            $imagePaths = [];
            if ($images) {
                foreach ($images as $image) {
                    $imagePaths[] = $imageResizer->resize($image, $this->getParameter('images_directory'));
                }
            }

            $product->setMainImage($mainImagePath);
            $product->setImages($imagePaths);
            $product->setUser($this->getUser());
            $product->setCreatedAt(new \DateTimeImmutable());
            $product->setStock(1);
            $product->setSold(false);

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            if ($this->getUser()->getIban() != null) {
                $this->addFlash('success', 'Nouveau produit ajouté !');
            } else {
                $this->addFlash('success', "Produit ajouté. N'oublie pas d'ajouter également un IBAN dans ton espace perso");
            }

            return $this->redirectToRoute('product_show', [
                'id' => $product->getId()
            ], Response::HTTP_SEE_OTHER);
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

    #[Route('/{id}/modifier', name: 'product_edit')]
    public function edit(
        Product $product,
        Request $request,
        ImageResizer $imageResizer
    ): Response {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mainImage = $form->get('mainImage')->getData();
            $oldMainImage = $product->getMainImage();

            if ($mainImage) {
                // Supprimer l'ancienne image principale si elle existe
                if ($oldMainImage) {
                    $oldMainImagePath = $this->getParameter('images_directory') . '/' . $oldMainImage;
                    if (file_exists($oldMainImagePath)) {
                        unlink($oldMainImagePath);
                    }
                }

                // Ajouter la nouvelle image principale
                $mainImagePath = $imageResizer->resize($mainImage, $this->getParameter('images_directory'));
                $product->setMainImage($mainImagePath);
            }

            $images = $form->get('images')->getData();
            $imagePaths = [];

            $oldImages = $product->getImages();
            $images = $form->get('images')->getData(); // Récupère les nouvelles images

            // Si de nouvelles images sont téléchargées
            if ($images !== null && count($images) > 0) {
                // Redimensionne et ajoute les nouvelles images
                $imagePaths = [];
                foreach ($images as $image) {
                    // Redimensionner et déplacer l'image, comme dans votre service ImageResizer
                    $imagePaths[] = $imageResizer->resize($image, $this->getParameter('images_directory'));
                }
                $product->setImages($imagePaths); // Ajoute les nouvelles images au produit

                // Supprime les anciennes images
                if ($oldImages) {
                    foreach ($oldImages as $oldImage) {
                        $oldImagePath = $this->getParameter('images_directory') . '/' . $oldImage;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath); // Supprime l'ancienne image
                        }
                    }
                }
            } else {
                // Si aucune image n'est téléchargée, on ne touche pas aux anciennes images
                $product->setImages($oldImages); // Les anciennes images restent intactes
            }

            $product->setUser($this->getUser());
            $product->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            if ($this->getUser()->getIban() != null) {
                $this->addFlash('success', 'Produit modifié !');
            } else {
                $this->addFlash('success', "Produit modifié. N'oublie pas d'ajouter également un IBAN dans ton espace perso");
            }

            return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
        }

        return $this->render('product/edit.html.twig', [
            'form'    => $form->createView(),
            'product' => $product
        ]);
    }

    #[Route('/{id}/supprimer', name: 'product_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Product $product,
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
            $this->entityManager->remove($product);
            $this->entityManager->flush();
        }

        $this->addFlash('success', 'Article supprimé !');

        return $this->redirectToRoute('product_index', [
            'mainCategory'   => 'vetements',
            'genderCategory' => 'tout'
        ], Response::HTTP_SEE_OTHER);
    }
}
