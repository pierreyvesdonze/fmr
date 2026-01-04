<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // Récupère les produits par catégorie de genre (ex: Femme, Homme, Non genré)
    public function findByGenderCategory(string $genderCategory): array
    {
        $products = $this->createQueryBuilder('p')
            ->innerJoin('p.genderCategory', 'g')
            ->andWhere('g.name = :genderCategory')
            ->setParameter('genderCategory', $genderCategory)
            ->getQuery()
            ->getResult();

        shuffle($products); // Mélange aléatoire
        return $products;
    }

    // Récupère tous les produits de façon aléatoire
    public function findRandomAllProducts(): array
    {
        $products = $this->findAll();
        shuffle($products);
        return $products;
    }

    // Récupère les produits d’un utilisateur spécifique
    public function findByUserId($userId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    // Récupère les produits par slug de catégorie principale
    public function findByMainCategory(?string $mainCategorySlug): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.category', 'c')
            ->innerJoin('c.mainCategory', 'm')
            ->where('m.slug = :mainCategory')
            ->setParameter('mainCategory', $mainCategorySlug)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Récupère les produits par catégorie principale ET catégorie de genre
    public function findByMainCategoryAndGender(?string $mainCategorySlug, string $genderCategory): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.category', 'c')
            ->innerJoin('c.mainCategory', 'm')
            ->innerJoin('p.genderCategory', 'g')
            ->where('m.slug = :mainCategory')
            ->andWhere('g.name = :genderCategory')
            ->setParameter('mainCategory', $mainCategorySlug)
            ->setParameter('genderCategory', $genderCategory)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Recherche filtrée via différents paramètres (utilisé par le formulaire)
    public function findFilteredProducts(array $filters)
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.category', 'c')
            ->innerJoin('c.mainCategory', 'mc')
            ->innerJoin('p.genderCategory', 'g')
            ->leftJoin('p.size', 's')
            ->leftJoin('p.brand', 'b')
            ->leftJoin('p.color', 'co');

        if (!empty($filters['category'])) {
            $qb->andWhere('c.id = :category')
                ->setParameter('category', $filters['category']);
        }

        if (!empty($filters['size'])) {
            $qb->andWhere('s.id = :size')
                ->setParameter('size', $filters['size']);
        }

        if (!empty($filters['brand'])) {
            $qb->andWhere('b.id = :brand')
                ->setParameter('brand', $filters['brand']);
        }

        if (!empty($filters['color'])) {
            $qb->andWhere('co.id = :color')
                ->setParameter('color', $filters['color']);
        }

        if (!empty($filters['mainCategory'])) {
            $qb->andWhere('mc.id = :mainCategory')
                ->setParameter('mainCategory', $filters['mainCategory']);
        }

        if (!empty($filters['genderCategory'])) {
            $qb->andWhere('g.id = :genderCategory')
                ->setParameter('genderCategory', $filters['genderCategory']);
        }

        return $qb->getQuery()->getResult();
    }

    // Recherche par mot-clé
    public function searchByKeyword(string $keyword)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.brand', 'b')
            ->innerJoin('p.category', 'c')
            ->innerJoin('p.color', 'co')
            ->innerJoin('p.size', 's')
            ->innerJoin('p.genderCategory', 'g')
            ->where('p.name LIKE :keyword')
            ->orWhere('p.description LIKE :keyword')
            ->orWhere('p.wear LIKE :keyword')
            ->orWhere('b.name LIKE :keyword')
            ->orWhere('c.name LIKE :keyword')
            ->orWhere('co.name LIKE :keyword')
            ->orWhere('s.name LIKE :keyword')
            ->orWhere('g.name LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->getQuery()
            ->getResult();
    }
}
