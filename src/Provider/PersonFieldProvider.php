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
 * Date: 7/08/2019
 * Time: 15:16
 */

namespace Kookaburra\UserAdmin\Provider;

use App\Provider\EntityProviderInterface;
use App\Manager\Traits\EntityTrait;
use Kookaburra\UserAdmin\Entity\PersonField;

/**
 * Class PersonFieldProvider
 * @package App\Provider
 */
class PersonFieldProvider implements EntityProviderInterface
{
    use EntityTrait;

    private $entityName = PersonField::class;

    /**
     * getCustomFields
     * @param null $student
     * @param null $staff
     * @param null $parent
     * @param null $other
     * @param null $applicationForm
     * @param null $dataUpdater
     * @param null $publicRegistration
     * @return bool
     * @throws \Exception
     */
    public function getCustomFields($student = null, $staff = null, $parent = null, $other = null, $applicationForm = null, $dataUpdater = null, $publicRegistration = null)
    {
        $data = [];
        $whereInner = '';
        if ($student) {
            $data['student'] = $student;
            $whereInner .= 'pf.activePersonStudent = :student OR ';
        }
        if ($staff) {
            $data['staff'] = $staff;
            $whereInner .= 'pf.activePersonStaff = :staff OR ';
        }
        if ($parent) {
            $data['parent'] = $parent;
            $whereInner .= 'pf.activePersonParent = :parent OR ';
        }
        if ($other) {
            $data['other'] = $other;
            $whereInner .= 'pf.activePersonOther = :other OR ';
        }
        if ($applicationForm) {
            $data['applicationForm'] = $applicationForm;
            $where[] = 'pf.activeApplicationForm = :applicationForm';
        }
        if ($dataUpdater) {
            $data['dataUpdater'] = $dataUpdater;
            $where[] = 'pf.activeDataUpdater = :dataUpdater';
        }
        if ($publicRegistration) {
            $data['publicRegistration'] = $publicRegistration;
            $where[] = 'pf.activePublicRegistration = :publicRegistration';
        }

        if ($whereInner != '') {
            $where[] = '(' . rtrim($whereInner, ' OR '). ') ';
        }

        return $this->getRepository()->getCustomFields($where, $data);
    }
}