<?php
/**
 * Created by PhpStorm.
 * User: andres
 * Date: 18/01/19
 * Time: 14:42
 */

namespace App\Repository;


use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

trait DoctrineUtilsTrait
{
    protected function createPaginator(Query $query, $page, $limit = 10): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage($page);

        return $paginator;
    }


    /**
     * Removes all non-alphanumeric characters except whitespaces.
     *
     * @param string $query
     *
     * @return string
     */
    protected function sanitizeSearchQuery($query)
    {
        return preg_replace('/[^[:alnum:] ]/', '', trim(preg_replace('/[[:space:]]+/', ' ', $query)));
    }

    /**
     * Splits the search query into terms and removes the ones which are irrelevant.
     *
     * @param string $searchQuery
     *
     * @return array
     */
    protected function extractSearchTerms($searchQuery)
    {
        $terms = array_unique(explode(' ', mb_strtolower($searchQuery)));

        return array_filter($terms, function ($term) {
            return 2 <= mb_strlen($term);
        });
    }
}