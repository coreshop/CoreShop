<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\Recyclebin\Item;
use Webmozart\Assert\Assert;

final class RecycleBinContext implements Context
{
    private SharedStorageInterface $sharedStorage;

    public function __construct(SharedStorageInterface $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Then /^I recycle the (product "[^"]+")$/
     * @Then /^I recycle the (product)$/
     */
    public function IAddTheObjectToTheBin(Concrete $concrete): void
    {
        /**
         * @var Item $item
         */
        $item = new Item();
        $item->setElement($concrete);
        $item->save();

        $concrete->delete();

        $this->sharedStorage->set(
            'data_object_recycle_' . $concrete->getId(),
            $item->getId()
        );
    }

    /**
     * @Then /^I restore the recycled (product "[^"]+")$/
     * @Then /^I restore the recycled (product)$/
     */
    public function iRestoreTheRecycledProduct(Concrete $concrete): void
    {
        $key = 'data_object_recycle_' . $concrete->getId();

        /**
         * @var Item $item
         */
        $item = Item::getById($this->sharedStorage->get($key));

        Assert::isInstanceOf($item, Item::class);

        $item->restore();

        $product = DataObject::getById($concrete->getId(), true);


        Assert::isInstanceOf($product, ProductInterface::class);

        //Force reload of restored product
        $this->sharedStorage->set('product', $product);
    }
}
