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

namespace CoreShop\Component\Order\Model;

use Carbon\Carbon;

interface OrderInvoiceInterface extends
    OrderDocumentInterface,
    AdjustableInterface,
    ConvertedAdjustableInterface
{
    public function getInvoiceDate(): ?Carbon;

    public function setInvoiceDate(?Carbon $invoiceDate);

    public function getInvoiceNumber(): ?string;

    public function setInvoiceNumber(?string $invoiceNumber);

    public function getTotal(bool $withTax = true): int;

    public function setTotal(int $total, bool $withTax = true);

    public function getTotalTax(): int;

    public function getSubtotalTax(): int;

    public function getSubtotal(bool $withTax = true): int;

    public function setSubtotal(int $subtotal, bool $withTax = true);

    public function getConvertedTotal(bool $withTax = true);

    public function setConvertedTotal(int $convertedTotal, bool $withTax = true);

    public function getConvertedSubtotalTax(): int;

    public function getConvertedSubtotal(bool $withTax = true): int;

    public function setConvertedSubtotal(int $convertedSubtotal, bool $withTax = true);
}
