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
use App\Util\ImageHelper;
use App\Util\TranslationsHelper;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Kookaburra\UserAdmin\Util\StudentHelper;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FamilyChild
 * @package Kookaburra\UserAdmin\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\UserAdmin\Repository\FamilyChildRepository")
 * @ORM\Table(options={"auto_increment": 1}, name="FamilyChild",
 *     indexes={@ORM\Index(name="family", columns={"family"}),@ORM\Index(name="person", columns={"person"})},
 *     uniqueConstraints={@ORM\UniqueConstraint(name="familyMember", columns={"family","person"})})
 * @UniqueEntity(fields={"family","person"}, errorPath="person")
 */
class FamilyChild implements EntityInterface
{
    /**
     * @var integer|null
     * @ORM\Id()
     * @ORM\Column(type="integer",columnDefinition="INT(8) UNSIGNED ZEROFILL")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Family|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Family")
     * @ORM\JoinColumn(name="family",referencedColumnName="id",nullable=false)
     * @Assert\NotBlank()
     */
    private $family;

    /**
     * @var Person|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Person", inversedBy="children")
     * @ORM\JoinColumn(name="gibbonPersonID",referencedColumnName="id",nullable=false)
     * @Assert\NotBlank()
     */
    private $person;

    /**
     * @var string|null
     * @ORM\Column(type="text",nullable=true)
     */
    private $comment;

    /**
     * @var Collection|FamilyRelationship[]
     * @ORM\OneToMany(targetEntity="Kookaburra\UserAdmin\Entity\FamilyRelationship",mappedBy="child",orphanRemoval=true)
     */
    private $relationships;

    /**
     * FamilyChild constructor.
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
     * @return FamilyChild
     */
    public function setId(?int $id): FamilyChild
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
     * @return FamilyChild
     */
    public function setFamily(?Family $family): FamilyChild
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
     * @return FamilyChild
     */
    public function setPerson(?Person $person): FamilyChild
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
     * @return FamilyChild
     */
    public function setComment(?string $comment): FamilyChild
    {
        $this->comment = $comment;
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
     * @return FamilyChild
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
     * @return array
     */
    public function toArray(?string $name = null): array
    {
        $person = $this->getPerson();

        return [
            'photo' => ImageHelper::getAbsoluteImageURL('File', $person->getImage240()),
            'fullName' => $person->formatName(['style' => 'long', 'preferredName' => false]),
            'status' => TranslationsHelper::translate($person->getStatus(), [], 'UserAdmin'),
            'roll' => StudentHelper::getCurrentRollGroup($person),
            'comment' => $this->getComment(),
            'family_id' => $this->getFamily()->getId(),
            'child_id' => $this->getId(),
        ];
    }

    /**
     * isEqualTo
     * @param FamilyAdult $adult
     * @return bool
     */
    public function isEqualTo(FamilyChild $child): bool
    {
        if($this->getPerson() === null || $child->getPerson() === null || $this->getFamily() === null || $child->getFamily() === null)
            return false;
        if (!$child->getPerson()->isEqualTo($this->getPerson()))
            return false;
        if (!$child->getFamily()->isEqualTo($this->getFamily()))
            return false;
        return true;
    }
}