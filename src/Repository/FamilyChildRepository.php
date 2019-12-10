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

use App\Util\SchoolYearHelper;
use Kookaburra\UserAdmin\Entity\Family;
use Kookaburra\UserAdmin\Entity\FamilyChild;
use Kookaburra\UserAdmin\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class FamilyChildRepository
 * @package Kookaburra\UserAdmin\Repository
 */
class FamilyChildRepository extends ServiceEntityRepository
{
    /**
     * FamilyChildRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FamilyChild::class);
    }

    /**
     * getChildrenFromParent
     * @param Person $person
     * @return array
     */
    public function findByParent(Person $person): array
    {
        $result = $this->createQueryBuilder('fc')
            ->leftJoin('fc.family', 'f')
            ->leftJoin('f.adults', 'fa')
            ->where('fa.person = :person')
            ->setParameter('person', $person)
            ->getQuery()
            ->getResult();

        $children = [];
        foreach($result as $child)
            $children[] = $child->getPerson()->getId();

        return $children;
    }

    /**
     * findByFamily
     * @param Family|integer $family
     * @return array
     */
    public function findByFamily($family, bool $asArray = false): array
    {
        $query = $this->createQueryBuilder('c')
            ->join('c.family', 'f')
            ->where('f.id = :family')
            ->setParameter('family', $family instanceof Family ? $family->getId() : $family)
            ->leftJoin('c.person', 'p')
            ->orderBy('p.surname', 'ASC')
            ->addOrderBy('p.firstName', 'ASC');

        if ($asArray)
            return $query->select(['p.title','p.surname','p.firstName AS first','p.preferredName AS preferred','p.image_240 AS photo','p.status','c.id AS child_id','c.comment','f.id AS family_id','p.id AS person'])
                ->getQuery()
                ->getResult();
        return $query->select(['p', 'c'])
            ->getQuery()
            ->getResult();
    }
}
