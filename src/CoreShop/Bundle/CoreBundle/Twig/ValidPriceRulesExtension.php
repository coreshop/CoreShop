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

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Bundle\CoreBundle\Templating\Helper\ValidPriceRulesHelperInterface;

final class ValidPriceRulesExtension extends \Twig_Extension
{
    /**
     * @var ValidPriceRulesHelperInterface
     */
    private $helper;

    /**
     * @param ValidPriceRulesHelperInterface $helper
     */
    public function __construct(ValidPriceRulesHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_Filter('coreshop_product_price_rules', [$this->helper, 'getValidRules']),
        ];
    }
}
