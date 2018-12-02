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

use CoreShop\Component\Core\Model\CurrencyInterface;
use CoreShop\Test\Base;
use CoreShop\Test\Data;

class Currency extends Base
{
    /**
     * Test Currency Creation.
     */
    public function testCurrencyCreation()
    {
        $this->printTestName();

        /**
         * @var CurrencyInterface
         */
        $currency = $this->getFactory('currency')->createNew();

        $currency->setName('test-country');
        $currency->setIsoCode('TEC');

        $this->assertNull($currency->getId());

        $this->getEntityManager()->persist($currency);
        $this->getEntityManager()->flush();

        $this->assertNotNull($currency->getId());
    }

    public function testCurrencyContext()
    {
        $this->printTestName();

        $this->assertEquals($this->get('coreshop.context.currency')->getCurrency()->getId(), Data::$store->getCurrency()->getId());
    }
}
