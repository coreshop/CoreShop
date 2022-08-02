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

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\Folder;

final class CategoryContext implements Context
{
    public function __construct(private SharedStorageInterface $sharedStorage, private FactoryInterface $categoryFactory)
    {
    }

    /**
     * @Given /^the site has a category "([^"]+)"$/
     */
    public function theSiteHasACategory(string $categoryName): void
    {
        $category = $this->createCategory($categoryName);

        $this->saveCategory($category);
    }

    /**
     * @Given /^the site has two categories "([^"]+)" and "([^"]+)"$/
     */
    public function theSiteHasTwoCategories(string $categoryName, string $categoryName2): void
    {
        $category = $this->createCategory($categoryName);
        $category2 = $this->createCategory($categoryName2);

        $this->saveCategory($category);
        $this->saveCategory($category2);
    }

    /**
     * @Given /^the (category) is child of (category "[^"]+")$/
     * @Given /^the (category "[^"]+") is child of (category "[^"]+")$/
     */
    public function categoryIsChildOfAnotherCategory(CategoryInterface $child, CategoryInterface $parent): void
    {
        $child->setParent($parent);

        $this->saveCategory($child);
    }

    private function createCategory(string $categoryName, StoreInterface $store = null): CategoryInterface
    {
        if (null === $store && $this->sharedStorage->has('store')) {
            $store = $this->sharedStorage->get('store');
        }

        /** @var CategoryInterface $category */
        $category = $this->categoryFactory->createNew();

        $category->setKey(File::getValidFilename($categoryName));
        $category->setParent(Folder::getByPath('/'));
        $category->setName($categoryName, 'en');

        if (null !== $store) {
            $category->setStores([$store->getId()]);
        }

        return $category;
    }

    private function saveCategory(CategoryInterface $category): void
    {
        $category->save();
        $this->sharedStorage->set('category', $category);
    }
}
