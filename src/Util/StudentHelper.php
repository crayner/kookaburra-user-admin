<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 2/12/2019
 * Time: 16:28
 */

namespace Kookaburra\UserAdmin\Util;

use App\Provider\ProviderFactory;
use App\Util\SchoolYearHelper;
use Kookaburra\UserAdmin\Entity\Person;

/**
 * Class StudentHelper
 * @package Kookaburra\UserAdmin\Util
 */
class StudentHelper
{
    /**
     * @var array
     */
    private static $noteNotificationList = [
        'Tutors',
        'Tutors and Teachers',
    ];

    /**
     * @return array
     */
    public static function getNoteNotificationList(): array
    {
        return self::$noteNotificationList;
    }

    /**
     * getCurrentRollGroup
     * @param Person|int $person
     */
    public static function getCurrentRollGroup($person): string
    {
        if (is_int($person))
            $person = ProviderFactory::getRepository(Person::class)->find($person);
        if (!$person instanceof Person)
            return '';

        if (!UserHelper::isStudent($person))
            return '';

        $se = null;
        foreach($person->getStudentEnrolments() as $enrolment)
        {
            if ($enrolment->getSchoolYear()->getId() === SchoolYearHelper::getCurrentSchoolYear()->getId()) {
                $se = $enrolment;
                break;
            }
        }
        if ($se === null)
            return '';

        return $se->getRollGroup()->getName();
    }
}