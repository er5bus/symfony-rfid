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

    public function findByUserQuery($userId, $search)
    {
        $qb = $this->createQueryBuilder('r')
            ->addSelect('book', 'ca', 'u')
            ->join('r.book', 'book')
            ->join('book.category', 'ca')
        ;
        $this->joinUser($userId, $qb);

        $this->filterReservation($search, $qb);
        $this->filterBooks($search, $qb);
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
        $this->reservedBooks($qb);

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
            ->join('r.user', 'u')
            ->where('u.id = :user_id')
            ->setParameter('user_id', $userId);
    }

    private function filterReservation($search, QueryBuilder $qb): void
    {
        if (!empty($search)) {
            $qb
                ->andWhere('r.createdAt LIKE :search_reservation')
                ->orWhere('r.updatedAt LIKE :search_reservation')
                ->orWhere('r.endBorrowingDate LIKE :search_reservation')
                ->orWhere('r.startBorrowingDate LIKE :search_reservation')
                ->orWhere('r.borrowedQuantity LIKE :search_reservation')
                ->orWhere('r.status LIKE :search_reservation')
                ->orWhere('r.requestedQuantity LIKE :search_reservation')
                ->setParameter('search_reservation', "%{$search}%");
        }
    }

    private function filterUsers($search, QueryBuilder $qb): void
    {
        if (!empty($search)) {
            $qb
                ->orWhere('u.username LIKE :search_user')
                ->orWhere('u.email LIKE :search_user')
                ->orWhere('u.address LIKE :search_user')
                ->orWhere('u.firstName LIKE :search_user')
                ->orWhere('u.lastName LIKE :search_user')
                ->orWhere('u.phoneNumber LIKE :search_user')
                ->setParameter('search_user', "%{$search}%");
        }
    }

    private function filterBooks($search, QueryBuilder $qb): void
    {
        if (!empty($search)) {
            $qb
                ->orWhere('ca.code LIKE :search_book')
                ->orWhere('ca.title LIKE :search_book')
                ->orWhere('book.description LIKE :search_book')
                ->orWhere('book.isbn LIKE :search_book')
                ->orWhere('book.title LIKE :search_book')
                ->orWhere('book.publishDate LIKE :search_book')
                ->orWhere('book.author LIKE :search_book')
                ->orWhere('book.section LIKE :search_book')
                ->setParameter('search_book', "%{$search}%");
        }
    }
}
