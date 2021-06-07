<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Product\Model;

class ProductSpecificPriceRule extends AbstractPriceRule implements ProductSpecificPriceRuleInterface
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var int
     */
    protected $product;

    /**
     * @var bool
     */
    protected $inherit = false;

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    public function getInherit()
    {
        return $this->inherit;
    }

    public function setInherit($inherit)
    {
        $this->inherit = $inherit;

        return $this;
    }

    protected function createTranslation()
    {
        return new ProductSpecificPriceRuleTranslation();
    }

    public function __clone()
    {
        $this->id = null;
    }
}
