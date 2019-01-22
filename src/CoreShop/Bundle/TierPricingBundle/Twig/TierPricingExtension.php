<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\TierPricingBundle\Twig;

use CoreShop\Bundle\TierPricingBundle\Templating\Helper\TierPricingHelper;

final class TierPricingExtension extends \Twig_Extension
{
    /**
     * @var TierPricingHelper
     */
    private $helper;

    /**
     * @param TierPricingHelper $helper
     */
    public function __construct(TierPricingHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('coreshop_tier_pricing_available', [$this->helper, 'hasActiveTierPricing']),
            new \Twig_SimpleFunction('coreshop_get_tier_price_ranges', [$this->helper, 'getTierPriceRanges']),
        ];
    }
}
