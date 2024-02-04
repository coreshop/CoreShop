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

namespace CoreShop\Bundle\CoreBundle\Pimcore\GridColumnConfig\Operator\Factory;

use CoreShop\Bundle\CoreBundle\Pimcore\GridColumnConfig\Operator\StorePrice;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\Bundle\AdminBundle\DataObject\GridColumnConfig\Operator\Factory\OperatorFactoryInterface;

final class StorePriceFactory implements OperatorFactoryInterface
{
    public function __construct(
        private StoreRepositoryInterface $storeRepository,
        private MoneyFormatterInterface $moneyFormatter,
    ) {
    }

    public function build(\stdClass $configElement, $context = null): StorePrice
    {
        return new StorePrice($this->storeRepository, $this->moneyFormatter, $configElement, $context);
    }
}
