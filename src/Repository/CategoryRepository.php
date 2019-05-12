<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getFindAllQuery($search)
    {
        $qb = $this->createQueryBuilder('c');

        $this->filterCategories($search, $qb);

        return $qb->getQuery();
    }

    /**
     * Filter the Categories by the given word
     *
     * @param mixed $search
     * @param QueryBuilder $qb
     */
    private function filterCategories($search, QueryBuilder $qb): void
    {
        if (!empty($search)) {
            $qb
                ->where('c.code = :search')
                ->orWhere('c.title = :search')
                ->setParameter('search', $search);
        }
    }
}
