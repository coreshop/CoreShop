<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Test\PHPUnit\Suites;

use CoreShop\Component\Core\Model\ConfigurationInterface;
use CoreShop\Test\Base;

class Configuration extends Base
{
    /**
     * Test Configuration.
     */
    public function testConfiguration()
    {
        $this->printTestName();

        /**
         * @var ConfigurationInterface
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
