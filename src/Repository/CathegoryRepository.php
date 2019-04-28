<?php

namespace App\Repository;

use App\Entity\Cathegory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Cathegory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cathegory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cathegory[]    findAll()
 * @method Cathegory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CathegoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cathegory::class);
    }

    // /**
    //  * @return Cathegory[] Returns an array of Cathegory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cathegory
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
