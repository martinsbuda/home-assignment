<?php

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Account>
 *
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @return Account[] Returns an array of Account objects by client ID
     */
    public function findByClientId($clientId)
    {
        // Retrieve accounts associated with the given client ID along with their currency information
        return $this->createQueryBuilder('a')
            ->innerJoin('a.currency', 'c')
            ->addSelect('c')
            ->where('a.client_id = :client_id')
            ->setParameter('client_id', $clientId)
            ->getQuery()
            ->getResult();
    }
}
