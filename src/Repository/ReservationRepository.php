<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function getFindAllQuery($search)
    {
        return $this->createQueryBuilder('r')
            ->join('r.book', 'book')
            ->getQuery();
    }

    public function getFindByUserQuery($userId)
    {
        $qb = $this->createQueryBuilder('r');
        $this->joinUser($userId, $qb);
        return $qb->getQuery();
    }

    public function countAllReservations()
    {
        return $this->createQueryBuilder('b')
            ->select('count(b.id) as total')
            ->getQuery()
            ->getSingleResult();
    }

    public function reservationStats()
    {
        return $this->createQueryBuilder('b')
            ->select('count(b.id) as total')
            ->groupBy()
            ->getQuery()
            ->getSingleResult();
    }

    public function getUserReservationGroupedByStatus($userId)
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.borrowingQuantity) as reservedBooks', 'r.status as status')
            ->groupBy('status');

        $this->joinUser($userId, $qb);

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function joinUser($userId, QueryBuilder $qb)
    {
        $qb
            ->join('r.user', 'user')
            ->where('user.id = :user_id')
            ->setParameter('user_id', $userId);
    }
}
