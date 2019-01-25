<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    use DoctrineUtilsTrait;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function findLatest($page = 1, $limit = 25)
    {
        $sql = <<<SQL
SELECT p, a, t, c
                FROM AppBundle:Note n
                JOIN p.author a
                JOIN p.category c
                LEFT JOIN p.tags t
                WHERE p.publishedAt <= :now
                AND c.publishedAt IS NOT NULL
ORDER BY p.publishedAt DESC
SQL;

        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('now', new \DateTime('now'))
        ;

        return $this->createPaginator($query, $page, $limit);
    }
}
