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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\Models;

use CoreShop\Test\Base;

/**
 * Class Zone
 * @package CoreShop\Test\Models
 */
class Zone extends Base
{
    /**
     * Test Zone Creation
     */
    public function testZoneCreation()
    {
        $this->printTestName();

        $this->assertNotNull(\CoreShop\Model\Zone::getById(1));
    }
}
