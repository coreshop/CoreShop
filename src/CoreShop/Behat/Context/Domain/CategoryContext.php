<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use Webmozart\Assert\Assert;

final class CategoryContext implements Context
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    ) {
    }

    /**
     * @Then /^there should be a category "([^"]+)"$/
     */
    public function thereShouldBeACategoryNamed($name): void
    {
        $categories = $this->categoryRepository->findBy(['name' => $name]);

        Assert::eq(
            count($categories),
            1,
            sprintf('%d categories has been found with name "%s".', count($categories), $name),
        );
    }

    /**
     * @Then /^the (category "[^"]+") should be child of (category "[^"]+")$/
     */
    public function theCategoryShouldBeChildOfCategory(CategoryInterface $child, CategoryInterface $parent): void
    {
        Assert::eq(
            $child->getParent()->getId(),
            $parent->getId(),
            sprintf('%d should have the same id as the assumed parent %d', $child->getParent()->getId(), $parent->getId()),
        );
    }

    /**
     * @Then /^the (product "[^"]+") should be in (category "[^"]+")$/
     */
    public function theProductShouldBeInCategory(ProductInterface $product, CategoryInterface $category): void
    {
        Assert::oneOf($category, $product->getCategories());
    }
}
