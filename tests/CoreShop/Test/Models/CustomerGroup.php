<?php

namespace CoreShop\Test\Models;

use CoreShop\Component\Customer\Model\CustomerGroupInterface;
use CoreShop\Test\Base;
use Pimcore\Model\Object\Service;

class CustomerGroup extends Base
{
    public function testCustomerGroupCreation() {
        $this->printTestName();

        /**
         * @var $customerGroup CustomerGroupInterface
         */
        $customerGroup = $this->getFactory('customer_group')->createNew();

        $customerGroup->setName('TestGroup');
        $customerGroup->setKey('test-group');
        $customerGroup->setParent(Service::createFolderByPath("/"));
        $customerGroup->save();

        $this->assertNotNull($customerGroup->getId());
    }
}
