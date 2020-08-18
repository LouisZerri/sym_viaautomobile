<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getByCollaborateur()
    {
        return $this->createQueryBuilder('u')
            ->select('u.nom, 
                            u.prenom, 
                            u.site_rattachement, 
                            mandat.nombre, 
                            count(h.users) as vente, 
                            sum(h.livree) as livree, 
                            sum(h.financement) as financement,
                            sum(h.frais_mer) as fraisMER,
                            sum(h.garantie) as garantie')
            ->innerJoin('u.mandat', 'mandat')
            ->leftJoin('u.venteHistoriques', 'h')
            ->groupBy('h.users')
            ->orderBy('mandat.nombre', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getBySiteForMandat()
    {
        return $this->createQueryBuilder('u')
            ->select('u.site_rattachement, sum(m.nombre) as mandat')
            ->innerJoin('u.mandat', 'm')
            ->groupBy('u.site_rattachement')
            ->getQuery()
            ->getResult();
    }

    public function getBySiteForVente()
    {
        return $this->createQueryBuilder('u')
            ->select('u.site_rattachement, count(h.users) as vente')
            ->innerJoin('u.venteHistoriques','h')
            ->groupBy('u.site_rattachement')
            ->getQuery()
            ->getResult();
            
    }

    public function getByConsolidation()
    {
        return $this->createQueryBuilder('u')
            ->select('u.nom, u.prenom, u.email, m.nombre, count(h.users) as vente')
            ->leftJoin('u.mandat', 'm')
            ->innerJoin('u.venteHistoriques', 'h')
            ->andWhere('LOWER(SUBSTRING(u.email, LOCATE(\'@\', u.email) + 1)) = \'viaautomobile.fr\'')
            ->groupBy('h.users')
            ->getQuery()
            ->getResult();
    }







    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
