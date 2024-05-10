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

namespace CoreShop\Bundle\CoreBundle\Pimcore\GridColumnConfig\Operator;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\Bundle\AdminBundle\DataObject\GridColumnConfig\Operator\AbstractOperator;
use Pimcore\Bundle\AdminBundle\DataObject\GridColumnConfig\ResultContainer;
use Pimcore\Model\Element\ElementInterface;

class StorePrice extends AbstractOperator
{
    private int $storeId;

    public function __construct(
        private StoreRepositoryInterface $storeRepository,
        private MoneyFormatterInterface $moneyFormatter,
        \stdClass $config,
        $context = null,
    ) {
        parent::__construct($config, $context);
        $this->storeId = $config->storeId;
    }

    public function getLabeledValue(array|ElementInterface $element): ResultContainer|\stdClass|null
    {
        $result = new \stdClass();
        $result->label = $this->label;

        /**
         * @var StoreInterface $store
         */
        $store = $this->storeRepository->find($this->storeId);

        if (!$element instanceof ProductInterface) {
            return $result;
        }

        $price = $element->getStoreValuesOfType('price', $store) ?? 0;

        $result->value = $this->moneyFormatter->format($price, $store->getCurrency()->getIsoCode());

        return $result;
    }
}
