<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 6/12/2019
 * Time: 07:52
 */

namespace Kookaburra\UserAdmin\Provider;

use App\Manager\Traits\EntityTrait;
use App\Provider\EntityProviderInterface;
use Kookaburra\UserAdmin\Entity\FamilyRelationship;

/**
 * Class FamilyRelationshipProvider
 * @package Kookaburra\UserAdmin\Provider
 */
class FamilyRelationshipProvider implements EntityProviderInterface
{
    use EntityTrait;

    /**
     * @var string
     */
    private $entityName = FamilyRelationship::class;

    /**
     * findOneRelationship
     * @param array $item
     * @return FamilyRelationship
     */
    public function findOneRelationship(array $item): FamilyRelationship
    {
        $fr = $this->getRepository()->findOneByFamilyAdultChild($item);

        return $fr ?: new FamilyRelationship();
    }
}