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

namespace Kookaburra\UserAdmin\Manager;

use App\Manager\ScriptManager;
use App\Manager\SpecialInterface;
use App\Provider\ProviderFactory;
use App\Util\ImageHelper;
use App\Util\TranslationsHelper;
use Kookaburra\UserAdmin\Entity\Person;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Util\StringUtil;

/**
 * Class PhotoImporter
 * @package Kookaburra\UserAdmin\Manager
 */
class PhotoImporter implements SpecialInterface
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
        $result['name'] = $this->getName();
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
        TranslationsHelper::setDomain('UserAdmin');
        $tx['Drop Image Here'] = TranslationsHelper::translate('Drop Image Here');
        $tx['Target Person'] = TranslationsHelper::translate('Target Person');
        $tx['target_person_help'] = TranslationsHelper::translate('Select the person and then drag the image from your computer to set the image for this person.');
        $tx['Remove Photo'] = TranslationsHelper::translate('Remove Photo');
        $tx['error_ratio'] = TranslationsHelper::translate('The image must ratio of {ratio}:1 is outside the allowed limits of 0.7:1 to 0.84:1.');
        $tx['error_height_width'] = TranslationsHelper::translate('The height and width maximum is 480 x 360px. The image supplied was {height} x {width}px.');
        $tx['error_height_width_minimum'] = TranslationsHelper::translate('The height and width minimum is 320 x 240px. The image supplied was {height} x {width}px.');
        $tx['error_size'] = TranslationsHelper::translate('The file is too big. Max 350k. File size given is {size}.');
        $tx['aborted'] = TranslationsHelper::translate('{name} upload failed...');
        $tx['Target this person...'] = TranslationsHelper::translate('Target this person...');
        $tx['Replace this image'] = TranslationsHelper::translate('Replace this image');
        $tx['Images [.jpg, .png, .jpeg] only'] = TranslationsHelper::translate('Images [.jpg, .png, .jpeg] only');
        $tx['Import Images'] = TranslationsHelper::translate('Import Images');
        $tx['Notes'] = TranslationsHelper::translate('Notes');
        $tx['drag_drop_page'] = TranslationsHelper::translate('Use this page to drag and drop images from your computer to the site for the targeted individual. Existing images are replaced.');
        $tx['File Name - The system modifies the filename when linked to the correct person.'] = TranslationsHelper::translate('File Name - The system modifies the filename when linked to the correct person.');
        $tx['File Type * - Images must be formatted as JPG or PNG.'] = TranslationsHelper::translate('File Type * - Images must be formatted as JPG, GIF or PNG.');
        $tx['Image Size * - Displayed at 240px by 320px.'] = TranslationsHelper::translate('Image Size * - Displayed at 240px by 320px.');
        $tx['Size Range * - Accepts images up to 360px by 480px.'] = TranslationsHelper::translate('Size Range * - Accepts images up to 360px by 480px.');
        $tx['Aspect Ratio Range * - Accepts aspect ratio between 0.7:1 and 0.84:1.'] = TranslationsHelper::translate('Aspect Ratio Range * - Accepts aspect ratio between 0.7:1 and 0.84:1.');
        return $tx;
    }

    /**
     * getName
     * @return string
     */
    public function getName(): string
    {
        return StringUtil::fqcnToBlockPrefix(static::class) ?: '';
    }
}