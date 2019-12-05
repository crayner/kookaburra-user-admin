<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 23/11/2018
 * Time: 15:27
 */
namespace Kookaburra\UserAdmin\Repository;

use Kookaburra\UserAdmin\Entity\Family;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Kookaburra\UserAdmin\Form\Entity\ManageSearch;

/**
 * Class FamilyRepository
 * @package Kookaburra\UserAdmin\Repository
 */
class FamilyRepository extends ServiceEntityRepository
{
    /**
     * FamilyRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Family::class);
    }

    /**
     * findBySearch
     * @param ManageSearch $search
     * @return array
     */
    public function findBySearch(ManageSearch $search): array
    {
        return $this->createQueryBuilder('f')
            ->select(['f','a','c'])
            ->leftJoin('f.adults', 'a')
            ->leftJoin('f.children', 'c')
            ->where('f.name LIKE :search')
            ->setParameter('search', '%'.$search->getSearch().'%')
            ->getQuery()
            ->getResult();
    }
}
