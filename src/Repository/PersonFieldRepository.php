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

use Kookaburra\UserAdmin\Entity\PersonField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PersonFieldRepository
 * @package Kookaburra\UserAdmin\Repository
 */
class PersonFieldRepository extends ServiceEntityRepository
{
    /**
     * ApplicationFormRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonField::class);
    }

    /**
     * getCustomFields
     * @param array $where
     * @param array $data
     * @return array
     */
    public function getCustomFields(array $where, array $data): array
    {
        $query = $this->createQueryBuilder('pf')
            ->where('pf.active = :yes');

        foreach($where as $search)
            $query = $query->andWhere($search);

        $query = $query->setParameters($data)
            ->setParameter('yes', 'Y')
            ->getQuery();

        return $query
            ->getResult();
    }
}
