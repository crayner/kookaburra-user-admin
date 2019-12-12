<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 12/12/2019
 * Time: 12:22
 */

namespace Kookaburra\UserAdmin\Validator;

use Kookaburra\UserAdmin\Entity\PersonField;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class PersonFieldOptionsValidator
 * @package Kookaburra\UserAdmin\Validator
 */
class PersonFieldOptionsValidator extends ConstraintValidator
{
    /**
     * validate
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof PersonField)
            return ;

        switch($value->getType()) {
            case 'varchar':
                if (empty($value->getOptions()) || strval(intval($value->getOptions())) !== $value->getOptions() || intval($value->getOptions()) > 255)
                    $this->context->buildViolation('personfield.options.error.varchar')
                        ->setTranslationDomain('UserAdmin')
                        ->atPath('options')
                        ->addViolation();
                break;
            case 'text':
                if (empty($value->getOptions()) || strval(intval($value->getOptions())) !== $value->getOptions())
                    $this->context->buildViolation('personfield.options.error.text')
                        ->setTranslationDomain('UserAdmin')
                        ->atPath('options')
                        ->addViolation();
                break;
            case 'select':
                if (empty($value->getOptions()) || implode( ',', explode(',', $value->getOptions())) !== $value->getOptions())
                    $this->context->buildViolation('personfield.options.error.select')
                        ->setTranslationDomain('UserAdmin')
                        ->atPath('options')
                        ->addViolation();
                break;
            default:
                $value->setOptions(null);
        }

        return $value;
    }

}