<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 3/12/2019
 * Time: 09:48
 */

namespace Kookaburra\UserAdmin\Provider;

use App\Manager\Traits\EntityTrait;
use App\Provider\EntityProviderInterface;
use Kookaburra\UserAdmin\Entity\StudentNoteCategory;

/**
 * Class StudentNoteCategoryProvider
 * @package Kookaburra\UserAdmin\Provider
 */
class StudentNoteCategoryProvider implements EntityProviderInterface
{
    use EntityTrait;

    /**
     * @var string
     */
    private $entityName = StudentNoteCategory::class;
}