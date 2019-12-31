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
 * Date: 23/08/2019
 * Time: 14:41
 */

namespace Kookaburra\UserAdmin\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class GeneratePasswordType extends AbstractType
{
    /**
     * getBlockPrefix
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'generate_password';
    }

    /**
     * getParent
     * @return string|null
     */
    public function getParent()
    {
        return RepeatedType::class;
    }
}