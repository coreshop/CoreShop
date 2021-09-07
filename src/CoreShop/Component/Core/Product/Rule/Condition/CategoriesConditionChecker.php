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

namespace CoreShop\Component\Core\Product\Rule\Condition;

use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use CoreShop\Component\Core\Rule\Condition\CategoriesConditionCheckerTrait;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Webmozart\Assert\Assert;

final class CategoriesConditionChecker implements ConditionCheckerInterface
{
    use CategoriesConditionCheckerTrait {
        CategoriesConditionCheckerTrait::__construct as private __traitConstruct;
    }

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->__traitConstruct($categoryRepository);
    }

    public function isValid(
        ResourceInterface $subject,
        RuleInterface $rule,
        array $configuration,
        array $params = []
    ): bool {
        Assert::keyExists($params, 'store');
        Assert::isInstanceOf($params['store'], StoreInterface::class);

        /**
         * @var $subject ProductInterface
         */
        Assert::isInstanceOf($subject, ProductInterface::class);

        $categoryIdsToCheck = $this->getCategoriesToCheck($configuration['categories'], $params['store'],
            $configuration['recursive'] ?: false);

        if (!is_array($subject->getCategories())) {
            return false;
        }

        foreach ($subject->getCategories() as $category) {
            if ($category instanceof ResourceInterface) {
                if (in_array($category->getId(), $categoryIdsToCheck)) {
                    return true;
                }
            }
        }

        return false;
    }
}
