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
 * @UniqueEntity({"family","adult","child"})
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
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Family", inversedBy="relationships")
     * @ORM\JoinColumn(name="gibbonFamilyID", referencedColumnName="gibbonFamilyID", nullable=false)
     * @Assert\NotBlank()
     */
    private $family;

    /**
     * @var Person|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Person")
     * @ORM\JoinColumn(name="gibbonPersonID1", referencedColumnName="gibbonPersonID", nullable=false)
     * @Assert\NotBlank()
     */
    private $adult;

    /**
     * @var Person|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Person")
     * @ORM\JoinColumn(name="gibbonPersonID2", referencedColumnName="gibbonPersonID", nullable=false)
     * @Assert\NotBlank()
     */
    private $child;

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
     * FamilyRelationship constructor.
     * @param Family|null $family
     * @param Person|null $adult
     * @param Person|null $child
     */
    public function __construct(?Family $family = null, ?Person $adult = null, ?Person $child = null)
    {
        $this->family = $family;
        $this->adult = $adult;
        $this->child = $child;
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
    public function getAdult(): ?Person
    {
        return $this->adult;
    }

    /**
     * Adult.
     *
     * @param Person|null $adult
     * @return FamilyRelationship
     */
    public function setAdult(?Person $adult): FamilyRelationship
    {
        $this->adult = $adult;
        return $this;
    }

    /**
     * @return Person|null
     */
    public function getChild(): ?Person
    {
        return $this->child;
    }

    /**
     * Child.
     *
     * @param Person|null $child
     * @return FamilyRelationship
     */
    public function setChild(?Person $child): FamilyRelationship
    {
        $this->child = $child;
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
        return $this->getFamily()->__toString() . ': ' . $this->getAdult()->formatName(['style' => 'formal']) . ' is ' . $this->getRelationship() . ' of ' . $this->getChild()->formatName(['style' => 'long']);
    }

    /**
     * isEqualTo
     * @param FamilyRelationship $relationship
     * @return bool
     */
    public function isEqualTo(FamilyRelationship $relationship): bool
    {
        if (!$relationship->getAdult()->isEqualTo($this->getAdult()))
            return false;
        if (!$relationship->getChild()->isEqualTo($this->getChild()))
            return false;
        return true;
    }
}