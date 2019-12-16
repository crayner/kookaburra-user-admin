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

namespace Kookaburra\UserAdmin\Manager;
use App\Util\TranslationsHelper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;

/**
 * Class PhotoImporter
 * @package Kookaburra\UserAdmin\Manager
 */
class PhotoImporter
{
    public function handleImport(UploadedFile $file, FlashBagInterface $flashBag)
    {
        $path = realpath(__DIR__.'/../../../../../public/uploads').'/imports';

        $fs = new Filesystem();

        $fs->mkdir($path, 0755);

        $path = realpath(__DIR__.'/../../../../../public/uploads/imports');

        $zip = new \ZipArchive();

        if ($zip->open($file->getPathname()) === true) {
            $zip->extractTo($path);
            $zip->close();
            echo 'ok';
        } else {
            echo 'failed';
        }
        $finder = new Finder();

        $finder->directories()->in($path);
        if ($finder->hasResults())
            foreach($finder as $dir) {
                $fs->remove($file->getRealPath());
            }
        $validator = Validation::createValidator();
        $violations = new ConstraintViolationList();
        $constraints = [
            new Image(['maxHeight' => 480, 'maxWidth' => 360, 'mimeTypes' => ['image/jpeg', 'image/png'], 'mimeTypesMessage' => 'The image is not a JPG/JPEG/PNG file type.', 'maxRatio' => 0.84, 'minRatio' => 0.7, 'minHeight' => 240]),
        ];
        $finder = new Finder();

        $finder->files()->in($path);
        if ($finder->hasResults())
            foreach($finder as $file) {
                $result = $validator->validate($file, $constraints);
                if ($result->count() > 0) {
                    $violations->addAll($result);
                    $fs->remove($file->getRealPath());
                    foreach($result as $error)
                        $flashBag->add('error', ['{name}: {message}', ['{message}' => $error->getMessage(), '{name}' => $file->getFilename()], 'UserAdmin']);
                }
            }

    }
}