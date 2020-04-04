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
 * Date: 2/09/2019
 * Time: 16:13
 */

namespace Kookaburra\UserAdmin\Form;

use App\Provider\ProviderFactory;
use Kookaburra\SystemAdmin\Entity\Setting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PasswordGeneratorType
 * @package Kookaburra\UserAdmin\Form
 */
class PasswordGeneratorType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * PasswordGeneratorType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * getParent
     * @return string|null
     */
    public function getParent()
    {
        return PasswordType::class;
    }

    /**
     * configureOptions
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $provider = ProviderFactory::create(Setting::class);
        $resolver->setDefault('generateButton', [
            'title' => $this->translator->trans('Generate', [], 'UserAdmin'),
            'class' => 'button generatePassword -ml-px button-right',
            'passwordPolicy' => [
                'alpha' => $provider->getSettingByScopeAsBoolean('System', 'passwordPolicyAlpha'),
                'numeric' => $provider->getSettingByScopeAsBoolean('System', 'passwordPolicyNumeric'),
                'punctuation' => $provider->getSettingByScopeAsBoolean('System', 'passwordPolicyNonAlphaNumeric'),
                "minLength" => $provider->getSettingByScopeAsInteger('System', 'passwordPolicyMinLength'),
            ],
            'onClick' => 'generateNewPassword',
            'alertPrompt' => $this->translator->trans('Copy this password if required', [],'UserAdmin'),
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['generateButton'] = $options['generateButton'];
    }
}