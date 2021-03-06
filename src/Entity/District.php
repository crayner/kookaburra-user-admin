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
use App\Provider\ProviderFactory;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class District
 * @package Kookaburra\UserAdmin\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\UserAdmin\Repository\DistrictRepository")
 * @ORM\Table(options={"auto_increment": 1}, name="District",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="locality",columns={"name","territory","post_code"})}
 * )
 * @UniqueEntity({"name","territory","postCode"})
 */
class District implements EntityInterface
{
    /**
     * @var integer|null
     * @ORM\Id()
     * @ORM\Column(type="integer", columnDefinition="INT(6) UNSIGNED")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(length=30)
     * @Assert\NotBlank()
     * @Assert\Length(max=30)
     */
    private $name;

    /**
     * @var string|null
     * @ORM\Column(length=30, nullable=true)
     * @Assert\Length(max=30)
     */
    private $territory;

    /**
     * @var string|null
     * @ORM\Column(length=10, nullable=true, name="post_code")
     * @Assert\Length(max=10)
     */
    private $postCode;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return District
     */
    public function setId(?int $id): District
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
     * @return District
     */
    public function setName(?string $name): District
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTerritory(): ?string
    {
        return $this->territory;
    }

    /**
     * Territory.
     *
     * @param string|null $territory
     * @return District
     */
    public function setTerritory(?string $territory): District
    {
        $this->territory = $territory;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    /**
     * PostalCode.
     *
     * @param string|null $postalCode
     * @return District
     */
    public function setPostCode(?string $postCode): District
    {
        $this->postCode = $postCode;
        return $this;
    }

    /**
     * toArray
     * @param string|null $name
     * @return array
     */
    public function toArray(?string $name = null): array
    {
        return [
            'name' => $this->getName(),
            'id' => $this->getId(),
            'territory' => $this->getTerritory(),
            'postCode' => $this->getPostCode(),
            'canDelete' => ProviderFactory::create(District::class)->canDelete($this) === 0,
        ];
    }

    /**
     * getFullName
     * @return string
     */
    public function getFullName(): string
    {
        return trim($this->getName().' '.$this->getTerritory().' '.$this->getPostCode());
    }
}