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

use Kookaburra\UserAdmin\Entity\FamilyUpdate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class FamilyUpdateRepository
 * @package Kookaburra\UserAdmin\Repository
 */
class FamilyUpdateRepository extends ServiceEntityRepository
{
    /**
     * FamilyUpdateRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FamilyUpdate::class);
    }
}
