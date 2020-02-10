<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 19/12/2018
 * Time: 12:17
 */
namespace Kookaburra\UserAdmin\Util;

use Kookaburra\SystemAdmin\Entity\Action;
use Kookaburra\SystemAdmin\Entity\Module;
use Kookaburra\UserAdmin\Entity\Person;
use App\Entity\Setting;
use App\Exception\RouteConfigurationException;
use Kookaburra\SystemAdmin\Provider\ActionProvider ;
use Kookaburra\SystemAdmin\Provider\ModuleProvider ;
use App\Provider\ProviderFactory;
use Kookaburra\UserAdmin\Manager\SecurityUser;
use Doctrine\DBAL\Driver\PDOException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

/**
 * Class SecurityHelper
 * @package App\Util
 */
class SecurityHelper
{

    /**
     * @var LoggerInterface
     */
    private static $logger;

    /**
     * @var AuthorizationCheckerInterface
     */
    private static $checker;

    /**
     * SecurityHelper constructor.
     * @param LoggerInterface $logger
     * @param AuthorizationCheckerInterface $checker
     */
    public function __construct(
        LoggerInterface $logger,
        AuthorizationCheckerInterface $checker
    ) {
        self::$logger = $logger;
        self::$checker = $checker;
    }

    /**
     * @return ActionProvider
     */
    public static function getActionProvider(): ActionProvider
    {
        return ProviderFactory::create(Action::class);
    }

    /**
     * @return ModuleProvider
     */
    public static function getModuleProvider(): ModuleProvider
    {
        return ProviderFactory::create(Module::class);
    }

    /**
     * @var array
     */
    private static $highestGroupedActionList = [];

    /**
     * getHighestGroupedAction
     * @param string $address
     * @return bool|string
     * @throws \Exception
     */
    public static function getHighestGroupedAction(string $address)
    {
        $module = self::checkModuleReady($address);
        if (isset(self::$highestGroupedActionList[$address]))
            return self::$highestGroupedActionList[$address];
        if ($user = UserHelper::getCurrentUser() === null)
            return self::$highestGroupedActionList[$address] = false;
        $result = self::getActionProvider()->getRepository()->findHighestGroupedAction(self::getActionName($address), $module);
        return self::$highestGroupedActionList[$address] = $result ? $result['name'] : false;
    }

    /**
     * @var array
     */
    private static $checkModuleReadyList = [];

    /**
     * checkModuleReady
     * @param string $address
     * @return \App\Manager\EntityInterface|bool
     */
    public static function checkModuleReady(string $address)
    {
        if (isset(self::$checkModuleReadyList[$address]))
            return self::$checkModuleReadyList[$address];
        try {
            return self::$checkModuleReadyList[$address] = self::getModuleProvider()->findOneBy(['name' => self::getModuleName($address), 'active' => 'Y']);
        } catch (PDOException | \PDOException $e) {
        }

        return self::$checkModuleReadyList[$address] = false;
    }

    /**
     * checkModuleRouteReady
     * @param string $address
     * @return \App\Manager\EntityInterface|bool
     */
    public static function checkModuleRouteReady(string $route)
    {
        try {
            return self::getModuleProvider()->findOneBy(['name' => self::getModuleNameFromRoute($route), 'active' => 'Y']);
        } catch (PDOException $e) {
        } catch (\PDOException $e) {
        }

        return false;
    }

    /**
     * getModuleName
     * @param string $address
     * @return bool|string
     */
    public static function getModuleName(string $address)
    {
        return substr(substr($address, 9), 0, strpos(substr($address, 9), '/'));
    }

    /**
     * getActionName
     * @param $address
     * @return bool|string
     */
    public static function getActionName($address)
    {
        return substr($address, (10 + strlen(self::getModuleName($address))));
    }

    /**
     * getModuleNameFromRoute
     * @param string $route
     * @return mixed
     * @throws RouteConfigurationException
     */
    public static function getModuleNameFromRoute(string $route)
    {
        $route = self::splitRoute($route);
        return $route['module'];
    }

    /**
     * getActionNameFromRoute
     * @param $route
     * @return mixed
     * @throws RouteConfigurationException
     */
    public static function getActionNameFromRoute($route)
    {
        $route = self::splitRoute($route);
        return $route['action'];
    }

    /**
     * splitRoute
     * @param string $route
     * @return array
     * @throws RouteConfigurationException
     */
    public static function splitRoute(string $route): array
    {
        $route = explode('__', $route);
        if (count($route) !== 2)
            throw new RouteConfigurationException(implode('__', $route));
        $route['module'] = ucwords(str_replace('_', ' ', $route[0]));
        $route['action'] = $route[1];
        return $route;
    }
    /**
     * isActionAccessible
     * @param string $address
     * @param string $sub
     * @return bool
     */
    public static function isActionAccessible(string $address, string $sub = '%', ?LoggerInterface $logger = null): bool
    {
        if (null !== $logger)
            self::$logger = $logger;
        return self::isActionAccessibleToRole(self::checkModuleReady($address),self::getActionName($address), $address, $sub);
    }

    /**
     * isActionAccessibleToRole
     * @param Module|bool $module
     * @param string $action
     * @param string $sub
     * @return bool
     */
    private static function isActionAccessibleToRole($module, string $action, string $address, string $sub)
    {
        if (UserHelper::getCurrentUser() instanceof Person) {
            //Check user has a current role set
            if (! empty(UserHelper::getCurrentUser()->getPrimaryRole())) {
                //Check module ready
                if ($module instanceof Module) {
                    //Check current role has access rights to the current action.
                    try {
                        $role = UserHelper::getCurrentUser()->getPrimaryRole();
                        if (count(self::getActionProvider()->findByURLListModuleRole(
                                [
                                    'name' => "%".$action."%",
                                    "module" => $module,
                                    'role' => $role,
                                    'sub' => $sub === '' ? '%' : $sub,
                                ]
                            )) > 0)
                            return true;
                    } catch (PDOException $e) {
                    }
                } else {
                    self::$logger->warning(sprintf('No module was linked to the address "%s"', $address));
                }
            } else {
                self::$logger->debug(sprintf('The user does not have a valid Primary Role.' ));
            }
        } else {
            self::$logger->debug(sprintf('The user was not valid!' ));
        }

        self::$logger->debug(sprintf('The action "%s", role "%s" and sub-action "%s" combination is not accessible.', $action, $role, $sub ));

        return false;
    }
    /**
     * isRouteAccessible
     * @param string $route
     * @param string $sub
     * @param LoggerInterface|null $logger
     * @return bool
     * @throws \Exception
     */
    public static function isRouteAccessible(string $route, string $sub = '%', ?LoggerInterface $logger = null): bool
    {
        if (null !== $logger)
            self::$logger = $logger;
        return self::isActionAccessibleToRole(self::checkModuleRouteReady($route),self::getActionNameFromRoute($route), $route, $sub);
    }

    /**
     * @return AuthorizationCheckerInterface
     */
    public static function getChecker(): AuthorizationCheckerInterface
    {
        return self::$checker;
    }

    /**
     * @var null|string
     */
    private static $passwordPolicy;

    /**
     * getPasswordPolicy
     * @return array
     */
    public static function getPasswordPolicy(): array
    {
        if (null !== self::$passwordPolicy)
            return self::$passwordPolicy;

        $output = [];
        $provider = ProviderFactory::create(Setting::class);
        $alpha = $provider->getSettingByScopeAsBoolean('System', 'passwordPolicyAlpha');
        $numeric = $provider->getSettingByScopeAsBoolean('System', 'passwordPolicyNumeric');
        $punctuation = $provider->getSettingByScopeAsBoolean('System', 'passwordPolicyNonAlphaNumeric');
        $minLength = $provider->getSettingByScopeAsInteger('System', 'passwordPolicyMinLength');

        if (!$alpha || !$numeric || !$punctuation || $minLength >= 0) {
            $output[] = 'The password policy stipulates that passwords must:';
            if ($alpha)
                $output[] = 'Contain at least one lowercase letter, and one uppercase letter.';
            if ($numeric)
                $output[] = 'Contain at least one number.';
            if ($punctuation)
                $output[] = 'Contain at least one non-alphanumeric character (e.g. a punctuation mark or space).';
            if ($minLength >= 0)
                $output[] = 'Must be at least {oneString} characters in length.';
        }
        $output['minLength'] = $minLength;

        self::$passwordPolicy = $output;
        return self::$passwordPolicy;
    }

    /**
     * isGranted
     * @param $role
     * @param null $object
     * @param null $field
     * @return bool
     */
    public static function isGranted($role, $object = null)
    {
        if (null === self::$checker) {
            return false;
        }

        try {
            return self::$checker->isGranted($role, $object);
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }

    /**
     * encodeAndSetPassword
     * @param SecurityUser $user
     * @param string $raw
     */
    public static function encodeAndSetPassword(SecurityUser $user, string $raw)
    {
        $password = UserHelper::getEncoder()->encodePassword($user, $raw);

        $person = $user->getPerson();

        $person->setPassword($password);
    }
}