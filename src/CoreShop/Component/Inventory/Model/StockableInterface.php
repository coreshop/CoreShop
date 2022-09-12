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

namespace CoreShop\Component\Inventory\Model;

interface StockableInterface
{
    public function getInventoryName(): ?string;

    public function isInStock(): bool;

    public function getOnHold(): ?int;

    public function setOnHold(?int $onHold);

    public function getOnHand(): ?int;

    public function setOnHand(?int $onHand);

    public function getIsTracked(): ?bool;

    public function setIsTracked(?bool $tracked);
}
