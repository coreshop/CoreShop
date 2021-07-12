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
