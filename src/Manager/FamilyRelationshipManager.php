<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 5/12/2019
 * Time: 15:22
 */

namespace Kookaburra\UserAdmin\Manager;

use App\Provider\ProviderFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Driver\PDOException;
use Kookaburra\UserAdmin\Entity\Family;
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
                ->setAdult($provider->getRepository(Person::class)->find($item['adult']))
                ->setChild($provider->getRepository(Person::class)->find($item['child']))
                ->setRelationship($item['relationship'])
            ;

            $errors = $this->getValidator()->validate($fr);
            if ($errors->count() > 0)
            {
                $request->getSession()->getBag('flashes')->add('error', ['return.error.1', [], 'messages']);
                $request->getSession()->getBag('flashes')->add('warning', ['Please refresh the family data before attempting to change family relationships.', [], 'UserAdmin']);
                $ok = false;
                break;
            }
            $relationships[] = $fr;
        }

        if ($ok) {
            $family->setRelationships(new ArrayCollection($relationships));
            try {
                $em = ProviderFactory::getEntityManager();
                $em->persist($family);
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
}