<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    private $sharedStorage;
    private $productRepository;

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
        $list->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT]);
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
     * @Transform /^product(?:|s) "([^"]+)" with unit "([^"]+)"$/
     */
    public function getProductWithUnitName($productName, $productUnit)
    {
        /**
         * @var ProductInterface $product
         */
        $product = $this->getProductByName($productName);

        foreach ($product->getUnitDefinitions()->getUnitDefinitions() as $unit) {
            if ($unit->getUnit()->getName() === $productUnit) {
                return [
                    'product' => $product,
                    'unit' => $unit,
                ];
            }
        }

        throw new \Exception(sprintf('Unit %s in product %s not found', $productUnit, $productName));
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

    /**
     * @Transform /^variant(?:|s)/
     * @Transform /^variant(?:|s)/
     */
    public function variant()
    {
        return $this->sharedStorage->get('variant');
    }
}
