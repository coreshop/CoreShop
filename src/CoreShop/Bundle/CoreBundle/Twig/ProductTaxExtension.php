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

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Bundle\CoreBundle\Templating\Helper\ProductTaxHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ProductTaxExtension extends AbstractExtension
{
    /**
     * @var ProductTaxHelperInterface
     */
    private $productTaxHelper;

    /**
     * @param ProductTaxHelperInterface $productTaxHelper
     */
    public function __construct(ProductTaxHelperInterface $productTaxHelper)
    {
        $this->productTaxHelper = $productTaxHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('coreshop_product_tax_amount', [$this->productTaxHelper, 'getTaxAmount']),
            new TwigFilter('coreshop_product_tax_rate', [$this->productTaxHelper, 'getTaxRate']),
        ];
    }
}
