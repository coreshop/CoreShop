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

interface ProductSpecificPriceRuleInterface extends PriceRuleInterface
{
    /**
     * @return bool
     */
    public function getInherit();

    /**
     * @param bool $inherit
     */
    public function setInherit($inherit);

    /**
     * @return int
     */
    public function getProduct();

    /**
     * @param int $product
     */
    public function setProduct($product);
}
