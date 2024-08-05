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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Variant\Event;

use CoreShop\Component\Core\Model\ProductInterface;
use Symfony\Contracts\EventDispatcher\Event;

class variantAvailabilityEvent extends Event
{
    private bool $conditionMet = true;

    public function __construct(private ProductInterface $product)
    {
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function setConditionMet(bool $conditionMet): void
    {
        $this->conditionMet = $conditionMet;
    }

    public function isConditionMet(): bool
    {
        return $this->conditionMet;
    }
}