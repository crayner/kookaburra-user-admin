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
}