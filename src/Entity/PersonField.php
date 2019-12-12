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
use Doctrine\ORM\Mapping as ORM;
use Kookaburra\UserAdmin\Validator as Validate;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PersonField
 * @package Kookaburra\UserAdmin\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\UserAdmin\Repository\PersonFieldRepository")
 * @ORM\Table(options={"auto_increment": 1}, name="PersonField")
 * @Validate\PersonFieldOptions()
 */
class PersonField implements EntityInterface
{
    use BooleanList;

    /**
     * @var integer|null
     * @ORM\Id
     * @ORM\Column(type="smallint", name="gibbonPersonFieldID", columnDefinition="INT(3) UNSIGNED ZEROFILL AUTO_INCREMENT")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(length=50)
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    private $name;

    /**
     * @var string|null
     * @ORM\Column(length=1, options={"default": "Y"})
     * @Assert\Choice(callback="getBooleanList")
     */
    private $active = 'Y';

    /**
     * @var string|null
     * @ORM\Column()
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $description;

    /**
     * @var string|null
     * @ORM\Column(length=10, name="type")
     * @Assert\Choice(callback="getTypeList")
     */
    private $type;

    /**
     * @var array
     */
    private static $typeList = ['varchar','text','date','url','select'];

    /**
     * @var string|null
     * @ORM\Column(type="text", options={"comment": "Field length for varchar, rows for text, comma-separate list for select/checkbox."}, nullable=true)
     */
    private $options;

    /**
     * @var string|null
     * @ORM\Column(length=1, options={"default": "N"})
     * @Assert\Choice(callback="getBooleanList")
     */
    private $required = 'N';

    /**
     * @var boolean|null
     * @ORM\Column(type="boolean", name="activePersonStudent", options={"default": "0"})
     */
    private $activePersonStudent = false;

    /**
     * @var boolean|null
     * @ORM\Column(type="boolean", name="activePersonStaff", options={"default": "0"})
     */
    private $activePersonStaff = false;

    /**
     * @var boolean|null
     * @ORM\Column(type="boolean", name="activePersonParent", options={"default": "0"})
     */
    private $activePersonParent = false;

    /**
     * @var boolean|null
     * @ORM\Column(type="boolean", name="activePersonOther", options={"default": "0"})
     */
    private $activePersonOther = false;

    /**
     * @var boolean|null
     * @ORM\Column(type="boolean", name="activeApplicationForm", options={"default": "0"})
     */
    private $activeApplicationForm = false;

    /**
     * @var boolean|null
     * @ORM\Column(type="boolean", name="activeDataUpdater", options={"default": "0"})
     */
    private $activeDataUpdater = false;

    /**
     * @var boolean|null
     * @ORM\Column(type="boolean", name="activePublicRegistration", options={"default": "0"})
     */
    private $activePublicRegistration = false;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return PersonField
     */
    public function setId(?int $id): PersonField
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
     * @return PersonField
     */
    public function setName(?string $name): PersonField
    {
        $this->name = $name;
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
     * @return PersonField
     */
    public function setActive(?string $active): PersonField
    {
        $this->active = self::checkBoolean($active);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return PersonField
     */
    public function setDescription(?string $description): PersonField
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return PersonField
     */
    public function setType(?string $type): PersonField
    {
        $this->type = in_array($type, self::getTypeList()) ? $type : null;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOptions(): ?string
    {
        return $this->options;
    }

    /**
     * @param string|null $options
     * @return PersonField
     */
    public function setOptions(?string $options): PersonField
    {
        $this->options = $options;
        return $this;
    }

    /**
     * isRequired
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->getRequired() === 'Y' ? true : false;
    }

    /**
     * @return string|null
     */
    public function getRequired(): ?string
    {
        return $this->required = self::checkBoolean($this->required, 'N');
    }

    /**
     * @param string|null $required
     * @return PersonField
     */
    public function setRequired(?string $required): PersonField
    {
        $this->required = self::checkBoolean($required, 'N');
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActivePersonStudent(): ?bool
    {
        return $this->activePersonStudent;
    }

    /**
     * @param bool|null $activePersonStudent
     * @return PersonField
     */
    public function setActivePersonStudent(?bool $activePersonStudent): PersonField
    {
        $this->activePersonStudent = $activePersonStudent;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActivePersonStaff(): ?bool
    {
        return $this->activePersonStaff;
    }

    /**
     * @param bool|null $activePersonStaff
     * @return PersonField
     */
    public function setActivePersonStaff(?bool $activePersonStaff): PersonField
    {
        $this->activePersonStaff = $activePersonStaff;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActivePersonParent(): ?bool
    {
        return $this->activePersonParent;
    }

    /**
     * @param bool|null $activePersonParent
     * @return PersonField
     */
    public function setActivePersonParent(?bool $activePersonParent): PersonField
    {
        $this->activePersonParent = $activePersonParent;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActivePersonOther(): ?bool
    {
        return $this->activePersonOther;
    }

    /**
     * @param bool|null $activePersonOther
     * @return PersonField
     */
    public function setActivePersonOther(?bool $activePersonOther): PersonField
    {
        $this->activePersonOther = $activePersonOther;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActiveApplicationForm(): ?bool
    {
        return $this->activeApplicationForm;
    }

    /**
     * @param bool|null $activeApplicationForm
     * @return PersonField
     */
    public function setActiveApplicationForm(?bool $activeApplicationForm): PersonField
    {
        $this->activeApplicationForm = $activeApplicationForm;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActiveDataUpdater(): ?bool
    {
        return $this->activeDataUpdater;
    }

    /**
     * @param bool|null $activeDataUpdater
     * @return PersonField
     */
    public function setActiveDataUpdater(?bool $activeDataUpdater): PersonField
    {
        $this->activeDataUpdater = $activeDataUpdater;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActivePublicRegistration(): ?bool
    {
        return $this->activePublicRegistration;
    }

    /**
     * @param bool|null $activePublicRegistration
     * @return PersonField
     */
    public function setActivePublicRegistration(?bool $activePublicRegistration): PersonField
    {
        $this->activePublicRegistration = $activePublicRegistration;
        return $this;
    }

    /**
     * @return array
     */
    public static function getTypeList(): array
    {
        return self::$typeList;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Value.
     *
     * @param mixed $value
     * @return PersonField
     */
    public function setValue($value)
    {
        $this->value = $value;
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
            'type' => TranslationsHelper::translate('personfield.type.'.$this->getType(), [], 'UserAdmin'),
            'active' => TranslationsHelper::translate($this->getActive() === 'Y' ? 'Yes' : 'No', [], 'messages'),
            'isActive' => $this->getActive() === 'Y',
            'categories' => $this->getCategoryNames(),
            'student' => $this->isActivePersonStudent(),
            'parent' => $this->isActivePersonParent(),
            'staff' => $this->isActivePersonStaff(),
            'other' => $this->isActivePersonOther(),
        ];
    }

    /**
     * getCategoryNames
     * @return string
     */
    public function getCategoryNames(): string
    {
        $result = '';
        $result .= $this->isActivePersonStudent() ? TranslationsHelper::translate('Student', [], 'UserAdmin') . "\n<br/>" : '';
        $result .= $this->isActivePersonStaff() ? TranslationsHelper::translate('Staff', [], 'UserAdmin') . "\n<br/>" : '';
        $result .= $this->isActivePersonParent() ? TranslationsHelper::translate('Parent', [], 'UserAdmin') . "\n<br/>" : '';
        $result .= $this->isActivePersonOther() ? TranslationsHelper::translate('Other', [], 'UserAdmin') . "\n<br/>" : '';
        return $result;
    }
}