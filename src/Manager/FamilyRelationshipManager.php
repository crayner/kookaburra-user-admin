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
 * Date: 5/12/2019
 * Time: 15:22
 */

namespace Kookaburra\UserAdmin\Manager;

use App\Provider\ProviderFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Driver\PDOException;
use Kookaburra\UserAdmin\Entity\Family;
use Kookaburra\UserAdmin\Entity\FamilyAdult;
use Kookaburra\UserAdmin\Entity\FamilyChild;
use Kookaburra\UserAdmin\Entity\FamilyRelationship;
use Kookaburra\UserAdmin\Entity\Person;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FamilyRelationshipManager
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * FamilyRelationshipManager constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * handleRequest
     * @param Request $request
     * @param Family $family
     */
    public function handleRequest(Request $request, Family $family)
    {
        $rels = $request->request->get('family_relationships');
        if (is_null($rels))
            return ;

        $provider = ProviderFactory::create(FamilyRelationship::class);
        $relationships = [];
        $ok = true;
        if (!isset($rels['relationships']))
            return;
        foreach($rels['relationships'] as $item)
        {
            $fr = $provider->findOneRelationship($item);
            $fr->setFamily($family)
                ->setAdult($provider->getRepository(FamilyAdult::class)->find($item['adult']))
                ->setChild($provider->getRepository(FamilyChild::class)->find($item['child']))
                ->setRelationship($item['relationship'])
            ;

            $errors = $this->getValidator()->validate($fr);
            if ($errors->count() > 0)
            {
                $request->getSession()->getBag('flashes')->add('error', ['return.error.1', [], 'messages']);
                $ok = false;
                break;
            }
            $relationships[] = $fr;
        }

        if ($ok) {
            try {
                $em = ProviderFactory::getEntityManager();
                foreach($relationships as $fr)
                    $em->persist($fr);
                $em->flush();
                $request->getSession()->getBag('flashes')->add('success', ['return.success.0', [], 'messages']);
            } catch (\PDOException | PDOException | \Exception $e) {
                $request->getSession()->getBag('flashes')->add('error', ['return.error.2', [], 'messages']);
            }
        }
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * getRelationships
     * @param Family $family
     * @return Collection
     */
    public function getRelationships(Family $family): Collection
    {
        $adults = FamilyManager::getAdults($family);
        $children = FamilyManager::getChildren($family);
        $relationships = $this->getExistingRelationships($family);
        if (count($adults) * count($children) === $relationships->count())
            return $relationships;

        foreach($adults as $adult)
            foreach($children as $child)
            {
                $relationship = new FamilyRelationship($family, $adult, $child);
                $save = true;
                foreach($relationships as $item)
                    if ($relationship->isEqualTo($item)) {
                        $save = false;
                        break;
                    }
                if ($save)
                    $relationships->add($relationship);
            }

        return $relationships;
    }

    /**
     * getExistingRelationships
     * @param Family $family
     * @return ArrayCollection
     */
    private function getExistingRelationships(Family $family): ArrayCollection
    {
        $result = ProviderFactory::getRepository(FamilyRelationship::class)->findByFamily($family);
        $result = new ArrayCollection($result);
        return $result;
    }
}