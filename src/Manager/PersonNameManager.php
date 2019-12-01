<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 1/12/2019
 * Time: 08:40
 */

namespace Kookaburra\UserAdmin\Manager;

use App\Util\Format;
use Kookaburra\UserAdmin\Entity\Person;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PersonNameManager
 * @package Kookaburra\UserAdmin\Manager
 */
class PersonNameManager
{
    /**
     * @var array
     */
    private static $formats;

    /**
     * @return array
     */
    public static function getFormats(): array
    {
        return self::$formats;
    }

    /**
     * @param array $formats
     */
    public static function setFormats(array $formats = []): void
    {
        self::$formats = $formats;
    }

    /**
     * formatName
     * @param Person $person
     * @param array $options
     * @return string
     */
    public static function formatName(Person $person, array $options): string
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(
            [
                'preferredName' => true,
                'reverse' => false,
                'informal' => false,
                'initial' => false,
                'title' => false,
                'style' => null,
            ]
        );
        $resolver->setAllowedValues('style', ['long','short','formal',null]);

        $options = $resolver->resolve($options);

        $personType = $person->getPersonType();

        $template = 'title firstName surname';

        if ($options['style'] === null)
        {
            if ($options['reverse'])
                $template = 'surname, firstName';

            if (!$options['title'])
                $template = str_replace('title ', '', $template);

            if ($options['informal'] || $options['preferredName'])
                $template = str_replace('firstName', 'preferredName', $template);

            if ($options['informal'] )
                $template = str_replace('title', '', $template);

            if ($options['initial'])
                $template = str_replace(['firstName', 'preferredName'], 'initial', $template);
        } else {
            $styles = self::getFormatByPersonType($personType);
            $template = 'formal';
            $length = 'long';
            if ($options['initial']) {
                $template = 'first';
                $length = 'short';
            }
            $direction = 'normal';
            if ($options['reverse']) {
                $direction = 'reversed';
                $template = 'first';
            }
            if ($options['informal'] || $options['preferredName'])
                $template = 'preferred';

            if ($template === 'formal')
                $template = $styles['formal'];
            else
                $template = $styles[$template][$length][$direction];
        }

        $template = trim($template);

        $name = trim(str_replace(
            ['first','surname','preferred','title','initial'],
            [$person->getFirstName(),$person->getSurname(), $person->getPreferredName(),$person->getTitle(),substr($person->getFirstName(), 0,1)],
            $template));
        return $name;
    }

    /**
     * getFormatByPersonType
     * @param string $personType
     * @return array
     */
    private static function getFormatByPersonType(string $personType): array
    {
        return self::getFormats()[strtolower($personType)];
    }
}