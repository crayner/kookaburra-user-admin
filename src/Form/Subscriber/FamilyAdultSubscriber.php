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
 * Date: 7/12/2019
 * Time: 14:25
 */

namespace Kookaburra\UserAdmin\Form\Subscriber;

use App\Provider\ProviderFactory;
use Kookaburra\UserAdmin\Entity\FamilyAdult;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class FamilyAdultSubscriber
 * @package Kookaburra\UserAdmin\Form\Subscriber
 */
class FamilyAdultSubscriber implements EventSubscriberInterface
{
    /**
     * getSubscribedEvents
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    /**
     * onPreSubmit
     * @param PreSubmitEvent $event
     */
    public function onPreSubmit(PreSubmitEvent $event)
    {
        $data = $event->getData();
        $provider = ProviderFactory::create(FamilyAdult::class);
        $adults = $provider->getRepository(FamilyAdult::class)->findByFamilyWithoutAdult($data['person'], $data['family']);
        if (!empty($adults)) {
            $priority = intval($data['contactPriority']);
            foreach ($adults as $adult)
                if ($adult->getContactPriority() === $priority)
                    $adult->setContactPriority(++$priority);

            $family = $adults[0]->getFamily();
            $provider->persistFlush($family);
        }
    }
}