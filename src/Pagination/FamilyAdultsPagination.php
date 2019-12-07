<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 6/12/2019
 * Time: 15:29
 */

namespace Kookaburra\UserAdmin\Pagination;


use App\Manager\Entity\PaginationAction;
use App\Manager\Entity\PaginationColumn;
use App\Manager\Entity\PaginationRow;
use App\Manager\ReactPaginationInterface;
use App\Manager\ReactPaginationManager;
use App\Util\TranslationsHelper;

class FamilyAdultsPagination extends ReactPaginationManager
{
    public function execute(): ReactPaginationInterface
    {
        TranslationsHelper::setDomain('UserAdmin');
        $row = new PaginationRow();

        $column = new PaginationColumn();
        $column->setLabel('Name')
            ->setContentKey(['fullName'])
            ->setContentType('link')
            ->setOptions(['route' => 'user_admin__edit', 'route_options' => ['person' => 'adult_id']])
            ->setClass('column relative pr-4 cursor-pointer widthAuto');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Status')
            ->setContentKey(['status'])
            ->setClass('column relative pr-4 cursor-pointer widthAuto');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Comment')
            ->setContentKey(['comment'])
            ->setClass('column relative pr-4 cursor-pointer widthAuto');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Data Access')
            ->setContentKey(['childDataAccess'])
            ->setClass('column relative pr-4 cursor-pointer maxWidth50 text-center');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Contact Priority')
            ->setContentKey(['contactPriority'])
            ->setClass('column relative pr-4 cursor-pointer maxWidth50 text-center');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Contact by Phone')
            ->setContentKey(['phone'])
            ->setClass('column relative pr-4 cursor-pointer maxWidth50 text-center');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Contact by SMS')
            ->setContentKey(['sms'])
            ->setClass('column relative pr-4 cursor-pointer maxWidth50 text-center');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Contact by Email')
            ->setContentKey(['email'])
            ->setClass('column relative pr-4 cursor-pointer maxWidth50 text-center');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Contact by Mail')
            ->setContentKey(['mail'])
            ->setClass('column relative pr-4 cursor-pointer maxWidth50 text-center');
        $row->addColumn($column);

        $action = new PaginationAction();
        $action->setTitle('Remove adult from family')
            ->setAClass('')
            ->setColumnClass('column p-2 sm:p-3')
            ->setSpanClass('fas fa-eraser fa-fw fa-1-5x text-gray-700')
            ->setRoute('user_admin__family_child_remove')
            ->setOnClick('areYouSure')
            ->setRouteParams(['family' => 'family_id', 'child' => 'id']);
        $row->addAction($action);

        $this->setRow($row);
        return $this;

    }

}