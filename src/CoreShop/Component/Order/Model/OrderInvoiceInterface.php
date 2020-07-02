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

interface OrderInvoiceInterface extends OrderDocumentInterface, AdjustableInterface, ConvertedAdjustableInterface
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
    public function getConvertedDiscount(bool $withTax = true): int;

    /**
     * @return int
     */
    public function getConvertedDiscountTax(): int;

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getConvertedShipping(bool $withTax = true): int;

    /**
     * @return int
     */
    public function getConvertedShippingTax(): int;

    /**
     * @return int
     */
    public function getConvertedTotalTax(): int;

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getConvertedTotal(bool $withTax = true);

    /**
     * @param int  $convertedTotal
     * @param bool $withTax
     */
    public function setConvertedTotal(int $convertedTotal, bool $withTax = true);

    /**
     * @return int
     */
    public function getConvertedSubtotalTax(): int;

    /**
     * @param bool $withTax
     *
     * @return int
     */
    public function getConvertedSubtotal(bool $withTax = true): int;

    /**
     * @param int  $convertedSubtotal
     * @param bool $withTax
     */
    public function setConvertedSubtotal(int $convertedSubtotal, bool $withTax = true);
}
