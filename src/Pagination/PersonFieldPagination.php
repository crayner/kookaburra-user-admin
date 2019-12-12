<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 12/12/2019
 * Time: 09:59
 */

namespace Kookaburra\UserAdmin\Pagination;

use App\Manager\Entity\PaginationAction;
use App\Manager\Entity\PaginationColumn;
use App\Manager\Entity\PaginationRow;
use App\Manager\ReactPaginationInterface;
use App\Manager\ReactPaginationManager;
use App\Util\TranslationsHelper;

/**
 * Class PersonFieldPagination
 * @package Kookaburra\UserAdmin\Pagination
 */
class PersonFieldPagination extends ReactPaginationManager
{
    /**
     * execute
     * @return ReactPaginationInterface
     */
    public function execute(): ReactPaginationInterface
    {
        TranslationsHelper::setDomain('UserAdmin');
        $row = new PaginationRow();

        $column = new PaginationColumn();
        $column->setLabel('Name')
            ->setContentKey(['name'])
            ->setSort(true)
            ->setClass('column relative pr-4 cursor-pointer widthAuto');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Field Type')
            ->setContentKey(['type'])
            ->setClass('column relative pr-4 cursor-pointer widthAuto');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Active')
            ->setContentKey(['active'])
            ->setClass('column relative pr-4 cursor-pointer widthAuto text-center');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Role Categories')
            ->setContentKey(['categories'])
            ->setClass('column relative pr-4 cursor-pointer widthAuto');
        $row->addColumn($column);

        $action = new PaginationAction();
        $action->setTitle('Edit')
            ->setAClass('')
            ->setColumnClass('column p-2 sm:p-3')
            ->setSpanClass('fas fa-edit fa-fw fa-1-5x text-gray-700')
            ->setRoute('user_admin__custom_field_edit')
            ->setRouteParams(['field' => 'id']);
        $row->addAction($action);

        $action = new PaginationAction();
        $action->setTitle('Delete')
            ->setAClass('')
            ->setColumnClass('column p-2 sm:p-3')
            ->setSpanClass('fas fa-trash-alt fa-fw fa-1-5x text-gray-700')
            ->setRoute('user_admin__custom_field_delete')
            ->setOnClick('areYouSure')
            ->setRouteParams(['field' => 'id']);
        $row->addAction($action);

        $this->setRow($row);
        return $this;
    }
}