<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 4/12/2019
 * Time: 10:07
 */

namespace Kookaburra\UserAdmin\Pagination;


use App\Manager\Entity\PaginationAction;
use App\Manager\Entity\PaginationColumn;
use App\Manager\Entity\PaginationRow;
use App\Manager\ReactPaginationInterface;
use App\Manager\ReactPaginationManager;
use App\Util\TranslationsHelper;

class RoleManagePagination extends ReactPaginationManager
{
    public function execute(): ReactPaginationInterface
    {
        TranslationsHelper::setDomain('UserAdmin');
        $row = new PaginationRow();

        $column = new PaginationColumn();
        $column->setLabel('Category')
            ->setContentKey('category')
            ->setSort(true)
            ->setClass('column relative pr-4 cursor-pointer widthAuto')
        ;
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Name')
            ->setContentKey('name')
            ->setSort(true)
            ->setClass('column relative pr-4 cursor-pointer widthAuto')
        ;
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Short Name')
            ->setContentKey('name_short')
            ->setClass('column relative pr-4 cursor-pointer widthAuto')
        ;
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Description')
            ->setContentKey('description')
            ->setClass('column relative pr-4 cursor-pointer widthAuto')
        ;
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Type')
            ->setContentKey('type')
            ->setSort(true)
            ->setClass('column relative pr-4 cursor-pointer widthAuto')
        ;
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Login Years')
            ->setContentKey('login_years')
            ->setClass('column relative pr-4 cursor-pointer widthAuto')
        ;
        $row->addColumn($column);

        $action = new PaginationAction();
        $action->setTitle('Edit')
            ->setAClass('')
            ->setColumnClass('column p-2 sm:p-3')
            ->setSpanClass('fas fa-edit fa-fw fa-1-5x text-gray-700')
            ->setRoute('user_admin__role_edit')
            ->setRouteParams(['role' => 'id']);
        $row->addAction($action);

        $action = new PaginationAction();
        $action->setTitle('Delete')
            ->setAClass('')
            ->setColumnClass('column p-2 sm:p-3')
            ->setSpanClass('far fa-trash-alt fa-fw fa-1-5x text-gray-700')
            ->setRoute('user_admin__role_delete')
            ->setOnClick('areYouSure')
            ->setDisplayWhen('isAdditional')
            ->setRouteParams(['role' => 'id']);
        $row->addAction($action);

        $action = new PaginationAction();
        $action->setTitle('Duplicate')
            ->setAClass('')
            ->setColumnClass('column p-2 sm:p-3')
            ->setSpanClass('far fa-copy fa-fw fa-1-5x text-gray-700')
            ->setRoute('user_admin__role_duplicate')
            ->setRouteParams(['role' => 'id']);
        $row->addAction($action);

        $this->setRow($row);
        return $this;
    }
}
