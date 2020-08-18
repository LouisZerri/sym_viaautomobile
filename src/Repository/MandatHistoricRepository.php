<?php

namespace App\Repository;

use App\Entity\MandatHistoric;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MandatHistoric|null find($id, $lockMode = null, $lockVersion = null)
 * @method MandatHistoric|null findOneBy(array $criteria, array $orderBy = null)
 * @method MandatHistoric[]    findAll()
 * @method MandatHistoric[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MandatHistoricRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MandatHistoric::class);
    }

    public function getMonthFromMandat($mois, $nom)
    {
        return $this->createQueryBuilder('m')
            ->select('m.date_mandat, m.nombre')
            ->leftJoin('m.users', 'user')
            ->andWhere('user.email = :val')
            ->andWhere('SUBSTRING(m.date_mandat, 6, 2) = :value')
            ->setParameter('val', $nom)
            ->setParameter('value', $mois)
            ->getQuery()
            ->getResult();

    }

    public function getAllMandat()
    {
        return $this->createQueryBuilder('h')
            ->select('u.nom, u.prenom, u.site_rattachement, sum(h.nombre) as nombre')
            ->leftJoin('h.users', 'u')
            ->groupBy('h.users')
            ->orderBy('nombre', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return MandatHistoric[] Returns an array of MandatHistoric objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MandatHistoric
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
