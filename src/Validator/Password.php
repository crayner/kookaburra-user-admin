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
 * Date: 25/07/2019
 * Time: 14:00
 */

namespace Kookaburra\UserAdmin\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class Password
 * @package Kookaburra\UserAdmin\Validator
 */
class Password extends Constraint
{
    /**
     * @var bool
     */
    public $assumeCurrentUser = true;
}