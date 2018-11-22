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

namespace CoreShop\Component\Product\Rule\Fetcher;

use CoreShop\Component\Product\Model\ProductInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class MemoryCachedValidRuleFetcher implements ValidRulesFetcherInterface
{
    /**
     * @var ValidRulesFetcherInterface
     */
    private $validRuleFetcher;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var array
     */
    private $checkedProducts = [];

    /**
     * @param ValidRulesFetcherInterface $validRuleFetcher
     * @param RequestStack               $requestStack
     */
    public function __construct(ValidRulesFetcherInterface $validRuleFetcher, RequestStack $requestStack)
    {
        $this->validRuleFetcher = $validRuleFetcher;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidRules(ProductInterface $product, array $context)
    {
        if ($this->requestStack->getMasterRequest() instanceof Request) {
            if (isset($this->checkedProducts[$product->getId()])) {
                return $this->checkedProducts[$product->getId()];
            }
        }

        $rules = $this->validRuleFetcher->getValidRules($product, $context);

        $this->checkedProducts[$product->getId()] = $rules;

        return $rules;
    }
}
