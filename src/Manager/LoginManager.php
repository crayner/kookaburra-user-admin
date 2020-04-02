<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * UserProvider: craig
 * Date: 24/11/2018
 * Time: 08:37
 */
namespace Kookaburra\UserAdmin\Manager;

use Kookaburra\SystemAdmin\Entity\Setting;
use App\Provider\ProviderFactory;
use Kookaburra\SystemAdmin\Provider\SettingProvider;
use App\Util\LocaleHelper;

/**
 * Class LoginManager
 * @package App\Form\Manager
 */
class LoginManager
{
    /**
     * @var SettingProvider
     */
    private $settingManager;

    /**
     * LoginManager constructor.
     * @param SettingProvider $settingManager
     */
    public function __construct(ProviderFactory $settingManager)
    {
        $this->settingManager = $settingManager->getProvider(Setting::class);
    }

    /**
     * getTranslationsDomain
     *
     * @return string
     */
    public function getTranslationDomain(): string
    {
        return 'messages';
    }

    /**
     * isLocale
     *
     * @return bool
     */
    public function isLocale(): bool
    {
        return true;
    }

    /**
     * getLocale
     *
     * @return string
     */
    public function getLocale(): string
    {
        return LocaleHelper::getLocale();
    }

    /**
     * isGoogleOAuthOn
     * @return bool
     * @throws \Exception
     */
    public function isGoogleOAuthOn(): bool
    {
        $setting = $this->getSettingManager()->getSettingByScope('System', 'googleOAuth');
        return $setting instanceof Setting && $setting->getValue() === 'Y' ? true : false ;
    }

    /**
     * getSettingManager
     * @return SettingProvider
     */
    public function getSettingManager(): SettingProvider
    {
        return $this->settingManager;
    }

    /**
     * getMessageManager
     * @return MessageManager
     */
    public function getMessageManager(): MessageManager
    {
        return $this->getSettingManager()->getMessageManager();
    }
}