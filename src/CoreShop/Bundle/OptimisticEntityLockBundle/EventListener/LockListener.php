<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OptimisticEntityLockBundle\EventListener;

use CoreShop\Bundle\OptimisticEntityLockBundle\Exception\OptimisticLockException;
use CoreShop\Bundle\OptimisticEntityLockBundle\Manager\EntityLockManager;
use CoreShop\Bundle\OptimisticEntityLockBundle\Model\OptimisticLockedInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\LockMode;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LockListener implements EventSubscriberInterface
{
    protected $lockManager;
    protected $connection;

    public function __construct(EntityLockManager $lockManager, Connection $connection)
    {
        $this->lockManager = $lockManager;
        $this->connection = $connection;
    }

    public static function getSubscribedEvents()
    {
        return [
            DataObjectEvents::PRE_ADD => 'preAddLock',
            DataObjectEvents::PRE_UPDATE => 'checkLock',
            DataObjectEvents::POST_UPDATE => 'postUpdateLock',
        ];
    }

    public function preAddLock(DataObjectEvent $dataObjectEvent)
    {
        $object = $dataObjectEvent->getObject();

        if (!$object instanceof OptimisticLockedInterface) {
            return;
        }

        if (!$object instanceof Concrete) {
            return;
        }

        $object->setOptimisticLockVersion(1);
    }

    public function postUpdateLock(DataObjectEvent $dataObjectEvent)
    {
        $object = $dataObjectEvent->getObject();

        if (!$object instanceof OptimisticLockedInterface) {
            return;
        }

        if (!$object instanceof Concrete) {
            return;
        }

        $this->lockManager->updateLock($object);
    }

    public function checkLock(DataObjectEvent $dataObjectEvent)
    {
        $object = $dataObjectEvent->getObject();

        if (!$object instanceof OptimisticLockedInterface) {
            return;
        }

        if (!$object instanceof Concrete) {
            return;
        }

        $this->ensureVersionMatch($object);
        $object->setOptimisticLockVersion(($object->getOptimisticLockVersion() ?? 1) + 1);
    }

    private function ensureVersionMatch(Concrete $object)
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

        $stmt = $queryBuilder->execute();
        $currentVersion = (int)$stmt->fetchOne();

        echo 'Object Lock: ' . $object->getOptimisticLockVersion() . PHP_EOL;
        echo 'Current Lock: ' . $currentVersion . PHP_EOL;

        if ($currentVersion === $object->getOptimisticLockVersion()) {
            return;
        }

        throw OptimisticLockException::lockFailedVersionMismatch(
            $object,
            $object->getOptimisticLockVersion(),
            $currentVersion
        );
    }
}
