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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FamilyRelationship
 * @package Kookaburra\UserAdmin\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\UserAdmin\Repository\FamilyRelationshipRepository")
 * @ORM\Table(options={"auto_increment": 1}, name="FamilyRelationship", uniqueConstraints={@ORM\UniqueConstraint(name="familyAdultChild", columns={"family","adult","child"})})
 * @UniqueEntity({"family","adult","child"})
 */
class FamilyRelationship implements EntityInterface
{
    /**
     * @var integer|null
     * @ORM\Id()
     * @ORM\Column(type="integer", columnDefinition="INT(9) UNSIGNED")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Family|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Family")
     * @ORM\JoinColumn(name="family", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $family;

    /**
     * @var FamilyAdult|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\FamilyAdult",inversedBy="relationships")
     * @ORM\JoinColumn(name="adult",referencedColumnName="id",nullable=false)
     * @Assert\NotBlank()
     */
    private $adult;

    /**
     * @var FamilyChild|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\FamilyChild",inversedBy="relationships")
     * @ORM\JoinColumn(name="child",referencedColumnName="id",nullable=false)
     * @Assert\NotBlank()
     */
    private $child;

    /**
     * @var string|null
     * @ORM\Column(length=31,nullable=true)
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
     * @param FamilyAdult|null $adult
     * @param FamilyChild|null $child
     */
    public function __construct(?Family $family = null, ?FamilyAdult $adult = null, ?FamilyChild $child = null)
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
     * @return FamilyAdult|null
     */
    public function getAdult(): ?FamilyAdult
    {
        return $this->adult;
    }

    /**
     * Adult.
     *
     * @param FamilyAdult|null $adult
     * @return FamilyRelationship
     */
    public function setAdult(?FamilyAdult $adult): FamilyRelationship
    {
        $this->adult = $adult;
        return $this;
    }

    /**
     * @return FamilyChild|null
     */
    public function getChild(): ?FamilyChild
    {
        return $this->child;
    }

    /**
     * Child.
     *
     * @param FamilyChild|null $child
     * @return FamilyRelationship
     */
    public function setChild(?FamilyChild $child): FamilyRelationship
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
        return $this->getFamily()->__toString() . ': ' . $this->getAdult()->getPerson()->formatName(['style' => 'formal']) . ' is ' . $this->getRelationship() . ' of ' . $this->getChild()->getPerson()->formatName(['style' => 'long']);
    }

    /**
     * isEqualTo
     * @param FamilyRelationship $relationship
     * @return bool
     */
    public function isEqualTo(FamilyRelationship $relationship): bool
    {
        if (!$relationship->getFamily()->isEqualTo($this->getFamily()))
            return false;
        if (!$relationship->getAdult()->isEqualTo($this->getAdult()))
            return false;
        if (!$relationship->getChild()->isEqualTo($this->getChild()))
            return false;
        return true;
    }

    /**
     * toArray
     * @param string|null $name
     * @return array
     */
    public function toArray(?string $name = null): array
    {
        return [];
    }
}