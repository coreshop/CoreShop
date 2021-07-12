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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Pimcore\GridColumnConfig\Operator\Factory;

use CoreShop\Bundle\CoreBundle\Pimcore\GridColumnConfig\Operator\StorePrice;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\Factory\OperatorFactoryInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\OperatorInterface;

final class StorePriceFactory implements OperatorFactoryInterface
{
    private StoreRepositoryInterface $storeRepository;
    private MoneyFormatterInterface $moneyFormatter;

    public function __construct(StoreRepositoryInterface $storeRepository, MoneyFormatterInterface $moneyFormatter)
    {
        $this->storeRepository = $storeRepository;
        $this->moneyFormatter = $moneyFormatter;
    }

    public function build(\stdClass $configElement, $context = null): OperatorInterface
    {
        return new StorePrice($this->storeRepository, $this->moneyFormatter, $configElement, $context);
    }
}
