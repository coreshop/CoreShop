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

namespace CoreShop\Test\PHPUnit\Suites;


use CoreShop\Component\Order\Model\QuoteInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Quote extends Base
{
    public function testQuoteCreation()
    {
        $this->printTestName();

        /**
         * @var QuoteInterface
         */
        $quote = $this->getFactory('quote')->createNew();

        $this->assertNotNull($quote);
    }

    public function testCartToQuoteTransformer()
    {
        $this->printTestName();

        $cart = Data::createCartWithProducts();
        /**
         * @var $quote QuoteInterface
         */
        $quote = $this->getFactory('quote')->createNew();
        $quote = $this->get('coreshop.order.transformer.cart_to_quote')->transform($cart, $quote);

        $this->assertNotNull($quote);
        $this->assertEquals(28800, $quote->getSubtotal());
    }
}
