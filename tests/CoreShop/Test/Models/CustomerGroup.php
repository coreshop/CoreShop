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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\Models;

use CoreShop\Test\Base;

class CustomerGroup extends Base
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testCustomerGroupCreation() {
        $this->printTestName();

        $group = new \CoreShop\Model\CustomerGroup();
        $group->setName("test");
        $group->setDiscount(0);
        $group->save();

        $this->assertNotNull(\CoreShop\Model\CustomerGroup::getById($group->getId()));
    }

    public function testCustomerGroupDiscount() {
        //@TODO: Customer Group Discount needs to be implemented first?
    }
}
