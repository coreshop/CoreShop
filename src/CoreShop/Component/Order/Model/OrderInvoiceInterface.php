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

interface OrderInvoiceInterface extends OrderDocumentInterface, AdjustableInterface, BaseAdjustableInterface
{
    /**
     * @return \DateTime
     */
    public function getInvoiceDate();

    /**
     * @param \DateTime $invoiceDate
     */
    public function setInvoiceDate($invoiceDate);

    /**
     * @return string
     */
    public function getInvoiceNumber();

    /**
     * @param string $invoiceNumber
     */
    public function setInvoiceNumber($invoiceNumber);

    /**
     * @return mixed
     */
    public function getPriceRuleItems();

    /**
     * @param mixed $priceRules
     */
    public function setPriceRuleItems($priceRules);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getDiscount(bool $withTax = true): int;

    /**
     * @return int
     */
    public function getDiscountTax(): int;

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getShipping(bool $withTax = true): int;

    /**
     * @return int
     */
    public function getShippingTaxRate();

    /**
     * @param int $shippingTaxRate
     */
    public function setShippingTaxRate($shippingTaxRate);

    /**
     * @return int
     */
    public function getShippingTax(): int;

    /**
     * @return int
     */
    public function getTotalTax(): int;

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getTotal(bool $withTax = true): int;

    /**
     * @param bool $withTax
     * @param int  $total
     */
    public function setTotal(int $total, bool $withTax = true);

    /**
     * @return int
     */
    public function getSubtotalTax(): int;

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getSubtotal(bool $withTax = true): int;

    /**
     * @param int  $subtotal
     * @param bool $withTax
     */
    public function setSubtotal(int $subtotal, bool $withTax = true);

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseDiscount(bool $withTax = true): int;

    /**
     * @return int
     */
    public function getBaseDiscountTax(): int;

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseShipping(bool $withTax = true): int;

    /**
     * @return int
     */
    public function getBaseShippingTax(): int;

    /**
     * @return int
     */
    public function getBaseTotalTax(): int;

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseTotal(bool $withTax = true);

    /**
     * @param int  $baseTotal
     * @param bool $withTax
     */
    public function setBaseTotal(int $baseTotal, bool $withTax = true);

    /**
     * @return int
     */
    public function getBaseSubtotalTax(): int;

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getBaseSubtotal(bool $withTax = true): int;

    /**
     * @param int  $baseSubtotal
     * @param bool $withTax
     */
    public function setBaseSubtotal(int $baseSubtotal, bool $withTax = true);
}
