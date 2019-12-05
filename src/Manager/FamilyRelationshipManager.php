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


use Kookaburra\UserAdmin\Entity\Family;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FamilyRelationshipManager
{
    public function handleRequest(Request $request, Family $family)
    {
        $rels = $request->request->get('family_relationships');
        dd($rels,$family);
    }

    /**
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }
}