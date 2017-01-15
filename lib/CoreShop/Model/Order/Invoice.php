<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Order;

use Carbon\Carbon;
use CoreShop\Exception;
use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Model\Configuration;
use CoreShop\Model\Currency;
use CoreShop\Model\Mail\Rule;
use CoreShop\Model\Order;
use CoreShop\Model\TaxCalculator;
use Pimcore\Date;
use Pimcore\Model\Object;

/**
 * Class Invoice
 * @package CoreShop\Model\Order
 *
 * @method static Object\Listing\Concrete getByOrder($value, $limit = 0)
 * @method static Object\Listing\Concrete getByInvoiceDate ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByInvoiceNumber ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByLang ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByCarrier ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByCurrency ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByDiscount ($value, $limit = 0)
 * @method static Object\Listing\Concrete getBySubtotal ($value, $limit = 0)
 * @method static Object\Listing\Concrete getBySubtotalWithoutTax ($value, $limit = 0)
 * @method static Object\Listing\Concrete getBySubtotalTax ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByShipping ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByShippingTaxRate ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByShippingWithoutTax ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByShippingTax ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByPaymentFee ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByPaymentFeeTaxRate ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByPaymentFeeWithoutTax ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByPaymentFeeTax ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTotalTax ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTotal ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTotalPayed ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTaxes ($value, $limit = 0)
 */
class Invoice extends Document
{
    /**
     * @var string
     */
    public static $documentType = "invoice";

    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\CoreShopOrderInvoice';

    /**
     * Creates next InvoiceNumber.
     *
     * @deprecated Use getNextDocumentNumber instead. Will be removed with CoreShop 1.3
     * @return int|string
     */
    public static function getNextInvoiceNumber()
    {
        return static::getNextDocumentNumber();
    }

    /**
     * @param $documentNumber
     * @return null|static
     */
    public static function findByDocumentNumber($documentNumber)
    {
        return static::findByInvoiceNumber($documentNumber);
    }

    /**
     * Get Order by Invoice Number
     *
     * @param $invoiceNumber
     * @return static|null
     */
    public static function findByInvoiceNumber($invoiceNumber)
    {
        $invoices = static::getByInvoiceNumber($invoiceNumber);

        if (count($invoices->getObjects())) {
            return $invoices->getObjects()[0];
        }

        return null;
    }

    /**
     * Converts any Number to a valid InvoiceNumber with Suffix and Prefix.
     *
     * @param $number
     *
     * @deprecated use getValidDocumentNumber instead. Will be removed with CoreShop 1.3
     * @return string
     */
    public static function getValidInvoiceNumber($number)
    {
        return static::getValidDocumentNumber($number);
    }

    /**
     * get folder for Invoice
     *
     * @param Order $order
     * @param \DateTime $date
     *
     * @deprecated use getPathForDocuments instead. Will be removed with CoreShop 1.3
     * @return Object\Folder
     */
    public static function getPathForNewInvoice(Order $order, $date = null)
    {
        return static::getPathForDocuments($order, $date);
    }

    /**
     * @param Order $order
     * @param array $items
     * @param array $params
     *
     * @return static
     *
     * @throws Exception
     */
    public function fillDocument(Order $order, array $items, array $params = []) {

        if ((bool) Configuration::get('SYSTEM.INVOICE.CREATE') === false) {
            throw new Exception('Invoicing not allowed');
        }

        if (!is_array($items)) {
            throw new Exception('Invalid Parameters');
        }

        if(!static::checkItemsAreProcessable($items)) {
            throw new Exception('You cannot invoice more items than sold items');
        }

        $this->setInvoiceNumber(static::getNextDocumentNumber());
        $this->setOrder($order);

        if (\Pimcore\Config::getFlag('useZendDate')) {
            $this->setInvoiceDate(Date::now());
        }
        else
        {
            $this->setInvoiceDate(Carbon::now());
        }

        $this->setLang($order->getLang());
        $this->setCurrency($order->getCurrency());
        $this->setParent($order->getPathForInvoices());
        $this->setKey(\Pimcore\File::getValidFilename($this->getInvoiceNumber()));
        $this->save(); //We need so save first, to create the items beneath the document

        $this->setPublished(true);
        $this->setItems($this->fillDocumentItems($items));
        $this->save();

        $this->calculateInvoice();

        //check orderState
        $order->checkOrderState();

        Rule::apply('invoice', $this);

        return $this;
    }

    /**
     * @param Item $orderItem
     * @param Document\Item $documentItem
     * @param $amount
     *
     * @return Order\Document\Item
     */
    protected function fillDocumentItem(Item $orderItem, Order\Document\Item $documentItem, $amount) {
        $documentItem = parent::fillDocumentItem($orderItem, $documentItem, $amount);

        if($documentItem instanceof Invoice\Item) {
            $documentItem->setRetailPrice($orderItem->getRetailPrice());
            $documentItem->setWholesalePrice($orderItem->getWholesalePrice());
            $documentItem->save();
        }

        return $documentItem;
    }

    /**
     * @return Order\Invoice\Item
     */
    public function createItemInstance()
    {
        return Order\Invoice\Item::create();
    }

    /**
     * Caluclate Shipping Prices for invoices
     */
    protected function calculateShipping() {
        $shippingWithTax = 0;
        $shippingWithoutTax = 0;
        $shippingTax = 0;

        $totalShipping = $this->getOrder()->getShipping();
        $invoicedShipping = $this->getProcessedValue('shipping');

        if ($totalShipping - $invoicedShipping > 0) {
            $shippingTaxRate = $this->getOrder()->getShippingTaxRate();

            $taxRate = \CoreShop\Model\Tax::create();
            $taxRate->setRate($shippingTaxRate);

            $taxCalculator = new TaxCalculator([$taxRate]);

            $shippingWithTax = $totalShipping - $invoicedShipping;
            $shippingWithoutTax = $taxCalculator->removeTaxes($shippingWithTax);
            $shippingTax = $shippingWithTax - $shippingWithoutTax;

            $this->addTax('shipping', $shippingTaxRate, $shippingTax);
        }

        $this->setShipping($shippingWithTax);
        $this->setShippingWithoutTax($shippingWithoutTax);
        $this->setShippingTax($shippingTax);
        $this->setShippingTaxRate($this->getOrder()->getShippingTaxRate());
    }

    /**
     * calculate Payment Fees for Invoice
     */
    protected function calculatePaymentFees() {
        $paymentFeeWithTax = 0;
        $paymentFeeWithoutTax = 0;
        $paymentFeeTax = 0;

        $totalPaymentFee = $this->getOrder()->getPaymentFee();
        $invoicedPaymentFees = $this->getProcessedValue('paymentFee');

        if ($totalPaymentFee - $invoicedPaymentFees > 0) {
            $paymentFeeTaxRate = $this->getOrder()->getPaymentFeeTaxRate();

            $taxRate = \CoreShop\Model\Tax::create();
            $taxRate->setRate($paymentFeeTaxRate);

            $taxCalculator = new TaxCalculator([$taxRate]);

            $paymentFeeWithTax = $totalPaymentFee - $invoicedPaymentFees;
            $paymentFeeWithoutTax = $taxCalculator->removeTaxes($paymentFeeWithTax);
            $paymentFeeTax = $paymentFeeWithTax - $paymentFeeWithoutTax;

            $this->addTax('payment', $paymentFeeTaxRate, $paymentFeeTax);
        }

        $this->setPaymentFee($paymentFeeWithTax);
        $this->setPaymentFeeWithoutTax($paymentFeeWithoutTax);
        $this->setPaymentFeeTax($paymentFeeTax);
        $this->setPaymentFeeTaxRate($this->getOrder()->getShippingTaxRate());
    }

    /**
     * Calculate Discount for Invoice
     */
    protected function calculateDiscount() {
        $discountWithTax = 0;
        $discountWithoutTax = 0;
        $discountTax = 0;

        $totalDiscount = $this->getOrder()->getDiscount();
        $invoicedDiscount = $this->getProcessedValue('discount');

        if ($totalDiscount - $invoicedDiscount > 0) {
            $discountWithTax = $totalDiscount - $invoicedDiscount;
            $discountWithoutTax = $this->getOrder()->getDiscountWithoutTax() - $this->getProcessedValue('discountWithoutTax');
            $discountTax = $discountWithTax - $discountWithoutTax;
        }

        $this->setDiscount($discountWithTax);
        $this->setDiscountWithoutTax($discountWithoutTax);
        $this->setDiscountTax($discountTax);
    }

    /**
     * Calculate Total for invoice
     */
    protected function calculateTotal() {
        $subtotalTax = $this->getSubtotalTax();
        $shippingTax = $this->getShippingTax();
        $paymentFeeTax = $this->getPaymentFeeTax();
        $discountTax = $this->getDiscountTax();

        $subtotalWithTax = $this->getSubtotal();
        $shippingWithTax = $this->getShipping();
        $paymentFeeWithTax = $this->getPaymentFee();
        $discountWithTax = $this->getDiscount();

        $subtotalWithoutTax = $this->getSubtotalWithoutTax();
        $shippingWithoutTax = $this->getShippingWithoutTax();
        $paymentFeeWithoutTax = $this->getPaymentFeeWithoutTax();
        $discountWithoutTax = $this->getDiscountWithoutTax();

        $totalTax = ($subtotalTax + $shippingTax + $paymentFeeTax) - $discountTax;
        $total = ($subtotalWithTax + $shippingWithTax + $paymentFeeWithTax) - $discountWithTax;
        $totalWithoutTax = ($subtotalWithoutTax + $shippingWithoutTax + $paymentFeeWithoutTax) - $discountWithoutTax;

        $this->setTotal($total);
        $this->setTotalTax($totalTax);
        $this->setTotalWithoutTax($totalWithoutTax);
    }



    /**
     * Calculates and sets taxes into this Document
     */
    protected function calculateSubtotal()
    {
        $discountPercentage = $this->getOrder()->getDiscountPercentage();

        $subtotalWithTax = 0;
        $subtotalWithoutTax = 0;
        $subtotalTax = 0;

        foreach ($this->getItems() as $item) {
            $subtotalWithTax += $item->getTotal();
            $subtotalWithoutTax += $item->getTotalWithoutTax();
            $subtotalTax += $item->getTotalTax();

            foreach ($item->getTaxes() as $tax) {
                if ($tax instanceof Tax) {
                    $this->addTax($tax->getName(), $tax->getRate(), $tax->getAmount() * $discountPercentage);
                }
            }
        }

        $this->setSubtotal($subtotalWithTax);
        $this->setSubtotalWithoutTax($subtotalWithoutTax);
        $this->setSubtotalTax($subtotalTax);
    }

    /**
     * @param $name
     * @param $rate
     * @param $amount
     */
    public function addTax($name, $rate, $amount) {
        $taxes = $this->getTaxes();

        if(!$taxes instanceof Object\Fieldcollection) {
            $taxes = new Object\Fieldcollection();
        }

        $found = false;

        foreach($taxes as $tax) {
            if($tax instanceof Tax) {
                if($tax->getName() === $name) {
                    $tax->setAmount($tax->getAmount() + $amount);
                    $found = true;
                    break;
                }
            }
        }

        if(!$found) {
            $tax = Tax::create([
                "name" => $name,
                "rate" => $rate,
                "amount" => $amount
            ]);

            $taxes->add($tax);
            $this->setTaxes($taxes);
        }
    }

    /**
     * Calculates Prices, Shipping, Discounts and Payment Fees for Invoice
     */
    public function calculateInvoice()
    {
        $this->calculateSubtotal();
        $this->calculateShipping();
        $this->calculatePaymentFees();
        $this->calculateDiscount();
        $this->calculateTotal();

        $this->save();
    }

    /**
     * @return Carbon
     */
    public function getDocumentDate()
    {
        return $this->getInvoiceDate();
    }

    /**
     * @param Carbon|Date $documentDate
     */
    public function setDocumentDate($documentDate)
    {
        $this->setInvoiceDate($documentDate);
    }

    /**
     * @return string
     */
    public function getDocumentNumber()
    {
        return $this->getInvoiceNumber();
    }

    /**
     * @param string $documentNumber
     */
    public function setDocumentNumber($documentNumber)
    {
        $this->setInvoiceNumber($documentNumber);
    }

    /**
     * @return Carbon
     *
     * @throws ObjectUnsupportedException
     */
    public function getInvoiceDate()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Carbon|Date $invoiceDate
     *
     * @throws ObjectUnsupportedException
     */
    public function setInvoiceDate($invoiceDate)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getInvoiceNumber()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $invoiceNumber
     *
     * @throws ObjectUnsupportedException
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Object\Fieldcollection|null
     *
     * @throws ObjectUnsupportedException
     */
    public function getPriceRuleFieldCollection()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Object\Fieldcollection $priceRules
     *
     * @throws ObjectUnsupportedException
     */
    public function setPriceRuleFieldCollection($priceRules)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Currency
     *
     * @throws ObjectUnsupportedException
     */
    public function getCurrency()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Currency $currency
     *
     * @throws ObjectUnsupportedException
     */
    public function setCurrency($currency)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getDiscount()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $discount
     *
     * @throws ObjectUnsupportedException
     */
    public function setDiscount($discount)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $discountWithoutTax
     *
     * @throws ObjectUnsupportedException
     */
    public function setDiscountWithoutTax($discountWithoutTax)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getDiscountTax()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }


    /**
     * @param double $discountTax
     *
     * @throws ObjectUnsupportedException
     */
    public function setDiscountTax($discountTax)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getDiscountWithoutTax()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getShipping()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $shipping
     *
     * @throws ObjectUnsupportedException
     */
    public function setShipping($shipping)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getShippingTaxRate()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $shippingTaxRate
     *
     * @throws ObjectUnsupportedException
     */
    public function setShippingTaxRate($shippingTaxRate)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getShippingWithoutTax()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $shippingWithoutTax
     *
     * @throws ObjectUnsupportedException
     */
    public function setShippingWithoutTax($shippingWithoutTax)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getShippingTax()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $shippingTax
     *
     * @throws ObjectUnsupportedException
     */
    public function setShippingTax($shippingTax)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getPaymentFee()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $paymentFee
     *
     * @throws ObjectUnsupportedException
     */
    public function setPaymentFee($paymentFee)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getPaymentFeeTaxRate()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $paymentFeeTaxRate
     *
     * @throws ObjectUnsupportedException
     */
    public function setPaymentFeeTaxRate($paymentFeeTaxRate)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getPaymentFeeWithoutTax()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $paymentFeeWithoutTax
     *
     * @throws ObjectUnsupportedException
     */
    public function setPaymentFeeWithoutTax($paymentFeeWithoutTax)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getPaymentFeeTax()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $paymentFeeTax
     *
     * @throws ObjectUnsupportedException
     */
    public function setPaymentFeeTax($paymentFeeTax)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getTotalTax()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $totalTax
     *
     * @throws ObjectUnsupportedException
     */
    public function setTotalTax($totalTax)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getTotalWithoutTax()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $totalWithtouTax
     *
     * @throws ObjectUnsupportedException
     */
    public function setTotalWithoutTax($totalWithtouTax)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getTotal()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $total
     *
     * @throws ObjectUnsupportedException
     */
    public function setTotal($total)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }


    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getSubtotalTax()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $subtotalTax
     *
     * @throws ObjectUnsupportedException
     */
    public function setSubtotalTax($subtotalTax)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getSubtotalWithoutTax()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $subtotalWithoutTax
     *
     * @throws ObjectUnsupportedException
     */
    public function setSubtotalWithoutTax($subtotalWithoutTax)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return double
     *
     * @throws ObjectUnsupportedException
     */
    public function getSubtotal()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param double $subtotal
     *
     * @throws ObjectUnsupportedException
     */
    public function setSubtotal($subtotal)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getTaxes()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $taxes
     *
     * @throws ObjectUnsupportedException
     */
    public function setTaxes($taxes)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
