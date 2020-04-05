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
 * Date: 16/12/2019
 * Time: 15:18
 */

namespace Kookaburra\UserAdmin\Controller;

use App\Manager\PageManager;
use App\Util\ImageHelper;
use App\Util\StringHelper;
use App\Util\TranslationsHelper;
use Kookaburra\UserAdmin\Entity\Person;
use Kookaburra\UserAdmin\Form\PhotoImportType;
use Kookaburra\UserAdmin\Manager\PhotoImporter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Validation;

/**
 * Class PhotoImportController
 * @package Kookaburra\UserAdmin\Controller
 * @Route("/user/admin", name="user_admin__")
 */
class PhotoImportController extends AbstractController
{
    /**
     * import
     * @param PhotoImporter $importer
     * @param PageManager $pageManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/photo/import/",name="import_photos")
     * @IsGranted("ROLE_ROUTE")
     */
    public function import(PhotoImporter $importer, PageManager $pageManager)
    {
        $importer->setPhotoLoaderScript();
        if ($pageManager->isNotReadyForJSON()) return $pageManager->getBaseResponse();

       return $pageManager->createBreadcrumbs('Import People Photos')
           ->render(['special' => $importer->toArray()]);
    }

    /**
     * uploadPhoto
     * @Route("/personal/photo/{person}/upload/",name="upload_photos")
     * @Security("is_granted('ROLE_ROUTE', ['user_admin__import_photos'])")
     * @param Request $request
     * @param Person $person
     * @return JsonResponse
     */
    public function uploadPhoto(Request $request, Person $person)
    {
        $file = $request->files->get('file');

        $validator = Validation::createValidator();
        $constraints = [
            new Image(['maxHeight' => 480, 'maxWidth' => 360, 'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/jpeg'], 'mimeTypesMessage' => 'The image is not a JPG/JPEG/GIF/PNG file type.', 'maxRatio' => 0.84, 'minRatio' => 0.7, 'minHeight' => 320, 'minWidth' => 240]),
        ];
        $violations = $validator->validate($file, $constraints);

        if ($violations->count() === 0) {
            $path = $this->getParameter('upload_path');
            $fs = new Filesystem();
            if (!is_dir($path))
                $fs->mkdir($path, 0755);

            $name = uniqid(StringHelper::toSnakeCase($person->formatName(['style' => 'long', 'reverse' => true])). '_') . '.' . $file->guessExtension();

            $file->move($path, $name);

            $fs->remove($file->getRealpath());

            $file = new File($path.DIRECTORY_SEPARATOR.$name);

            $person->setImage240($file->getRealpath());

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($person);
                $em->flush();
            } catch (IOException $e) {
                $fs->remove($file->getRealpath());
                return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()]);
            }

            $photo = [];
            $photo['id'] = $person->getId();
            $photo['name'] = $person->formatName(['style' => 'long', 'reverse' => true]);
            $photo['photo'] = ImageHelper::getAbsoluteImageURL('file', $person->getImage240());
            return new JsonResponse(['status' => 'success', 'message' => TranslationsHelper::translate('return.success.0', [], 'messages'), 'person' => $photo], 200);
        }
        return new JsonResponse(['status' => 'error', 'message' => $violations[0]->getMessage()], 200);
    }

    /**
     * removePhoto
     * @Route("/personal/photo/{person}/remove/",name="remove_photo")
     * @Security("is_granted('ROLE_ROUTE', ['user_admin__import_photos'])")
     * @param Person $person
     * @param Request $request
     * @return JsonResponse
     */
    public function removePhoto(Person $person, Request $request)
    {
        try {
            $person->setImage240(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();
            $photo = [];
            $photo['id'] = $person->getId();
            $photo['name'] = $person->formatName(['style' => 'long', 'reverse' => true]);
            $photo['photo'] = ImageHelper::getAbsoluteImageURL('file', $person->getImage240());
            return new JsonResponse(['status' => 'success', 'message' => 'The photo was removed.', 'person' => $photo], 200);
        } catch ( \Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }
}