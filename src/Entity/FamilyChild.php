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
use Doctrine\ORM\Mapping as ORM;
use Kookaburra\UserAdmin\Util\StudentHelper;

/**
 * Class FamilyChild
 * @package Kookaburra\UserAdmin\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\UserAdmin\Repository\FamilyChildRepository")
 * @ORM\Table(options={"auto_increment": 1}, name="FamilyChild", indexes={@ORM\Index(name="gibbonFamilyIndex", columns={"gibbonFamilyID"}),@ORM\Index(name="gibbonPersonIndex", columns={"gibbonPersonID"})})
 */
class FamilyChild implements EntityInterface
{
    /**
     * @var integer|null
     * @ORM\Id()
     * @ORM\Column(type="integer", name="gibbonFamilyChildID", columnDefinition="INT(8) UNSIGNED ZEROFILL AUTO_INCREMENT")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Family|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Family", inversedBy="children")
     * @ORM\JoinColumn(name="gibbonFamilyID", referencedColumnName="gibbonFamilyID", nullable=false)
     */
    private $family;

    /**
     * @var Person|null
     * @ORM\ManyToOne(targetEntity="Kookaburra\UserAdmin\Entity\Person", inversedBy="children")
     * @ORM\JoinColumn(name="gibbonPersonID", referencedColumnName="gibbonPersonID", nullable=false)
     */
    private $person;

    /**
     * @var string|null
     * @ORM\Column(type="text",nullable=true)
     */
    private $comment;

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
            'child_id' => $person->getId(),
        ];
    }
}