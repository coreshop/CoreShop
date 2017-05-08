<?php

namespace CoreShop\Bundle\IndexBundle\EventListener;

use CoreShop\Component\Index\Service\IndexUpdaterServiceInterface;
use Pimcore\Event\Model\ElementEventInterface;
use Pimcore\Event\Model\ObjectEvent;

final class IndexObjectListener
{
    /**
     * @var IndexUpdaterServiceInterface
     */
    protected $indexUpdaterService;

    /**
     * @param IndexUpdaterServiceInterface $indexUpdaterService
     */
    public function __construct(IndexUpdaterServiceInterface $indexUpdaterService)
    {
        $this->indexUpdaterService = $indexUpdaterService;
    }

    public function onPostUpdate(ElementEventInterface $e)
    {
        if ($e instanceof ObjectEvent) {
            $object = $e->getObject();

            $this->indexUpdaterService->updateIndices($object);
        }
    }
}
