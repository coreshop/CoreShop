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

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Twig;

use CoreShop\Component\ProductQuantityPriceRules\Detector\QuantityReferenceDetectorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Exception\NoRuleFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use Doctrine\Common\Collections\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductQuantityPriceRuleRangesExtension extends AbstractExtension
{
    public function __construct(
        protected QuantityReferenceDetectorInterface $quantityReferenceDetector,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('coreshop_quantity_price_rule_ranges_available', [$this, 'hasActiveQuantityPriceRuleRanges']),
            new TwigFunction('coreshop_quantity_price_rule', [$this->quantityReferenceDetector, 'detectRule']),
            new TwigFunction('coreshop_quantity_price_rule_ranges', [$this, 'getQuantityPriceRuleRanges']),
        ];
    }

    public function hasActiveQuantityPriceRuleRanges(QuantityRangePriceAwareInterface $product, array $context): bool
    {
        try {
            $this->quantityReferenceDetector->detectRule($product, $context);
        } catch (NoRuleFoundException) {
            return false;
        }

        return true;
    }

    public function getQuantityPriceRuleRanges(QuantityRangePriceAwareInterface $product, array $context): Collection|array
    {
        $productQuantityPriceRule = $this->quantityReferenceDetector->detectRule($product, $context);

        return $productQuantityPriceRule->getRanges();
    }
}
