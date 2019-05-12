<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Reservation;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function countAllBooks()
    {
        return $this->createQueryBuilder('b')
            ->select('SUM(b.quantity) as total')
            ->getQuery()
            ->getSingleResult();
    }

    public function getFindAllQuery($search)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c as book')
            ->join('c.category', 'ca');

        $this->getReservedBooks($qb);
        $this->filterBooks($search, $qb);

        return $qb->getQuery();
    }

    /**
     * Select the reserved books
     *
     * @param QueryBuilder $qb
     */
    private function getReservedBooks(QueryBuilder $qb): void
    {
        $qb
            ->addSelect(sprintf(
                "(SELECT SUM(r.borrowedQuantity)
            FROM %s as r
            WHERE r MEMBER OF c.reservations 
            AND :now BETWEEN r.startBorrowingDate AND r.endBorrowingDate
            AND r.status in (:book_status) ) AS reservedBooks"
                , Reservation::class))
            ->setParameter('now', new DateTime('now'))
            ->setParameter('book_status', [Reservation::FULLY_ACCEPTED, Reservation::PARTIALLY_ACCEPTED]);
    }

    /**
     * Filter the books by the given word
     *
     * @param mixed $search
     * @param QueryBuilder $qb
     */
    private function filterBooks($search, QueryBuilder $qb): void
    {
        if (!empty($search)) {
            $qb
                ->where('ca.code LIKE :search')
                ->orWhere('ca.title LIKE :search')
                ->orWhere('c.description LIKE :search')
                ->orWhere('c.isbn LIKE :search')
                ->orWhere('c.title LIKE :search')
                ->orWhere('c.publishDate LIKE :search')
                ->orWhere('c.author LIKE :search')
                ->orWhere('c.section LIKE :search')
                ->setParameter('search', "%{$search}%");
        }
    }
}
