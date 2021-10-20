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

namespace CoreShop\Bundle\ProductQuantityPriceRulesBundle\Twig;

use CoreShop\Component\ProductQuantityPriceRules\Detector\QuantityReferenceDetectorInterface;
use CoreShop\Component\ProductQuantityPriceRules\Exception\NoRuleFoundException;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangePriceAwareInterface;
use Doctrine\Common\Collections\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductQuantityPriceRuleRangesExtension extends AbstractExtension
{
    public function __construct(protected QuantityReferenceDetectorInterface $quantityReferenceDetector)
    {
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
