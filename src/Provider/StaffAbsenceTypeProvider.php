<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 3/12/2019
 * Time: 13:11
 */

namespace Kookaburra\UserAdmin\Provider;

use App\Manager\Traits\EntityTrait;
use App\Provider\EntityProviderInterface;
use Kookaburra\UserAdmin\Entity\StaffAbsenceType;

/**
 * Class StaffAbsenceTypeProvider
 * @package Kookaburra\UserAdmin\Pagination
 */
class StaffAbsenceTypeProvider implements EntityProviderInterface
{
    use EntityTrait;

    /**
     * @var string
     */
    private $entityName = StaffAbsenceType::class;
}