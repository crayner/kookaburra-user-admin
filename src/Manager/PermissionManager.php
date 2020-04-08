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
 * Date: 8/04/2020
 * Time: 09:13
 */

namespace Kookaburra\UserAdmin\Manager;

use App\Manager\SpecialInterface;
use App\Provider\ProviderFactory;
use App\Util\TranslationsHelper;
use Kookaburra\SystemAdmin\Entity\Action;
use Kookaburra\SystemAdmin\Entity\Role;
use Symfony\Component\Form\Util\StringUtil;

/**
 * Class PermissionManager
 * @package Kookaburra\UserAdmin\Manager
 */
class PermissionManager implements SpecialInterface
{
    /**
     * getName
     * @return string
     */
    public function getName(): string
    {
        return StringUtil::fqcnToBlockPrefix(static::class) ?: '';
    }

    /**
     * getContent
     * @return array
     */
    public function getContent(): array
    {
        $content = [];
        $content['roles'] = ProviderFactory::getRepository(Role::class)->findPermissionTitles();
        $content['content'] = array_values(ProviderFactory::create(Action::class)->findPaginationContent());
        $content['modules'] = ProviderFactory::getRepository(Action::class)->findModuleNameList();
        $content['name'] = $this->getName();
        $content['translations'] = $this->getTranslations();
        return $content;
    }

    /**
     * getTranslations
     * @return array
     */
    private function getTranslations(): array
    {
        $tx = [];
        TranslationsHelper::setDomain('UserAdmin');
        $tx['Module'] = TranslationsHelper::translate('Module', [], 'messages');
        $tx['Action'] = TranslationsHelper::translate('Action', [], 'messages');
        $tx['Yes/No'] = TranslationsHelper::translate('Yes/No', [], 'messages');
        $tx['Filter'] = TranslationsHelper::translate('Filter', [], 'messages');
        $tx['Filter Select'] = TranslationsHelper::translate('Filter Select', [], 'messages');
        $tx['Search for'] = TranslationsHelper::translate('Search For', [], 'messages');
        $tx['No results matched your search.'] = TranslationsHelper::translate('No results matched your search.', [], 'messages');
        $tx['Manage Permissions'] = TranslationsHelper::translate('Manage Permissions');
        $tx['permission_help'] = TranslationsHelper::translate('permission_help');
        return $tx;
    }
}