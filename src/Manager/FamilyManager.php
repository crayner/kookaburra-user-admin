<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 10/12/2019
 * Time: 08:47
 */

namespace Kookaburra\UserAdmin\Manager;

use App\Provider\ProviderFactory;
use App\Util\ImageHelper;
use App\Util\TranslationsHelper;
use Kookaburra\UserAdmin\Entity\Family;
use Kookaburra\UserAdmin\Entity\FamilyAdult;
use Kookaburra\UserAdmin\Entity\FamilyChild;
use Kookaburra\UserAdmin\Form\Entity\ManageSearch;
use Kookaburra\UserAdmin\Util\StudentHelper;

/**
 * Class FamilyManager
 * @package Kookaburra\UserAdmin\Manager
 */
class FamilyManager
{
    /**
     * findBySearch
     * @param ManageSearch $search
     * @return array
     */
    public function findBySearch(ManageSearch $search): array
    {
        $result = ProviderFactory::getRepository(Family::class)->findBySearch($search);

        foreach($result as $q=>$family)
        {
            $family['adults'] = self::getAdultNames($family['id']);
            $family['children'] = self::getChildrenNames($family['id']);
            $result[$q] = $family;
        }
        return $result;
    }

    /**
     * getAdultNames
     * @param Family $family
     * @return string
     */
    public function getAdultNames($family): string
    {
        $result = '';
        foreach (self::getAdults($family, true) as $adult) {
            $adult['personType'] = 'Parent';
            $result .= PersonNameManager::formatName($adult, ['style' => 'formal']) . "\n<br />";
        }
        return $result;
    }

    /**
     * getChildrenNames
     * @param Family $family
     * @return string
     */
    public function getChildrenNames($family): string
    {
        $result = '';

        foreach (self::getChildren($family, true) as $student) {
            $adult['personType'] = 'Student';
            $result .= PersonNameManager::formatName($student, ['style' => 'formal']) . "\n<br />";
        }
        return $result;
    }

    /**
     * getAdults
     * @param Family $family
     * @return array
     */
    public static function getAdults($family, bool $asArray = false): array
    {
        $result = ProviderFactory::getRepository(FamilyAdult::class)->findByFamily($family, $asArray);
        if ($asArray) {
            foreach($result as $q=>$adult) {
                $adult['personType'] = 'Parent';
                $adult['fullName'] = PersonNameManager::formatName($adult, ['style' => 'formal']);
                $adult['status'] = TranslationsHelper::translate($adult['status'], [], 'UserAdmin');
                $result[$q] = $adult;
            }
        }
        return $result;
    }

    /**
     * getAdults
     * @param Family $family
     * @return array
     */
    public static function getChildren($family, bool $asArray = false): array
    {
        $result = ProviderFactory::getRepository(FamilyChild::class)->findByFamily($family, $asArray);
        if ($asArray) {
            foreach($result as $q=>$child) {
                $child['personType'] = 'Student';
                $child['fullName'] = PersonNameManager::formatName($child, ['style' => 'long', 'preferredName' => false]);
                if ($family instanceof Family)
                    $child['roll'] = StudentHelper::getCurrentRollGroup($child['person']);
                $child['status'] = TranslationsHelper::translate($child['status'], [], 'UserAdmin');
                $child['photo'] = ImageHelper::getAbsoluteImageURL('File', $child['photo'] ?: '/build/static/DefaultPerson.png');

                $result[$q] = $child;
            }
        }
        return $result;
    }
}