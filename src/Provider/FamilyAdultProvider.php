<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 6/12/2019
 * Time: 14:52
 */

namespace Kookaburra\UserAdmin\Provider;

use App\Manager\Traits\EntityTrait;
use App\Provider\EntityProviderInterface;
use Kookaburra\UserAdmin\Entity\FamilyAdult;

/**
 * Class FamilyAdultProvider
 * @package Kookaburra\UserAdmin\Provider
 */
class FamilyAdultProvider implements EntityProviderInterface
{
    use EntityTrait;

    private $entityName = FamilyAdult::class;
}