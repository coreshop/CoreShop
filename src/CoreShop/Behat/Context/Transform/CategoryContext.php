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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
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
     * @param SharedStorageInterface      $sharedStorage
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Transform /^category(?:|s) "([^"]+)"$/
     */
    public function getCategoryByName($categoryName)
    {
        /**
         * @var \Pimcore\Model\DataObject\Listing\Concrete $list
         */
        $list = $this->categoryRepository->getList();
        $list->setLocale('en');
        $list->setCondition('name = ?', [$categoryName]);
        $list->load();

        Assert::eq(
            count($list->getObjects()),
            1,
            sprintf('%d categories has been found with name "%s".', count($list->getObjects()), $categoryName)
        );

        return reset($list->getObjects());
    }

    /**
     * @Transform /^categories "([^"]+)", "([^"]+)"$/
     */
    public function getCategoriesByName($category1, $category2)
    {
        $categories = [];

        foreach ([$category1, $category2] as $cat) {
            $categories[] = $this->getCategoryByName($cat);
        }

        return $categories;
    }

    /**
     * @Transform /^category$/
     */
    public function category()
    {
        return $this->sharedStorage->get('category');
    }
}
