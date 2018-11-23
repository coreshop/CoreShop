<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Pimcore\GridColumnConfig\Operator;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Currency\Formatter\MoneyFormatterInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\DataObject\GridColumnConfig\Operator\AbstractOperator;

class StorePrice extends AbstractOperator
{
    /**
     * @var int
     */
    private $storeId;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var MoneyFormatterInterface
     */
    private $moneyFormatter;

    /**
     * @param StoreRepositoryInterface $storeRepository
     * @param MoneyFormatterInterface  $moneyFormatter
     * @param \stdClass                $config
     * @param null                     $context
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        MoneyFormatterInterface $moneyFormatter,
        \stdClass $config,
        $context = null
    ) {
        parent::__construct($config, $context);

        $this->storeRepository = $storeRepository;
        $this->moneyFormatter = $moneyFormatter;
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

        $price = $element->getStorePrice($store);

        $result->value = $this->moneyFormatter->format($price, $store->getCurrency()->getIsoCode());

        return $result;
    }
}
