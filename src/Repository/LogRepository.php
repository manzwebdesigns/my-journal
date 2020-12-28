<?php namespace App\Repository;

use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    /**
     * LogRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    /**
     * @param $date
     *
     * @return Log|null
     * @throws NonUniqueResultException
     */
    public function findOneByLogDate($date): ?Log
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.log_date = :val')
            ->setParameter('val', $date)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
