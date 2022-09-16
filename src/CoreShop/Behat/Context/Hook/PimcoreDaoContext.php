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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Context\Hook;

use CoreShop\Bundle\TestBundle\Context\Hook\PimcoreDaoContext as BasePimcoreDaContext;
use CoreShop\Component\Order\Repository\OrderRepositoryInterface;
use Doctrine\DBAL\Connection;
use Pimcore\Cache;
use Pimcore\Model\DataObject\Listing;
use Symfony\Component\HttpKernel\KernelInterface;

final class PimcoreDaoContext extends BasePimcoreDaContext
{
    public function __construct(
        private KernelInterface $kernel,
        private OrderRepositoryInterface $orderRepository,
        private Connection $connection,
    ) {
        parent::__construct($this->kernel, $this->connection);
    }

    /**
     * @BeforeScenario
     */
    public function purgeObjects(): void
    {
        /**
         * Delete Orders first, otherwise the CustomerDeletionListener would trigger.
         *
         * @var Listing $list
         */
        $list = $this->orderRepository->getList();
        $list->setUnpublished(true);
        $list->load();

        foreach ($list->getObjects() as $obj) {
            $obj->delete();
        }

        parent::purgeObjects();
    }
}
