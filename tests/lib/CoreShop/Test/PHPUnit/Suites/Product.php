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

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Component\Core\Product\TaxedProductPriceCalculatorInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Product extends Base
{
    /**
     * Test Product Creation.
     */
    public function testProductCreation()
    {
        $this->printTestName();

        $this->assertNotNull(Data::$product1);
    }

    /**
     * Test Product Price.
     */
    public function testProductPrice()
    {
        $this->printTestName();

        $this->assertEquals(1800, $this->getPriceCalculator()->getPrice(Data::$product1, $this->getContext()));
        $this->assertEquals(1500, $this->getPriceCalculator()->getPrice(Data::$product1, $this->getContext(), false));
    }

    public function testProductPriceGross()
    {
        $this->printTestName();

        self::get('coreshop.context.store.fixed')->setStore(Data::$storeGrossPrices);

        $this->assertEquals(1800, $this->getPriceCalculator()->getPrice(Data::$product1, $this->getContext()));
        $this->assertEquals(1500, $this->getPriceCalculator()->getPrice(Data::$product1, $this->getContext(), false));

        self::get('coreshop.context.store.fixed')->setStore(Data::$store);
    }

    /**
     * @return TaxedProductPriceCalculatorInterface
     */
    private function getPriceCalculator()
    {
        return $this->get('coreshop.product.taxed_price_calculator');
    }

    /**
     * @return array
     */
    protected function getContext()
    {
        return [
            'store' => $this->get('coreshop.context.shopper')->getStore(),
            'customer' => $this->get('coreshop.context.shopper')->hasCustomer() ? $this->get('coreshop.context.shopper')->getCustomer() : null,
            'currency' => $this->get('coreshop.context.shopper')->getCurrency(),
            'country' => $this->get('coreshop.context.shopper')->getCountry(),
            'cart' => $this->get('coreshop.context.shopper')->getCart()
        ];
    }
}
