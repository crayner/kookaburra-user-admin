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

use App\Manager\ScriptManager;
use App\Provider\ProviderFactory;
use App\Util\ImageHelper;
use App\Util\TranslationsHelper;
use Kookaburra\UserAdmin\Entity\Person;
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
    /**
     * @var string
     */
    private $path = __DIR__.'/../../../../../public/uploads/imports';

    /**
     * @var ScriptManager
     */
    private $scriptManager;

    /**
     * PhotoImporter constructor.
     * @param ScriptManager $scriptManager
     */
    public function __construct(ScriptManager $scriptManager)
    {
        $this->scriptManager = $scriptManager;
    }

    /**
     * getPath
     * @return string
     */
    public function getPath(): string
    {
        if (!is_dir($this->path))
        {
            $fs = new Filesystem();
            $fs->mkdir($this->path, 0755);
        }

        return realpath($this->path);
    }

    /**
     * clearPhotos
     */
    public function clearPhotos()
    {
        $finder = new Finder();
        $fs = new Filesystem();
        $finder->files()->in($this->getPath());
        if ($finder->hasResults())
            foreach($finder as $file)
                $fs->remove($file->getRealPath());
    }

    /**
     * toArray
     * @return array
     */
    public function toArray(): array
    {
        $result['people'] = ProviderFactory::create(Person::class)->groupedChoiceList();
        $result['absolute_url'] = ImageHelper::getAbsoluteURL();
        $result['messages'] = $this->getTranslations();
        return $result;
    }

    /**
     * setPhotoLoaderScript
     * @return PhotoImporter
     */
    public function setPhotoLoaderScript(): PhotoImporter
    {
        $this->getScriptManager()->addAppProp('photoLoader', $this->toArray());
        return $this;
    }

    /**
     * @return ScriptManager
     */
    public function getScriptManager(): ScriptManager
    {
        return $this->scriptManager;
    }

    /**
     * getTranslations
     * @return array
     */
    private function getTranslations(): array
    {
        $tx = [];
        $tx['Drop Image Here'] = TranslationsHelper::translate('Drop Image Here', [], 'UserAdmin');
        $tx['Target Person'] = TranslationsHelper::translate('Target Person', [], 'UserAdmin');
        $tx['target_person_help'] = TranslationsHelper::translate('Select the person and then drag the image from your computer to set the image for this person.', [], 'UserAdmin');
        $tx['Remove Photo'] = TranslationsHelper::translate('Remove Photo', [], 'UserAdmin');
        $tx['error_ratio'] = TranslationsHelper::translate('The image must ratio of {ratio}:1 is outside the allowed limits of 0.7:1 to 0.84:1.', [], 'UserAdmin');
        $tx['error_height_width'] = TranslationsHelper::translate('The height and width maximum is 480 x 360px. The image supplied was {height} x {width}px.', [], 'UserAdmin');
        $tx['error_size'] = TranslationsHelper::translate('The file is too big. Max 350k. File size given is {size}.', [], 'UserAdmin');
        $tx['aborted'] = TranslationsHelper::translate('{name} upload failed...', [], 'UserAdmin');
        $tx['Target this person...'] = TranslationsHelper::translate('Target this person...', [], 'UserAdmin');
        $tx['Replace this image'] = TranslationsHelper::translate('Replace this image', [], 'UserAdmin');
        $tx['Images [.jpg, .png, .jpeg] only'] = TranslationsHelper::translate('Images [.jpg, .png, .jpeg] only', [], 'UserAdmin');
        return $tx;
    }
}