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
