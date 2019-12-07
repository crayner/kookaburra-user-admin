<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 5/12/2018
 * Time: 16:22
 */

namespace Kookaburra\UserAdmin\Entity;

use App\Manager\EntityInterface;
use App\Manager\Traits\BooleanList;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class StudentNoteCategory
 * @package App\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\UserAdmin\Repository\StudentNoteCategoryRepository")
 * @ORM\Table(options={"auto_increment": 1}, name="StudentNoteCategory", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @UniqueEntity(fields={"name"})
 */
class StudentNoteCategory implements EntityInterface
{
    use BooleanList;

    /**
     * @var integer|null
     * @ORM\Id
     * @ORM\Column(type="smallint", name="gibbonStudentNoteCategoryID", columnDefinition="INT(5) UNSIGNED ZEROFILL AUTO_INCREMENT")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(length=30)
     */
    private $name;

    /**
     * @var string|null
     * @ORM\Column(type="text",nullable=true)
     */
    private $template;

    /**
     * @var string|null
     * @ORM\Column(length=1, options={"default": "Y"})
     */
    private $active = 'Y';

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return StudentNoteCategory
     */
    public function setId(?int $id): StudentNoteCategory
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
     * @return StudentNoteCategory
     */
    public function setName(?string $name): StudentNoteCategory
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * @param string|null $template
     * @return StudentNoteCategory
     */
    public function setTemplate(?string $template): StudentNoteCategory
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getActive(): ?string
    {
        return $this->active;
    }

    /**
     * @param string|null $active
     * @return StudentNoteCategory
     */
    public function setActive(?string $active): StudentNoteCategory
    {
        $this->active = self::checkBoolean($active);
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
            'active' => $this->getActive() === 'Y' ? 'Yes' : 'No',
            'id' => $this->getId(),
        ];
    }
}