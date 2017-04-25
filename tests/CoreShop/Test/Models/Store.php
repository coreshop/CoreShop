<?php

namespace CoreShop\Test\Models;

use CoreShop\Test\Base;

class Store extends Base
{
    /**
     * Test Shop Creation
     */
    public function testStandardStoreCreation()
    {
        $this->printTestName();

        $this->assertNotNull($this->getRepository('store')->findStandard());
    }

    public function testStoreCreation() {
        $this->printTodoTestName();
        //TODO
    }

    public function testStoreContext() {
        $this->printTodoTestName();
        //TODO
    }
}
