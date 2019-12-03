<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 5/12/2018
 * Time: 16:28
 */
namespace Kookaburra\UserAdmin\Repository;

use Kookaburra\UserAdmin\Entity\StudentNoteCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class StudentNoteCategoryRepository
 * @package App\Repository
 */
class StudentNoteCategoryRepository extends ServiceEntityRepository
{
    /**
     * StudentNoteCategoryRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentNoteCategory::class);
    }
}
