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

namespace CoreShop\Component\Product\Model;

/**
 * @psalm-suppress MissingConstructor
 */
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

    protected function createTranslation(): ProductSpecificPriceRuleTranslationInterface
    {
        return new ProductSpecificPriceRuleTranslation();
    }
}
