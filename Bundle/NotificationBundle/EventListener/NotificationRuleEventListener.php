<?php

namespace CoreShop\Bundle\NotificationBundle\EventListener;

use CoreShop\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use CoreShop\Component\Notification\Model\NotificationRuleInterface;
use CoreShop\Component\Notification\Repository\NotificationRuleRepositoryInterface;

class NotificationRuleEventListener
{
    /**
     * @var NotificationRuleRepositoryInterface
     */
    private $repository;

    /**
     * NotificationRuleEventListener constructor.
     * @param NotificationRuleRepositoryInterface $repository
     */
    public function __construct(NotificationRuleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function preCreate(ResourceControllerEvent $event)
    {
        $object = $event->getSubject();

        if (!$object instanceof NotificationRuleInterface) {
            return;
        }

        $object->setSort(count($this->repository->findAll()) + 1);
    }
}