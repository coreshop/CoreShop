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
 *
*/

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
