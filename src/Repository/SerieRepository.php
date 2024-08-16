<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    public function findBestSeriesWithSpecificGenre(array $genres, string $terme): array
    {

         $q = $this->createQueryBuilder('s')
            ->orderBy('s.nbVote', 'DESC')
            ->addOrderBy('s.popularity', 'DESC')
            ->addOrderBy('s.name', 'ASC')
            ->andWhere('s.name like :terme or s.firstAirDate < :airdate')
            ->setParameter(':terme', $terme)
            ->setParameter(':airdate', new \DateTime('2018-01-01'))
            ->andWhere('s.status like :status')
            ->setParameter(':status', 'returning');

         $q->andWhere('s.Genders IN (:genre)')
         ->setParameter(':genre', $genres);

         $expr = $q->expr();

         $cond1 = $expr->like('s.name', ':terme2');
         $cond2 = $expr->gte('s.firstAirDate', ':seuil');

         $q->andWhere($expr->orX($cond1, $cond2));
         $q->setParameter(':seuil', new \DateTime('2010-01-01'));
         $q->setParameter(':terme2', '%u%');

         return $q->getQuery()
                ->getResult();

    }

    public function getBestSeriesInDQL(): array
    {
        $dql = "SELECT s FROM App\Entity\Serie AS s 
        WHERE s.name like :terme AND s.nbVote > 6 
        OR (s.popularity > 50 AND s.firstAirDate >= '2019-01-01') 
        ORDER BY s.nbVote ASC, s.popularity ASC";

        return $this->getEntityManager()->createQuery($dql)
            ->setParameter('terme', '%ili%')
            ->execute();
    }

    public function getBestSeriesInRawSQL():array
    {
        $sql = "SELECT first_air_date AS firstAirDate, 
                last_air_date as lastAirDate, 
                date_created as dateCreated, 
                date_modified as dateModified 
                FROM Serie AS s 
                WHERE s.first_air_date > :seuil 
                ORDER BY s.popularity DESC";

        $connection = $this->getEntityManager()->getConnection();
        return $connection->prepare($sql)
            ->executeQuery(['seuil' => '2018-02-14'])
            ->fetchAllAssociative(); // Tu me le met dans un tableau associatif (système key-value)
    }

    //    /**
    //     * @return Serie[] Returns an array of Serie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Serie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
