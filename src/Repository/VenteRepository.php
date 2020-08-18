<?php

namespace App\Repository;

use App\Entity\Vente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vente[]    findAll()
 * @method Vente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vente::class);
    }

    public function getVenteByUser($value)
    {
        return $this
            ->createQueryBuilder('v')
            ->select('count(v.id) as vente')
            ->leftJoin('v.users', 'user')
            ->andWhere('user.email = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getVenteByLivree($value)
    {
        return $this
            ->createQueryBuilder('v')
            ->select('count(v.id) as livree')
            ->leftJoin('v.users', 'user')
            ->andWhere('user.email = :val')
            ->andWhere('v.livree = 1')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getVenteByFraisMER($value)
    {
        return $this
            ->createQueryBuilder('v')
            ->select('count(v.id) as fraisMER')
            ->leftJoin('v.users', 'user')
            ->andWhere('user.email = :val')
            ->andWhere('v.frais_mer = 1')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getVenteByGarantie($value)
    {
        return $this
            ->createQueryBuilder('v')
            ->select('count(v.id) as garantie')
            ->leftJoin('v.users', 'user')
            ->andWhere('user.email = :val')
            ->andWhere('v.garantie = 1')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getVenteByFinancement($value)
    {
        return $this
            ->createQueryBuilder('v')
            ->select('count(v.id) as financement')
            ->leftJoin('v.users', 'user')
            ->andWhere('user.email = :val')
            ->andWhere('v.financement = 1')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function enTeteChallengeVente()
    {
        return $this->createQueryBuilder('v')
            ->select('user.nom, user.prenom, count(v.id) as vente')
            ->leftJoin('v.users','user')
            ->groupBy('user.id')
            ->orderBy('vente', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }




    // /**
    //  * @return Vente[] Returns an array of Vente objects
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
    public function findOneBySomeField($value): ?Vente
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
