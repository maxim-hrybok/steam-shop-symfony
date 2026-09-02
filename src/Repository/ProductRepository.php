<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\Tools\Pagination\Paginator;
/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Paginator Returns a special Doctrine object that automatically calculates the total number of pages
     */
    // public function getFilteredProducts(int $page = 1, int $limit = 9, string $search = '', array $categories = []): Paginator
    // {
    //     // 'p' - is Product
    //     $qb = $this->createQueryBuilder('p')
    //         ->where('p.status = :status')
    //         ->setParameter('status', 'available')
    //         ->orderBy('p.id', 'ASC  '); 

    //     // if search term is provided, filter products by name
    //     if ($search !== '') {
    //         $qb->andWhere('p.name LIKE :search')
    //            ->setParameter('search', '%' . $search . '%');
    //     }

    //     // if categories are provided, filter products by categories
    //     if (!empty($categories)) {
    //         $qb->join('p.categories', 'c') // JOIN делается одной строкой!
    //            ->andWhere('c.id IN (:categories)')
    //            ->setParameter('categories', $categories);
    //     }

    //     // pagination: set the limit and offset based on the current page
    //     $qb->setMaxResults($limit)
    //        ->setFirstResult(($page - 1) * $limit);

    //     // Returns a special Doctrine object that automatically calculates the total number of pages
    //     return new Paginator($qb);
    // }
    public function getFilteredProducts(int $page = 1, int $limit = 9, string $search = '', array $categories = [], string $status = 'all'): Paginator
    {
        $qb = $this->createQueryBuilder('p')->orderBy('p.id', 'ASC');

        // if status is not 'all', filter products by status
        if ($status !== 'all') {
            $qb->andWhere('p.status = :status')
               ->setParameter('status', $status);
        }

        if ($search !== '') {
            $qb->andWhere('p.name LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        if (!empty($categories)) {
            $qb->join('p.categories', 'c')
               ->andWhere('c.id IN (:categories)')
               ->setParameter('categories', $categories);
        }

        $qb->setMaxResults($limit)
           ->setFirstResult(($page - 1) * $limit);

        return new Paginator($qb);
    }
    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
