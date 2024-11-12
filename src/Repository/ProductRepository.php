<?php


// src/Repository/ProductRepository.php

// src/Repository/ProductRepository.php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function searchQueryBuilder(array $filters = [], array $sort = [], int $page = 1, int $limit = 10): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        // Apply filters
        if (isset($filters['category'])) {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', $filters['category']);
        }

        if (isset($filters['min_price'])) {
            $qb->andWhere('p.price >= :min_price')
                ->setParameter('min_price', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $qb->andWhere('p.price <= :max_price')
                ->setParameter('max_price', $filters['max_price']);
        }

        // Apply sorting
        if (!empty($sort)) {
            foreach ($sort as $field => $direction) {
                $qb->addOrderBy('p.' . $field, strtoupper($direction));
            }
        }

        // Pagination
        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        return $qb;
    }
}




