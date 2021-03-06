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
 * Time: 07:21
 */

namespace Kookaburra\UserAdmin\Validator;

use Kookaburra\UserAdmin\Entity\Person;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UsernameValidator
 * @package Kookaburra\UserAdmin\Validator
 */
class UsernameValidator extends ConstraintValidator
{
    /**
     * validate
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        dump($value);
        if (!$value instanceof Person || $value->isCanLogin() === false)
            return;

        if ($value->getUsername() === null || $value->getUsername() === '')
            $value->setUsername($value->getEmail());

        if ($value->getUsername() === null || $value->getUsername() === '')
            $this->context->buildViolation('This value is not valid.')
                ->setTranslationDomain('UserAdmin')
                ->atPath('username')
                ->addViolation();
    }
}