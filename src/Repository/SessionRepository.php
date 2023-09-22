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
        
        // Créer un query builder
        $sub = $entityManager->createQueryBuilder();

        $queryBuilder = $sub;

        // Sélectionner tous les stagiaires d'une session dont l'id est passé en paramètre
        $queryBuilder->select('s')
            ->from('App\Entity\Stagiaire', 's')
            ->leftJoin('s.sessions', 'se')
            ->where('se.id = :id');

        $sub = $entityManager->createQueryBuilder();
        // Sélectionner tous les stagiaires qui ne SONT PAS(NOT IN) dans le résultat précédent
        // On obtient donc les stagiaires non inscrits pour une session défini
        $sub->select('st')
            ->from('App\Entity\Stagiaire', 'st')
            ->where($sub->expr()->notIn('st.id', $queryBuilder->getDQL()))
            // Requête paramétrée
            ->setParameter('id', $session_id)
            // trier la liste des stagiaires sur le nom de famille
            ->orderBy('st.nom');

        // renvoyer le résultat
        $query = $sub->getQuery();
        return $query->getResult();
    }

    // Fonction requête DQL pour afficher les modules non présente dans la session
    public function findNonProgrammer($session_id)
    {
        // Récupérer l'entity manager
        $entityManager = $this->getEntityManager();

        // Créer un query builder
        $sub = $entityManager->createQueryBuilder();

        // On copie le query builder initial dans une autre variable
        $queryBuilder = $sub;

        // Sélectionner tous les modules d'une session dont l'id est passé en paramètre
        $queryBuilder->select('m')
            ->from('App\Entity\Programme', 'm')
            ->leftJoin('m.session', 'se')
            ->where('se.id = :id');

        // Créer un autre query builder
        $sub = $entityManager->createQueryBuilder();
        // Sélectionner tous les modules qui ne SONT PAS(NOT IN) dans le résultat précédent
        // On obtient donc les modules non présente dans la session défini
        $sub->select('mo')
            ->from('App\Entity\Modules', 'mo')
            ->where($sub->expr()->notIn('mo.id', $queryBuilder->getDQL()))
            // Requête paramétrée
            ->setParameter('id', $session_id)
            // trier la liste des modules sur le nom
            ->orderBy('mo.nomModule');
        
        // On obtient la requête DQL finale en utilisant getQuery();
        $query = $sub->getQuery();
        // renvoyer le résultat
        return $query->getResult();
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
