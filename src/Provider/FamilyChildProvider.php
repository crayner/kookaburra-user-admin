<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 6/12/2019
 * Time: 11:29
 */

namespace Kookaburra\UserAdmin\Provider;

use App\Manager\Traits\EntityTrait;
use App\Provider\EntityProviderInterface;
use Kookaburra\UserAdmin\Entity\FamilyChild;

/**
 * Class FamilyChildProvider
 * @package Kookaburra\UserAdmin\Provider
 */
class FamilyChildProvider implements EntityProviderInterface
{
    use EntityTrait;

    /**
     * @var string
     */
    private $entityName = FamilyChild::class;
}