<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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
            $actualObject = Concrete::getById($dataObject->getId(), true);

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
