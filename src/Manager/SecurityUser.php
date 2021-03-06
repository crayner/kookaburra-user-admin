<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 12/02/2019
 * Time: 16:06
 */

namespace Kookaburra\UserAdmin\Manager;

use Doctrine\DBAL\Driver\PDOException;
use Kookaburra\UserAdmin\Entity\Person;
use Kookaburra\SystemAdmin\Entity\Role;
use Kookaburra\SystemAdmin\Entity\Setting;
use App\Exception\MissingClassException;
use App\Provider\ProviderFactory;
use App\Util\EntityHelper;
use Kookaburra\UserAdmin\Util\UserHelper;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityUser implements UserInterface, EncoderAwareInterface, EquatableInterface, \Serializable
{
    /**
     * SecurityUser constructor.
     * @param Person $user
     */
    public function __construct(Person $user = null)
    {
        if ($this->isUser($user))
        {
            $this->setId($user->getId());
            $this->setUserPassword($user);
            $this->setUsername($user->getUsername());
            $this->setSystemAdmin($user->isSystemAdmin());
            $this->setAllRoles($user->getAllRoles());
            $this->setPrimaryRole($user->getPrimaryRole());
            $this->setEmail($user->getEmail());
            $this->setGoogleAPIRefreshToken($user->getGoogleAPIRefreshToken());
            $this->setLocale($user->getI18nPersonal());
            $this->person = $user;
        }
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return array('ROLE_USER');
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles(): array
    {
        $roles[] = $this->getMappedRole($this->getPrimaryRole());
        if ($this->isSystemAdmin())
            $roles[] = 'ROLE_SYSTEM_ADMIN';
        foreach($this->getAllRolesAsArray() as $role)
            $roles[] = $this->getMappedRole($role);
        return $this->roles = array_unique($roles);
    }

    /**
     * getAllRolesAsArray
     * @return array
     */
    public function getAllRolesAsArray(): array
    {
        $roleIDs = $this->getAllRoles() ?: [];
        $roles = [];
        foreach($roleIDs as $roleID)
            $roles[] = EntityHelper::getRepository(Role::class)->find($roleID);
        return $roles;
    }

    /**
     * getMappedRole
     * @param Role $role
     * @return string
     */
    private function getMappedRole(Role $role = null): string
    {
        if (is_null($role)) return '';
        switch (strtoupper($role->getNameShort())) {
            case 'ADM':
                return 'ROLE_ADMIN';
            case 'TCR':
                return 'ROLE_TEACHER';
            case 'STD':
                return 'ROLE_STUDENT';
            case 'PRT':
                return 'ROLE_PARENT';
            case 'SST':
                return 'ROLE_STAFF';
            default:
                return 'ROLE_USER';
        }
    }

    /**
     * @var Role|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\SystemAdmin\Entity\Role")
     * @ORM\JoinColumn(name="gibbonRoleIDPrimary", referencedColumnName="gibbonRoleID", nullable=false)
     */
    private $primaryRole;

    /**
     * @return Role|null
     */
    public function getPrimaryRole(): ?Role
    {
        return $this->primaryRole;
    }

    /**
     * setPrimaryRole
     * @param Role|null $primaryRole
     * @return SecurityUser
     */
    public function setPrimaryRole(?Role $primaryRole): SecurityUser
    {
        $this->primaryRole = $primaryRole;
        return $this;
    }

    /**
     * isUser
     * @param Person|null $user
     * @return bool
     */
    private function isUser(?Person $user): bool
    {
        if ($user)
            return $user instanceof Person;
        if ($this->person instanceof Person && $this->getId() === $this->person->getId())
            return true;
        return false;
    }

    /**
     * @var string|null
     */
    private $password;

    /**
     * @var string 
     */
    private $encoderName = 'native';

    /**
     * setUserPassword
     * @param Person $user
     * @return SecurityUser
     */
    public function setUserPassword(Person $user): SecurityUser
    {
        if (! $this->isUser($user))
            return $this->setPassword(null);

        $x = empty($user->getPassword()) ? null : $user->getPassword();

        return $this->setPassword($x);
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return SecurityUser
     */
    public function setPassword(?string $password): SecurityUser
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncoderName(): string
    {
        return $this->encoderName;
    }

    /**
     * @param string $encoderName
     * @return SecurityUser
     */
    public function setEncoderName(string $encoderName): SecurityUser
    {
        $this->encoderName = $encoderName;
        return $this;
    }

    /**
     * @var string|null
     */
    private $username;

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @return SecurityUser
     */
    public function setUsername(?string $username): SecurityUser
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }

    /**
     * serialize
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->getId(),
            $this->getUsername(),
            $this->getPassword(),
            $this->getSalt(),
            $this->getEmail(),
            $this->getPrimaryRole(),
            $this->getAllRoles(),
        ));
    }

    /**
     * unserialize
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
            $this->email,
            $this->primaryRole,
            $this->allRoles,
            ) = unserialize($serialized);
    }

    /**
     * @var integer|null
     */
    private $id;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return SecurityUser
     */
    public function setId(?int $id): SecurityUser
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @var string
     */
    private $salt;

    /**
     * getSalt
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt ?: '';
    }

    /**
     * setSalt
     * @param null|string $salt
     * @return SecurityUser
     */
    public function setSalt(?string $salt): SecurityUser
    {
        $this->salt = $salt ?: '';
        return $this;
    }

    /**
     * @var bool
     */
    private $systemAdmin = false;

    /**
     * @return bool
     */
    public function isSystemAdmin(): bool
    {
        return $this->systemAdmin;
    }

    /**
     * @param bool $systemAdmin
     * @return SecurityUser
     */
    public function setSystemAdmin(bool $systemAdmin): SecurityUser
    {
        $this->systemAdmin = $systemAdmin;
        return $this;
    }

    /**
     * @var array|null
     */
    private $allRoles;

    /**
     * getAllRoles
     * @return array|null
     */
    public function getAllRoles(): ?array
    {
        return $this->allRoles;
    }

    /**
     * setAllRoles
     * @param array|null $allRoles
     * @return SecurityUser
     */
    public function setAllRoles(?array $allRoles): SecurityUser
    {
        $this->allRoles = $allRoles;
        return $this;
    }

    /**
     * @var string|null
     */
    private $email;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return SecurityUser
     */
    public function setEmail(?string $email): SecurityUser
    {
        $this->email = $email;
        return $this;
    }

    /**
     * isEqualTo
     * @param Person $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if ($user->getId() !== $this->getId())
            return false;
        if ($user->getUsername() !== $this->getUsername())
            return false;
        if ($user->getEmail() !== $this->getEmail())
            return false;
        if ($user->getPassword() !== $this->getPassword())
            return false;
        if ($user->getSalt() !== $this->getSalt())
            return false;
        return true;
    }

    /**
     * formatName
     * @param bool $preferredName
     * @param bool $reverse
     * @param bool $informal
     * @param bool $initial
     * @return string
     * @throws \Exception
     */
    public function formatName(bool $preferredName = true, bool $reverse = false, bool $informal = false, bool $initial = false)
    {
        return UserHelper::getCurrentUser()->formatName($preferredName, $reverse, $informal, $initial);
    }

    /**
     * @var null|string
     */
    private $googleAPIRefreshToken;

    /**
     * @return string|null
     */
    public function getGoogleAPIRefreshToken(): ?string
    {
        return $this->googleAPIRefreshToken;
    }

    /**
     * @param string|null $googleAPIRefreshToken
     * @return SecurityUser
     */
    public function setGoogleAPIRefreshToken(?string $googleAPIRefreshToken): SecurityUser
    {
        $this->googleAPIRefreshToken = $googleAPIRefreshToken;
        return $this;
    }

    /**
     * @var string
     */
    private $locale;

    /**
     * getLocale
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale ?: 'en_GB';
    }

    /**
     * @param string $locale
     * @return SecurityUser
     */
    public function setLocale(?string $locale): SecurityUser
    {
        $this->locale = $locale ?: 'en_GB';
        return $this;
    }

    /**
     * doesPasswordMatchPolicy
     * @param $passwordNew
     * @return bool
     */
    function doesPasswordMatchPolicy($passwordNew)
    {
        $output = true;
        $settingProvider = ProviderFactory::create(Setting::class);
        
        $alpha = $settingProvider->getSettingByScope('System', 'passwordPolicyAlpha');
        $numeric = $settingProvider->getSettingByScope('System', 'passwordPolicyNumeric');
        $punctuation = $settingProvider->getSettingByScope('System', 'passwordPolicyNonAlphaNumeric');
        $minLength = $settingProvider->getSettingByScope('System', 'passwordPolicyMinLength');

        if ($alpha === false || $numeric === false || $punctuation === false || $minLength === false) {
            $output = false;
        } else {
            if ($alpha !== 'N' || $numeric !== 'N' || $punctuation !== 'N' || $minLength >= 0) {
                if ($alpha == 'Y') {
                    if (preg_match('`[A-Z]`', $passwordNew) === false || preg_match('`[a-z]`', $passwordNew) === false) {
                        $output = false;
                    }
                }
                if ($numeric === 'Y') {
                    if (preg_match('`[0-9]`', $passwordNew) === false) {
                        $output = false;
                    }
                }
                if ($punctuation === 'Y') {
                    if (preg_match('/[^a-zA-Z0-9]/', $passwordNew) === false && strpos($passwordNew, ' ') === false) {
                        $output = false;
                    }
                }
                if ($minLength > 0) {
                    if (strlen($passwordNew) < $minLength) {
                        $output = false;
                    }
                }
            }
        }

        return $output;
    }

    /**
     * createSalt
     * @return string
     */
    function createSalt()
    {
        $c = explode(' ', '. / a A b B c C d D e E f F g G h H i I j J k K l L m M n N o O p P q Q r R s S t T u U v V w W x X y Y z Z 0 1 2 3 4 5 6 7 8 9');
        $ks = array_rand($c, 22);
        $s = '';
        foreach ($ks as $k) {
            $s .= $c[$k];
        }

        return $s;
    }

    /**
     * @var Person
     */
    private $person;

    /**
     * @return Person
     */
    public function getPerson(): Person
    {
        if (null === $this->person && $this->getId() > 0)
        {
            $this->person = $this->__construct(ProviderFactory::getRepository(Person::class)->find($this->getId()));
        }
        return $this->person;
    }

    /**
     * isPasswordValid
     * @param string|null $raw
     * @return bool
     */
    public function isPasswordValid(?string $raw): bool
    {
        return $raw ? $this->getEncoder()->isPasswordValid($this, $raw) : false;
    }

    /**
     * changePassword
     * @param string $raw
     * @return bool
     */
    public function changePassword(string $raw): bool
    {
        try {
            $password = $this->getEncoder()->encodePassword($this, $raw);
            $person = $this->getPerson();
            $provider =  ProviderFactory::create(Person::class);
            $provider->refresh($person);
            $person->setPassword($password);
            $person->setPasswordForceReset('N'); //  Always reset.
            $provider->saveEntity();
            $this->setPassword($password);
            return true;
        } catch (PDOException $e) {
            return false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * @return UserPasswordEncoderInterface
     */
    public function getEncoder(): UserPasswordEncoderInterface
    {
        return UserHelper::getEncoder();
    }
}