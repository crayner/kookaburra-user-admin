<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 *
 * (c) 2018 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * UserProvider: craig
 * Date: 23/11/2018
 * Time: 09:01
 */
namespace Kookaburra\UserAdmin\Repository;

use App\Provider\ProviderFactory;
use App\Util\TranslationsHelper;
use Doctrine\ORM\NoResultException;
use Kookaburra\SystemAdmin\Entity\Role;
use Kookaburra\UserAdmin\Entity\District;
use Kookaburra\UserAdmin\Entity\Person;
use App\Entity\RollGroup;
use Kookaburra\SchoolAdmin\Entity\AcademicYear;
use Kookaburra\SchoolAdmin\Util\AcademicYearHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Common\Persistence\ManagerRegistry;
use Kookaburra\UserAdmin\Form\Entity\ManageSearch;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class PersonRepository
 * @package Kookaburra\UserAdmin\Repository
 */
class PersonRepository extends ServiceEntityRepository
{
    /**
     * PersonRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Person::class);
    }

    /**
     * Loads the user for the given username.
     *
     * This method must return null if the user is not found.
     *
     * @param string $username The username
     *
     * @return UserInterface|null
     */
    public function loadUserByUsernameOrEmail($username)
    {
        return $this->createQueryBuilder('p')
            ->select(['p','s','r'])
            ->leftJoin('p.staff', 's')
            ->leftJoin('p.primaryRole', 'r')
            ->where('p.email = :email OR p.username = :username')
            ->setParameter('email', $username)
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * findStaffForFastFinder
     * @param string $staffTitle
     * @return array|null
     * @throws \Exception
     */
    public function findStaffForFastFinder(string $staffTitle): ?array
    {
        return $this->createQueryBuilder('p')
            ->select(["CONCAT('".$staffTitle . "', p.surname, ', ', p.preferredName) as text", "CONCAT('Sta-', p.id) AS id", "CONCAT(p.username, ' ', p.email) AS search"])
            ->join('p.staff', 's')
            ->where('p.status = :full')
            ->andWhere('s.person IS NOT NULL')
            ->andWhere('(p.dateStart IS NULL OR p.dateStart <= :today)')
            ->andWhere('(p.dateEnd IS NULL OR p.dateEnd >= :today)')
            ->setParameters(['full' => 'Full', 'today' => new \DateTime(date('Y-m-d'))])
            ->orderBy('text')
            ->getQuery()
            ->getResult();
    }

    /**
     * findStudentsForFastFinder
     * @param AcademicYear $AcademicYear
     * @param string $studentTitle
     * @return array|null
     * @throws \Exception
     */
    public function findStudentsForFastFinder(AcademicYear $academicYear, string $studentTitle): ?array
    {
        return $this->createQueryBuilder('p')
            ->select([
                "CONCAT('".$studentTitle."', p.surname, ', ', p.preferredName, ' (', rg.name, ', ', p.studentID, ')') AS text",
                "CONCAT(p.username, ' ', p.firstName, ' ', p.email) AS search",
                "CONCAT('Stu-', p.id) AS id",
            ])
            ->join('p.studentEnrolments', 'se')
            ->join('se.rollGroup', 'rg')
            ->where('se.academicYear = :academicYear')
            ->andWhere('p.status = :full')
            ->andWhere('(p.dateStart IS NULL OR p.dateStart <= :today)')
            ->andWhere('(p.dateEnd IS NULL OR p.dateEnd >= :today)')
            ->setParameters(['today' => new \DateTime(date('Y-m-d')), 'academicYear' => $academicYear, 'full' => 'Full'])
            ->orderBy('text')
            ->getQuery()
            ->getResult();
    }

    /**
     * findStudentsByRollGroup
     * @param RollGroup $rollGroup
     * @return mixed
     */
    public function findStudentsByRollGroup(RollGroup $rollGroup, string $sortBy = 'rollOrder')
    {
        $query = $this->createQueryBuilder('p')
            ->select(['p','se','s'])
            ->join('p.studentEnrolments', 'se')
            ->leftJoin('p.staff', 's')
            ->where('se.rollGroup = :rollGroup')
            ->andWhere('s.id IS NULL')
            ->setParameter('rollGroup', $rollGroup)
            ->andWhere('p.status = :full')
            ->setParameter('full', 'Full');

        switch (substr($sortBy, 0, 4)) {
            case 'roll':
                $query->orderBy('se.rollOrder', 'ASC')
                    ->addOrderBy('p.surname', 'ASC')
                    ->addOrderBy('p.preferredName', 'ASC');
                break;
            case 'surn':
                $query->orderBy('p.surname', 'ASC')
                    ->addOrderBy('p.preferredName', 'ASC');
                break;
            case 'pref':
                $query->orderBy('p.preferredName', 'ASC')
                    ->addOrderBy('p.surname', 'ASC');
                break;
        }

        return $query->getQuery()
            ->getResult();
    }

    /**
     * findByRoles
     * @param array $roles
     * @return mixed
     */
    public function findByRoles(array $roles = [])
    {
        return $this->createQueryBuilder('p')
            ->select(['p', 'r.name'])
            ->join('p.primaryRole', 'r', 'with', 'p.primaryRole IN (:roles)')
            ->setParameter('roles', $roles, Connection::PARAM_INT_ARRAY)
            ->where('p.status = :full')
            ->setParameter('full', 'Full')
            ->groupBy('p.id')
            ->orderBy('r.name', 'ASC')
            ->addOrderBy('p.surname', 'ASC')
            ->addOrderBy('p.firstName', "ASC")
            ->getQuery()
            ->getResult();
    }

    /**
     * findCurrentStudents
     * @return array
     */
    public function findCurrentStudents(): array
    {
        $AcademicYear = AcademicYearHelper::getCurrentAcademicYear();
        $today = new \DateTime(date('Y-m-d'));
        return $this->createQueryBuilder('p')
            ->join('p.studentEnrolments','se')
            ->where('se.AcademicYear = :AcademicYear')
            ->setParameter('AcademicYear', $AcademicYear)
            ->andWhere('p.status = :full')
            ->setParameter('full', 'Full')
            ->andWhere('(p.dateStart IS NULL OR p.dateStart <= :today)')
            ->andWhere('(p.dateEnd IS NULL OR p.dateEnd >= :today)')
            ->setParameter('today', $today)
            ->orderBy('p.surname', 'ASC')
            ->addOrderBy('p.preferredName', 'ASC')
            ->getQuery()
            ->getResult();
        ;
    }

    /**
     * findCurrentStaff
     * @return array
     * @throws \Exception
     */
    public function findCurrentStaff(): array
    {
        $today = new \DateTime(date('Y-m-d'));
        return $this->createQueryBuilder('p')
            ->select(['p','s'])
            ->join('p.staff','s')
            ->where('s.id IS NOT NULL')
            ->andWhere('p.status = :full')
            ->setParameter('full', 'Full')
            ->andWhere('(p.dateStart IS NULL OR p.dateStart <= :today)')
            ->andWhere('(p.dateEnd IS NULL OR p.dateEnd >= :today)')
            ->setParameter('today', $today)
            ->orderBy('p.surname')
            ->addOrderBy('p.preferredName')
            ->getQuery()
            ->getResult();
        ;
    }

    /**
     * findCurrentStaff
     * @return array
     * @throws \Exception
     */
    public function findCurrentStaffAsArray(): array
    {
        $today = new \DateTime(date('Y-m-d'));
        return $this->createQueryBuilder('p')
            ->select(['p.id', "CONCAT(p.surname, ', ', p.preferredName) AS fullName", "'".TranslationsHelper::translate('Staff', [], 'UserAdmin')."' AS type", 'p.image_240 AS photo'])
            ->join('p.staff','s')
            ->where('s.id IS NOT NULL')
            ->andWhere('p.status = :full')
            ->setParameter('full', 'Full')
            ->andWhere('(p.dateStart IS NULL OR p.dateStart <= :today)')
            ->andWhere('(p.dateEnd IS NULL OR p.dateEnd >= :today)')
            ->setParameter('today', $today)
            ->orderBy('p.surname')
            ->addOrderBy('p.preferredName')
            ->getQuery()
            ->getResult();
        ;
    }

    /**
     * findAllFullList
     * @return array
     */
    public function findAllFullList(): array
    {
        return $this->createQueryBuilder('p')
            ->select(['p.id', "CONCAT(p.surname, ': ', p.preferredName) AS fullName"])
            ->where('p.status = :full')
            ->setParameter('full', 'Full')
            ->orderBy('p.surname', 'ASC')
            ->addOrderBy('p.preferredName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * findAllStudentsByRollGroup
     * @return mixed
     */
    public function findAllStudentsByRollGroup()
    {
        return $this->createQueryBuilder('p')
            ->select(['p.id', 'p.studentID', "CONCAT(p.surname, ', ', p.preferredName) AS fullName", 'rg.name AS rollGroup', 'rg.name AS type', 'p.image_240 AS photo'])
            ->where('p.status = :full')
            ->setParameter('full', 'Full')
            ->join('p.studentEnrolments', 'se')
            ->andWhere('se.AcademicYear = :currentYear')
            ->setParameter('currentYear', AcademicYearHelper::getCurrentAcademicYear())
            ->join('se.rollGroup', 'rg')
            ->leftJoin('p.staff', 's')
            ->andWhere('s.id IS NULL')
            ->orderBy('rg.name', 'ASC')
            ->addOrderBy('p.surname', 'ASC')
            ->addOrderBy('p.preferredName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * findCurrentParents
     * @return array
     */
    public function findCurrentParents(): array
    {
        return $this->createQueryBuilder('p')
            ->select(['p','fa','s'])
            ->join('p.adults', 'fa')
            ->where('(fa.contactPriority <= 2 and fa.contactPriority > 0)')
            ->andWhere('p.status = :full')
            ->leftJoin('p.staff', 's')
            ->andWhere('s.id IS NULL')
            ->setParameter('full', 'Full')
            ->orderBy('p.surname', 'ASC')
            ->addOrderBy('p.preferredName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * findCurrentParents
     * @return array
     */
    public function findCurrentParentsAsArray(): array
    {
        return $this->createQueryBuilder('p')
            ->select(['p.id', "CONCAT(p.surname, ', ', p.preferredName) AS fullName", "'".TranslationsHelper::translate('Parent', [], 'UserAdmin')."' AS type", 'p.image_240 AS photo'])
            ->join('p.adults', 'fa')
            ->where('(fa.contactPriority <= 2 and fa.contactPriority > 0)')
            ->andWhere('p.status = :full')
            ->leftJoin('p.staff', 's')
            ->andWhere('s.id IS NULL')
            ->setParameter('full', 'Full')
            ->orderBy('p.surname', 'ASC')
            ->addOrderBy('p.preferredName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * findOneUsingQuickSearch
     * @param string $search
     * @return Person|null
     */
    public function findOneUsingQuickSearch(string $search): ?Person
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.id = :searchInt')->setParameter('searchInt', intval($search));
        if ($search !== '')
            $query->orWhere('p.studentID = :search')->orWhere('p.username = :search')->setParameter('search', $search);

        try {
            return $query
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * findOthers
     * @return array
     */
    public function findOthers(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.adults', 'fa')
            ->where('fa.id IS NULL')
            ->leftJoin('p.studentEnrolments', 'se')
            ->andWhere('se.id IS NULL')
            ->leftJoin('p.staff', 's')
            ->andWhere('s.id IS NULL')
            ->orderBy('p.surname', 'ASC')
            ->addOrderBy('p.preferredName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * findBySearch
     * @param ManageSearch $search
     * @return array
     */
    public function findBySearch(ManageSearch $search): array
    {
        $query = $this->createQueryBuilder('p')
            ->select(['p','fa','fc','fama','famc','s', 'r'])
            ->leftJoin('p.adults', 'fa')
            ->leftJoin('p.primaryRole', 'r')
            ->leftJoin('p.children', 'fc')
            ->leftJoin('fa.family', 'fama')
            ->leftJoin('fc.family', 'famc')
            ->leftJoin('p.staff', 's')
            ->leftJoin('p.studentEnrolments', 'se')
            ->where('p.vehicleRegistration LIKE :search OR p.phone1 LIKE :search OR p.phone2 LIKE :search OR p.phone3 LIKE :search OR p.phone4 LIKE :search OR p.email LIKE :search OR p.emailAlternate LIKE :search OR p.studentID LIKE :search OR p.username LIKE :search OR p.surname LIKE :search OR p.preferredName LIKE :search')
            ->setParameter('search', '%'.$search->getSearch().'%')
            ->orderBy('p.surname')
            ->addOrderBy('p.preferredName')
        ;
        switch ($search->getFilter()) {
            case '':
                break;
            case 'role:student':
                $query->andWhere('se IS NOT NULL');
                break;
            case 'role:parent':
                $query->andWhere('fa IS NOT NULL')
                    ->andWhere('fa.contactPriority <= 2');
                break;
            case 'role:staff':
                $query->andWhere('s IS NOT NULL');
                break;
            case 'status:full':
                $query->andWhere('p.status = :status')
                    ->setParameter('status', 'Full');
                break;
            case 'status:left':
                $query->andWhere('p.status = :status')
                    ->setParameter('status', 'Left');
                break;
            case 'status:expected':
                $query->andWhere('p.status = :status')
                    ->setParameter('status', 'Expected');
                break;
            case 'date:starting':
                $query->andWhere('p.dateStart > :today')
                    ->setParameter('today', new \DateTimeImmutable());
                break;
            case 'date:ended':
                $query->andWhere('p.dateEnd < :today')
                    ->setParameter('today', new \DateTimeImmutable());
                break;
        }

        return $query->getQuery()
            ->getResult();
    }

    /**
     * countDistrictUsage
     * @param District $district
     * @return int
     */
    public function countDistrictUsage(District $district): int
    {
        try {
            return $this->createQueryBuilder('p')
                ->select('COUNT(p.id)')
                ->where('p.address1District = :district')
                ->orWhere('p.address2District = :district')
                ->setParameter('district', $district)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            return 0;
        }
    }
}
