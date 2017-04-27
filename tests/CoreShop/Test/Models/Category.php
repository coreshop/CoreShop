<?php

namespace CoreShop\Test\Models;

use CoreShop\Component\Product\Model\CategoryInterface;
use CoreShop\Test\Base;
use Pimcore\Model\Object\Service;

class Category extends Base
{
    public function testCategoryCreation() {
        $this->printTestName();

        /**
         * @var $category CategoryInterface
         */
        $category = $this->getFactory('category')->createNew();
        $category->setName('test');
        $category->setPublished(true);
        $category->setKey('test-category');
        $category->setParent(Service::createFolderByPath('/'));
        $category->save();

        $this->assertNotNull($category->getid());
    }
}
