<?php

// src/Controller/ProductController.php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'product_search', methods: ['GET'])]
    public function search(Request $request, ProductRepository $repository, CacheInterface $cache)
    {
        // Get filters, sorting, and pagination from the query parameters
        $filters = $request->query->all();
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);
        $sort = $request->query->get('sort', []);

        // Cache key based on filters and sorting
        $cacheKey = 'product_search_' . md5(json_encode($filters) . json_encode($sort) . $page . $limit);
        $products = $cache->get($cacheKey, function (ItemInterface $item) use ($repository, $filters, $sort, $page, $limit) {
            $item->expiresAfter(3600); // Cache for 1 hour
            $qb = $repository->searchQueryBuilder($filters, $sort, $page, $limit);
            return $qb->getQuery()->getResult();
        });

        return $this->json($products);
    }
}



