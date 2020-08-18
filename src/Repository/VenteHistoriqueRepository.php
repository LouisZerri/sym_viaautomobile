<?php

namespace App\Repository;

use App\Entity\VenteHistorique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VenteHistorique|null find($id, $lockMode = null, $lockVersion = null)
 * @method VenteHistorique|null findOneBy(array $criteria, array $orderBy = null)
 * @method VenteHistorique[]    findAll()
 * @method VenteHistorique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VenteHistoriqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VenteHistorique::class);
    }

    public function getMonthFromVente($mois, $nom)
    {
        return $this->createQueryBuilder('v')
            ->select('v.id, v.date_vente, v.immatriculation, v.livree, v.frais_mer, v.garantie, v.financement')
            ->leftJoin('v.users', 'user')
            ->andWhere('user.email = :val')
            ->andWhere('SUBSTRING(v.date_vente, 6, 2) = :value')
            ->setParameter('val', $nom)
            ->setParameter('value', $mois)
            ->getQuery()
            ->getResult();

    }

    // /**
    //  * @return VenteHistorique[] Returns an array of VenteHistorique objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VenteHistorique
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
