<?php

namespace CoreShop\Test\Models;

use CoreShop\Component\Core\Model\ConfigurationInterface;
use CoreShop\Test\Base;

class Configuration extends Base
{
    /**
     * Test Configuration
     */
    public function testConfiguration()
    {
        $this->printTestName();

        /**
         * @var ConfigurationInterface $config
         */
        $config = $this->getFactory('configuration')->createNew();
        $config->setKey('anyKey');
        $config->setData('data');

        $this->assertNull($config->getId());

        $this->getEntityManager()->persist($config);
        $this->getEntityManager()->flush();

        $this->assertNotNull($config->getId());

        $this->getEntityManager()->remove($config);
        $this->getEntityManager()->flush();

        $this->assertNull($config->getId());
    }
}
