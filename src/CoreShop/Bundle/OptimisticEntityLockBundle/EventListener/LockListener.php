<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OptimisticEntityLockBundle\EventListener;

use CoreShop\Bundle\OptimisticEntityLockBundle\Exception\OptimisticLockException;
use CoreShop\Bundle\OptimisticEntityLockBundle\Manager\EntityLockManager;
use CoreShop\Bundle\OptimisticEntityLockBundle\Model\OptimisticLockedInterface;
use Doctrine\DBAL\Connection;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LockListener implements EventSubscriberInterface
{
    public function __construct(
        protected EntityLockManager $lockManager,
        protected Connection $connection,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvents::PRE_ADD => 'preAddLock',
            DataObjectEvents::PRE_UPDATE => 'checkLock',
            DataObjectEvents::POST_UPDATE => 'postUpdateLock',
        ];
    }

    public function preAddLock(DataObjectEvent $dataObjectEvent): void
    {
        $object = $dataObjectEvent->getObject();

        if (!$object instanceof OptimisticLockedInterface) {
            return;
        }

        if (!$object instanceof Concrete) {
            return;
        }

        /**
         * @var Concrete $object
         * @var OptimisticLockedInterface $object
         */
        $object->setOptimisticLockVersion(1);
    }

    public function postUpdateLock(DataObjectEvent $dataObjectEvent): void
    {
        $object = $dataObjectEvent->getObject();

        if (!$object instanceof OptimisticLockedInterface) {
            return;
        }

        if (!$object instanceof Concrete) {
            return;
        }

        /**
         * @var Concrete $object
         */
        $this->lockManager->updateLock($object);
    }

    public function checkLock(DataObjectEvent $dataObjectEvent): void
    {
        $object = $dataObjectEvent->getObject();

        if (!$object instanceof OptimisticLockedInterface) {
            return;
        }

        if (!$object instanceof Concrete) {
            return;
        }

        $this->ensureVersionMatch($object);

        /**
         * @var Concrete $object
         * @var OptimisticLockedInterface $object
         */
        $object->setOptimisticLockVersion(($object->getOptimisticLockVersion() ?? 1) + 1);
    }

    private function ensureVersionMatch(Concrete $object): void
    {
        if (!$object instanceof OptimisticLockedInterface) {
            return;
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->from('object_store_' . $object->getClassId())
            ->select('optimisticLockVersion')
            ->where('oo_id = :id')
            ->setMaxResults(1)
            ->setParameter('id', $object->getId())
        ;

        $currentVersion = (int) $this->connection->fetchOne($queryBuilder->getSQL(), $queryBuilder->getParameters());

        if ($currentVersion === $object->getOptimisticLockVersion()) {
            return;
        }

        throw OptimisticLockException::lockFailedVersionMismatch(
            $object,
            $object->getOptimisticLockVersion(),
            $currentVersion,
        );
    }
}
