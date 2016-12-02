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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model\Order;

use Carbon\Carbon;
use CoreShop\Exception;
use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Helper\Zend\Action;
use CoreShop\Model\Base;
use CoreShop\Model\Configuration;
use CoreShop\Model\Currency;
use CoreShop\Model\NumberRange;
use CoreShop\Model\Order;
use CoreShop\Model\Shop;
use CoreShop\Model\TaxCalculator;
use CoreShop\Model\User;
use CoreShop\Tool\Wkhtmltopdf;
use Pimcore\Logger;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Document;
use Pimcore\Model\Asset\Service;
use Pimcore\Model\Object;
use Pimcore\Model\User as PimcoreUser;

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
 * @method static Object\Listing\Concrete getByShop ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTaxes ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByItems ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByCustomer ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByShippingAddress ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByBillingAddress ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByExtraInformation ($value, $limit = 0)
 */
class Invoice extends Base
{
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\CoreShopOrderInvoice';

    /**
     * Creates next InvoiceNumber.
     *
     * @return int|string
     */
    public static function getNextInvoiceNumber()
    {
        $number = NumberRange::getNextNumberForType('invoice');

        return self::getValidInvoiceNumber($number);
    }

    /**
     * Get Order by InvoiceNumber
     *
     * @param $invoiceNumber
     * @return static|null
     */
    public static function findByInvoiceNumber($invoiceNumber) {
        $invoices = static::getByInvoiceNumber($invoiceNumber);

        if(count($invoices->getObjects())) {
            return $invoices->getObjects()[0];
        }

        return null;
    }

    /**
     * Converts any Number to a valid InvoiceNumber with Suffix and Prefix.
     *
     * @param $number
     *
     * @return string
     */
    public static function getValidInvoiceNumber($number)
    {
        $prefix = Configuration::get('SYSTEM.INVOICE.PREFIX');
        $suffix = Configuration::get('SYSTEM.INVOICE.SUFFIX');

        if ($prefix) {
            $number = $prefix.$number;
        }

        if ($suffix) {
            $number = $number.$suffix;
        }

        return $number;
    }

    /**
     * get folder for Invoice
     *
     * @param Order $order
     * @param \DateTime $date
     *
     * @return Object\Folder
     */
    public static function getPathForNewInvoice(Order $order, $date = null)
    {
        if(is_null($date)) {
            $date = new Carbon();
        }

        return Object\Service::createFolderByPath($order->getFullPath() . "/invoices/" . $date->format("Y/m/d"));
    }

    /**
     * @return null
     */
    public function getPathForItems() {
        return Object\Service::createFolderByPath($this->getFullPath().'/items/');
    }

    /**
     * Returns array with key=>value for tax and value.
     *
     * @return array
     */
    public function getTaxRates()
    {
        $taxes = array();

        $taxValues = array();

        foreach ($this->getTaxes() as $tax) {
            $taxValues[] = array(
                'rate' => $tax->getRate(),
                'name' => $tax->getName(),
                'value' => $tax->getAmount(),
            );
        }

        foreach ($taxValues as $tax) {
            if (!array_key_exists($tax['name'], $taxes)) {
                $taxes[$tax['name']] = 0;
            }

            $taxes[(string) $tax['name']] += $tax['value'];
        }

        return $taxes;
    }

    /**
     * Calculates Prices, Shipping, Discounts and Payment Fees for Invoice
     */
    public function calculatePrices() {
        $order = $this->getOrder();

        $discountPercentage = $order->getDiscountPercentage();

        $subtotalWithTax = 0;
        $subtotalWithoutTax = 0;
        $subtotalTax = 0;

        $taxes = [];

        $addTax = function($tax, $rate, $name) use (&$taxes) {
            if(!array_key_exists($name, $taxes)) {
                $taxes[$name] = [
                    'rate' => $rate,
                    'name' => $name,
                    'amount' => $tax
                ];
            }
            else {
                $taxes[$name]['amount'] += $tax;
            }
        };

        foreach($this->getItems() as $item) {
            $subtotalWithTax += $item->getTotal();
            $subtotalWithoutTax += $item->getTotalWithoutTax();
            $subtotalTax += $item->getTotalTax();

            foreach($item->getTaxes() as $tax) {
                if($tax instanceof Tax) {
                    $addTax($tax->getAmount() * $discountPercentage, $tax->getRate(), $tax->getName());
                }
            }
        }

        $this->setSubtotal($subtotalWithTax);
        $this->setSubtotalWithoutTax($subtotalWithoutTax);
        $this->setSubtotalTax($subtotalTax);

        //SHIPPING
        $shippingWithTax = 0;
        $shippingWithoutTax = 0;
        $shippingTax = 0;

        $totalShipping = $order->getShipping();
        $invoicedShipping = $order->getInvoicedValue('shipping');

        if($totalShipping - $invoicedShipping > 0) {
            $shippingTaxRate = $order->getShippingTaxRate();

            $taxRate = new \CoreShop\Model\Tax();
            $taxRate->setRate($shippingTaxRate);

            $taxCalculator = new TaxCalculator([$taxRate]);

            $shippingWithTax = $totalShipping - $invoicedShipping;
            $shippingWithoutTax = $taxCalculator->removeTaxes($shippingWithTax);
            $shippingTax = $shippingWithTax - $shippingWithoutTax;

            $addTax($shippingTax, $shippingTaxRate, 'shipping');
        }

        $this->setShipping($shippingWithTax);
        $this->setShippingWithoutTax($shippingWithoutTax);
        $this->setShippingTax($shippingTax);
        $this->setShippingTaxRate($order->getShippingTaxRate());

        //PAYMENT FEES
        $paymentFeeWithTax = 0;
        $paymentFeeWithoutTax = 0;
        $paymentFeeTax = 0;

        $totalPaymentFee = $order->getPaymentFee();
        $invoicedPaymentFees = $order->getInvoicedValue('paymentFee');

        if($totalPaymentFee - $invoicedPaymentFees > 0) {
            $paymentFeeTaxRate = $order->getPaymentFeeTaxRate();

            $taxRate = new \CoreShop\Model\Tax();
            $taxRate->setRate($paymentFeeTaxRate);

            $taxCalculator = new TaxCalculator([$taxRate]);

            $paymentFeeWithTax = $totalPaymentFee - $invoicedPaymentFees;
            $paymentFeeWithoutTax = $taxCalculator->removeTaxes($paymentFeeWithTax);
            $paymentFeeTax = $paymentFeeWithTax - $paymentFeeWithoutTax;

            $addTax($paymentFeeTax, $paymentFeeTaxRate, 'payment');
        }

        $this->setPaymentFee($paymentFeeWithTax);
        $this->setPaymentFeeWithoutTax($paymentFeeWithoutTax);
        $this->setPaymentFeeTax($paymentFeeTax);
        $this->setPaymentFeeTaxRate($order->getShippingTaxRate());

        //DISCOUNT
        $discountWithTax = 0;
        $discountWithoutTax = 0;
        $discountTax = 0;

        $totalDiscount = $order->getDiscount();
        $invoicedDiscount = $order->getInvoicedValue('discount');

        if($totalDiscount - $invoicedDiscount > 0) {
            $discountWithTax = $totalDiscount - $invoicedDiscount;
            $discountWithoutTax = $order->getDiscountWithoutTax() - $order->getInvoicedValue('discountWithoutTax');
            $discountTax = $discountWithTax - $discountWithoutTax;
        }

        $this->setDiscount($discountWithTax);
        $this->setDiscountWithoutTax($discountWithoutTax);

        $totalTax = ($subtotalTax + $shippingTax + $paymentFeeTax) - $discountTax;
        $total = ($subtotalWithTax + $shippingWithTax + $paymentFeeWithTax) - $discountWithTax;
        $totalWithoutTax = ($subtotalWithoutTax + $shippingWithoutTax + $paymentFeeWithoutTax) - $discountWithoutTax;

        $itemTaxes = new Object\Fieldcollection();

        foreach($taxes as $tax) {
            $taxItem = Tax::create();
            $taxItem->setRate($tax['rate']);
            $taxItem->setName($tax['name']);
            $taxItem->setAmount(\CoreShop::getTools()->roundPrice($tax['amount']));

            $itemTaxes->add($taxItem);
        }

        $this->setTaxes($itemTaxes);
        $this->setTotal($total);
        $this->setTotalTax($totalTax);
        $this->setTotalWithoutTax($totalWithoutTax);
        $this->save();
    }

    /**
     * @return Asset
     */
    public function getAsset() {
        $path = $this->getOrder()->getPathForInvoices();
        $fileName = 'invoice-'.$this->getInvoiceNumber().'.pdf';

        return Asset::getByPath($path . "/" . $fileName);
    }

    /**
     * Renders the Invoice to a PDF
     *
     * @throws Exception
     *
     * @return Document|bool
     */
    public function generate()
    {
        $locale = new \Zend_Locale($this->getOrder()->getLang());

        $params = [
            "order" => $this->getOrder(),
            "invoice" => $this,
            "language" => (string) $locale,
            "type" => "invoice"
        ];

        $forward = new Action();
        $html = $forward->action("invoice", "order-print", "CoreShop", $params);
        $header = $forward->action("header", "order-print", "CoreShop", $params);
        $footer = $forward->action("footer", "order-print", "CoreShop", $params);

        try {
            $pdfContent = Wkhtmltopdf::fromString($html, $header, $footer, array('options' => array(Configuration::get('SYSTEM.INVOICE.WKHTML'))));

            if ($pdfContent) {
                $fileName = 'invoice-'.$this->getInvoiceNumber().'.pdf';
                $path = $this->getOrder()->getPathForInvoices();

                $invoice = Document::getByPath($path.'/'.$fileName);

                if ($invoice instanceof Document) {
                    $invoice->delete();
                }

                $invoice = new Document();
                $invoice->setFilename($fileName);
                $invoice->setParent(Service::createFolderByPath($path));
                $invoice->setData($pdfContent);
                $invoice->save();

                return $invoice;
            }
        } catch (Exception $ex) {
            Logger::warn('wkhtmltopdf library not found, no invoice was generated');
        }

        return false;
    }

    /**
     * @return Order
     *
     * @throws ObjectUnsupportedException
     */
    public function getOrder()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Order $order
     *
     * @throws ObjectUnsupportedException
     */
    public function setOrder($order)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
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
     * @param Date\\DateTime $invoiceDate
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
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getLang()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $lang
     *
     * @throws ObjectUnsupportedException
     */
    public function setLang($lang)
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
    public function getDiscountWithoutTax()
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
     * @return Shop
     *
     * @throws ObjectUnsupportedException
     */
    public function getShop()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Shop $shop
     *
     * @throws ObjectUnsupportedException
     */
    public function setShop($shop)
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

    /**
     * @return Invoice\Item[]
     *
     * @throws ObjectUnsupportedException
     */
    public function getItems()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Invoice\Item[] $items
     *
     * @throws ObjectUnsupportedException
     */
    public function setItems($items)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return User
     *
     * @throws ObjectUnsupportedException
     */
    public function getCustomer()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param User $customer
     *
     * @throws ObjectUnsupportedException
     */
    public function setCustomer($customer)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getShippingAddress()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $shippingAddress
     *
     * @throws ObjectUnsupportedException
     */
    public function setShippingAddress($shippingAddress)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getBillingAddress()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $billingAddress
     *
     * @throws ObjectUnsupportedException
     */
    public function setBillingAddress($billingAddress)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getExtraInformation()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $extraInformation
     *
     * @throws ObjectUnsupportedException
     */
    public function setExtraInformation($extraInformation)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
