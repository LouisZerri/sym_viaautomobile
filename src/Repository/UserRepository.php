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

    public function getByCollaborateurByMonth(string $month)
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
            ->andWhere('SUBSTRING(h.date_vente, 6, 2) = :value')
            ->setParameter('value', $month)
            ->groupBy('h.users')
            ->orderBy('mandat.nombre', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getMandatByMonth($month)
    {
        return $this->createQueryBuilder('u')
            ->select('u.nom, u.prenom, u.site_rattachement, sum(h.nombre) as nombre')
            ->innerJoin('u.mandatHistorics', 'h')
            ->andWhere('SUBSTRING(h.date_mandat, 6, 2) = :value')
            ->setParameter('value', $month)
            ->groupBy('h.users')
            ->orderBy('nombre', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getByCollaborateurByTrimester($trimestre)
    {
        $query = $this->createQueryBuilder('u')
            ->select('u.nom, 
                            u.prenom, 
                            u.site_rattachement, 
                            mandat.nombre as nombre, 
                            count(h.users) as vente, 
                            sum(h.livree) as livree, 
                            sum(h.financement) as financement,
                            sum(h.frais_mer) as fraisMER,
                            sum(h.garantie) as garantie')
            ->innerJoin('u.mandat', 'mandat')
            ->leftJoin('u.venteHistoriques', 'h');

        if($trimestre == 1)
        {
            $query
                ->andWhere('SUBSTRING(h.date_vente, 6, 2) BETWEEN 01 AND 03');
        }
        else if($trimestre == 2)
        {
            $query
                ->andWhere('SUBSTRING(h.date_vente, 6, 2) BETWEEN 04 AND 06');
        }
        else if($trimestre == 3)
        {
            $query
                ->andWhere('SUBSTRING(h.date_vente, 6, 2) BETWEEN 07 AND 09');
        }
        else if($trimestre == 4)
        {
            $query
                ->andWhere('SUBSTRING(h.date_vente, 6, 2) BETWEEN 10 AND 12');
        }

        return $query
            ->groupBy('h.users')
            ->orderBy('nombre', 'DESC')
            ->getQuery()
            ->getResult();

    }

    public function getMandatByTrimester($trimestre)
    {
        $query = $this->createQueryBuilder('u')
            ->select('u.nom, u.prenom, u.site_rattachement, sum(h.nombre) as nombre')
            ->innerJoin('u.mandatHistorics', 'h');

        if($trimestre == 1)
        {
            $query
                ->andWhere('SUBSTRING(h.date_mandat, 6, 2) BETWEEN 01 AND 03');
        }
        else if($trimestre == 2)
        {
            $query
                ->andWhere('SUBSTRING(h.date_mandat, 6, 2) BETWEEN 04 AND 06');
        }
        else if($trimestre == 3)
        {
            $query
                ->andWhere('SUBSTRING(h.date_mandat, 6, 2) BETWEEN 07 AND 09');
        }
        else if($trimestre == 4)
        {
            $query
                ->andWhere('SUBSTRING(h.date_mandat, 6, 2) BETWEEN 10 AND 12');
        }

        return $query
            ->groupBy('h.users')
            ->orderBy('nombre', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getUserForResetPassword($id, $token)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->andWhere('u.reset_token IS NOT NULL')
            ->andWhere('u.reset_token = :token')
            ->setParameter('token', $token)
            ->andWhere('u.reset_at > :date')
            ->setParameter('date', new \DateTime('-30 minutes'))
            ->getQuery()
            ->getOneOrNullResult();
    }




}
