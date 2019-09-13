<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Core\Model\CartItemInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Order\Model\CartInterface;
use Webmozart\Assert\Assert;

trait CoreSaleCreationTrait
{
    /**
     * @var ViewHandlerInterface
     */
    protected $viewHandler;

    protected function prepareCartItem(CartInterface $cart, CartItemInterface $item)
    {
        $itemFlat = parent::prepareCartItem($cart, $item);

        $units = [];

        if ($item->getProduct() instanceof ProductInterface && $item->getProduct()->hasUnitDefinitions()) {
            foreach ($item->getProduct()->getUnitDefinitions()->getUnitDefinitions() as $unitDefinition) {
                $units[] = [
                    'id' => $unitDefinition->getId(),
                    'name' => $unitDefinition->getUnitName(),
                ];
            }
        }

        $itemFlat['unitDefinition'] = $item->getUnitDefinition() ? $item->getUnitDefinition()->getId() : null;
        $itemFlat['unitDefinitionRecord'] = $item->getUnitDefinition() ? [
            'id' => $item->getUnitDefinition()->getId(),
            'name' => $item->getUnitDefinition()->getUnitName(),
        ] : null;
        $itemFlat['units'] = $units;

        return $itemFlat;
    }

    protected function getCartDetails(CartInterface $cart)
    {
        $cartDetails = parent::getCartDetails($cart);

        $cartDetails['carriers'] = $this->getCarrierDetails($cart);

        return $cartDetails;
    }

    public function getCarrierDetails(CartInterface $cart)
    {
        if (null === $cart->getShippingAddress()) {
            return [];
        }

        $carriers = $this->get('coreshop.carrier.resolver')->resolveCarriers($cart, $cart->getShippingAddress());

        $currentCurrency = $cart->getStore()->getCurrency()->getIsoCode();
        $result = [];

        /**
         * @var CarrierInterface $carrier
         */
        foreach ($carriers as $carrier) {
            $price = $this->get('coreshop.carrier.price_calculator.taxed')->getPrice($carrier, $cart,
                $cart->getShippingAddress());
            $priceConverted = $this->get('coreshop.currency_converter')->convert($price, $currentCurrency,
                $cart->getCurrency()->getIsoCode());

            $result[] = [
                'id' => $carrier->getId(),
                'name' => $carrier->getIdentifier(),
                'price' => $price
            ];
        }

        return $result;
    }

    protected function getCartSummary(CartInterface $cart)
    {
        /**
         * @var \CoreShop\Component\Core\Model\CartInterface $cart
         */
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\CartInterface::class);

        $result = parent::getCartSummary($cart);

        array_splice($result, 3, 0, [
            [
                'key' => 'shipping_without_tax',
                'value' => $cart->getShipping(false),
            ],
            [
                'key' => 'shipping_tax',
                'value' => $cart->getShipping(true) - $cart->getShipping(false),
            ],
            [
                'key' => 'shipping',
                'value' => $cart->getShipping(true),
            ],
        ]);

        return $result;
    }
}
