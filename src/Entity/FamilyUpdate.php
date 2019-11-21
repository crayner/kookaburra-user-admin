<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 23/11/2018
 * Time: 15:27
 */

namespace Kookaburra\UserAdmin\Entity;

use App\Entity\SchoolYear;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class FamilyUpdate
 * @package Kookaburra\UserAdmin\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\UserAdmin\Repository\FamilyUpdateRepository")
 * @ORM\Table(options={"auto_increment": 1}, name="FamilyUpdate", indexes={@ORM\Index(name="gibbonFamilyIndex", columns={"gibbonFamilyID", "gibbonSchoolYearID"})})
 */
class FamilyUpdate
{
    /**
     * @var integer|null
     * @ORM\Id()
     * @ORM\Column(type="integer", name="gibbonFamilyUpdateID", columnDefinition="INT(9) UNSIGNED ZEROFILL AUTO_INCREMENT")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var SchoolYear|null
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolYear")
     * @ORM\JoinColumn(name="gibbonSchoolYearID", referencedColumnName="gibbonSchoolYearID")
     */
    private $schoolYear;

    /**
     * @var string
     * @ORM\Column(length=8, options={"default": "Pending"})
     */
    private $status = 'Pending';

    /**
     * @var array
     */
    private static $statusList = ['Pending', 'Complete'];

    /**
     * @var Family|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Family")
     * @ORM\JoinColumn(name="gibbonFamilyID", referencedColumnName="gibbonFamilyID", nullable=false)
     */
    private $family;

    /**
     * @var string
     * @ORM\Column(length=100, name="nameAddress")
     */
    private $nameAddress;

    /**
     * @var string|null
     * @ORM\Column(type="text", name="homeAddress")
     */
    private $homeAddress;

    /**
     * @var string|null
     * @ORM\Column(name="homeAddressDistrict")
     */
    private $homeAddressDistrict;

    /**
     * @var string|null
     * @ORM\Column(name="homeAddressCountry")
     */
    private $homeAddressCountry;

    /**
     * @var string|null
     * @ORM\Column(name="languageHomePrimary", length=30)
     */
    private $languageHomePrimary;

    /**
     * @var string|null
     * @ORM\Column(name="languageHomeSecondary", length=30)
     */
    private $languageHomeSecondary;

    /**
     * @var Person|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Person")
     * @ORM\JoinColumn(name="gibbonPersonIDUpdater", referencedColumnName="gibbonPersonID", nullable=false)
     */
    private $personUpdater;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $timestamp;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return FamilyUpdate
     */
    public function setId(?int $id): FamilyUpdate
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return SchoolYear|null
     */
    public function getSchoolYear(): ?SchoolYear
    {
        return $this->schoolYear;
    }

    /**
     * @param SchoolYear|null $schoolYear
     * @return FamilyUpdate
     */
    public function setSchoolYear(?SchoolYear $schoolYear): FamilyUpdate
    {
        $this->schoolYear = $schoolYear;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return FamilyUpdate
     */
    public function setStatus(string $status): FamilyUpdate
    {
        $this->status = in_array($status, self::getStatusList()) ? $status : 'Pending';
        return $this;
    }

    /**
     * @return Family|null
     */
    public function getFamily(): ?Family
    {
        return $this->family;
    }

    /**
     * @param Family|null $family
     * @return FamilyUpdate
     */
    public function setFamily(?Family $family): FamilyUpdate
    {
        $this->family = $family;
        return $this;
    }

    /**
     * @return string
     */
    public function getNameAddress(): string
    {
        return $this->nameAddress;
    }

    /**
     * @param string $nameAddress
     * @return FamilyUpdate
     */
    public function setNameAddress(string $nameAddress): FamilyUpdate
    {
        $this->nameAddress = $nameAddress;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHomeAddress(): ?string
    {
        return $this->homeAddress;
    }

    /**
     * @param string|null $homeAddress
     * @return FamilyUpdate
     */
    public function setHomeAddress(?string $homeAddress): FamilyUpdate
    {
        $this->homeAddress = $homeAddress;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHomeAddressDistrict(): ?string
    {
        return $this->homeAddressDistrict;
    }

    /**
     * @param string|null $homeAddressDistrict
     * @return FamilyUpdate
     */
    public function setHomeAddressDistrict(?string $homeAddressDistrict): FamilyUpdate
    {
        $this->homeAddressDistrict = $homeAddressDistrict;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHomeAddressCountry(): ?string
    {
        return $this->homeAddressCountry;
    }

    /**
     * @param string|null $homeAddressCountry
     * @return FamilyUpdate
     */
    public function setHomeAddressCountry(?string $homeAddressCountry): FamilyUpdate
    {
        $this->homeAddressCountry = $homeAddressCountry;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLanguageHomePrimary(): ?string
    {
        return $this->languageHomePrimary;
    }

    /**
     * @param string|null $languageHomePrimary
     * @return FamilyUpdate
     */
    public function setLanguageHomePrimary(?string $languageHomePrimary): FamilyUpdate
    {
        $this->languageHomePrimary = $languageHomePrimary;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLanguageHomeSecondary(): ?string
    {
        return $this->languageHomeSecondary;
    }

    /**
     * @param string|null $languageHomeSecondary
     * @return FamilyUpdate
     */
    public function setLanguageHomeSecondary(?string $languageHomeSecondary): FamilyUpdate
    {
        $this->languageHomeSecondary = $languageHomeSecondary;
        return $this;
    }

    /**
     * @return Person|null
     */
    public function getPersonUpdater(): ?Person
    {
        return $this->personUpdater;
    }

    /**
     * @param Person|null $personUpdater
     * @return FamilyUpdate
     */
    public function setPersonUpdater(?Person $personUpdater): FamilyUpdate
    {
        $this->personUpdater = $personUpdater;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getTimestamp(): ?\DateTime
    {
        return $this->timestamp;
    }

    /**
     * setTimestamp
     * @param \DateTime|null $timestamp
     * @return FamilyUpdate
     * @throws \Exception
     * @ORM\PrePersist()
     */
    public function setTimestamp(?\DateTime $timestamp = null): FamilyUpdate
    {
        $this->timestamp = $timestamp ?: new \DateTime('now');
        return $this;
    }

    /**
     * getStatusList
     * @return array
     */
    public static function getStatusList(): array
    {
        return self::$statusList;
    }
}