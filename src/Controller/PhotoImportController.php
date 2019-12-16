<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 16/12/2019
 * Time: 15:18
 */

namespace Kookaburra\UserAdmin\Controller;

use Kookaburra\UserAdmin\Form\PhotoImportType;
use Kookaburra\UserAdmin\Manager\PhotoImporter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PhotoImportController
 * @package Kookaburra\UserAdmin\Controller
 * @Route("/user/admin", name="user_admin__")
 */
class PhotoImportController extends AbstractController
{
    /**
     * import
     * @param Request $request
     * @Route("/photo/import/",name="import_photos")
     * @IsGranted("ROLE_ROUTE")
     */
    public function import(Request $request, PhotoImporter $importer)
    {
        $form = $this->createForm(PhotoImportType::class, null);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $importer->handleImport($form->get('file')->getData(), $request->getSession()->getBag('flashes'));
        }
        $path = realpath(__DIR__.'/../../../../../public/uploads/imports');

        $finder = new Finder();
        $finder->files()->in($path);

        return $this->render('@KookaburraUserAdmin/photo_import.html.twig',
            [
                'form' => $form->createView(),
                'finder' => $finder->hasResults() ? $finder : [],
            ]
        );
    }
}