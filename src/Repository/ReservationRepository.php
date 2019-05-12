<?php

namespace App\Repository;

use App\Entity\Reservation;
use DateTime;
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
        $qb = $this->createQueryBuilder('r')
            ->join('r.book', 'book')
            ->join('book.category', 'ca')
            ->join('r.user', 'u');
        $this->filterReservation($search, $qb);
        $this->filterBooks($search, $qb);
        $this->filterUsers($search, $qb);

        return $qb->getQuery();
    }

    public function findByBook($bookId)
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.borrowedQuantity) as borrowedQuantity')
            ->addSelect('book.quantity as quantity');

        $this->joinBook($bookId, $qb);
        $this->reservedBooks($qb);

        return $qb->getQuery()->getSingleResult();
    }

    public function getFindByUserQuery($userId)
    {
        $qb = $this->createQueryBuilder('r')
            ->addSelect('book')
            ->join('r.book', 'book');
        $this->joinUser($userId, $qb);
        return $qb->getQuery();
    }

    public function countAllReservations()
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.borrowedQuantity) as total')
            ->getQuery()
            ->getSingleResult();
    }

    public function reservationStats()
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.borrowedQuantity) as borrowedQuantity', 'r.createdAt as createdAt');

        $this->reservedBooks($qb);

        return $qb->groupBy('createdAt')
            ->getQuery()
            ->getResult();
    }

    public function getUserReservationGroupedByStatus($userId)
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.borrowedQuantity) as reservedBooks', 'r.status as status')
            ->groupBy('status');

        $this->joinUser($userId, $qb);

        return $qb
            ->getQuery()
            ->getResult();
    }

    private function reservedBooks(QueryBuilder $qb){
        $qb
            ->andWhere(':now BETWEEN r.startBorrowingDate AND r.endBorrowingDate')
            ->andWhere('r.status in (:book_status)')
            ->setParameter('now', new DateTime('now'))
            ->setParameter('book_status', [Reservation::FULLY_ACCEPTED, Reservation::PARTIALLY_ACCEPTED])
        ;
    }

    private function joinBook($bookId, QueryBuilder $qb)
    {
        $qb
            ->join('r.book', 'book')
            ->where('book.id = :book_id')
            ->setParameter('book_id', $bookId);
    }

    private function joinUser($userId, QueryBuilder $qb)
    {
        $qb
            ->join('r.user', 'user')
            ->where('user.id = :user_id')
            ->setParameter('user_id', $userId);
    }

    private function filterReservation($search, QueryBuilder $qb): void
    {
        if (!empty($search)) {
            $qb
                ->where('r.createdAt LIKE :search')
                ->orWhere('r.updatedAt LIKE :search')
                ->orWhere('r.endBorrowingDate LIKE :search')
                ->orWhere('r.startBorrowingDate LIKE :search')
                ->orWhere('r.borrowedQuantity LIKE :search')
                ->orWhere('r.status LIKE :search')
                ->orWhere('r.requestedQuantity LIKE :search')
                ->orWhere('r.section LIKE :search')
                ->setParameter('search', "%{$search}%");
        }
    }

    private function filterUsers($search, QueryBuilder $qb): void
    {
        if (!empty($search)) {
            $qb
                ->where('u.username LIKE :search')
                ->orWhere('u.email LIKE :search')
                ->orWhere('u.address LIKE :search')
                ->orWhere('u.firstName LIKE :search')
                ->orWhere('u.lastName LIKE :search')
                ->orWhere('u.phoneNumber LIKE :search')
                ->setParameter('search', "%{$search}%");
        }
    }

    private function filterBooks($search, QueryBuilder $qb): void
    {
        if (!empty($search)) {
            $qb
                ->where('ca.code LIKE :search')
                ->orWhere('ca.title LIKE :search')
                ->orWhere('book.description LIKE :search')
                ->orWhere('book.isbn LIKE :search')
                ->orWhere('book.title LIKE :search')
                ->orWhere('book.publishDate LIKE :search')
                ->orWhere('book.author LIKE :search')
                ->orWhere('book.section LIKE :search')
                ->setParameter('search', "%{$search}%");
        }
    }
}
