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

namespace CoreShop\Bundle\OptimisticEntityLockBundle\Manager;

use CoreShop\Bundle\OptimisticEntityLockBundle\Exception\OptimisticLockException;
use CoreShop\Bundle\OptimisticEntityLockBundle\Model\OptimisticLockedInterface;
use Pimcore\Model\DataObject\Concrete;

final class EntityLockManager implements EntityLockManagerInterface
{
    private array $lockedVersions = [];

    public function lock(Concrete $dataObject, $lockVersion = null): void
    {
        if (!$dataObject instanceof OptimisticLockedInterface) {
            throw OptimisticLockException::notVersioned($dataObject::class);
        }

        if ($lockVersion === null) {
            return;
        }

        if (!isset($this->lockedVersions[$dataObject->getId()])) {
            $actualObject = Concrete::getById($dataObject->getId(), ['force' => true]);

            if (!$actualObject instanceof OptimisticLockedInterface) {
                throw OptimisticLockException::notVersioned($dataObject::class);
            }

            $this->lockedVersions[$dataObject->getId()] = $actualObject;
        }

        $entityVersion = $this->lockedVersions[$dataObject->getId()];

        if (null === $entityVersion) {
            return;
        }

        $entityVersionLock = $entityVersion->getOptimisticLockVersion() ?? 1;

        if ($entityVersionLock !== $lockVersion) {
            throw OptimisticLockException::lockFailedVersionMismatch($dataObject, $lockVersion, $entityVersionLock);
        }
    }

    /**
     * @internal
     */
    public function updateLock(Concrete $concrete): void
    {
        $this->lockedVersions[$concrete->getId()] = $concrete;
    }
}
