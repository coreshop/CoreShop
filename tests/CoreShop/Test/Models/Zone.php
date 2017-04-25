<?php

namespace CoreShop\Test\Models;

use CoreShop\Component\Address\Model\ZoneInterface;
use CoreShop\Test\Base;

class Zone extends Base
{
    /**
     * Test Zone Creation
     */
    public function testZoneCreation()
    {
        $this->printTestName();

        /**
         * @var $zone ZoneInterface
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
