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

    // Fonction requête DQL pour afficher les stagiaires non inscrit
    public function findNonInscrits($session_id)
    {

        // Récupérer l'entity manager
        $entityManager = $this->getEntityManager();

        // Créer un query builder comme sous requête
        $subQuery = $entityManager->createQueryBuilder();
        // Créer la sous-requête pour sélectionner les stagiaires inscrits
        $subQuery->select('stagiaire.id')
            ->from('App\Entity\Stagiaire', 'stagiaire')
            ->innerJoin('stagiaire.sessions', 'session')
            ->where('session.id = :session_id');

        // Créer un query builder
        $qb = $entityManager->createQueryBuilder();
        // Utiliser la sous-requête pour sélectionner les stagiaires non inscrits
        $qb->select('st')
            ->from('App\Entity\Stagiaire', 'st')
            ->where($qb->expr()->notIn('st.id', $subQuery->getDQL()))
            ->setParameter('session_id', $session_id)
            ->orderBy('st.nom');

        // Exécuter la requête
        $result = $qb->getQuery()->getResult();

        return $result;
    }
    

    // Fonction requête DQL pour afficher les modules non présente dans la session
    public function findNonProgrammer($session_id)
    {
        // Récupérer l'entity manager
        $entityManager = $this->getEntityManager();

        // Créer un query builder comme sous requête
        $subQuery = $entityManager->createQueryBuilder();
        // Créer un query builder pour la sous-requête qui sélectionne les IDs des modules déjà programmés
        // On extrait l'ID de l'entité Module liée à un programme avec la fonction IDENTITY()
        $subQuery->select('IDENTITY(programme.module)')
            ->from('App\Entity\Programme', 'programme')
            ->where('programme.session = :session_id');

        // Créer un query builder
        $qb = $entityManager->createQueryBuilder();
        // Utiliser la sous-requête pour sélectionner les modules non présents dans la session
        $qb->select('module')
            ->from('App\Entity\Modules', 'module')
            // On utilise la fonction NOT IN pour sélectionner les modules dont l'ID n'est pas dans la sous-requête
            ->where($qb->expr()->notIn('module.id', $subQuery->getDQL()))
            ->setParameter('session_id', $session_id)
            ->orderBy('module.nomModule');

        // Exécuter la requête
        $result = $qb->getQuery()->getResult();

        return $result;
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
