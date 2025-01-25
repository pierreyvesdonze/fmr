<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query\QueryBuilder;
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

    public function findByGenderCategory(string $genderCategory): array
    {
        $products = $this->createQueryBuilder('p')
            ->innerJoin('p.genderCategory', 'c')
            ->andWhere('c.name = :genderCategory')
            ->setParameter('genderCategory', $genderCategory)
            ->getQuery()
            ->getResult();

        shuffle($products); // Mélange les produits aléatoirement

        return array_slice($products, 0);
    }

    public function findRandomAllProducts(): array
    {
        $products = $this->findAll(); // Récupère tous les produits

        shuffle($products); // Mélange les produits aléatoirement
        return array_slice($products, 0);
    }

    public function findByUserId($userId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findByMainCategory(string $mainCategorySlug): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->join('c.mainCategory', 'm')
            ->where('m.slug = :mainCategory')
            ->setParameter('mainCategory', $mainCategorySlug)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByMainCategoryAndGender(string $mainCategorySlug, string $genderCategory): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->join('c.mainCategory', 'm')
            ->join('p.genderCategory', 'g')
            ->where('m.slug = :mainCategory')
            ->andWhere('g.name = :genderCategory')
            ->setParameter('mainCategory', $mainCategorySlug)
            ->setParameter('genderCategory', $genderCategory)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findFilteredProducts(array $filters)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.size', 's')
            ->leftJoin('p.brand', 'b')
            ->leftJoin('p.color', 'co')
            ->leftJoin('c.mainCategory', 'mc')
            ->leftJoin('p.genderCategory', 'g');

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

    public function searchByKeyword(string $keyword)
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :keyword')
            ->orWhere('p.description LIKE :keyword')
            ->orWhere('p.wear LIKE :keyword')
            ->orWhere('b.name LIKE :keyword')
            ->orWhere('c.name LIKE :keyword')
            ->leftJoin('p.brand', 'b')
            ->leftJoin('p.color', 'c')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->getQuery()
            ->getResult();
    }
}
