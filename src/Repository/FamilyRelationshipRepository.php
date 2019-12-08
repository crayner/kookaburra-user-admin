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

use Doctrine\DBAL\Driver\PDOException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Kookaburra\UserAdmin\Entity\Family;
use Kookaburra\UserAdmin\Entity\FamilyRelationship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Kookaburra\UserAdmin\Entity\Person;

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

    /**
     * removeFamilyChild
     * @param Family $family
     * @param Person $child
     * @param array $data
     * @return array
     */
    public function removeFamilyChild(Family $family, Person $child, array $data = []): array
    {
        try {
            foreach ($this->findBy(['family' => $family, 'child' => $child]) as $fc)
                $this->getEntityManager()->remove($fc);
            $this->getEntityManager()->flush();
        } catch (\PDOException | PDOException | ORMException | OptimisticLockException $e) {
            $data['status'] = 'error';
            $data['errors'][] = ['class' => 'error', 'message' => ['return.error.1', [], 'messages']];
        }
        return $data;
    }

    /**
     * removeFamilyAdult
     * @param Family $family
     * @param Person $adult
     * @param array $data
     * @return array
     */
    public function removeFamilyAdult(Family $family, Person $adult, array $data = []): array
    {
        try {
            foreach ($this->findBy(['family' => $family, 'adult' => $adult]) as $fa)
                $this->getEntityManager()->remove($fa);
            $this->getEntityManager()->flush();
        } catch (\PDOException | PDOException | ORMException | OptimisticLockException $e) {
            $data['status'] = 'error';
            $data['errors'][] = ['class' => 'error', 'message' => ['return.error.1', [], 'messages']];
        }
        return $data;
    }
}
