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
use Doctrine\ORM\NoResultException;
use Kookaburra\UserAdmin\Entity\District;
use Kookaburra\UserAdmin\Entity\Family;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
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
     * @return array
     */
    public function findBySearch(): array
    {
        return $this->createQueryBuilder('f')
            ->select(['f.id','f.name','f.status'])
            ->orderBy('f.name')
            ->getQuery()
            ->getResult();
    }

    /**
     * countDistrictUsage
     * @param District $district
     * @return int
     */
    public function countDistrictUsage(District $district): int
    {
        try {
            return $this->createQueryBuilder('f')
                ->select('COUNT(f.id)')
                ->where('f.homeAddressDistrict = :district')
                ->setParameter('district', $district)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            return 0;
        }
    }
}
