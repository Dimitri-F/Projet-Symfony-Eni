<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function findFilteredSorties($data, $isOrganisateur, $isInscrit, $isNotInscrit, $isPassed)
    {
        $qb = $this->createQueryBuilder('s');

        if ($data['site']) {
            $qb->andWhere('s.site = :site')
                ->setParameter('site', $data['site']);
        }

        if ($data['nomSortie']) {
            $qb->andWhere('s.nom LIKE :nomSortie')
                ->setParameter('nomSortie', '%'.$data['nomSortie'].'%');
        }

        if ($data['dateStart']) {
            $qb->andWhere('s.dateDebut >= :dateStart')
                ->setParameter('dateStart', $data['dateStart']);
        }

        if ($data['dateEnd']) {
            $qb->andWhere('s.dateDebut <= :dateEnd')
                ->setParameter('dateEnd', $data['dateEnd']);
        }

        if ($isOrganisateur) {
            $qb->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $data['userId']);
        }

        if ($isInscrit && $isNotInscrit) {
        } else {
            if ($isInscrit) {
                $qb->andWhere('s.id IN (:id)')
                    ->setParameter('id', $data['inscrit']);
            }

            if ($isNotInscrit) {
                $qb->andWhere('s.id NOT IN (:id)')
                    ->setParameter('id', $data['inscrit']);
            }
        }

        if ($isPassed) {
            $now = new \DateTime(null, new \DateTimeZone('Europe/Paris')); // Ajustez le fuseau horaire à votre localisation
            $qb->andWhere('s.dateDebut > :now')
                ->setParameter('now', $now);
        }


        return $qb->getQuery()->getResult();
    }


//    /**
//     * @return Sortie[] Returns an array of Sortie objects
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

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
