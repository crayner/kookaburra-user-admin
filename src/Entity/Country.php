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

/**
 * Class Country
 * @package App\Entity
 * @ORM\Entity(repositoryClass="Kookaburra\UserAdmin\Repository\CountryRepository")
 * @ORM\Table(options={"auto_increment": 1}, name="Country")
 */
class Country implements EntityInterface
{
    /**
     * @var integer|null
     * @ORM\Id()
     * @ORM\Column(type="integer", columnDefinition="INT(4) UNSIGNED")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(length=80,unique=true)
     */
    private $printable_name;

    /**
     * @var string|null
     * @ORM\Column(length=7, name="iddCountryCode")
     */
    private $iddCountryCode;
    
    /**
     * @var array
     */
    private static $codeList;

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
     * @return Country
     */
    public function setId(?int $id): Country
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrintableName(): ?string
    {
        return $this->printable_name;
    }

    /**
     * PrintableName.
     *
     * @param string|null $printable_name
     * @return Country
     */
    public function setPrintableName(?string $printable_name): Country
    {
        $this->printable_name = $printable_name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIddCountryCode(): ?string
    {
        return $this->iddCountryCode;
    }

    /**
     * IddCountryCode.
     *
     * @param string|null $iddCountryCode
     * @return Country
     */
    public function setIddCountryCode(?string $iddCountryCode): Country
    {
        $this->iddCountryCode = $iddCountryCode;
        return $this;
    }

    /**
     * getCountryCodeList
     * @return array
     */
    public static function getCountryCodeList(): array
    {
        if (null !== self::$codeList)
            return self::$codeList;
        self::$codeList = [];
        foreach(ProviderFactory::getRepository(Country::class)->getCountryCodeList() as $code)
            self::$codeList[$code->getPrintableName() . ' ('.$code->getIddCountryCode().')'] = $code->getIddCountryCode();

        return self::$codeList;
    }

    /**
     * nameWithCode
     * @return string
     */
    public function nameWithCode(): string
    {
        return $this->getPrintableName() . ' (' . $this->getIddCountryCode() . ')';
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