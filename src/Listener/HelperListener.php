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
 * Date: 1/12/2019
 * Time: 09:31
 */

namespace Kookaburra\UserAdmin\Listener;

use Kookaburra\UserAdmin\Manager\PersonNameManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class HelperListener
 * @package Kookaburra\UserAdmin\Listener
 */
class HelperListener implements EventSubscriberInterface
{
    /**
     * HelperListener constructor.
     */
    public function __construct(
        PersonNameManager $personNameManager
    )
    {
    }

    /**
     * getSubscribedEvents
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['initiate', -256],
        ];
    }
    /**
     * gibbonInitiate
     * @param RequestEvent $event
     */
    public function initiate(RequestEvent $event)
    {
        PersonNameManager::getFormats();
    }
}