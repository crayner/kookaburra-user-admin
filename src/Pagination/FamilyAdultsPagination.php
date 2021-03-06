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
 * Date: 6/12/2019
 * Time: 15:29
 */

namespace Kookaburra\UserAdmin\Pagination;


use App\Manager\Entity\PaginationAction;
use App\Manager\Entity\PaginationColumn;
use App\Manager\Entity\PaginationRow;
use App\Manager\PaginationInterface;
use App\Manager\AbstractPaginationManager;
use App\Util\TranslationsHelper;

class FamilyAdultsPagination extends AbstractPaginationManager
{
    public function execute(): PaginationInterface
    {
        TranslationsHelper::setDomain('UserAdmin');
        $row = new PaginationRow();

        $column = new PaginationColumn();
        $column->setLabel('Name')
            ->setContentKey(['fullName'])
            ->setContentType('link')
            ->setOptions(['route' => 'user_admin__edit', 'route_options' => ['person' => 'person']])
            ->setClass('column relative pr-4 cursor-pointer');
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
        $action->setTitle('Edit')
            ->setAClass('thickbox p-3 sm:p-0')
            ->setColumnClass('column p-2 sm:p-3')
            ->setSpanClass('fas fa-edit fa-fw fa-1-5x text-gray-700')
            ->setRoute('user_admin__family_adult_edit')
            ->setRouteParams(['family' => 'family_id', 'adult' => 'adult_id']);
        $row->addAction($action);

        $action = new PaginationAction();
        $action->setTitle('Remove adult from family')
            ->setAClass('thickbox p-3 sm:p-0')
            ->setColumnClass('column p-2 sm:p-3')
            ->setSpanClass('fas fa-eraser fa-fw fa-1-5x text-gray-700')
            ->setRoute('user_admin__family_adult_remove')
            ->setOnClick('areYouSure')
            ->setRouteParams(['family' => 'family_id', 'adult' => 'adult_id']);
        $row->addAction($action);

        $action = new PaginationAction();
        $action->setTitle('Change Password')
            ->setAClass('thickbox p-3 sm:p-0')
            ->setColumnClass('column p-2 sm:p-3')
            ->setSpanClass('fas fa-user-lock fa-fw fa-1-5x text-gray-700')
            ->setRoute('user_admin__reset_password')
            ->setRouteParams(['person' => 'person']);
        $row->addAction($action);

        $this->setRow($row);
        return $this;
    }
}