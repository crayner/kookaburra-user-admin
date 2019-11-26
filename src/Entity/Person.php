<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * UserProvider: craig
 * Date: 23/11/2018
 * Time: 08:17
 */
namespace Kookaburra\UserAdmin\Entity;

use App\Entity\ApplicationForm;
use App\Entity\House;
use App\Entity\I18n;
use App\Entity\SchoolYear;
use App\Entity\Setting;
use App\Entity\Staff;
use App\Entity\StudentEnrolment;
use App\Entity\Theme;
use App\Manager\EntityInterface;
use App\Manager\Traits\BooleanList;
use App\Provider\ProviderFactory;
use App\Util\Format;
use App\Util\ImageHelper;
use App\Util\TranslationsHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Kookaburra\SystemAdmin\Entity\Role;
use Symfony\Component\Validator\Constraints as ASSERT;
use Symfony\Component\Intl\Languages;

/**
 * Class GibbonPerson
 * @package Kookaburra\UserAdmin\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\UserAdmin\Repository\PersonRepository")
 * @ORM\Table(
 *     options={"auto_increment": 1},
 *     name="Person",
 *     options={"auto_increment": 1},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"username"})},
 *     indexes={@ORM\Index(name="username_2", columns={"username", "email"}),
 *     @ORM\Index(name="phone_code_1",columns={"phone1CountryCode"}),
 *     @ORM\Index(name="phone_code_2",columns={"phone2CountryCode"}),
 *     @ORM\Index(name="phone_code_3",columns={"phone3CountryCode"}),
 *     @ORM\Index(name="phone_code_4",columns={"phone4CountryCode"}),
 *     @ORM\Index(name="house",columns={"gibbonHouseID"}),
 *     @ORM\Index(name="school_year_class_of",columns={"gibbonSchoolYearIDClassOf"}),
 *     @ORM\Index(name="application_form",columns={"gibbonApplicationFormID"}),
 *     @ORM\Index(name="theme",columns={"gibbonThemeIDPersonal"}),
 *     @ORM\Index(name="primary_role",columns={"gibbonRoleIDPrimary"}),
 *     @ORM\Index(name="i18n",columns={"gibboni18nIDPersonal"})
 * }
 *     )
 */
class Person implements EntityInterface
{
    use BooleanList;

    /**
     * Person constructor.
     */
    public function __construct()
    {
        $this->adults = new ArrayCollection();
        $this->courseClassPerson = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->studentEnrolments = new ArrayCollection();
        $this->primaryRole = new Role();
    }

    /**
     * @var integer|null
     * @ORM\Id
     * @ORM\Column(type="integer", name="gibbonPersonID", columnDefinition="INT(10) UNSIGNED ZEROFILL")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @return array
     */
    public static function getPhoneTypeList(): array
    {
        return self::$phoneTypeList;
    }

    /**
     * @return array
     */
    public static function getStatusList(): array
    {
        return self::$statusList;
    }

    /**
     * getGenderList
     *
     * @return array
     */
    public static function getGenderList(): array
    {
        return self::$genderList;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Id.
     *
     * @param int|null $id
     * @return Person
     */
    public function setId(?int $id): Person
    {
        $this->id = $id;
        return $this;
    }


    /**
     * @var string|null
     * @ORM\Column(length=5)
     */
    private $title = '';

    /**
     * @var array
     */
    private static $titleList = [
        'Ms',
        'Miss',
        'Mr',
        'Mrs',
        'Dr',
    ];

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title = in_array(rtrim($this->title,'.'), self::$titleList) ? rtrim($this->title,'.') : '';
    }

    /**
     * @param null|string $title
     * @return Person
     */
    public function setTitle(?string $title): Person
    {
        $this->title = in_array($title, self::getTitleList()) ? $title : '';
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=60)
     */
    private $surname;

    /**
     * @return null|string
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param null|string $surname
     * @return Person
     */
    public function setSurname(?string $surname): Person
    {
        $this->surname = mb_substr($surname, 0, 60);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=60, name="firstName")
     */
    private $firstName;

    /**
     * @return null|string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param null|string $firstName
     * @return Person
     */
    public function setFirstName(?string $firstName): Person
    {
        $this->firstName = mb_substr($firstName, 0, 60);

        if (null === $this->getPreferredName())
            return $this->setPreferredName($firstName);

        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=60, name="preferredName")
     */
    private $preferredName;

    /**
     * @return null|string
     */
    public function getPreferredName(): ?string
    {
        return $this->preferredName;
    }

    /**
     * @param null|string $preferredName
     * @return Person
     */
    public function setPreferredName(?string $preferredName): Person
    {
        $this->preferredName = mb_substr($preferredName, 0, 60);

        if (null === $this->getFirstName())
            return $this->setFirstName($preferredName);

        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=150, name="officialName")
     */
    private $officialName = '';

    /**
     * @return null|string
     */
    public function getOfficialName(): ?string
    {
        return $this->officialName;
    }

    /**
     * @param null|string $officialName
     * @return Person
     */
    public function setOfficialName(?string $officialName): Person
    {
        $this->officialName = mb_substr($officialName, 0, 150);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=60, name="nameInCharacters")
     */
    private $nameInCharacters = '';

    /**
     * @return null|string
     */
    public function getNameInCharacters(): ?string
    {
        return $this->nameInCharacters;
    }

    /**
     * @param null|string $nameInCharacters
     * @return Person
     */
    public function setNameInCharacters(?string $nameInCharacters): Person
    {
        $this->nameInCharacters = mb_substr($nameInCharacters, 0, 60);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=16, options={"default": "Unspecified"})
     */
    private $gender = 'Unspecified';

    /**
     * @var array
     */
    private static $genderList = [
        'Female' => 'F',
        'Male' => 'M',
        'Other' => 'Other',
        'Unspecified' => 'Unspecified',
    ];

    /**
     * @return null|string
     */
    public function getGender(): ?string
    {
        return $this->gender = in_array($this->gender, self::getGenderList()) ? $this->gender : 'Unspecified';
    }

    /**
     * @param null|string $gender
     * @return Person
     */
    public function setGender(?string $gender): Person
    {
        $this->gender = in_array($gender, self::getGenderList()) ? $gender : 'Unspecified';
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=20,unique=true)
     */
    private $username;

    /**
     * @return null|string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param null|string $username
     * @return Person
     */
    public function setUsername(?string $username): Person
    {
        $this->username = mb_substr($username, 0, 20);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, name="password")
     */
    private $MD5Password = '';

    /**
     * @return string|null
     */
    public function getMD5Password(): ?string
    {
        return $this->MD5Password;
    }

    /**
     * @param string|null $MD5Password
     * @return Person
     */
    public function setMD5Password(?string $MD5Password): Person
    {
        $this->MD5Password = $MD5Password;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, name="passwordStrong")
     */
    private $passwordStrong = '';

    /**
     * @return null|string
     */
    public function getPasswordStrong(): ?string
    {
        return $this->passwordStrong;
    }

    /**
     * @param null|string $passwordStrong
     * @return Person
     */
    public function setPasswordStrong(?string $passwordStrong): Person
    {
        $this->passwordStrong = mb_substr($passwordStrong, 0, 255);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, name="passwordStrongSalt")
     */
    private $passwordStrongSalt = '';

    /**
     * @return null|string
     */
    public function getPasswordStrongSalt(): ?string
    {
        return $this->passwordStrongSalt;
    }

    /**
     * @param null|string $passwordStrongSalt
     * @return Person
     */
    public function setPasswordStrongSalt(?string $passwordStrongSalt): Person
    {
        $this->passwordStrongSalt = mb_substr($passwordStrongSalt, 0, 255);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=1, options={"default": "N", "comment": "Force user to reset password on next login."}, name="passwordForceReset")
     */
    private $passwordForceReset = 'N';

    /**
     * isPasswordForceReset
     * @return bool
     */
    public function isPasswordForceReset(): bool
    {
        return $this->getPasswordForceReset() === 'Y' ? true : false;
    }

    /**
     * @return null|string
     */
    public function getPasswordForceReset(): ?string
    {
        return $this->passwordForceReset = in_array($this->passwordForceReset, self::getBooleanList()) ? $this->passwordForceReset : 'N' ;
    }

    /**
     * @param null|string $passwordForceReset
     * @return Person
     */
    public function setPasswordForceReset(?string $passwordForceReset): Person
    {
        $this->passwordForceReset = in_array($passwordForceReset, self::getBooleanList()) ? $passwordForceReset : 'N' ;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=16, options={"default": "Full"})
     */
    private $status = 'Full';

    /**
     * @var array
     */
    private static $statusList = [
        'Full',
        'Expected',
        'Left',
        'Pending Approval',
    ];

    /**
     * @return null|string
     */
    public function getStatus(): ?string
    {
        return $this->status = in_array($this->status, self::getStatusList()) ? $this->status : 'Full' ;
    }

    /**
     * @param null|string $status
     * @return Person
     */
    public function setStatus(?string $status): Person
    {
        $this->status = in_array($status, self::getStatusList()) ? $status : 'Full' ;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=1, options={"default": "Y"}, name="canLogin")
     */
    private $canLogin = 'Y';

    /**
     * isCanLogin
     * @return bool
     */
    public function isCanLogin(): bool
    {
        return $this->getCanLogin() === 'Y' ? true : false;
    }

    /**
     * @return null|string
     */
    public function getCanLogin(): ?string
    {
        return $this->canLogin = in_array($this->canLogin, self::getBooleanList()) ? $this->canLogin : 'N' ;
    }

    /**
     * @param null|string $canLogin
     * @return Person
     */
    public function setCanLogin(?string $canLogin): Person
    {
        $this->canLogin = in_array($canLogin, self::getBooleanList()) ? $canLogin : 'N' ;
        return $this;
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
     * @param Role|null $primaryRole
     * @return Person
     */
    public function setPrimaryRole(?Role $primaryRole): Person
    {
        $this->primaryRole = $primaryRole;
        return $this;
    }

    /**
     * @var array
     * @ORM\Column(name="gibbonRoleIDAll", type="simple_array", nullable=true)
     */
    private $allRoles = [];

    /**
     * @return array
     */
    public function getAllRoles(): array
    {
        if ($this->getPrimaryRole() instanceof Role && ! in_array($this->getPrimaryRole()->getId(), $this->allRoles ?: []))
            $this->allRoles[] = $this->getPrimaryRole()->getId();
        return $this->allRoles = $this->allRoles ?: [];
    }

    /**
     * @param null|array $allRoles
     * @return Person
     */
    public function setAllRoles(?array $allRoles): Person
    {
        if (null === $allRoles)
            $allRoles = [];
        foreach($allRoles as $q=>$w)
            if ($w instanceof Role)
                $allRoles[$q] = $w->getId();
        if ($this->getPrimaryRole() instanceof Role && ! in_array($this->getPrimaryRole()->getId(), $allRoles)) {
            $allRoles[] = $this->getPrimaryRole()->getId();
        }
        $this->allRoles = array_unique($allRoles ?: []);
        return $this;
    }

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    private $dob;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDob(): ?\DateTime
    {
        return $this->dob;
    }

    /**
     * @param \DateTime|null $dob
     * @return Person
     */
    public function setDob(?\DateTimeImmutable $dob): Person
    {
        $this->dob = $dob;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=75, nullable=true)
     */
    private $email;

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     * @return Person
     */
    public function setEmail(?string $email): Person
    {
        $this->email = mb_substr($email, 0, 75);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=75, name="emailAlternate", nullable=true)
     */
    private $emailAlternate;

    /**
     * @return null|string
     */
    public function getEmailAlternate(): ?string
    {
        return $this->emailAlternate;
    }

    /**
     * @param null|string $emailAlternate
     * @return Person
     */
    public function setEmailAlternate(?string $emailAlternate): Person
    {
        $this->emailAlternate = mb_substr($emailAlternate, 0, 75);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, nullable=true)
     */
    private $image_240;

    /**
     * getImage240
     * @param bool $default
     * @return string|null
     */
    public function getImage240(bool $default = true): ?string
    {
        if (empty($this->image_240) && $default)
            return '/build/static/DefaultPerson.png';
        return $this->image_240;
    }

    /**
     * @param null|string $image_240
     * @return Person
     */
    public function setImage240(?string $image_240): Person
    {
        $this->image_240 = mb_substr($image_240, 0, 75);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=15, name="lastIPAddress")
     */
    private $lastIPAddress = '';

    /**
     * @return null|string
     */
    public function getLastIPAddress(): ?string
    {
        return $this->lastIPAddress;
    }

    /**
     * @param null|string $lastIPAddress
     * @return Person
     */
    public function setLastIPAddress(?string $lastIPAddress): Person
    {
        $this->lastIPAddress = mb_substr($lastIPAddress, 0, 15);
        return $this;
    }

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true, name="lastTimestamp")
     */
    private $lastTimestamp;

    /**
     * @return \DateTime|null
     */
    public function getLastTimestamp(): ?\DateTime
    {
        return $this->lastTimestamp;
    }

    /**
     * @param \DateTime|null $lastTimestamp
     * @return Person
     */
    public function setLastTimestamp(?\DateTime $lastTimestamp): Person
    {
        $this->lastTimestamp = $lastTimestamp;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=15, nullable=true, name="lastFailIPAddress")
     */
    private $lastFailIPAddress;

    /**
     * @return null|string
     */
    public function getLastFailIPAddress(): ?string
    {
        return $this->lastFailIPAddress;
    }

    /**
     * @param null|string $lastFailIPAddress
     * @return Person
     */
    public function setLastFailIPAddress(?string $lastFailIPAddress): Person
    {
        $this->lastFailIPAddress = mb_substr($lastFailIPAddress, 0, 15);
        return $this;
    }

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true, name="lastFailTimestamp")
     */
    private $lastFailTimestamp;

    /**
     * isLastFailTimestampTooOld
     * @param $session
     */
    public function isLastFailTimestampTooOld(int $timeout = 1200): bool
    {
        if (null === $this->getLastFailTimestamp() || $this->getLastFailTimestamp()->getTimestamp() < strtotime('-'.$timeout.' seconds'))
            return true;
        return false;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastFailTimestamp(): ?\DateTime
    {
        return $this->lastFailTimestamp;
    }

    /**
     * @param \DateTime|null $lastFailTimestamp
     * @return Person
     */
    public function setLastFailTimestamp(?\DateTime $lastFailTimestamp): Person
    {
        $this->lastFailTimestamp = $lastFailTimestamp;
        return $this;
    }

    /**
     * @var integer|null
     * @ORM\Column(type="smallint", columnDefinition="INT(1)", nullable=true, name="failCount", options={"default": "0"})
     */
    private $failCount;

    /**
     * incFailCount
     * @return int
     */
    public function incFailCount(): int
    {
        $failCount = intval($this->failCount);
        $this->setFailCount(++$failCount);
        return $this->getFailCount();
    }

    /**
     * @return int|null
     */
    public function getFailCount(): int
    {
        return intval($this->failCount);
    }

    /**
     * @param int|null $failCount
     * @return Person
     */
    public function setFailCount(?int $failCount): Person
    {
        $this->failCount = $failCount;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(type="text")
     */
    private $address1 = '';

    /**
     * @return null|string
     */
    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    /**
     * @param null|string $address1
     * @return Person
     */
    public function setAddress1(?string $address1): Person
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, name="address1District")
     */
    private $address1District = '';

    /**
     * @return null|string
     */
    public function getAddress1District(): ?string
    {
        return $this->address1District;
    }

    /**
     * @param null|string $address1District
     * @return Person
     */
    public function setAddress1District(?string $address1District): Person
    {
        $this->address1District = mb_substr($address1District, 0, 255);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, name="address1Country")
     */
    private $address1Country = '';

    /**
     * @return null|string
     */
    public function getAddress1Country(): ?string
    {
        return $this->address1Country;
    }

    /**
     * @param null|string $address1Country
     * @return Person
     */
    public function setAddress1Country(?string $address1Country): Person
    {
        $this->address1Country = mb_substr($address1Country, 0, 255);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(type="text")
     */
    private $address2 = '';

    /**
     * @return null|string
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * @param null|string $address2
     * @return Person
     */
    public function setAddress2(?string $address2): Person
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, name="address2District")
     */
    private $address2District = '';

    /**
     * @return null|string
     */
    public function getAddress2District(): ?string
    {
        return $this->address2District;
    }

    /**
     * @param null|string $address2District
     * @return Person
     */
    public function setAddress2District(?string $address2District): Person
    {
        $this->address2District = mb_substr($address2District, 0, 255);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, name="address2Country")
     */
    private $address2Country = '';

    /**
     * @return null|string
     */
    public function getAddress2Country(): ?string
    {
        return $this->address2Country;
    }

    /**
     * @param null|string $address2Country
     * @return Person
     */
    public function setAddress2Country(?string $address2Country): Person
    {
        $this->address2Country = mb_substr($address2Country, 0, 255);
        return $this;
    }

    /**
     * @var array
     */
    private static $phoneTypeList = ['','Mobile','Home','Work','Fax','Pager','Other'];

    /**
     * @var string|null
     * @ORM\Column(length=6, name="phone1Type")
     */
    private $phone1Type = '';

    /**
     * @return null|string
     */
    public function getPhone1Type(): ?string
    {
        return $this->phone1Type = in_array($this->phone1Type, self::getPhoneTypeList()) ? $this->phone1Type : '' ;
    }

    /**
     * @param null|string $phone1Type
     * @return Person
     */
    public function setPhone1Type(?string $phone1Type): Person
    {
        $this->phone1Type = in_array($phone1Type, self::getPhoneTypeList()) ? $phone1Type : '' ;
        return $this;
    }

    /**
     * @var Country|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Country")
     * @ORM\JoinColumn(nullable=true, name="phone1CountryCode", referencedColumnName="id")
     */
    private $phone1CountryCode = '';

    /**
     * @return null|Country
     */
    public function getPhone1CountryCode(): ?Country
    {
        return $this->phone1CountryCode;
    }

    /**
     * setPhone1CountryCode
     * @param Country|null $phone1CountryCode
     * @return Person
     */
    public function setPhone1CountryCode(?Country $phone1CountryCode): Person
    {
        $this->phone1CountryCode = $phone1CountryCode;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=20)
     */
    private $phone1 = '';

    /**
     * @return null|string
     */
    public function getPhone1(): ?string
    {
        return $this->phone1;
    }

    /**
     * @param null|string $phone1
     * @return Person
     */
    public function setPhone1(?string $phone1): Person
    {
        $this->phone1 = mb_substr($phone1, 0, 20);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=6, name="phone2Type")
     */
    private $phone2Type = '';

    /**
     * @return null|string
     */
    public function getPhone2Type(): ?string
    {
        return $this->phone2Type = in_array($this->phone2Type, self::getPhoneTypeList()) ? $this->phone2Type : '' ;
    }

    /**
     * @param null|string $phone2Type
     * @return Person
     */
    public function setPhone2Type(?string $phone2Type): Person
    {
        $this->phone2Type = in_array($phone2Type, self::getPhoneTypeList()) ? $phone2Type : '' ;
        return $this;
    }

    /**
     * @var Country|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Country")
     * @ORM\JoinColumn(nullable=true, name="phone2CountryCode", referencedColumnName="id")
     */
    private $phone2CountryCode = '';

    /**
     * @return null|Country
     */
    public function getPhone2CountryCode(): ?Country
    {
        return $this->phone2CountryCode;
    }

    /**
     * setPhone2CountryCode
     * @param Country|null $phone2CountryCode
     * @return Person
     */
    public function setPhone2CountryCode(?Country $phone2CountryCode): Person
    {
        $this->phone2CountryCode = $phone2CountryCode;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=20)
     */
    private $phone2 = '';

    /**
     * @return null|string
     */
    public function getPhone2(): ?string
    {
        return $this->phone2;
    }

    /**
     * @param null|string $phone2
     * @return Person
     */
    public function setPhone2(?string $phone2): Person
    {
        $this->phone2 = mb_substr($phone2, 0, 20);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=6, name="phone3Type")
     */
    private $phone3Type = '';

    /**
     * @return null|string
     */
    public function getPhone3Type(): ?string
    {
        return $this->phone3Type = in_array($this->phone3Type, self::getPhoneTypeList()) ? $this->phone3Type : '' ;
    }

    /**
     * @param null|string $phone3Type
     * @return Person
     */
    public function setPhone3Type(?string $phone3Type): Person
    {
        $this->phone3Type = in_array($phone3Type, self::getPhoneTypeList()) ? $phone3Type : '' ;
        return $this;
    }

    /**
     * @var Country|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Country")
     * @ORM\JoinColumn(nullable=true, name="phone3CountryCode", referencedColumnName="id")
     */
    private $phone3CountryCode = '';

    /**
     * @return null|Country
     */
    public function getPhone3CountryCode(): ?Country
    {
        return $this->phone3CountryCode;
    }

    /**
     * setPhone3CountryCode
     * @param Country|null $phone3CountryCode
     * @return Person
     */
    public function setPhone3CountryCode(?Country $phone3CountryCode): Person
    {
        $this->phone3CountryCode = $phone3CountryCode;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=20)
     */
    private $phone3 = '';

    /**
     * @return null|string
     */
    public function getPhone3(): ?string
    {
        return $this->phone3;
    }

    /**
     * @param null|string $phone3
     * @return Person
     */
    public function setPhone3(?string $phone3): Person
    {
        $this->phone3 = mb_substr($phone3, 0, 20);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=6, name="phone4Type")
     */
    private $phone4Type = '';

    /**
     * @return null|string
     */
    public function getPhone4Type(): ?string
    {
        return $this->phone4Type = in_array($this->phone4Type, self::getPhoneTypeList()) ? $this->phone4Type : '' ;
    }

    /**
     * @param null|string $phone4Type
     * @return Person
     */
    public function setPhone4Type(?string $phone4Type): Person
    {
        $this->phone4Type = in_array($phone4Type, self::getPhoneTypeList()) ? $phone4Type : '' ;
        return $this;
    }

    /**
     * @var Country
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Country")
     * @ORM\JoinColumn(nullable=true, name="phone4CountryCode", referencedColumnName="id")
     */
    private $phone4CountryCode = '';

    /**
     * @return null|Country
     */
    public function getPhone4CountryCode(): ?Country
    {
        return $this->phone4CountryCode;
    }

    /**
     * setPhone4CountryCode
     * @param Country|null $phone4CountryCode
     * @return Person
     */
    public function setPhone4CountryCode(?Country $phone4CountryCode): Person
    {
        $this->phone4CountryCode = $phone4CountryCode;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=20)
     */
    private $phone4 = '';

    /**
     * @return null|string
     */
    public function getPhone4(): ?string
    {
        return $this->phone4;
    }

    /**
     * @param null|string $phone4
     * @return Person
     */
    public function setPhone4(?string $phone4): Person
    {
        $this->phone4 = mb_substr($phone4, 0, 20);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255)
     */
    private $website = '';

    /**
     * @return null|string
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param null|string $website
     * @return Person
     */
    public function setWebsite(?string $website): Person
    {
        $this->website = mb_substr($website, 0, 255);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=30, name="languageFirst")
     */
    private $languageFirst = '';

    /**
     * @return null|string
     */
    public function getLanguageFirst(): ?string
    {
        return $this->languageFirst;
    }

    /**
     * @param null|string $languageFirst
     * @return Person
     */
    public function setLanguageFirst(?string $languageFirst): Person
    {
        $this->languageFirst = mb_substr($languageFirst, 0, 30);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=30, name="languageSecond")
     */
    private $languageSecond = '';

    /**
     * @return null|string
     */
    public function getLanguageSecond(): ?string
    {
        return $this->languageSecond;
    }

    /**
     * @param null|string $languageSecond
     * @return Person
     */
    public function setLanguageSecond(?string $languageSecond): Person
    {
        $this->languageSecond = mb_substr($languageSecond, 0, 30);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=30, name="languageThird")
     */
    private $languageThird = '';

    /**
     * @return null|string
     */
    public function getLanguageThird(): ?string
    {
        return $this->languageThird;
    }

    /**
     * @param null|string $languageThird
     * @return Person
     */
    public function setLanguageThird(?string $languageThird): Person
    {
        $this->languageThird = mb_substr($languageThird, 0, 30);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=30, name="countryOfBirth")
     */
    private $countryOfBirth = '';

    /**
     * @return null|string
     */
    public function getCountryOfBirth(): ?string
    {
        return $this->countryOfBirth;
    }

    /**
     * @param null|string $countryOfBirth
     * @return Person
     */
    public function setCountryOfBirth(?string $countryOfBirth): Person
    {
        $this->countryOfBirth = mb_substr($countryOfBirth, 0, 30);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, name="birthCertificateScan")
     * @ASSERT\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"image/*","application/pdf","application/x-pdf"}
     * )
     */
    private $birthCertificateScan = '';

    /**
     * @return null|string
     */
    public function getBirthCertificateScan(): ?string
    {
        return $this->birthCertificateScan;
    }

    /**
     * @param null|string $birthCertificateScan
     * @return Person
     */
    public function setBirthCertificateScan(?string $birthCertificateScan): Person
    {
        $this->birthCertificateScan = mb_substr($birthCertificateScan, 0, 255);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255)
     */
    private $ethnicity = '';

    /**
     * @var array
     */
    private static $ethnicityList = [
        'Australian Peoples',
        'New Zealand Peoples',
        'Melanesian and Papuan',
        'Micronesian',
        'Polynesian',
        'British',
        'Irish',
        'Western European',
        'Northern European',
        'Southern European',
        'South Eastern European',
        'Eastern European',
        'Arab',
        'Jewish',
        'Peoples of the Sudan',
        'Other North African and Middle Eastern',
        'Mainland South-East Asian',
        'Maritime South-East Asian',
        'Chinese Asian',
        'Other North-East Asian',
        'Southern Asian',
        'Central Asian',
        'North American',
        'South American',
        'Central American',
        'Caribbean Islander',
        'Central and West African',
        'Southern and East African'
    ];

    /**
     * getEthnicityList
     * @return array
     */
    public static function getEthnicityList(): array
    {
        if (($x = ProviderFactory::create(Setting::class)->getSettingByScopeAsArray('User Admin', 'ethnicity')) !== []) {
            return $x;
        }
        return self::$ethnicityList;
    }

    /**
     * @return null|string
     */
    public function getEthnicity(): ?string
    {
        return $this->ethnicity;
    }

    /**
     * @param null|string $ethnicity
     * @return Person
     */
    public function setEthnicity(?string $ethnicity): Person
    {
        $this->ethnicity = mb_substr($ethnicity, 0, 255);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255)
     */
    private $citizenship1 = '';

    /**
     * @return null|string
     */
    public function getCitizenship1(): ?string
    {
        return $this->citizenship1;
    }

    /**
     * @param null|string $citizenship1
     * @return Person
     */
    public function setCitizenship1(?string $citizenship1): Person
    {
        $this->citizenship1 = mb_substr($citizenship1, 0, 255);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=30, name="citizenship1Passport")
     */
    private $citizenship1Passport = '';

    /**
     * getCitizenship1Passport
     * @return string|null
     */
    public function getCitizenship1Passport(): ?string
    {
        return $this->citizenship1Passport;
    }

    /**
     * setCitizenship1Passport
     * @param string|null $citizenship1Passport
     * @return Person
     */
    public function setCitizenship1Passport(?string $citizenship1Passport): Person
    {
        $this->citizenship1Passport = mb_substr($citizenship1Passport, 0, 30);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, name="citizenship1PassportScan")
     * @ASSERT\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"image/*","application/pdf","application/x-pdf"}
     * )
     */
    private $citizenship1PassportScan = '';

    /**
     * @return null|string
     */
    public function getCitizenship1PassportScan(): ?string
    {
        return $this->citizenship1PassportScan;
    }

    /**
     * @param null|string $citizenship1PassportScan
     * @return Person
     */
    public function setCitizenship1PassportScan(?string $citizenship1PassportScan): Person
    {
        $this->citizenship1PassportScan = mb_substr($citizenship1PassportScan, 0, 255);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255)
     */
    private $citizenship2 = '';

    /**
     * @return null|string
     */
    public function getCitizenship2(): ?string
    {
        return $this->citizenship2;
    }

    /**
     * @param null|string $citizenship2
     * @return Person
     */
    public function setCitizenship2(?string $citizenship2): Person
    {
        $this->citizenship2 = mb_substr($citizenship2, 0, 255);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=30, name="citizenship2Passport")
     */
    private $citizenship2Passport = '';

    /**
     * @return null|string
     */
    public function getCitizenship2Passport(): ?string
    {
        return $this->citizenship2Passport;
    }

    /**
     * @param null|string $citizenship2Passport
     * @return Person
     */
    public function setCitizenship2Passport(?string $citizenship2Passport): Person
    {
        $this->citizenship2Passport = mb_substr($citizenship2Passport, 0, 30);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=30)
     */
    private $religion = '';

    /**
     * @return null|string
     */
    public function getReligion(): ?string
    {
        return $this->religion;
    }

    /**
     * @param null|string $religion
     * @return Person
     */
    public function setReligion(?string $religion): Person
    {
        $this->religion = mb_substr($religion, 0, 30);
        return $this;
    }

    /**
     * getReligionList
     * @return array
     */
    public static function getReligionList(): array
    {
        return ProviderFactory::create(Setting::class)->getSettingByScopeAsArray('User Admin', 'religions');
    }

    /**
     * @var string|null
     * @ORM\Column(length=30, name="nationalIDCardNumber")
     */
    private $nationalIDCardNumber = '';

    /**
     * @return null|string
     */
    public function getNationalIDCardNumber(): ?string
    {
        return $this->nationalIDCardNumber;
    }

    /**
     * @param null|string $nationalIDCardNumber
     * @return Person
     */
    public function setNationalIDCardNumber(?string $nationalIDCardNumber): Person
    {
        $this->nationalIDCardNumber = mb_substr($nationalIDCardNumber, 0, 30);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, name="nationalIDCardScan")
     */
    private $nationalIDCardScan = '';

    /**
     * @return null|string
     */
    public function getNationalIDCardScan(): ?string
    {
        return $this->nationalIDCardScan;
    }

    /**
     * @param null|string $nationalIDCardScan
     * @return Person
     */
    public function setNationalIDCardScan(?string $nationalIDCardScan): Person
    {
        $this->nationalIDCardScan = mb_substr($nationalIDCardScan, 0, 255);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, name="residencyStatus")
     */
    private $residencyStatus = '';

    /**
     * @return null|string
     */
    public function getResidencyStatus(): ?string
    {
        return $this->residencyStatus;
    }

    /**
     * @param null|string $residencyStatus
     * @return Person
     */
    public function setResidencyStatus(?string $residencyStatus): Person
    {
        $this->residencyStatus = mb_substr($residencyStatus, 0, 255);
        return $this;
    }

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(nullable=true, type="date_immutable", name="visaExpiryDate")
     */
    private $visaExpiryDate;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getVisaExpiryDate(): ?\DateTimeImmutable
    {
        return $this->visaExpiryDate;
    }

    /**
     * VisaExpiryDate.
     *
     * @param \DateTimeImmutable|null $visaExpiryDate
     * @return Person
     */
    public function setVisaExpiryDate(?\DateTimeImmutable $visaExpiryDate): Person
    {
        $this->visaExpiryDate = $visaExpiryDate;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=90)
     */
    private $profession = '';

    /**
     * @return null|string
     */
    public function getProfession(): ?string
    {
        return $this->profession;
    }

    /**
     * @param null|string $profession
     * @return Person
     */
    public function setProfession(?string $profession): Person
    {
        $this->profession = mb_substr($profession, 0, 90);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=90)
     */
    private $employer = '';

    /**
     * @return null|string
     */
    public function getEmployer(): ?string
    {
        return $this->employer;
    }

    /**
     * @param null|string $employer
     * @return Person
     */
    public function setEmployer(?string $employer): Person
    {
        $this->employer = mb_substr($employer, 0, 90);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=90, name="jobTitle")
     */
    private $jobTitle = '';

    /**
     * @return null|string
     */
    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    /**
     * @param null|string $jobTitle
     * @return Person
     */
    public function setJobTitle(?string $jobTitle): Person
    {
        $this->jobTitle = mb_substr($jobTitle, 0, 90);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=90, name="emergency1Name")
     */
    private $emergency1Name = '';

    /**
     * @return null|string
     */
    public function getEmergency1Name(): ?string
    {
        return $this->emergency1Name;
    }

    /**
     * @param null|string $emergency1Name
     * @return Person
     */
    public function setEmergency1Name(?string $emergency1Name): Person
    {
        $this->emergency1Name = mb_substr($emergency1Name, 0, 90);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=30, name="emergency1Number1")
     */
    private $emergency1Number1 = '';

    /**
     * @return null|string
     */
    public function getEmergency1Number1(): ?string
    {
        return $this->emergency1Number1;
    }

    /**
     * @param null|string $emergency1Number1
     * @return Person
     */
    public function setEmergency1Number1(?string $emergency1Number1): Person
    {
        $this->emergency1Number1 = mb_substr($emergency1Number1, 0, 30);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=30, name="emergency1Number2")
     */
    private $emergency1Number2 = '';

    /**
     * @return null|string
     */
    public function getEmergency1Number2(): ?string
    {
        return $this->emergency1Number2;
    }

    /**
     * @param null|string $emergency1Number2
     * @return Person
     */
    public function setEmergency1Number2(?string $emergency1Number2): Person
    {
        $this->emergency1Number2 = mb_substr($emergency1Number2, 0, 30);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=30, name="emergency1Relationship")
     */
    private $emergency1Relationship = '';

    /**
     * @return null|string
     */
    public function getEmergency1Relationship(): ?string
    {
        return $this->emergency1Relationship;
    }

    /**
     * @param null|string $emergency1Relationship
     * @return Person
     */
    public function setEmergency1Relationship(?string $emergency1Relationship): Person
    {
        $this->emergency1Relationship = mb_substr($emergency1Relationship, 0, 30);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=90, name="emergency2Name")
     */
    private $emergency2Name = '';

    /**
     * @return null|string
     */
    public function getEmergency2Name(): ?string
    {
        return $this->emergency2Name;
    }

    /**
     * @param null|string $emergency2Name
     * @return Person
     */
    public function setEmergency2Name(?string $emergency2Name): Person
    {
        $this->emergency2Name = mb_substr($emergency2Name, 0, 90);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=30, name="emergency2Number1")
     */
    private $emergency2Number1 = '';

    /**
     * @return null|string
     */
    public function getEmergency2Number1(): ?string
    {
        return $this->emergency2Number1;
    }

    /**
     * @param null|string $emergency2Number1
     * @return Person
     */
    public function setEmergency2Number1(?string $emergency2Number1): Person
    {
        $this->emergency2Number1 = mb_substr($emergency2Number1, 0, 30);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=30, name="emergency2Number2")
     */
    private $emergency2Number2 = '';

    /**
     * @return null|string
     */
    public function getEmergency2Number2(): ?string
    {
        return $this->emergency2Number2;
    }

    /**
     * @param null|string $emergency2Number2
     * @return Person
     */
    public function setEmergency2Number2(?string $emergency2Number2): Person
    {
        $this->emergency2Number2 = mb_substr($emergency2Number2, 0, 30);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=30, name="emergency2Relationship")
     */
    private $emergency2Relationship = '';

    /**
     * @return null|string
     */
    public function getEmergency2Relationship(): ?string
    {
        return $this->emergency2Relationship;
    }

    /**
     * @param null|string $emergency2Relationship
     * @return Person
     */
    public function setEmergency2Relationship(?string $emergency2Relationship): Person
    {
        $this->emergency2Relationship = mb_substr($emergency2Relationship, 0, 30);
        return $this;
    }

    /**
     * @var House|null
     * @ORM\ManyToOne(targetEntity="App\Entity\House")
     * @ORM\JoinColumn(nullable=true, name="gibbonHouseID", referencedColumnName="gibbonHouseID")
     */
    private $house;

    /**
     * @return House|null
     */
    public function getHouse(): ?House
    {
        return $this->house;
    }

    /**
     * @param House|null $house
     * @return Person
     */
    public function setHouse(?House $house): Person
    {
        $this->house = $house;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=10, name="studentID")
     */
    private $studentID = '';

    /**
     * @return null|string
     */
    public function getStudentID(): ?string
    {
        return $this->studentID;
    }

    /**
     * @param null|string $studentID
     * @return Person
     */
    public function setStudentID(?string $studentID): Person
    {
        $this->studentID = mb_substr($studentID, 0, 10);
        return $this;
    }

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="date_immutable", name="dateStart", nullable=true)
     */
    private $dateStart;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDateStart(): ?\DateTimeImmutable
    {
        return $this->dateStart;
    }

    /**
     * @param \DateTimeImmutable|null $dateStart
     * @return Person
     */
    public function setDateStart(?\DateTimeImmutable $dateStart): Person
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @var \DateTimeImmutable|null
     * @ORM\Column(type="date_immutable", name="dateEnd", nullable=true)
     */
    private $dateEnd;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDateEnd(): ?\DateTimeImmutable
    {
        return $this->dateEnd;
    }

    /**
     * @param \DateTimeImmutable|null $dateEnd
     * @return Person
     */
    public function setDateEnd(?\DateTimeImmutable $dateEnd): Person
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    /**
     * @var SchoolYear|null
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolYear")
     * @ORM\JoinColumn(nullable=true, name="gibbonSchoolYearIDClassOf", referencedColumnName="gibbonSchoolYearID")
     */
    private $schoolYearClassOf;

    /**
     * @return SchoolYear|null
     */
    public function getSchoolYearClassOf(): ?SchoolYear
    {
        return $this->schoolYearClassOf;
    }

    /**
     * @param SchoolYear|null $schoolYearClassOf
     * @return Person
     */
    public function setSchoolYearClassOf(?SchoolYear $schoolYearClassOf): Person
    {
        $this->schoolYearClassOf = $schoolYearClassOf;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=100, name="lastSchool")
     */
    private $lastSchool = '';

    /**
     * @return null|string
     */
    public function getLastSchool(): ?string
    {
        return $this->lastSchool;
    }

    /**
     * @param null|string $lastSchool
     * @return Person
     */
    public function setLastSchool(?string $lastSchool): Person
    {
        $this->lastSchool = mb_substr($lastSchool, 0, 100);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=100, name="nextSchool")
     */
    private $nextSchool = '';

    /**
     * @return null|string
     */
    public function getNextSchool(): ?string
    {
        return $this->nextSchool;
    }

    /**
     * @param null|string $nextSchool
     * @return Person
     */
    public function setNextSchool(?string $nextSchool): Person
    {
        $this->nextSchool = mb_substr($nextSchool, 0, 100);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=50, name="departureReason")
     */
    private $departureReason = '';

    /**
     * @return null|string
     */
    public function getDepartureReason(): ?string
    {
        return $this->departureReason;
    }

    /**
     * @param null|string $departureReason
     * @return Person
     */
    public function setDepartureReason(?string $departureReason): Person
    {
        $this->departureReason = mb_substr($departureReason, 0, 50);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column()
     */
    private $transport = '';

    /**
     * @return null|string
     */
    public function getTransport(): ?string
    {
        return $this->transport;
    }

    /**
     * @param null|string $transport
     * @return Person
     */
    public function setTransport(?string $transport): Person
    {
        $this->transport = mb_substr($transport, 0, 50);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(type="text", name="transportNotes")
     */
    private $transportNotes = '';

    /**
     * @return null|string
     */
    public function getTransportNotes(): ?string
    {
        return $this->transportNotes;
    }

    /**
     * @param null|string $transportNotes
     * @return Person
     */
    public function setTransportNotes(?string $transportNotes): Person
    {
        $this->transportNotes = $transportNotes;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=192, name="calendarFeedPersonal", nullable=true)
     */
    private $calendarFeedPersonal;

    /**
     * @return null|string
     */
    public function getCalendarFeedPersonal(): ?string
    {
        return $this->calendarFeedPersonal;
    }

    /**
     * @param null|string $calendarFeedPersonal
     * @return Person
     */
    public function setCalendarFeedPersonal(?string $calendarFeedPersonal): Person
    {
        $this->calendarFeedPersonal = $calendarFeedPersonal;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=1, options={"default": "Y"}, name="viewCalendarSchool")
     */
    private $viewCalendarSchool = 'N';

    /**
     * getViewCalendarSchool
     * @return string|null
     */
    public function getViewCalendarSchool(): ?string
    {
        return $this->viewCalendarSchool;
    }

    /**
     * @param null|string $viewCalendarSchool
     * @return Person
     */
    public function setViewCalendarSchool(?string $viewCalendarSchool): Person
    {
        $this->viewCalendarSchool = in_array($viewCalendarSchool, self::getBooleanList()) ? $viewCalendarSchool : 'Y';
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=1, options={"default": "Y"}, name="viewCalendarPersonal")
     */
    private $viewCalendarPersonal = 'Y';

    /**
     * getViewCalendarPersonal
     * @return string|null
     */
    public function getViewCalendarPersonal(): ?string
    {
        return $this->viewCalendarPersonal;
    }

    /**
     * @param null|string $viewCalendarPersonal
     * @return Person
     */
    public function setViewCalendarPersonal(?string $viewCalendarPersonal): Person
    {
        $this->viewCalendarPersonal = in_array($viewCalendarPersonal, self::getBooleanList()) ? $viewCalendarPersonal : 'Y';
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=1, options={"default": "N"}, name="viewCalendarSpaceBooking")
     */
    private $viewCalendarSpaceBooking = 'N';

    /**
     * @return null|string
     */
    public function getViewCalendarSpaceBooking(): ?string
    {
        return $this->viewCalendarSpaceBooking;
    }

    /**
    /**
     * @param null|string $viewCalendarSpaceBooking
     * @return Person
     */
    public function setViewCalendarSpaceBooking(?string $viewCalendarSpaceBooking): Person
    {
        $this->viewCalendarSpaceBooking = in_array($viewCalendarSpaceBooking, self::getBooleanList()) ? $viewCalendarSpaceBooking : 'N';
        return $this;
    }

    /**
     * @var ApplicationForm|null
     * @ORM\ManyToOne(targetEntity="App\Entity\ApplicationForm")
     * @ORM\JoinColumn(name="gibbonApplicationFormID", referencedColumnName="gibbonApplicationFormID", nullable=true)
     */
    private $applicationForm;

    /**
     * @return ApplicationForm|null
     */
    public function getApplicationForm(): ?ApplicationForm
    {
        return $this->applicationForm;
    }

    /**
     * @param ApplicationForm|null $applicationForm
     * @return Person
     */
    public function setApplicationForm(?ApplicationForm $applicationForm): Person
    {
        $this->applicationForm = $applicationForm;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=20, name="lockerNumber")
     */
    private $lockerNumber = '';

    /**
     * @return null|string
     */
    public function getLockerNumber(): ?string
    {
        return $this->lockerNumber;
    }

    /**
     * @param null|string $lockerNumber
     * @return Person
     */
    public function setLockerNumber(?string $lockerNumber): Person
    {
        $this->lockerNumber = mb_substr($lockerNumber, 0, 20);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=20, name="vehicleRegistration")
     */
    private $vehicleRegistration = '';

    /**
     * @return null|string
     */
    public function getVehicleRegistration(): ?string
    {
        return $this->vehicleRegistration;
    }

    /**
     * @param null|string $vehicleRegistration
     * @return Person
     */
    public function setVehicleRegistration(?string $vehicleRegistration): Person
    {
        $this->vehicleRegistration = mb_substr($vehicleRegistration, 0, 20);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, name="personalBackground")
     */
    private $personalBackground = '';

    /**
     * @return null|string
     */
    public function getPersonalBackground(): ?string
    {
        return $this->personalBackground;
    }

    /**
     * @param null|string $personalBackground
     * @return Person
     */
    public function setPersonalBackground(?string $personalBackground): Person
    {
        $this->personalBackground = mb_substr($personalBackground, 0, 255);
        return $this;
    }

    /**
     * @var \DateTime|null
     * @ORM\Column(type="date", nullable=true, name="messengerLastBubble")
     */
    private $messengerLastBubble;

    /**
     * @return \DateTime|null
     */
    public function getMessengerLastBubble(): ?\DateTime
    {
        return $this->messengerLastBubble;
    }

    /**
     * @param \DateTime|null $messengerLastBubble
     * @return Person
     */
    public function setMessengerLastBubble(?\DateTime $messengerLastBubble): Person
    {
        $this->messengerLastBubble = $messengerLastBubble;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $privacy;

    /**
     * @return null|string
     */
    public function getPrivacy(): ?string
    {
        return $this->privacy;
    }

    /**
     * @param null|string $privacy
     * @return Person
     */
    public function setPrivacy(?string $privacy): Person
    {
        $this->privacy = $privacy;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, nullable=true, name="dayType", options={"comment": "Student day type, as specified in the application form."})
     */
    private $dayType;

    /**
     * @return null|string
     */
    public function getDayType(): ?string
    {
        return $this->dayType;
    }

    /**
     * @param null|string $dayType
     * @return Person
     */
    public function setDayType(?string $dayType): Person
    {
        $this->dayType = mb_substr($dayType, 0, 255);
        return $this;
    }

    /**
     * @var Theme|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Theme")
     * @ORM\JoinColumn(name="gibbonThemeIDPersonal", referencedColumnName="gibbonThemeID", nullable=true)
     */
    private $theme;

    /**
     * @return Theme|null
     */
    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    /**
     * @param Theme|null $theme
     * @return Person
     */
    public function setTheme(?Theme $theme): Person
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * @var I18n|null
     * @ORM\ManyToOne(targetEntity="App\Entity\I18n")
     * @ORM\JoinColumn(name="gibboni18nIDPersonal", referencedColumnName="gibboni18nID", nullable=true)
     */
    private $i18nPersonal;

    /**
     * @return I18n|null
     */
    public function getI18nPersonal(): ?I18n
    {
        return $this->i18nPersonal;
    }

    /**
     * @param I18n|null $i18nPersonal
     * @return Person
     */
    public function setI18nPersonal(?I18n $i18nPersonal): Person
    {
        $this->i18nPersonal = $i18nPersonal;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true, name="studentAgreements")
     */
    private $studentAgreements;

    /**
     * @return null|string
     */
    public function getStudentAgreements(): ?string
    {
        return $this->studentAgreements;
    }

    /**
     * @param null|string $studentAgreements
     * @return Person
     */
    public function setStudentAgreements(?string $studentAgreements): Person
    {
        $this->studentAgreements = $studentAgreements;
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=255, name="googleAPIRefreshToken")
     */
    private $googleAPIRefreshToken = '';

    /**
     * @return null|string
     */
    public function getGoogleAPIRefreshToken(): ?string
    {
        return $this->googleAPIRefreshToken;
    }

    /**
     * @param null|string $googleAPIRefreshToken
     * @return Person
     */
    public function setGoogleAPIRefreshToken(?string $googleAPIRefreshToken): Person
    {
        $this->googleAPIRefreshToken = mb_substr($googleAPIRefreshToken, 0, 255);
        return $this;
    }

    /**
     * @var string|null
     * @ORM\Column(length=1, options={"default": "Y"}, name="receiveNotificationEmails")
     */
    private $receiveNotificationEmails = 'Y';

    /**
     * @return bool
     */
    public function isReceiveNotificationEmails(): bool
    {
        return $this->getReceiveNotificationEmails() === 'Y';
    }

    /**
     * @return null|string
     */
    public function getReceiveNotificationEmails(): ?string
    {
        return $this->receiveNotificationEmails = self::checkBoolean($this->receiveNotificationEmails);
    }

    /**
     * @param null|string $receiveNotificationEmails
     * @return Person
     */
    public function setReceiveNotificationEmails(?string $receiveNotificationEmails): Person
    {
        $this->receiveNotificationEmails = self::checkBoolean($receiveNotificationEmails);
        return $this;
    }

    /**
     * @var string
     * @ORM\Column(type="array", options={"comment": "Serialised array of custom field values"}, nullable=true)
     * Gibbon does not support NULL for this field.
     */
    private $fields = [];

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields = is_array($this->fields) ? $this->fields : [];
    }

    /**
     * @param string|array $fields
     * @return Person
     */
    public function setFields(array $fields): Person
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * addField
     * @param $field
     * @param $value
     * @return Person
     */
    public function addField($key, $value): Person
    {
        $this->getFields();

        $this->fields[$key] = $value;
        return $this;
    }

    /**
     * mergeFields
     * @param array $fields
     * @return Person
     */
    public function mergeFields(array $fields): Person
    {
        foreach($fields as $field)
            if (! isset($this->getFields()[$field->getId()]))
                $this->fields[$field->getId()] = null;
        ksort($this->fields);

        return $this;
    }

    /**
     * isSystemAdmin
     * @return bool
     */
    public function isSystemAdmin(): bool
    {
        return $this->getId() === 1;
    }

    /**
     * @var Staff|null
     * @ORM\OneToOne(targetEntity="App\Entity\Staff", mappedBy="person")
     */
    private $staff;

    /**
     * @return Staff|null
     */
    public function getStaff(): ?Staff
    {
        return $this->staff;
    }

    /**
     * setStaff
     * @param Staff|null $staff
     * @param bool $add
     * @return Person
     */
    public function setStaff(?Staff $staff, bool $add = true): Person
    {
        if ($staff instanceof Staff && $add)
            $staff->setPerson($this, false);
        $this->staff = $staff;
        return $this;
    }

    /**
     * @var Collection|null
     * @ORM\OneToMany(targetEntity="App\Entity\CourseClassPerson", mappedBy="person")
     */
    private $courseClassPerson;

    /**
     * getCourseClassPerson
     * @return Collection|null
     */
    public function getCourseClassPerson(): ?Collection
    {
        if (empty($this->courseClassPerson))
            $this->courseClassPerson = new ArrayCollection();

        if ($this->courseClassPerson instanceof PersistentCollection)
            $this->courseClassPerson->initialize();

        return $this->courseClassPerson;
    }

    /**
     * @param Collection|null $courseClassPerson
     * @return Person
     */
    public function setCourseClassPerson(?Collection $courseClassPerson): Person
    {
        $this->courseClassPerson = $courseClassPerson;
        return $this;
    }

    /**
     * renderImage
     * @param int $dimension
     * @param bool $asHeight
     * @return string
     * @deprecated 4/Sep 2019: Please use Person::photo()
     */
    public function renderImage(int $dimension = 75, bool $asHeight = false)
    {
        trigger_error('Deprecated 4/Sep 2019: Please use Person::photo()', E_USER_DEPRECATED);
        return $this->photo($dimension);
    }

    /**
     * formatName
     * @param bool|array $preferredName
     * @param bool $reverse
     * @param bool $informal
     * @param bool $initial
     * @return string
     */
    public function formatName($preferredName = true, bool $reverse = false, bool $informal = false, bool $initial = false, bool $title = false)
    {
        if (is_array($preferredName))
        {
            $format = $preferredName;
            $preferredName = isset($format['preferredName']) ? (bool) $format['preferredName'] : true;
            $reverse = isset($format['reverse']) ? (bool) $format['reverse'] : false;
            $informal = isset($format['informal']) ? (bool) $format['informal'] : false;
            $initial = isset($format['initial']) ? (bool) $format['initial'] : false;
            $title = isset($format['title']) ? (bool) $format['title'] : false;
        }
        $name = $preferredName ? $this->getPreferredName() : $this->getFirstName();
        $name = $initial ? substr($name, 0, 1).'.' : $name;
        return Format::name($title ? $this->getTitle() : '', $name, $this->getSurname(),$this->getPrimaryRole() ? $this->getPrimaryRole()->getCategory() : 'Staff', $reverse, $informal);
    }

    /**
     * getFullName
     * This is used for sorting purposes.
     * @return string
     */
    public function getFullName()
    {
        return $this->getSurname().': '.$this->getFirstName();
    }

    /**
     * getLocale
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->getI18nPersonal();
    }

    /**
     * @return array
     */
    public static function getTitleList(bool $forChoice = false): array
    {
        if ($forChoice)
        {
            $choice = [];
            foreach(self::$titleList as $name)
                $choice[$name] = $name;
            return $choice;
        }
        return self::$titleList;
    }

    /**
     * Returns an HTML <img> based on the supplied photo path, using a placeholder image if none exists. Size may be either 75 or 240 at this time.
     *
     * @param int|string $size
     * @param string $class
     * @return array
     */
    public function photo($size = 75, string $class = '')
    {
        $class .= ' inline-block shadow bg-white border border-gray-600 ';

        $path = $this->getImage240(true);

        switch ($size) {
            case 240:
            case 'lg':
                $class .= 'w-48 sm:w-64 max-w-full p-1 mx-auto';
                $imageSize = 240;
                break;
            case 75:
            case 'md':
                $class .= 'w-20 lg:w-24 p-1';
                $imageSize = 75;
                break;
            case 'sm':
                $class .= 'w-12 sm:w-20 p-px sm:p-1';
                $imageSize = 75;
                break;
            default:
                $imageSize = $size;
        }

        if (!file_exists(__DIR__ . '/../../public/' .$path) ) {
            $path = '/themes/{theme}/img/anonymous_'.$imageSize.'.jpg';
        }

        $result['class'] = $class;
        $result['asset'] = $path;
        $result['fileName'] = $path;
        $result['title'] = $this->formatName(['informal' => true]);
        $result['fileExists'] = true;
        return $result;
    }

    /**
     * Display an icon if this user's birthday is within the next week.
     *
     * @return string
     */
    public function birthdayIcon()
    {
        if (!$this->getDob() instanceof \DateTime)
            return '';

        $dob = new \DateTime(date('Y-') . $this->getDob()->format('m-d'));
        $today = new \DateTime('now');
        if ($today->format('Ymd') > $dob->format('Ymd'))
            return '';

        $daysUntilNextBirthday = $today->diff($dob)->days;
        if ($daysUntilNextBirthday >= 8)
            return '';

        // HEY SHORTY IT'S YOUR BIRTHDAY! (or Close)
        $result['colour'] = 'text-pink-800';
        $result['params']['{name}'] = $this->getPreferredName();
        $result['params']['count'] = $daysUntilNextBirthday;
        if ($daysUntilNextBirthday > 0)
            $result['colour'] = 'text-gray-800';

        return $result;
    }

    /**
     * @var StudentEnrolment[]|Collection||null
     * @ORM\OneToMany(targetEntity="App\Entity\StudentEnrolment", mappedBy="person")
     */
    private $studentEnrolments;

    /**
     * getStudentEnrolments
     * @return Collection|null
     */
    public function getStudentEnrolments(): ?Collection
    {
        if (null === $this->studentEnrolments)
            $this->studentEnrolments = new ArrayCollection();

        if ($this->studentEnrolments instanceof PersistentCollection)
            $this->studentEnrolments->initialize();

        return $this->studentEnrolments;
    }

    /**
     * StudentEnrolments.
     *
     * @param StudentEnrolment|null $studentEnrolments
     * @return Person
     */
    public function setStudentEnrolments(?StudentEnrolment $studentEnrolments): Person
    {
        $this->studentEnrolments = $studentEnrolments;
        return $this;
    }

    /**
     * @var FamilyAdult|null
     * @ORM\OneToMany(targetEntity="Kookaburra\UserAdmin\Entity\FamilyAdult", mappedBy="person")
     */
    private $adults;

    /**
     * @return Collection|FamilyAdult[]|null
     */
    public function getAdults(): Collection
    {
        if (!$this->adults)
            $this->adults = new ArrayCollection();
        
        if ($this->adults instanceof PersistentCollection)
            $this->adults->initialize();
        
        return $this->adults;
    }

    /**
     * Adults.
     *
     * @param FamilyAdult|null $adults
     * @return Person
     */
    public function setAdults(?FamilyAdult $adults): Person
    {
        $this->adults = $adults;
        return $this;
    }

    /**
     * @var FamilyChild|null
     * @ORM\OneToMany(targetEntity="Kookaburra\UserAdmin\Entity\FamilyChild", mappedBy="person")
     */
    private $children;

    /**
     * @return Collection|FamilyChild[]|null
     */
    public function getChildren(): Collection
    {
        if (!$this->children)
            $this->children = new ArrayCollection();

        if ($this->children instanceof PersistentCollection)
            $this->children->initialize();

        return $this->children;
    }

    /**
     * Children.
     *
     * @param FamilyChild|null $children
     * @return Person
     */
    public function setChildren(?FamilyChild $children): Person
    {
        $this->children = $children;
        return $this;
    }

    /**
     * getEmergencyRelationshipList
     * @return array
     */
    public static function getEmergencyRelationshipList():array
    {
        return [
            'Parent',
            'Spouse',
            'Offspring',
            'Friend',
            'Other Relation',
            'Doctor',
            'Other',
        ];
    }

    /**
     * __toString
     * @return string
     */
    public function __toString(): string
    {
        return $this->formatName();
    }

    /**
     * getLanguageList
     * @return array|string[]
     */
    public static function getLanguageList()
    {
        $languages = Languages::getNames();
        return array_flip($languages);
    }

    /**
     * uniqueIdentifier
     * @return string
     */
    public function uniqueIdentifier(): string
    {
        if (is_string($this->getStudentID()) && $this->getStudentID() !== '')
            return $this->getStudentID();

        if (is_string($this->getUsername()) && $this->getUsername() !== '')
                return $this->getUsername();

        return str_pad($this->getId(), 10, '0', STR_PAD_LEFT);
    }

    /**
     * toArray
     * @return array
     */
    public function toArray(): array
    {
        return [
            'fullName' => $this->formatName(['informal' => true, 'reverse' => true]),
            'photo' => ImageHelper::getAbsoluteImageURL('File', $this->getImage240()),
            'status' => TranslationsHelper::translate($this->getStatus()),
            'family' => $this->getFamilyName(),
            'username' => $this->getUsername(),
        ];
    }

    /**
     * getFamilyName
     * @return string
     */
    public function getFamilyName(): string
    {
        if ($this->getAdults()->count() > 0) {
            $adult = $this->getAdults()->first();
            if ($adult->getFamily() instanceof Family)
                return $adult->getFamily()->getName();
        }
        if ($this->getChildren()->count() > 0) {
            $adult = $this->getChildren()->first();
            if ($adult->getFamily() instanceof Family)
                return $adult->getFamily()->getName();
        }
        return '';
    }
}