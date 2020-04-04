<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 * (c) 2020 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 5/04/2020
 * Time: 07:20
 */

namespace Kookaburra\UserAdmin\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class Username
 * @package Kookaburra\UserAdmin\Validator
 * @Annotation
 */
class Username extends Constraint
{
    /**
     * getTargets
     * @return array|string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}