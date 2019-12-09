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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Family
 * @package Kookaburra\UserAdmin\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\UserAdmin\Repository\FamilyRepository")
 * @ORM\Table(options={"auto_increment": 1}, name="Family", uniqueConstraints={@ORM\UniqueConstraint(name="name",columns={"name"})})
 * @ORM\HasLifecycleCallbacks()
 */
class Family implements EntityInterface
{
    /**
     * @var integer|null
     * @ORM\Id
     * @ORM\Column(type="integer", name="gibbonFamilyID", columnDefinition="INT(7) UNSIGNED ZEROFILL AUTO_INCREMENT")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(length=100, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    private $name;

    /**
     * @var string|null
     * @ORM\Column(length=100, name="nameAddress", options={"comment": "The formal name to be used for addressing the family (e.g. Mr. & Mrs. Smith)"})
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
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
     * @Assert\Country()
     */
    private $homeAddressCountry;

    /**
     * @var string|null
     * @ORM\Column(length=12)
     * @Assert\Choice({"Married","Separated","Divorced","De Facto","Other"})
     */
    private $status = 'Unknown';

    /**
     * @var array
     */
    private static $statusList = ['Married', 'Separated', 'Divorced', 'De Facto', 'Other'];

    /**
     * @var string|null
     * @ORM\Column(length=30, name="languageHomePrimary")
     * @Assert\Language()
     * @Assert\NotBlank()
     */
    private $languageHomePrimary;

    /**
     * @var string|null
     * @ORM\Column(length=30, name="languageHomeSecondary", nullable=true)
     * @Assert\Language()
     */
    private $languageHomeSecondary;

    /**
     * @var string|null
     * @ORM\Column(length=50, name="familySync", nullable=true, unique=true)
     */
    private $familySync;

    /**
     * @var Collection|null
     * @ORM\OneToMany(mappedBy="family", targetEntity="Kookaburra\UserAdmin\Entity\FamilyAdult")
     * @ORM\OrderBy({"contactPriority" = "ASC"})
     */
    private $adults;

    /**
     * @var Collection|null
     * @ORM\OneToMany(mappedBy="family", targetEntity="Kookaburra\UserAdmin\Entity\FamilyChild")
     */
    private $children;

    /**
     * @var Collection|null
     * @ORM\OneToMany(mappedBy="family", targetEntity="Kookaburra\UserAdmin\Entity\FamilyRelationship")
     */
    private $relationships;

    /**
     * Family constructor.
     */
    public function __construct()
    {
        $this->adults = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * __toString
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName() ?: '';
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
     * @return Family
     */
    public function setId(?int $id): Family
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Family
     */
    public function setName(?string $name): Family
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNameAddress(): ?string
    {
        return $this->nameAddress ?: trim(str_replace('<br />', ' & ', $this->getAdultNames()), ' &');
    }

    /**
     * @param string|null $nameAddress
     * @return Family
     */
    public function setNameAddress(?string $nameAddress): Family
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
     * @return Family
     */
    public function setHomeAddress(?string $homeAddress): Family
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
     * @return Family
     */
    public function setHomeAddressDistrict(?string $homeAddressDistrict): Family
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
     * @return Family
     */
    public function setHomeAddressCountry(?string $homeAddressCountry): Family
    {
        $this->homeAddressCountry = $homeAddressCountry;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return Family
     */
    public function setStatus(?string $status): Family
    {
        $this->status = in_array($status, self::getStatusList()) ? $status : 'Unknown';
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
     * @return Family
     */
    public function setLanguageHomePrimary(?string $languageHomePrimary): Family
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
     * @return Family
     */
    public function setLanguageHomeSecondary(?string $languageHomeSecondary): Family
    {
        $this->languageHomeSecondary = $languageHomeSecondary;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFamilySync(): ?string
    {
        return $this->familySync;
    }

    /**
     * @param string|null $familySync
     * @return Family
     */
    public function setFamilySync(?string $familySync): Family
    {
        $this->familySync = $familySync;
        return $this;
    }

    /**
     * @return array
     */
    public static function getStatusList(): array
    {
        return self::$statusList;
    }

    /**
     * @var bool
     */
    private $adultsSorted = false;

    /**
     * getAdults
     * @return Collection|null
     */
    public function getAdults(): ?Collection
    {
        $this->adults = $this->adults ?: new ArrayCollection();

        if ($this->adults instanceof PersistentCollection)
            $this->adults->initialize();
        
        return $this->sortAdults()->adults;
    }

    /**
     * @param Collection|null $adults
     * @return Family
     */
    public function setAdults(?Collection $adults): Family
    {
        $this->adults = $adults;
        return $this;
    }

    /**
     * addAdult
     * @param FamilyAdult $adult
     * @return Family
     */
    public function addAdult(FamilyAdult $adult): Family
    {
        if ($this->getAdults()->contains($adult))
            return $this;

        $adult->setFamily($this);
        $this->adults->add($adult);
        return $this->sortAdults(true);
    }

    /**
     * removeAdult
     * @param FamilyAdult $adult
     * @return Family
     */
    public function removeAdult(FamilyAdult $adult): Family
    {
        $this->getAdults()->removeElement($adult);

        return $this->sortAdults(true);
    }

    /**
     * sortAdults
     * @return Family
     */
    public function sortAdults(bool $refresh = false): Family
    {
        if ($this->adultsSorted && !$refresh)
            return $this;

        $iterator = $this->adults->getIterator();
        $iterator->uasort(
            function ($a, $b) {
                return $a->getContactPriority() < $b->getContactPriority() ? -1 : 1;
            }
        );
        $this->adults = new ArrayCollection(iterator_to_array($iterator, false));
        $this->adultsSorted = true;
        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getChildren(): ?Collection
    {
        $this->children = $this->children ?: new ArrayCollection();

        if ($this->children instanceof PersistentCollection)
            $this->children->initialize();

        $this->sortChildren();
        return $this->children;
    }

    /**
     * @param Collection|null $children
     * @return Family
     */
    public function setChildren(?Collection $children): Family
    {
        $this->children = $children;
        return $this;
    }

    /**
     * addChild
     * @param FamilyChild $child
     * @return Family
     */
    public function addChild(FamilyChild $child): Family
    {
        if ($this->getChildren()->contains($child))
            return $this;

        $child->setFamily($this);
        $this->children->add($child);
        return $this->sortChildren();
    }

    /**
     * sortChildren
     * @return Family
     * @throws \Exception
     */
    public function sortChildren(): Family
    {
        $iterator = $this->children->getIterator();
        $iterator->uasort(
            function ($a, $b) {
                return ($a->getPerson()->formatName(['reverse' => true]) < $b->getPerson()->formatName(['reverse' => true])) ? -1 : 1;
            }
        );
        $this->children = new ArrayCollection(iterator_to_array($iterator, false));
        return $this;
    }

    /**
     * toArray
     * @param string|null $name
     * @return array
     */
    public function toArray(?string $name = NULL): array
    {
        return [
            'name' => $this->getName(),
            'status' => $this->getStatus(),
            'adults' => $this->getAdultNames(),
            'children' => $this->getChildrenNames(),
        ];
    }

    /**
     * getAdultNames
     * @return string
     */
    public function getAdultNames(): string
    {
        $result = '';
        foreach ($this->getAdults() as $adult)
            $result .= $adult->getPerson()->formatName(['style' => 'formal']) . "\n<br />";

        return $result;
    }

    /**
     * getChildrenNames
     * @return string
     */
    public function getChildrenNames(): string
    {
        $result = '';
        foreach ($this->getChildren() as $adult)
            $result .= $adult->getPerson()->formatName(['style' => 'formal']) . "\n<br />";

        return $result;
    }

    /**
     * @var bool
     */
    private $relationshipsSorted = false;

    /**
     * getRelationships
     * @return Collection
     */
    public function getRelationships(): Collection
    {
        $this->relationships = $this->relationships ?: new ArrayCollection();

        if ($this->relationships instanceof PersistentCollection)
            $this->relationships->initialize();

        $this->sortRelationships();
        return $this->relationships;
    }

    /**
     * Relationships.
     *
     * @param Collection|FamilyRelationship[]|null $relationships
     * @return Family
     */
    public function setRelationships(?Collection $relationships): Family
    {
        $this->relationships = $relationships;
        return $this;
    }

    /**
     * addRelationship
     * @param FamilyRelationship $relationship
     * @return Family
     */
    public function addRelationship(FamilyRelationship $relationship): Family
    {
        if ($this->getRelationships()->contains($relationship))
            return $this;

        foreach($this->relationships as $item)
            if ($relationship->isEqualTo($item))
                return $this;

        $relationship->setFamily($this);

        $this->relationships->add($relationship);

        return $this->sortRelationships();
    }

    /**
     * addRelationship
     * @param FamilyRelationship $relationship
     * @return Family
     */
    public function removeRelationship(FamilyRelationship $relationship): Family
    {
        if ($this->getRelationships()->contains($relationship))
            return $this;

        $relationship->setFamily($this);
        $this->relationships->add($relationship);
        return $this->sortRelationships();
    }

    /**
     * sortRelationships
     * @return Family
     */
    public function sortRelationships(): Family
    {
        try {
            $iterator = $this->relationships->getIterator();
        }  catch (\Exception $e) {
            return $this;
        }
        $iterator->uasort(
            function ($a, $b) {
                return ($a->getAdult()->formatName(['preferredName' => false, 'reverse' => true]) . $a->getChild()->formatName(['reverse' => true]) < $b->getAdult()->formatName(['preferredName' => false, 'reverse' => true]) . $b->getChild()->formatName(['reverse' => true])) ? -1 : 1;
            }
        );

        $this->relationships = new ArrayCollection(iterator_to_array($iterator, false));
        return $this;
    }

    /**
     * isEqualTo
     * @param Family $family
     * @return bool
     */
    public function isEqualTo(Family $family)
    {
        if ($this->getName() !== $family->getName())
            return false;
        if ($this->getAdults()->count() !== $family->getAdults()->count())
            return false;
        if ($this->getChildren()->count() !== $family->getChildren()->count())
            return false;
        if ($this->getAdultNames() !== $family->getAdultNames())
            return false;
        if ($this->getChildrenNames() !== $family->getChildrenNames())
            return false;

        return true;
    }


    /**
     * hasRelationshipsNumbers
     * @return bool
     */
    public function hasRelationshipsNumbers(): bool
    {
        return $this->getRelationships()->count() === $this->getAdults()->count() * $this->getChildren()->count();
    }
}