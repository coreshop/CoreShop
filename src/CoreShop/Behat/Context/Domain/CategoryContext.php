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
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use CoreShop\Component\Core\Repository\ProductRepositoryInterface;
use CoreShop\Component\Product\Calculator\ProductPriceCalculatorInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Pimcore\Model\DataObject\Folder;
use Webmozart\Assert\Assert;

final class CategoryContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(SharedStorageInterface $sharedStorage, CategoryRepositoryInterface $categoryRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Then /^there should be a category "([^"]+)"$/
     */
    public function thereShouldBeACategoryNamed($name)
    {
        $categories = $this->categoryRepository->findBy(['name' => $name]);

        Assert::eq(
            count($categories),
            1,
            sprintf('%d categories has been found with name "%s".', count($categories), $name)
        );
    }

    /**
     * @Then /^the (category "[^"]+") should be child of (category "[^"]+")$/
     */
    public function theCategoryShouldBeChildOfCategory(CategoryInterface $child, CategoryInterface $parent) {
        Assert::eq(
            $child->getParent()->getId(),
            $parent->getId(),
            sprintf('%d should have the same id as the assumed parent %d', $child->getParent()->getId(), $parent->getId())
        );
    }

    /**
     * @Then /^the (product "[^"]+") should be in (category "[^"]+")$/
     */
    public function theProductShouldBeInCategory(ProductInterface $product, CategoryInterface $category)
    {
        Assert::oneOf($category, $product->getCategories());
    }
}
