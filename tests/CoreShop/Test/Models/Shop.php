<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\Models;

use CoreShop\Model\Carrier\DeliveryPrice;
use CoreShop\Model\Carrier\RangePrice;
use CoreShop\Model\Carrier\RangeWeight;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Shop extends Base
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testShopCreation()
    {
        $this->printTestName();

        $this->assertNotNull(\CoreShop\Model\Shop::getDefaultShop());
        $this->assertNotNull(\CoreShop\Model\Shop::getById(2));
    }
}
