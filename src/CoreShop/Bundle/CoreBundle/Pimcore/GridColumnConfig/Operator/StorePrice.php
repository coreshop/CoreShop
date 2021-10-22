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

namespace CoreShop\Bundle\CoreBundle\Pimcore\GridColumnConfig\Operator;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\AbstractOperator;

class StorePrice extends AbstractOperator
{
    private int $storeId;

    public function __construct(
        private StoreRepositoryInterface $storeRepository,
        private MoneyFormatterInterface $moneyFormatter,
        \stdClass $config,
        $context = null
    ) {
        parent::__construct($config, $context);
        $this->storeId = $config->storeId;
    }

    /**
     * @param \Pimcore\Model\Element\ElementInterface $element
     *
     * @return null|\stdClass|string
     */
    public function getLabeledValue($element)
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

        $price = $element->getStoreValuesOfType('price', $store);

        $result->value = $this->moneyFormatter->format($price, $store->getCurrency()->getIsoCode());

        return $result;
    }
}
