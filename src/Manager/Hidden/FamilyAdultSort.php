<?php
/**
 * Created by PhpStorm.
 *
 * Kookaburra
 * (c) 2020 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 6/04/2020
 * Time: 09:41
 */

namespace Kookaburra\UserAdmin\Manager\Hidden;

use App\Manager\PaginationSortInterface;
use App\Provider\ProviderFactory;
use App\Util\ErrorMessageHelper;
use Kookaburra\UserAdmin\Entity\FamilyAdult;
use Kookaburra\UserAdmin\Pagination\FamilyAdultsPagination;

/**
 * Class FamilyAdultSort
 * @package Kookaburra\UserAdmin\Manager\Hidden
 */
class FamilyAdultSort implements PaginationSortInterface
{
    /**
     * @var FamilyAdult
     */
    private $source;

    /**
     * @var FamilyAdult
     */
    private $target;

    /**
     * @var FamilyAdultsPagination
     */
    private $pagination;

    /**
     * @var array
     */
    private $details = [];

    /**
     * @var array
     */
    private $content = [];

    /**
     * ScaleGradeSort constructor.
     *
     * @param FamilyAdult $source
     * @param FamilyAdult $target
     * @param FamilyAdultsPagination $pagination
     */
    public function __construct(FamilyAdult $source, FamilyAdult $target, FamilyAdultsPagination $pagination)
    {
        $this->source = $source;
        $this->target = $target;
        $this->pagination = $pagination;
        $this->details = ErrorMessageHelper::getSuccessMessage($this->details, true);

        if (!$this->source->getFamily()->isEqualTo($this->target->getFamily()))
        {
            $this->details = ErrorMessageHelper::getInvalidInputsMessage($this->details, true);
            return;
        }

        $provider = ProviderFactory::create(FamilyAdult::class);

        $content = $provider->getRepository()->findBy(['family' => $source->getFamily()->getId()], ['contactPriority' => 'ASC']);

        $key = 1;
        $result = [];
        foreach($content as $q => $item)
        {
            if ($item === $source)
                continue;
            if ($item === $target) {
                $source->setContactPriority($key++);
                $result[] = $source;
            }
            $item->setContactPriority($key++);
            $result[] = $item;
        }
        $this->details = $provider->saveAdults($result, $this->details);
        $this->content = $result;

    }

    /**
     * @return array
     */
    public function getDetails(): array
    {
        if ($this->details['status'] === 'success') {
            $this->details['content'] = $this->pagination->setContent($this->content)->toArray()['content'];
        }
        return $this->details;
    }

    /**
     * Details.
     *
     * @param array $details
     * @return $this
     */
    public function setDetails(array $details)
    {
        $this->details = $details;
        return $this;
    }

    /**
     * @return array
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * Content.
     *
     * @param array $content
     * @return FamilyAdultSort
     */
    public function setContent(array $content): FamilyAdultSort
    {
        $this->content = $content;
        return $this;
    }
}