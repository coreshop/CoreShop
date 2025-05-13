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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\ProductQuantityPriceRules\Model\ProductQuantityPriceRule as BaseProductQuantityPriceRule;
use CoreShop\Component\ProductQuantityPriceRules\Model\QuantityRangeInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @psalm-suppress MissingConstructor
 */
class ProductQuantityPriceRule extends BaseProductQuantityPriceRule implements ProductQuantityPriceRuleInterface
{
    /**
     * @var ArrayCollection|QuantityRangeInterface[]
     */
    protected $ranges;

    public function __construct(
        ) {
        parent::__construct();

        $this->ranges = new ArrayCollection();
    }
}
