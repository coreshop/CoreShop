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

namespace CoreShop\Model;

use Carbon\Carbon;
use CoreShop\Exception;
use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Mail\Rule;
use CoreShop\Model\Messaging\Contact;
use CoreShop\Model\Messaging\Thread;
use CoreShop\Model\Order\Invoice;
use CoreShop\Model\Order\Item;
use CoreShop\Model\Order\Payment;
use CoreShop\Model\Order\Shipment;
use CoreShop\Model\Plugin\Payment as CorePayment;
use CoreShop\Model\User\Address;
use CoreShop\Tool\Service;
use Pimcore\Cache;
use Pimcore\Date;
use Pimcore\File;
use Pimcore\Logger;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Document;
use Pimcore\Model\Element\Note;
use Pimcore\Model\Object;
use Pimcore\Model\User as PimcoreUser;
use Pimcore\Model\Version;
use Pimcore\Tool\Authentication;

/**
 * Class Order
 * @package CoreShop\Model
 *
 * @method static Object\Listing\Concrete getByOrderState ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByOrderDate ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByOrderNumber ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByTrackingCode ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByLang ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByCarrier ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByPriceRule ($value, $limit = 0)
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
 * @method static Object\Listing\Concrete getByPaymentProvider ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByPaymentProviderDescription ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByPaymentProviderToken ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByPayments ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByItems ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByCustomer ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByShippingAddress ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByBillingAddress ($value, $limit = 0)
 * @method static Object\Listing\Concrete getByExtraInformation ($value, $limit = 0)
 */
class Order extends Base
{
    /**
     * Note Identifier for Payment
     */
    const NOTE_PAYMENT = 'Payment';

    /**
     * Note Identifier for Update Order
     */
    const NOTE_UPDATE_ORDER = 'Update Order';

    /**
     * Note Identifier for Update Order Item
     */
    const NOTE_UPDATE_ORDER_ITEM = 'Update Order Item';

    /**
     * Note Identifier for emails
     */
    const NOTE_EMAIL = 'Email';

    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\CoreShopOrder';

    /**
     * Creates next OrderNumber.
     *
     * @return int|string
     */
    public static function getNextOrderNumber()
    {
        $number = NumberRange::getNextNumberForType('order');

        return self::getValidOrderNumber($number);
    }

    /**
     * Get Order by OrderNumber
     *
     * @param $orderNumber
     * @return static|null
     */
    public static function findByOrderNumber($orderNumber)
    {
        $orders = static::getByOrderNumber($orderNumber);

        if (count($orders->getObjects())) {
            return $orders->getObjects()[0];
        }

        return null;
    }

    /**
     * Converts any Number to a valid OrderNumber with Suffix and Prefix.
     *
     * @param $number
     *
     * @return string
     */
    public static function getValidOrderNumber($number)
    {
        $prefix = Configuration::get('SYSTEM.ORDER.PREFIX');
        $suffix = Configuration::get('SYSTEM.ORDER.SUFFIX');

        if ($prefix) {
            $number = $prefix.$number;
        }

        if ($suffix) {
            $number = $number.$suffix;
        }

        return $number;
    }

    /**
     * get folder for order
     *
     * @param \DateTime $date
     *
     * @return Object\Folder
     */
    public static function getPathForNewOrder($date = null)
    {
        if (is_null($date)) {
            $date = new Carbon();
        }

        if (Configuration::multiShopEnabled()) {
            return Object\Service::createFolderByPath('/coreshop/orders/' . File::getValidFilename(Shop::getShop()->getName()) . '/' . $date->format("Y/m/d"));
        }

        return Object\Service::createFolderByPath('/coreshop/orders/' . $date->format("Y/m/d"));
    }

    /**
     * @return Object\Folder
     */
    public function getPathForAddresses()
    {
        return Object\Service::createFolderByPath($this->getFullPath() . "/addresses/");
    }

    /**
     * @return Object\Folder
     */
    public function getPathForInvoices()
    {
        return Object\Service::createFolderByPath($this->getFullPath() . "/invoices/");
    }

    /**
     * @return Object\Folder
     */
    public function getPathForShipments()
    {
        return Object\Service::createFolderByPath($this->getFullPath() . "/shipments/");
    }

    /**
     * @return null
     */
    public function getPathForItems()
    {
        return Object\Service::createFolderByPath($this->getFullPath().'/items/');
    }

    /**
     * Import a Cart to the Order.
     *
     * @param Cart $cart
     *
     * @return bool
     */
    public function importCart(Cart $cart)
    {
        $items = [];
        $i = 1;

        foreach ($cart->getItems() as $cartItem) {
            $item = Item::create();
            $item->setKey(strval($i));
            $item->setParent($this->getPathForItems());
            $item->setPublished(true);

            $item->setProduct($cartItem->getProduct());
            $item->setWholesalePrice($cartItem->getProductWholesalePrice());
            $item->setRetailPrice($cartItem->getProductRetailPrice());
            $item->setPrice($cartItem->getProductPrice(true));
            $item->setPriceWithoutTax($cartItem->getProductPrice(false));
            $item->setAmount($cartItem->getAmount());
            $item->setExtraInformation($cartItem->getExtraInformation());
            $item->setIsGiftItem($cartItem->getIsGiftItem());
            $item->setTotal(\CoreShop::getTools()->roundPrice($cartItem->getTotal()));
            $item->setTotalTax(\CoreShop::getTools()->roundPrice($cartItem->getTotalProductTax()));
            $item->setIsVirtualProduct($cartItem->getIsVirtualProduct());

            if ($cartItem->getVirtualAsset() instanceof Asset) {
                $item->setVirtualAsset($cartItem->getVirtualAsset());
            }

            $itemTaxes = new Object\Fieldcollection();

            foreach ($cartItem->getTaxes(false) as $taxes) {
                $itemTax = Order\Tax::create();

                $tax = $taxes['tax'];

                if ($tax instanceof Tax) {
                    $itemTax->setName($tax->getName());
                    $itemTax->setRate($tax->getRate());
                    $itemTax->setAmount($taxes['amount']);

                    $itemTaxes->add($itemTax);
                }
            }

            $item->setTaxes($itemTaxes);
            $item->save();

            //Stock Management
            $cartItem->getProduct()->updateQuantity(-$cartItem->getAmount());

            $items[] = $item;

            ++$i;
        }

        $taxes = new Object\Fieldcollection();

        foreach ($cart->getTaxes() as $tax) {
            $taxObject = $tax['tax'];
            $taxAmount = $tax['amount'];

            $tax = Order\Tax::create();
            $tax->setName($taxObject->getName());
            $tax->setRate($taxObject->getRate());
            $tax->setAmount(\CoreShop::getTools()->roundPrice($taxAmount));

            $taxes->add($tax);
        }

        $this->setTaxes($taxes);
        $this->setDiscount($cart->getDiscount());
        $this->setDiscountWithoutTax($cart->getDiscount(false));

        $fieldCollection = new Object\Fieldcollection();

        foreach ($cart->getPriceRules() as $priceRule) {
            $fieldCollection->add($priceRule);
        }

        $this->setPriceRuleFieldCollection($fieldCollection);

        if ($this->getPriceRuleFieldCollection() instanceof Object\Fieldcollection) {
            foreach ($this->getPriceRuleFieldCollection()->getItems() as $ruleItem) {
                if ($ruleItem instanceof \CoreShop\Model\PriceRule\Item) {
                    $rule = $ruleItem->getPriceRule();

                    if ($rule instanceof PriceRule) {
                        $ruleItem->setDiscount($rule->getDiscount($cart));

                        $rule->applyOrder($this, $ruleItem);
                    }
                }
            }
        }

        $this->setItems($items);
        $this->save();

        //Store Order into cart for statistic purpose
        $cart->setOrder($this);
        $cart->save();

        return true;
    }

    /**
     * Update Order Item and recalc total and taxes
     *
     * @param Item $item
     * @param $amount
     * @param $priceWithoutTax
     * @throws \Pimcore\Model\Element\ValidationException|Exception
     */
    public function updateOrderItem(Item $item, $amount, $priceWithoutTax)
    {
        $invoicesCount = count($this->getInvoices());

        if ($invoicesCount > 0) {
            throw new Exception("You are not allowed to edit this order anymore");
        }

        $currentPrice = $item->getPriceWithoutTax();
        $currentAmount = $item->getAmount();

        $item->setAmount($amount);
        $item->setPriceWithoutTax($priceWithoutTax);

        //Recalc Tax
        $totalTax = 0;

        foreach ($item->getTaxes() as $tax) {
            $taxValue = ((($tax->getRate() / 100) * $item->getPriceWithoutTax())) ;
            $totalTax += $taxValue;

            $tax->setAmount($taxValue * $item->getAmount());
        }

        //$item->setTaxes($taxes);
        $item->setTotalTax($totalTax * $item->getAmount());
        $item->setPrice($priceWithoutTax + $totalTax);
        $item->setTotal($item->getAmount() * $item->getPrice());
        $item->save();

        $allItems = $this->getItems();

        //Replace existing item with new item to be able to update summary right
        foreach ($allItems as &$oldItem) {
            if ($item->getId() === $oldItem->getId()) {
                $oldItem = $item;
            }
        }

        $translate = \CoreShop::getTools()->getTranslate();
        $note = $item->createNote(self::NOTE_UPDATE_ORDER_ITEM);
        $note->setTitle($translate->translate('coreshop_note_updateOrderItem'));
        $note->setDescription($translate->translate('coreshop_note_updateOrderItem_description'));

        if ($currentAmount != $amount) {
            $note->addData('fromAmount', 'text', $currentAmount);
            $note->addData('toAmount', 'text', $amount);
        }

        if ($currentPrice != $priceWithoutTax) {
            $note->addData('fromPrice', 'text', $currentPrice);
            $note->addData('toPrice', 'text', $priceWithoutTax);
        }

        $note->save();

        $this->setItems($allItems);
        $this->updateOrderSummary();
    }

    /**
     * Update Order Summary and Taxes
     */
    protected function updateOrderSummary()
    {
        $totalTax = 0;
        $subTotalTax = 0;
        $subTotal = 0;
        $taxRateValues = [];

        $currentTotal = $this->getTotal();

        $addTax = function ($rate, $amount) use (&$taxRateValues) {
            if (!array_key_exists((string)$rate, $taxRateValues)) {
                $taxRateValues[(string)$rate] = 0;
            }

            $taxRateValues[(string)$rate] += $amount;
        };

        $newSubTotalWithoutDiscount = 0;

        foreach ($this->getItems() as $orderItem) {
            $newSubTotalWithoutDiscount += $orderItem->getTotalWithoutTax();
        }

        //((100 / 217,92) * (217,91-20,89))/100
        $newSubTotal = $newSubTotalWithoutDiscount - $this->getDiscountWithoutTax();
        $newDiscountPercentage = ((100 / $newSubTotalWithoutDiscount) * $newSubTotal) / 100;


        //Recaluclate Subtotal and taxes
        foreach ($this->getItems() as $item) {
            $subTotalTax += $item->getTotalTax() * $newDiscountPercentage;
            $subTotal += $item->getTotal();

            foreach ($item->getTaxes() as $tax) {
                $addTax($tax->getRate(), $tax->getAmount() * $newDiscountPercentage);
            }
        }

        //Recalculate Total and TotalTax
        $total = ($subTotal + $this->getShipping() + $this->getPaymentFee() + $totalTax) - $this->getDiscount();
        $totalTax = $subTotalTax + $this->getShippingTax() + $this->getPaymentFeeTax();

        $this->setSubtotal($subTotal);
        $this->setSubtotalTax($subTotalTax);
        $this->setTotal($total);
        $this->setTotalTax($totalTax);

        //Recalculate detailed Taxes
        if ($this instanceof self) {
            if ($this->getPaymentFeeTaxRate() > 0) {
                $addTax($this->getPaymentFeeTaxRate(), $this->getPaymentFeeTax());
            }

            if ($this->getShippingTaxRate()) {
                $addTax($this->getShippingTaxRate(), $this->getShippingTax());
            }
        }

        foreach ($this->getTaxes() as $tax) {
            if (array_key_exists((string)$tax->getRate(), $taxRateValues)) {
                $tax->setAmount($taxRateValues[(string)$tax->getRate()]);
            }
        }

        $this->save();


        $translate = \CoreShop::getTools()->getTranslate();

        $note = $this->createNote(self::NOTE_UPDATE_ORDER);
        $note->setTitle($translate->translate('coreshop_note_updateOrderSummary'));
        $note->setDescription($translate->translate('coreshop_note_updateOrderSummary_description'));

        if ($currentTotal != $this->getTotal()) {
            $note->addData('fromTotal', 'text', $currentTotal);
            $note->addData('toTotal', 'text', $this->getTotal());
        }

        $note->save();
    }

    /**
     * calculates discount percentage for cart
     *
     * @return float
     */
    public function getDiscountPercentage()
    {
        $totalWithoutDiscount = $this->getSubtotalWithoutTax();
        $totalWithDiscount = $this->getSubtotalWithoutTax() - $this->getDiscountWithoutTax();

        return ((100 / $totalWithoutDiscount) * $totalWithDiscount) / 100;
    }

    /**
     * Create a new Payment.
     *
     * @param CorePayment $provider
     * @param $amount
     * @param bool $paid
     * @param $transactionId
     *
     * @return Payment
     */
    public function createPayment(CorePayment $provider, $amount, $paid = false, $transactionId = null)
    {
        $payment = Payment::create();
        $payment->setKey(uniqid());
        $payment->setPublished(true);
        $payment->setParent(Object\Service::createFolderByPath($this->getFullPath().'/payments/'));
        $payment->setAmount($amount);
        $payment->setTransactionIdentifier(!is_null($transactionId) ? $transactionId : uniqid());
        $payment->setProvider($provider->getIdentifier());

        if (\Pimcore\Config::getFlag('useZendDate')) {
            $payment->setDatePayment(Date::now());
        } else {
            $payment->setDatePayment(Carbon::now());
        }

        $payment->setPayed($paid);
        $payment->save();

        $this->addPayment($payment);

        $translate = \CoreShop::getTools()->getTranslate();

        $note = $this->createNote(self::NOTE_PAYMENT);
        $note->setTitle(sprintf($translate->translate('coreshop_note_order_payment'), $provider->getName(), $this->formatPrice($amount)));
        $note->setDescription(sprintf($translate->translate('coreshop_note_order_payment_description'), $provider->getName(), $this->formatPrice(($amount))));
        $note->addData('provider', 'text', $provider->getName());
        $note->addData('amount', 'text', $this->formatPrice($amount));
        $note->save();

        return $payment;
    }

    /**
     * @param $price
     * @return string
     */
    public function formatPrice($price) {
        return \CoreShop::getTools()->formatPrice($price, $this->getBillingAddress() instanceof Address ? $this->getBillingAddress()->getCountry() : null, $this->getCurrency());
    }

    /***** INVOICING *****/

    /**
     * @return Invoice[]
     */
    public function getInvoices()
    {
        $list = Invoice::getList();
        $list->setCondition("order__id = ?", [$this->getId()]);
        $list->load();

        return $list->getObjects();
    }

    /**
     * Check if Items are available for Shipping
     *
     * @param $items
     * @return bool
     */
    protected function checkItemsAvailableForShipping($items) {
        if(!is_array($items)) {
            return false;
        }

        foreach ($items as $item) {
            $orderItem = Item::getById($item['orderItemId']);
            $amount = $item['amount'];

            if ($orderItem instanceof Item) {
                $shippedAmount = $orderItem->getShippedAmount();
                $newShippedAmount = $shippedAmount + $amount;

                if ($newShippedAmount > $orderItem->getAmount()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Creates an Invoice for all Items
     *
     * @return Invoice
     */
    public function createInvoiceForAllItems()
    {
        return $this->createInvoice($this->getAllItemsForDocumentCreation());
    }

    /**
     * get all items that are still invoice-able

     * @return array
     */
    public function getInvoiceAbleItems()
    {
        $items = $this->getItems();
        $invoicedItems = $this->getInvoicedItems();
        $invoiceAbleItems = [];

        foreach ($items as $item) {
            if (array_key_exists($item->getId(), $invoicedItems)) {
                if ($invoicedItems[$item->getId()]['amount'] < $item->getAmount()) {
                    $invoiceAbleItems[$item->getId()] = [
                        "amount" => $item->getAmount() - $invoicedItems[$item->getId()]['amount'],
                        "item" => $item
                    ];
                }
            } else {
                $invoiceAbleItems[$item->getId()] = [
                    "amount" => $item->getAmount(),
                    "item" => $item
                ];
            }
        }

        return $invoiceAbleItems;
    }

    /**
     * get all invoiced items with amounts
     *
     * @return array
     */
    public function getInvoicedItems()
    {
        $invoices = $this->getInvoices();
        $invoicedItems = [];

        foreach ($invoices as $invoice) {
            foreach ($invoice->getItems() as $invoiceItem) {
                $orderItem = $invoiceItem->getOrderItem();

                if ($orderItem instanceof Item) {
                    if (array_key_exists($orderItem->getId(), $invoicedItems)) {
                        $invoicedItems[$orderItem->getId()]['amount'] += $invoiceItem->getAmount();
                    } else {
                        $invoicedItems[$orderItem->getId()] = [
                            'amount' => $invoiceItem->getAmount(),
                            'orderItem' => $orderItem
                        ];
                    }
                }
            }
        }

        return $invoicedItems;
    }

    /**
     * Creates a new invoices
     *
     * @param $items
     * @throws Exception
     *
     * @return Invoice
     */
    public function createInvoice($items)
    {
        if ((bool) Configuration::get('SYSTEM.INVOICE.CREATE') === false) {
            throw new Exception('Invoicing not allowed');
        }

        if (!is_array($items)) {
            throw new Exception('Invalid Parameters');
        }

        if (!$this->checkItemsAvailableForInvoice($items)) {
            throw new Exception('You cannot invoice more items than sold items');
        }

        $invoice = Invoice::create();

        $invoice->setInvoiceNumber(Invoice::getNextInvoiceNumber());
        $invoice->setOrder($this);
        if (\Pimcore\Config::getFlag('useZendDate')) {
            $invoice->setInvoiceDate(Date::now());
        } else {
            $invoice->setInvoiceDate(Carbon::now());
        }
        $invoice->setLang($this->getLang());
        $invoice->setCurrency($this->getCurrency());
        $invoice->setShop($this->getShop());
        $invoice->setCustomer($this->getCustomer());
        $invoice->setShippingAddress($this->getShippingAddress());
        $invoice->setBillingAddress($this->getBillingAddress());
        $invoice->setExtraInformation($this->getExtraInformation());
        $invoice->setParent($this->getPathForInvoices());
        $invoice->setKey(\Pimcore\File::getValidFilename($invoice->getInvoiceNumber()));
        $invoice->save();

        $invoiceItems = [];

        foreach ($items as $item) {
            $orderItem = Item::getById($item['orderItemId']);
            $amount = $item['amount'];

            if ($orderItem instanceof Item) {
                $invoiceItem = Invoice\Item::create();

                Service::copyObject($orderItem, $invoiceItem);

                $invoiceItem->setAmount($amount);
                $invoiceItem->setParent($invoice->getPathForItems());
                $invoiceItem->setTotal($orderItem->getPrice() * $amount);
                $invoiceItem->setTotalTax(($orderItem->getPrice() - $orderItem->getPriceWithoutTax()) * $amount);

                $invoiceItemTaxes = new Object\Fieldcollection();
                $totalTax = 0;

                foreach ($orderItem->getTaxes() as $tax) {
                    if ($tax instanceof Order\Tax) {
                        $taxRate = Tax::create();
                        $taxRate->setRate($tax->getRate());

                        $taxCalculator = new TaxCalculator([$taxRate]);

                        $itemTax = Order\Tax::create();
                        $itemTax->setName($tax->getName());
                        $itemTax->setRate($tax->getRate());
                        $itemTax->setAmount($taxCalculator->getTaxesAmount($invoiceItem->getTotalWithoutTax()));

                        $invoiceItemTaxes->add($itemTax);

                        $totalTax += $itemTax->getAmount();
                    }
                }

                $invoiceItem->setOrderItem($orderItem);
                $invoiceItem->setKey($orderItem->getKey());
                $invoiceItem->setTaxes($invoiceItemTaxes);
                $invoiceItem->setTotalTax($totalTax);
                $invoiceItem->setPublished(true);
                $invoiceItem->save();

                $invoiceItems[] = $invoiceItem;
            }
        }

        $invoice->setPublished(true);
        $invoice->setItems($invoiceItems);
        $invoice->save();

        $invoice->calculatePrices();

        //check orderState
        $this->checkOrderState();

        Rule::apply('invoice', $invoice);

        return $invoice;
    }


    /**
     * Check if order is fully invoiced
     *
     * @return bool
     */
    public function isFullyInvoiced() {
        return count($this->getInvoiceAbleItems()) === 0;
    }

    /**
     * get any accumulated invoiced value for a field
     *
     * @param $field
     * @return float
     */
    public function getInvoicedValue($field)
    {
        $invoices = $this->getInvoices();
        $invoicedValue = 0;

        foreach ($invoices as $invoice) {
            $invoicedValue += $invoice->getValueForFieldName($field);
        }

        return $invoicedValue;
    }

    /***** SHIPMENT *****/

    /**
     * @return Invoice[]
     */
    public function getShipments()
    {
        $list = Shipment::getList();
        $list->setCondition("order__id = ?", [$this->getId()]);
        $list->load();

        return $list->getObjects();
    }

    /**
     * Creates a Shipment for all Items
     *
     * @return Shipment
     */
    public function createShipmentForAllItems()
    {
        return $this->createShipment($this->getAllItemsForDocumentCreation(), $this->getCarrier());
    }

    /**
     * get all items that are still ship-able
     *
     * @return array
     */
    public function getShipAbleItems()
    {
        $items = $this->getItems();
        $shippedItems = $this->getShippedItems();
        $shipAbleItems = [];

        foreach ($items as $item) {
            if (array_key_exists($item->getId(), $shippedItems)) {
                if ($shippedItems[$item->getId()]['amount'] < $item->getAmount()) {
                    $shipAbleItems[$item->getId()] = [
                        "amount" => $item->getAmount() - $shippedItems[$item->getId()]['amount'],
                        "item" => $item
                    ];
                }
            } else {
                $shipAbleItems[$item->getId()] = [
                    "amount" => $item->getAmount(),
                    "item" => $item
                ];
            }
        }

        return $shipAbleItems;
    }

    /**
     * get all shipped items with amounts
     *
     * @return array
     */
    public function getShippedItems()
    {
        $shipments = $this->getShipments();
        $shippedItems = [];

        foreach ($shipments as $shipment) {
            foreach ($shipment->getItems() as $shipmentItem) {
                $orderItem = $shipmentItem->getOrderItem();

                if ($orderItem instanceof Item) {
                    if (array_key_exists($orderItem->getId(), $shippedItems)) {
                        $shippedItems[$orderItem->getId()]['amount'] += $shipmentItem->getAmount();
                    } else {
                        $shippedItems[$orderItem->getId()] = [
                            'amount' => $shipmentItem->getAmount(),
                            'orderItem' => $orderItem
                        ];
                    }
                }
            }
        }

        return $shippedItems;
    }

    /**
     * Creates a new Shipment
     *
     * @param $items
     * @param Carrier $carrier,
     * @param string $trackingCode
     *
     * @throws Exception
     *
     * @return Shipment
     */
    public function createShipment($items, Carrier $carrier, $trackingCode = null)
    {
        if (!is_array($items)) {
            throw new Exception('Invalid Parameters');
        }

        if (!$this->checkItemsAvailableForShipping($items)) {
            throw new Exception('You cannot ship more items than sold items');
        }

        $shipment = Shipment::create();

        $shipment->setShipmentNumber(Shipment::getNextShipmentNumber());
        $shipment->setOrder($this);
        if (\Pimcore\Config::getFlag('useZendDate')) {
            $shipment->setShipmentDate(Date::now());
        } else {
            $shipment->setShipmentDate(Carbon::now());
        }
        $shipment->setLang($this->getLang());
        $shipment->setShop($this->getShop());
        $shipment->setCustomer($this->getCustomer());
        $shipment->setShippingAddress($this->getShippingAddress());
        $shipment->setBillingAddress($this->getBillingAddress());
        $shipment->setExtraInformation($this->getExtraInformation());
        $shipment->setParent($this->getPathForShipments());
        $shipment->setKey(\Pimcore\File::getValidFilename($shipment->getShipmentNumber()));
        $shipment->setCarrier($carrier);
        $shipment->setTrackingCode($trackingCode);
        $shipment->save();

        $shipmentItems = [];

        $totalWeight = 0;

        foreach ($items as $item) {
            $orderItem = Item::getById($item['orderItemId']);
            $amount = $item['amount'];

            if ($orderItem instanceof Item) {
                $shipmentItem = Shipment\Item::create();

                Service::copyObject($orderItem, $shipmentItem);

                $shipmentItem->setAmount($amount);
                $shipmentItem->setParent($shipment->getPathForItems());
                $shipmentItem->setTotal($orderItem->getPrice() * $amount);
                $shipmentItem->setTotalTax(($orderItem->getPrice() - $orderItem->getPriceWithoutTax()) * $amount);

                $invoiceItemTaxes = new Object\Fieldcollection();
                $totalTax = 0;

                foreach ($orderItem->getTaxes() as $tax) {
                    if ($tax instanceof Order\Tax) {
                        $taxRate = Tax::create();
                        $taxRate->setRate($tax->getRate());

                        $taxCalculator = new TaxCalculator([$taxRate]);

                        $itemTax = Order\Tax::create();
                        $itemTax->setName($tax->getName());
                        $itemTax->setRate($tax->getRate());
                        $itemTax->setAmount($taxCalculator->getTaxesAmount($shipmentItem->getTotalWithoutTax()));

                        $invoiceItemTaxes->add($itemTax);

                        $totalTax += $itemTax->getAmount();
                    }
                }

                $shipmentItem->setWeight($orderItem->getProduct()->getWeight() * $orderItem->getAmount());
                $shipmentItem->setOrderItem($orderItem);
                $shipmentItem->setKey($orderItem->getKey());
                $shipmentItem->setTaxes($invoiceItemTaxes);
                $shipmentItem->setTotalTax($totalTax);
                $shipmentItem->setPublished(true);
                $shipmentItem->save();

                $totalWeight += $shipmentItem->getWeight();

                $shipmentItems[] = $shipmentItem;
            }
        }

        $shipment->setWeight($totalWeight);
        $shipment->setPublished(true);
        $shipment->setItems($shipmentItems);
        $shipment->save();

        //check orderState
        $this->checkOrderState();

        Rule::apply('shipment', $shipment);

        return $shipment;
    }


    /**
     * get all items for document creation
     *
     * @return array
     */
    protected function getAllItemsForDocumentCreation() {
        $items = [];

        foreach ($this->getItems() as $item) {
            $items[] = [
                'orderItemId' => $item->getId(),
                'amount' => $item->getAmount()
            ];
        }

        return $items;
    }

    /**
     * Check if items are available for invoicing
     *
     * @param $items
     * @return bool
     */
    protected static function checkItemsAvailableForInvoice($items) {
        if(!is_array($items)) {
            return false;
        }

        foreach ($items as $item) {
            $orderItem = Item::getById($item['orderItemId']);
            $amount = $item['amount'];

            if ($orderItem instanceof Item) {
                $invoicedAmount = $orderItem->getInvoicedAmount();
                $newInvoicedAmount = $invoicedAmount + $amount;

                if ($newInvoicedAmount > $orderItem->getAmount()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if order is fully shipped
     *
     * @return bool
     */
    public function isFullyShipped() {
        return count($this->getShipAbleItems()) === 0;
    }

    /**
     * Add a new Payment.
     *
     * @param Payment $payment
     */
    public function addPayment(Payment $payment)
    {
        $payments = $this->getPayments();

        if (!is_array($payments)) {
            $payments = [];
        }

        $payments[] = $payment;

        $this->setPayments($payments);
        $this->save();
    }

    /**
     * Returns the total payed amount for the Order.
     *
     * @return float|int
     *
     * @throws ObjectUnsupportedException
     */
    public function getPayedTotal()
    {
        $totalPayed = 0;

        foreach ($this->getPayments() as $payment) {
            if ($payment->getPayed()) {
                $totalPayed += $payment->getAmount();
            }
        }

        return $totalPayed;
    }

    /**
     * Check if the order is fully paid
     *
     * @return bool
     */
    public function getIsPayed()
    {
        return ($this->getTotal() === $this->getPayedTotal());
    }

    /**
     * check if order has payments
     *
     * @return bool
     */
    public function hasPayments() {
        return count($this->getPayments()) > 0;
    }

    /**
     * calculates the total weight of the cart.
     *
     * @todo: Total Weight should be stored in the OrderItem
     *
     * @return int
     */
    public function getTotalWeight()
    {
        $weight = 0;

        foreach ($this->getItems() as $item) {
            if ($item->getProduct() instanceof Product) {
                $weight += ($item->getAmount() * $item->getProduct()->getWeight());
            }
        }

        return $weight;
    }

    /**
     * Checks if Shipping and Billing addresses are the same.
     *
     * @returns boolean
     */
    public function isShippingAndBillingAddressEqual()
    {
        $shipping = $this->getShippingAddress();
        $billing = $this->getBillingAddress();

        if ($shipping instanceof Address && $billing instanceof Address) {
            if ($shipping->getId() === $billing->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Payment Provider Object.
     *
     * @return bool|\CoreShop\Model\Plugin\Payment
     *
     * @throws ObjectUnsupportedException
     */
    public function getPaymentProviderObject()
    {
        $paymentProvider = $this->getPaymentProvider();

        return \CoreShop::getPaymentProvider($paymentProvider);
    }

    /**
     * @param $identifier
     *
     * @return bool|Payment
     */
    public function getOrderPaymentByIdentifier($identifier)
    {
        $payments = $this->getPayments();
        if (count($payments) === 0) {
            return false;
        }

        /** @var \CoreShop\Model\Order\Payment $payment */
        foreach ($payments as $payment) {
            if ($payment->getTransactionIdentifier() === $identifier) {
                return $payment;
            }
        }

        return false;
    }

    /**
     * Returns array with key=>value for tax and value.
     *
     * @return array
     */
    public function getTaxRates()
    {
        $taxes = [];

        $taxValues = [];

        foreach ($this->getTaxes() as $tax) {
            $taxValues[] = [
                'rate' => $tax->getRate(),
                'name' => $tax->getName(),
                'value' => $tax->getAmount(),
            ];
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
     * Create Shipping Tracking Url.
     *
     * @return string|null
     */
    public function getShippingTrackingUrl()
    {
        if ($this->getCarrier() instanceof Carrier) {
            if ($trackingUrl = $this->getCarrier()->getTrackingUrl()) {
                return sprintf($trackingUrl, $this->getTrackingCode());
            }
        }

        return null;
    }

    /**
     * get current order state
     *
     * @return string
     */
    public function getOrderState()
    {
        $currentStateInfo = Order\State::getOrderCurrentState($this);
        return isset($currentStateInfo['state']) ? $currentStateInfo['state'] : false;
    }

    /**
     * get current order status
     *
     * @return string
     */
    public function getOrderStatus()
    {
        $currentStateInfo = Order\State::getOrderCurrentState($this);
        return isset($currentStateInfo['status']) ? $currentStateInfo['status'] : false;
    }

    /**
     * check order state.
     * - if all invoices and shipments has been created: set status to complete.
     * - next, if current state is not processing, change it to processing.
     */
    public function checkOrderState()
    {
        $currentStateInfo = Order\State::getOrderCurrentState($this);

        try {
            //all items has been checked
            if ($this->isFullyInvoiced() && $this->isFullyShipped()) {
                $params = [
                    'newState'      => Order\State::STATE_COMPLETE,
                    'newStatus'     => Order\State::STATE_COMPLETE,
                ];
                Order\State::changeOrderState($this, $params);
            } else {
                if ($currentStateInfo['state']['name'] !== Order\State::STATE_PROCESSING) {
                    $params = [
                        'newState'      => Order\State::STATE_PROCESSING,
                        'newStatus'     => Order\State::STATE_PROCESSING,
                    ];
                    Order\State::changeOrderState($this, $params);
                }
            }
        } catch (\Exception $e) {
            //fail silently.
        }
    }

    /**
     * get all order-state changes
     *
     * @return Note[]
     */
    public function getOrderStateHistory()
    {
        $noteList = new Note\Listing();
        /* @var \Pimcore\Model\Element\Note\Listing $noteList */

        $noteList->addConditionParam('type = ?', 'coreshop-orderstate');
        $noteList->addConditionParam('cid = ?', $this->getId());

        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');

        return $noteList->load();
    }

    /**
     * get all threads regarding this order
     *
     * @return Thread|Messaging\Thread[]|null
     */
    public function getCustomerThreads()
    {
        $threadList = Thread::searchThread($this->getCustomer()->getEmail(), null, $this->getShop()->getId(), $this->getId(), null, true);

        return $threadList;
    }

    /**
     * @return Date
     *
     * @throws ObjectUnsupportedException
     */
    public function getOrderDate()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Date\\DateTime $orderDate
     *
     * @throws ObjectUnsupportedException
     */
    public function setOrderDate($orderDate)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getOrderNumber()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $orderNumber
     *
     * @throws ObjectUnsupportedException
     */
    public function setOrderNumber($orderNumber)
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
     * @return Carrier
     *
     * @throws ObjectUnsupportedException
     */
    public function getCarrier()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Carrier $carrier
     *
     * @throws ObjectUnsupportedException
     */
    public function setCarrier($carrier)
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
    public function setTotalWithtoutTax($totalWithtouTax)
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
     * @return mixed
     *
     * @throws ObjectUnsupportedException
     */
    public function getPaymentProvider()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $paymentProvider
     *
     * @throws ObjectUnsupportedException
     */
    public function setPaymentProvider($paymentProvider)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getPaymentProviderDescription()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $paymentProviderDescription
     *
     * @throws ObjectUnsupportedException
     */
    public function setPaymentProviderDescription($paymentProviderDescription)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getPaymentProviderToken()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $paymentProviderToken
     *
     * @throws ObjectUnsupportedException
     */
    public function setPaymentProviderToken($paymentProviderToken)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return array
     *
     * @throws ObjectUnsupportedException
     */
    public function getPayments()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param array $payments
     *
     * @throws ObjectUnsupportedException
     */
    public function setPayments($payments)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Item[]
     *
     * @throws ObjectUnsupportedException
     */
    public function getItems()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Item[] $items
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

    /**
     * @return int
     *
     * @throws ObjectUnsupportedException
     */
    public function getVisitorId()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param int $visitorId
     *
     * @throws ObjectUnsupportedException
     */
    public function setVisitorId($visitorId)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }
}
