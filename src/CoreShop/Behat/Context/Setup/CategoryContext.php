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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\Folder;

final class CategoryContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $categoryFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @param SharedStorageInterface      $sharedStorage
     * @param FactoryInterface            $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(SharedStorageInterface $sharedStorage, FactoryInterface $categoryFactory, CategoryRepositoryInterface $categoryRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Given /^the site has a category "([^"]+)"$/
     */
    public function theSiteHasACategory(string $categoryName)
    {
        $category = $this->createCategory($categoryName);

        $this->saveCategory($category);
    }

    /**
     * @Given /^the site has two categories "([^"]+)" and "([^"]+)"$/
     */
    public function theSiteHasTwoCategories(string $categoryName, string $categoryName2)
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
    public function categoryIsChildOfAnotherCategory(CategoryInterface $child, CategoryInterface $parent)
    {
        $child->setParent($parent);

        $this->saveCategory($child);
    }

    /**
     * @param string              $categoryName
     * @param StoreInterface|null $store
     *
     * @return CategoryInterface
     */
    private function createCategory(string $categoryName, StoreInterface $store = null)
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

    /**
     * @param CategoryInterface $category
     */
    private function saveCategory(CategoryInterface $category)
    {
        $category->save();
        $this->sharedStorage->set('category', $category);
    }
}
