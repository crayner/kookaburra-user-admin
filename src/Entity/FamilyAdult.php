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

use App\Manager\EntityInterface;
use App\Manager\Traits\BooleanList;
use App\Util\TranslationsHelper;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FamilyAdult
 * @package Kookaburra\UserAdmin\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\UserAdmin\Repository\FamilyAdultRepository")
 * @ORM\Table(options={"auto_increment": 1}, name="FamilyAdult", indexes={@ORM\Index(name="gibbonPersonIndex", columns={"gibbonPersonID"})}, uniqueConstraints={@ORM\UniqueConstraint(name="familyContactPriority", columns={"gibbonFamilyID","contactPriority"}), @ORM\UniqueConstraint(name="familymember", columns={"gibbonFamilyID","gibbonPersonID"})})
 * @UniqueEntity(fields={"family","person"},errorPath="person")
 * @UniqueEntity(fields={"family","contactPriority"},errorPath="contactPriority")
 */
class FamilyAdult implements EntityInterface
{
    use BooleanList;

    /**
     * @var integer|null
     * @ORM\Id()
     * @ORM\Column(type="integer", name="gibbonFamilyAdultID", columnDefinition="INT(8) UNSIGNED ZEROFILL")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Family|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Family")
     * @ORM\JoinColumn(name="gibbonFamilyID", referencedColumnName="gibbonFamilyID", nullable=false)
     * @Assert\NotBlank()
     */
    private $family;

    /**
     * @var Person|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Person", inversedBy="adults")
     * @ORM\JoinColumn(name="gibbonPersonID", referencedColumnName="gibbonPersonID", nullable=false)
     * @Assert\NotBlank()
     */
    private $person;

    /**
     * @var string|null
     * @ORM\Column(type="text",nullable=true)
     */
    private $comment;

    /**
     * @var string|null
     * @ORM\Column(length=1, name="childDataAccess")
     * @Assert\Choice({"Y","N"})
     */
    private $childDataAccess;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", name="contactPriority", options={"default": 1}, columnDefinition="INT(2)")
     * @Assert\NotBlank()
     * @Assert\Range(min=1,max=99)
     */
    private $contactPriority;

    /**
     * @var string|null
     * @ORM\Column(length=1, name="contactCall")
     * @Assert\Choice({"Y","N"})
     */
    private $contactCall;

    /**
     * @var string|null
     * @ORM\Column(length=1, name="contactSMS")
     * @Assert\Choice({"Y","N"})
     */
    private $contactSMS;

    /**
     * @var string|null
     * @ORM\Column(length=1, name="contactEmail")
     * @Assert\Choice({"Y","N"})
     */
    private $contactEmail;

    /**
     * @var string|null
     * @ORM\Column(length=1, name="contactMail")
     * @Assert\Choice({"Y","N"})
     */
    private $contactMail;

    /**
     * @var Collection|FamilyRelationship[]
     * @ORM\OneToMany(targetEntity="Kookaburra\UserAdmin\Entity\FamilyRelationship",mappedBy="adult",orphanRemoval=true)
     */
    private $relationships;

    /**
     * FamilyAdult constructor.
     * @param Family|null $family
     */
    public function __construct(?Family $family = null)
    {
        $this->family = $family;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return FamilyAdult
     */
    public function setId(?int $id): FamilyAdult
    {
        $this->id = $id;
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
     * @return FamilyAdult
     */
    public function setFamily(?Family $family): FamilyAdult
    {
        $this->family = $family;
        return $this;
    }

    /**
     * @return Person|null
     */
    public function getPerson(): ?Person
    {
        return $this->person;
    }

    /**
     * @param Person|null $person
     * @return FamilyAdult
     */
    public function setPerson(?Person $person): FamilyAdult
    {
        $this->person = $person;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     * @return FamilyAdult
     */
    public function setComment(?string $comment): FamilyAdult
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getChildDataAccess(): ?string
    {
        return $this->childDataAccess;
    }

    /**
     * @param string|null $childDataAccess
     * @return FamilyAdult
     */
    public function setChildDataAccess(?string $childDataAccess): FamilyAdult
    {
        $this->childDataAccess = $childDataAccess;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getContactPriority(): ?int
    {
        return $this->contactPriority;
    }

    /**
     * @param int|null $contactPriority
     * @return FamilyAdult
     */
    public function setContactPriority(?int $contactPriority): FamilyAdult
    {
        $this->contactPriority = $contactPriority;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContactCall(): ?string
    {
        return $this->contactCall;
    }

    /**
     * @param string|null $contactCall
     * @return FamilyAdult
     */
    public function setContactCall(?string $contactCall): FamilyAdult
    {
        $this->contactCall = $contactCall;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContactSMS(): ?string
    {
        return $this->contactSMS;
    }

    /**
     * @param string|null $contactSMS
     * @return FamilyAdult
     */
    public function setContactSMS(?string $contactSMS): FamilyAdult
    {
        $this->contactSMS = $contactSMS;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    /**
     * @param string|null $contactEmail
     * @return FamilyAdult
     */
    public function setContactEmail(?string $contactEmail): FamilyAdult
    {
        $this->contactEmail = $contactEmail;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContactMail(): ?string
    {
        return $this->contactMail;
    }

    /**
     * @param string|null $contactMail
     * @return FamilyAdult
     */
    public function setContactMail(?string $contactMail): FamilyAdult
    {
        $this->contactMail = $contactMail;
        return $this;
    }

    /**
     * @return Collection|FamilyRelationship[]
     */
    public function getRelationships()
    {
        return $this->relationships;
    }

    /**
     * Relationships.
     *
     * @param Collection|FamilyRelationship[] $relationships
     * @return FamilyAdult
     */
    public function setRelationships($relationships)
    {
        $this->relationships = $relationships;
        return $this;
    }

    /**
     * __toString
     * @return string
     */
    public function __toString(): string
    {
        return $this->getFamily()->getName() . ': ' . $this->getPerson()->formatName();
    }

    /**
     * toArray
     * @param string|null $name
     * @return array
     */
    public function toArray(?string $name = null): array
    {
        $person = $this->getPerson();

        return [
            'fullName' => $person->formatName(['style' => 'formal']),
            'status' => TranslationsHelper::translate($person->getStatus(), [], 'UserAdmin'),
            'comment' => $this->getComment(),
            'adult_id' => $this->getId(),
            'family_id' => $this->getFamily()->getId(),
            'childDataAccess' => TranslationsHelper::translate(($this->getChildDataAccess() === 'Y' ? 'Yes' : 'No'), [], 'messages'),
            'phone' => TranslationsHelper::translate(($this->getContactCall() === 'Y' ? 'Yes' : 'No'), [], 'messages'),
            'sms' => TranslationsHelper::translate(($this->getContactSMS() === 'Y' ? 'Yes' : 'No'), [], 'messages'),
            'email' => TranslationsHelper::translate(($this->getContactEmail() === 'Y' ? 'Yes' : 'No'), [], 'messages'),
            'mail' => TranslationsHelper::translate(($this->getContactMail() === 'Y' ? 'Yes' : 'No'), [], 'messages'),
            'contactPriority' => $this->getContactPriority(),
        ];
    }

    /**
     * isEqualTo
     * @param FamilyAdult $adult
     * @return bool
     */
    public function isEqualTo(FamilyAdult $adult): bool
    {
        if($this->getPerson() === null || $adult->getPerson() === null || $this->getFamily() === null || $adult->getFamily() === null)
            return false;
        if (!$adult->getPerson()->isEqualTo($this->getPerson()))
            return false;
        if (!$adult->getFamily()->isEqualTo($this->getFamily()))
            return false;
        return true;
    }
}