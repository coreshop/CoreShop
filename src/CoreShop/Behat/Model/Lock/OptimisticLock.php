<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

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

    public function setOptimisticLockVersion(?int $optimisticLockVersion)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setUser($user)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
