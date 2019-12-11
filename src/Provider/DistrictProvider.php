<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 11/12/2019
 * Time: 13:21
 */

namespace Kookaburra\UserAdmin\Provider;

use App\Manager\Traits\EntityTrait;
use App\Provider\EntityProviderInterface;
use Kookaburra\UserAdmin\Entity\District;
use Kookaburra\UserAdmin\Entity\Family;
use Kookaburra\UserAdmin\Entity\Person;

/**
 * Class DistrictProvider
 * @package Kookaburra\UserAdmin\Provider
 */
class DistrictProvider implements EntityProviderInterface
{
    use EntityTrait;

    /**
     * @var string
     */
    private $entityName = District::class;

    /**
     * countUsage
     * @param District $district
     * @return int
     */
    public function countUsage(District $district): int
    {
        $result = $this->getRepository(Person::class)->countDistrictUsage($district);
        if ($result > 0)
            return $result;
        $result += $this->getRepository(Family::class)->countDistrictUsage($district);
        return $result;
    }
}