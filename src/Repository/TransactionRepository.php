<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findByAccountId($account_id, $offset = 0, $limit = 10)
    {
        // Create a query builder
        $qb = $this->getEntityManager()->createQueryBuilder();

        // Select from the Transaction entity
        $qb->select('t')
            ->from('App\Entity\Transaction', 't')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->eq('t.source_account_id', ':account_id'),
                    $qb->expr()->eq('t.destination_account_id', ':account_id')
                )
            )
            ->setParameter('account_id', $account_id)
            ->orderBy('t.transaction_date', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        // Execute the query and return the results
        return $qb->getQuery()->getResult();
    }
}
