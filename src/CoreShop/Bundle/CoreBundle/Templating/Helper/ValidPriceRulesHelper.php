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

namespace CoreShop\Bundle\CoreBundle\Templating\Helper;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Rule\Fetcher\ValidRulesFetcherInterface;
use Symfony\Component\Templating\Helper\Helper;

class ValidPriceRulesHelper extends Helper implements ValidPriceRulesHelperInterface
{
    /**
     * @var ValidRulesFetcherInterface
     */
    protected $validPriceRulesFetcher;

    /**
     * @var ShopperContextInterface
     */
    protected $shopperContext;

    /**
     * @param ValidRulesFetcherInterface $validPriceRulesFetcher
     * @param ShopperContextInterface    $shopperContext
     */
    public function __construct(ValidRulesFetcherInterface $validPriceRulesFetcher, ShopperContextInterface $shopperContext)
    {
        $this->validPriceRulesFetcher = $validPriceRulesFetcher;
        $this->shopperContext = $shopperContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidRules(ProductInterface $product, array $context = [])
    {
        if (empty($context)) {
            $context = $this->shopperContext->getContext();

            @trigger_error(
                'Calling getValidRules without a context is deprecated since 2.1.0 and will be removed with 2.2.0',
                E_USER_DEPRECATED
            );
        }

        return $this->validPriceRulesFetcher->getValidRules($product, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_product_price_rules';
    }
}
