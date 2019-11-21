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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FamilyRelationship
 * @package Kookaburra\UserAdmin\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\UserAdmin\Repository\FamilyRelationshipRepository")
 * @ORM\Table(options={"auto_increment": 1}, name="FamilyRelationship", uniqueConstraints={@ORM\UniqueConstraint(name="familyAdultChild", columns={"gibbonFamilyID","gibbonPersonID1","gibbonPersonID2"})})
 * @UniqueEntity({"family","person1","person2"})
 */
class FamilyRelationship
{
    /**
     * @var integer|null
     * @ORM\Id()
     * @ORM\Column(type="integer", name="gibbonFamilyRelationshipID", columnDefinition="INT(9) UNSIGNED ZEROFILL AUTO_INCREMENT")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Family|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Family")
     * @ORM\JoinColumn(name="gibbonFamilyID", referencedColumnName="gibbonFamilyID", nullable=false)
     */
    private $family;

    /**
     * @var Person|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Person")
     * @ORM\JoinColumn(name="gibbonPersonID1", referencedColumnName="gibbonPersonID", nullable=false)
     */
    private $person1;

    /**
     * @var Person|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Person")
     * @ORM\JoinColumn(name="gibbonPersonID2", referencedColumnName="gibbonPersonID", nullable=false)
     */
    private $person2;

    /**
     * @var string|null
     * @ORM\Column(length=50)
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getRelationshipList")
     */
    private $relationship;

    /**
     * @var array
     */
    private static $relationshipList = [
        'Mother',
        'Father',
        'Step-Mother',
        'Step-Father',
        'Adoptive Parent',
        'Guardian',
        'Grandmother',
        'Grandfather',
        'Aunt',
        'Uncle',
        'Nanny/Helper',
        'Other',
    ];


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return FamilyRelationship
     */
    public function setId(?int $id): FamilyRelationship
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
     * @return FamilyRelationship
     */
    public function setFamily(?Family $family): FamilyRelationship
    {
        $this->family = $family;
        return $this;
    }

    /**
     * @return Person|null
     */
    public function getPerson1(): ?Person
    {
        return $this->person1;
    }

    /**
     * @param Person|null $person1
     * @return FamilyRelationship
     */
    public function setPerson1(?Person $person1): FamilyRelationship
    {
        $this->person1 = $person1;
        return $this;
    }

    /**
     * @return Person|null
     */
    public function getPerson2(): ?Person
    {
        return $this->person2;
    }

    /**
     * @param Person|null $person2
     * @return FamilyRelationship
     */
    public function setPerson2(?Person $person2): FamilyRelationship
    {
        $this->person2 = $person2;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    /**
     * @param string|null $relationship
     * @return FamilyRelationship
     */
    public function setRelationship(?string $relationship): FamilyRelationship
    {
        $this->relationship = $relationship;
        return $this;
    }

    /**
     * @return array
     */
    public static function getRelationshipList(): array
    {
        return self::$relationshipList;
    }

    /**
     * __toString
     * @return string
     */
    public function __toString(): string
    {
        return $this->getFamily()->__toString() . ': ' . $this->getPerson1()->formatName() . ' is ' . $this->getRelationship() . ' of ' . $this->getPerson2()->formatName();
    }
}