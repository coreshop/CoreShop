<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Shipping\Model;

interface ShippableInterface
{
    /**
     * @return ShippableItemInterface[]|null
     */
    public function getItems(): ?array;

    public function getWeight(): ?float;

    public function setWeight(?float $weight);

    public function getSubtotal(bool $withTax = true): int;

    public function getTotal(bool $withTax = true): int;

    public function getShipping(bool $withTax = true): int;
}
