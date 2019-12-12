<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 12/12/2019
 * Time: 12:21
 */

namespace Kookaburra\UserAdmin\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class PersonFieldOptions
 * @package Kookaburra\UserAdmin\Validator
 * @Annotation
 */
class PersonFieldOptions extends Constraint
{
    /**
     * getTargets
     * @return array|string
     */
    public function getTargets()
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}