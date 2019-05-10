<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function countAllUsers()
    {
        return $this->createQueryBuilder('b')
            ->select('count(b.id) as total')
            ->getQuery()
            ->getSingleResult();
    }

    public function getFindAllQuery($search)
    {
        $qb = $this->createQueryBuilder('u');
        $this->filterUsers($search, $qb);

        return $qb->getQuery();
    }

    /**
     * Filter the books by the given word
     *
     * @param mixed $search
     * @param QueryBuilder $qb
     */
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
}
