<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    // Fonction requête DQL pour récupérer les sessions qui sont actuellement en cours
    public function findCurrentSessions(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateSession <= :date')
            ->andWhere('s.dateFin >= :date')
            ->setParameter('date', new \DateTime())
            ->orderBy('s.dateSession', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // Fonction requête DQL pour récupérer les sessions qui sont à venir
    public function findFutureSessions(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateSession > :date')
            ->setParameter('date', new \DateTime())
            ->orderBy('s.dateSession', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // Fonction requête DQL pour récupérer les sessions qui sont passées
    public function findPastSessions(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateFin < :date')
            ->setParameter('date', new \DateTime())
            ->orderBy('s.dateSession', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // Fonction requête DQL pour récupérer les sessions appartenant à une formation
    public function findSessionsByFormation(int $id): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.formation = :id')
            ->setParameter('id', $id)
            ->orderBy('s.dateSession', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // Fonction requête DQL pour récupérer les sessions appartenant à un formateur
    public function findSessionsByFormateur(int $id): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.formateur = :id')
            ->setParameter('id', $id)
            ->orderBy('s.dateSession', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Session[] Returns an array of Session objects
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

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
