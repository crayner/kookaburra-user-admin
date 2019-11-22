<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 22/11/2019
 * Time: 13:39
 */

namespace Kookaburra\UserAdmin\Form\Entity;

/**
 * Class ManageSearch
 * @package Kookaburra\UserAdmin\Form\Entity
 */
class ManageSearch
{
    /**
     * @var string
     */
    private $search = '';

    /**
     * @var string
     */
    private $filter = '';

    /**
     * @return string
     */
    public function getSearch(): string
    {
        return $this->search;
    }

    /**
     * Search.
     *
     * @param string $search
     * @return ManageSearch
     */
    public function setSearch(?string $search): ManageSearch
    {
        $this->search = $search ?: '';
        return $this;
    }

    /**
     * @return string
     */
    public function getFilter(): string
    {
        return $this->filter;
    }

    /**
     * Filter.
     *
     * @param string $filter
     * @return ManageSearch
     */
    public function setFilter(?string $filter): ManageSearch
    {
        $this->filter = $filter ?: '';
        return $this;
    }
}