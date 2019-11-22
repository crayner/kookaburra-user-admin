<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 22/11/2019
 * Time: 12:16
 */

namespace Kookaburra\UserAdmin\Pagination;

use App\Manager\Entity\PaginationAction;
use App\Manager\Entity\PaginationColumn;
use App\Manager\Entity\PaginationRow;
use App\Manager\ReactPaginationInterface;
use App\Manager\ReactPaginationManager;

/**
 * Class ManagePagination
 * @package Kookaburra\UserAdmin\Pagination
 */
class ManagePagination extends ReactPaginationManager
{
    public function execute(): ReactPaginationInterface
    {
        $row = new PaginationRow();

        $column = new PaginationColumn();
        $column->setLabel('Photo')
            ->setContentKey('photo')
            ->setContentType('image')
            ->setClass('column relative pr-4 cursor-pointer widthAuto text-centre')
            ->setOptions(['class' => 'max75 user'])
        ;
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Name')
            ->setContentKey(['fullName'])
            ->setSort(true)
            ->setClass('column relative pr-4 cursor-pointer widthAuto');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Status')
            ->setContentKey(['status'])
            ->setSort(false)
            ->setClass('column relative pr-4 cursor-pointer widthAuto');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Family')
            ->setContentKey(['family'])
            ->setSort(false)
            ->setClass('column hidden sm:table-cell relative pr-4 cursor-pointer widthAuto');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Username')
            ->setContentKey(['username'])
            ->setSort(false)
            ->setClass('column hidden sm:table-cell relative pr-4 cursor-pointer widthAuto');
        $row->addColumn($column);

        $action = new PaginationAction();
        $action->setTitle('Edit')
            ->setAClass('')
            ->setColumnClass('column p-2 sm:p-3')
            ->setSpanClass('fas fa-edit fa-fw fa-1-5x text-gray-700')
            ->setRoute('user_admin__manage')
            ->setRouteParams(['item' => 'id']);
        $row->addAction($action);

        $this->setRow($row);
        return $this;
    }

}