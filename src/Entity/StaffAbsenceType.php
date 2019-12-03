<?php
/**
 * Created by PhpStorm.
 *
 * bilby
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 24/06/2019
 * Time: 15:30
 */

namespace Kookaburra\UserAdmin\Entity;

use App\Manager\EntityInterface;
use App\Manager\Traits\BooleanList;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class StaffAbsenceType
 * @package App\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\UserAdmin\Repository\StaffAbsenceTypeRepository")
 * @ORM\Table(options={"auto_increment": 1}, name="StaffAbsenceType", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"}),@ORM\UniqueConstraint(name="name_short", columns={"nameShort"})})
 * @UniqueEntity(fields={"name"})
 * @UniqueEntity(fields={"nameShort"})
 * @UniqueEntity(fields={"sequenceNumber"})
 */
class StaffAbsenceType implements EntityInterface
{
    use BooleanList;

    /**
     * @var integer|null
     * @ORM\Id
     * @ORM\Column(type="integer", name="gibbonStaffAbsenceTypeID", columnDefinition="INT(6) UNSIGNED ZEROFILL AUTO_INCREMENT")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(length=60,unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=60)
     */
    private $name;

    /**
     * @var string|null
     * @ORM\Column(length=10,unique=true,name="nameShort")
     * @Assert\NotBlank()
     * @Assert\Length(max=10)
     */
    private $nameShort;

    /**
     * @var string|null
     * @ORM\Column(length=1, options={"default": "Y"})
     */
    private $active = 'Y';

    /**
     * @var string|null
     * @ORM\Column(length=1, options={"default": "N"}, name="requiresApproval")
     */
    private $requiresApproval = 'N';

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $reasons;

    /**
     * @var integer
     * @ORM\Column(type="smallint", columnDefinition="INT(3)", name="sequenceNumber", options={"default": 0})
     * @Assert\NotBlank()
     */
    private $sequenceNumber;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Id.
     *
     * @param int|null $id
     * @return StaffAbsenceType
     */
    public function setId(?int $id): StaffAbsenceType
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
     * Name.
     *
     * @param string|null $name
     * @return StaffAbsenceType
     */
    public function setName(?string $name): StaffAbsenceType
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNameShort(): ?string
    {
        return $this->nameShort;
    }

    /**
     * NameShort.
     *
     * @param string|null $nameShort
     * @return StaffAbsenceType
     */
    public function setNameShort(?string $nameShort): StaffAbsenceType
    {
        $this->nameShort = $nameShort;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getActive(): ?string
    {
        return self::checkBoolean($this->active);
    }

    /**
     * Active.
     *
     * @param string|null $active
     * @return StaffAbsenceType
     */
    public function setActive(?string $active): StaffAbsenceType
    {
        $this->active = self::checkBoolean($active);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRequiresApproval(): ?string
    {
        return self::checkBoolean($this->requiresApproval, 'N');
    }

    /**
     * RequiresApproval.
     *
     * @param string|null $requiresApproval
     * @return StaffAbsenceType
     */
    public function setRequiresApproval(?string $requiresApproval): StaffAbsenceType
    {
        $this->requiresApproval = self::checkBoolean($requiresApproval, 'N');
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReasons(): ?string
    {
        return $this->reasons;
    }

    /**
     * Reasons.
     *
     * @param string|null $reasons
     * @return StaffAbsenceType
     */
    public function setReasons(?string $reasons): StaffAbsenceType
    {
        $this->reasons = $reasons;
        return $this;
    }

    /**
     * @return int
     */
    public function getSequenceNumber(): int
    {
        return intval($this->sequenceNumber);
    }

    /**
     * SequenceNumber.
     *
     * @param int $sequenceNumber
     * @return StaffAbsenceType
     */
    public function setSequenceNumber(int $sequenceNumber): StaffAbsenceType
    {
        $this->sequenceNumber = intval($sequenceNumber);
        return $this;
    }

    /**
     * toArray
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'nameShort' => $this->getNameShort(),
            'reasons' => $this->getReasons(),
            'requiresApproval' => $this->getRequiresApproval() === 'Y' ? 'Yes' : 'No',
            'active' => $this->getActive() === 'Y' ? 'Yes' : 'No',
        ];
    }
}