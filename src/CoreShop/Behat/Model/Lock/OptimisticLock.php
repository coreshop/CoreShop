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

namespace CoreShop\Behat\Model\Lock;

use CoreShop\Bundle\OptimisticEntityLockBundle\Model\OptimisticLockedInterface;
use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

class OptimisticLock extends AbstractPimcoreModel implements OptimisticLockedInterface
{
    public function getOptimisticLockVersion(): ?int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /** @noinspection ReturnTypeCanBeDeclaredInspection */
    public function setOptimisticLockVersion(?int $optimisticLockVersion)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @inheritdoc
     */
    public function getUser(): void
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * @inheritdoc
     */
    public function setUser($user): void
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
