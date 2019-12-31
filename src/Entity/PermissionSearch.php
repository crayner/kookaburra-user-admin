<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 4/12/2019
 * Time: 14:45
 */

namespace Kookaburra\UserAdmin\Entity;

use Kookaburra\SystemAdmin\Entity\Module;
use Kookaburra\SystemAdmin\Entity\Role;

class PermissionSearch
{
    /**
     * @var Module|null
     */
    private $module;

    /**
     * @var null|Role
     */
    private $role;

    /**
     * @return Module|null
     */
    public function getModule(): ?Module
    {
        return $this->module;
    }

    /**
     * Module.
     *
     * @param Module|null $module
     * @return PermissionSearch
     */
    public function setModule(?Module $module): PermissionSearch
    {
        $this->module = $module instanceof Module ? $module : null;
        return $this;
    }

    /**
     * @return Role|null
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }

    /**
     * Role.
     *
     * @param Role|null $role
     * @return PermissionSearch
     */
    public function setRole(?Role $role): PermissionSearch
    {
        $this->role = $role instanceof Role ? $role : null;
        return $this;
    }
}