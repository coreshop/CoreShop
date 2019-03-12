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

use CoreShop\Bundle\CoreBundle\Templating\Helper\ProductUnitDefinitionsHelper;

final class ProductUnitDefinitionsExtension extends \Twig_Extension
{
    /**
     * @var ProductUnitDefinitionsHelper
     */
    private $helper;

    /**
     * @param ProductUnitDefinitionsHelper $helper
     */
    public function __construct(ProductUnitDefinitionsHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('coreshop_product_unit_definitions_available', [$this->helper, 'hasAvailableUnitDefinitions']),
            new \Twig_SimpleFunction('coreshop_product_additional_unit_definitions_with_prices', [$this->helper, 'getAdditionalUnitDefinitionsWithPrices']),
        ];
    }
}
