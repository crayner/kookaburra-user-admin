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

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Kookaburra\UserAdmin\Entity\Country;

/**
 * Class CountryRepository
 * @package Kookaburra\UserAdmin\Repository
 */
class CountryRepository extends ServiceEntityRepository
{
    /**
     * CountryRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    /**
     * getCountyCodeList
     */
    public function getCountryCodeList(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.printable_name', 'ASC')
            ->addOrderBy('c.iddCountryCode', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
