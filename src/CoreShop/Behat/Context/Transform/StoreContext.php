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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Webmozart\Assert\Assert;

final class StoreContext implements Context
{
    public function __construct(
        private StoreRepositoryInterface $storeRepository,
    ) {
    }

    /**
     * @Transform /^store(?:|s) "([^"]+)"$/
     * @Transform /^store to "([^"]+)"$/
     */
    public function getStoreByName($name): StoreInterface
    {
        /**
         * @var StoreInterface[] $stores
         */
        $stores = $this->storeRepository->findBy(['name' => $name]);

        Assert::eq(
            count($stores),
            1,
            sprintf('%d stores has been found with name "%s".', count($stores), $name),
        );

        return reset($stores);
    }
}
