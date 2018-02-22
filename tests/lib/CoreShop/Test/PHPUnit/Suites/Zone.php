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

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Test\Base;

class Zone extends Base
{
    /**
     * Test Zone Creation.
     */
    public function testZoneCreation()
    {
        $this->printTestName();

        /**
         * @var ZoneInterface
         */
        $zone = $this->getFactory('zone')->createNew();
        $zone->setName('test');
        $zone->setActive(true);

        $this->getEntityManager()->persist($zone);
        $this->getEntityManager()->flush();

        $this->assertNotNull($zone->getId());

        $this->getEntityManager()->remove($zone);
        $this->getEntityManager()->flush();

        $this->assertNull($zone->getId());
    }
}
