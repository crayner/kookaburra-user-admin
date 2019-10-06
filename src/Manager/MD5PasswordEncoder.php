<?php
/**
 * Created by PhpStorm.
 *
 * Gibbon-Responsive
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * UserProvider: craig
 * Date: 23/11/2018
 * Time: 15:27
 */
namespace Kookaburra\UserAdmin\Manager;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

/**
 * Class MD5PasswordEncoder
 * @package Kookaburra\UserAdmin\Manager
 */
class MD5PasswordEncoder extends BasePasswordEncoder
{
    /**
     * Encodes the raw password.
     *
     * @param string $raw  The password to encode
     * @param string $salt The salt
     *
     * @return string The encoded password
     */
    public function encodePassword($raw, $salt): string
    {
        return md5($raw);
    }

    /**
     * Checks a raw password against an encoded password.
     *
     * @param string $encoded An encoded password
     * @param string $raw     A raw password
     * @param string $salt    The salt
     *
     * @return bool true if the password is valid, false otherwise
     */
    public function isPasswordValid($encoded, $raw, $salt): bool
    {
        if ($encoded === $this->encodePassword($raw, $salt))
            return true;
        return false;
    }
}
