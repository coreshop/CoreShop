<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param SharedStorageInterface     $sharedStorage
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductRepositoryInterface $productRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
    }

    /**
     * @Transform /^product(?:|s) "([^"]+)"$/
     * @Transform /^"([^"]+)" product(?:|s)$/
     */
    public function getProductByName($productName)
    {
        /**
         * @var \Pimcore\Model\DataObject\Listing\Concrete $list
         */
        $list = $this->productRepository->getList();
        $list->setLocale('en');
        $list->setCondition('name = ?', [$productName]);
        $list->load();

        Assert::eq(
            count($list->getObjects()),
            1,
            sprintf('%d products has been found with name "%s".', count($list->getObjects()), $productName)
        );

        $product = \reset($list->getObjects());

        //This is to not run into cache issues
        return $this->productRepository->forceFind($product->getId());
    }

    /**
     * @Transform /^products "([^"]+)", "([^"]+)"$/
     */
    public function getProductsByName($product1, $product2)
    {
        $products = [];

        foreach ([$product1, $product2] as $cat) {
            $products[] = $this->getProductByName($cat);
        }

        return $products;
    }

    /**
     * @Transform /^product/
     */
    public function product()
    {
        return $this->sharedStorage->get('product');
    }
}
