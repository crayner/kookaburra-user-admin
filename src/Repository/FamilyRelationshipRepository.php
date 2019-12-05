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

use Doctrine\ORM\NonUniqueResultException;
use Kookaburra\UserAdmin\Entity\FamilyRelationship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class FamilyRelationshipRepository
 * @package Kookaburra\UserAdmin\Repository
 */
class FamilyRelationshipRepository extends ServiceEntityRepository
{
    /**
     * FamilyRelationshipRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FamilyRelationship::class);
    }

    /**
     * findOneByFamilyAdultChild
     * @param array $item
     * @return mixed
     */
    public function findOneByFamilyAdultChild(array $item): ?FamilyRelationship
    {
        try {
            return $this->createQueryBuilder('fr')
                ->join('fr.family', 'f')
                ->join('fr.adult', 'a')
                ->join('fr.child', 'c')
                ->where('f.id = :family')
                ->andWhere('a.id = :adult')
                ->andWhere('c.id = :child')
                ->setParameters(['family' => $item['family'], 'adult' => $item['adult'], 'child' => $item['child']])
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
