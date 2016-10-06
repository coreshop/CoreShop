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

namespace CoreShop\Model;

use Carbon\Carbon;
use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Order\Item;
use CoreShop\Model\Order\Payment;
use CoreShop\Model\Plugin\Payment as CorePayment;
use CoreShop\Model\User\Address;
use Pimcore\Cache;
use Pimcore\Date;
use Pimcore\File;
use Pimcore\Logger;
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
     * Converts any Number to a valid OrderNumber with Suffix and Prefix.
     *
     * @param $number
     *
     * @return string
     */
    public static function getValidOrderNumber($number)
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
     * get folder for order
     *
     * @param \DateTime $date
     *
     * @return Object\Folder
     */
    public static function getPathForNewOrder($date = null)
    {
        if(is_null($date)) {
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
    public function getPathForAddresses() {
        return Object\Service::createFolderByPath($this->getFullPath() . "/addresses/");
    }

    /**
     * @return null
     */
    public function getPathForItems() {
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
        $items = array();
        $i = 1;

        foreach ($cart->getItems() as $cartItem) {
            $item = Item::create();
            $item->setKey($i);
            $item->setParent($this->getPathForItems());
            $item->setPublished(true);

            $item->setProduct($cartItem->getProduct());
            $item->setWholesalePrice($cartItem->getProductWholesalePrice());
            $item->setRetailPrice($cartItem->getProductRetailPrice());
            $item->setPrice($cartItem->getProductPrice());
            $item->setPriceWithoutTax($cartItem->getProductPrice(false));
            $item->setAmount($cartItem->getAmount());
            $item->setExtraInformation($cartItem->getExtraInformation());
            $item->setIsGiftItem($cartItem->getIsGiftItem());
            $item->setTotal(\CoreShop::getTools()->roundPrice($cartItem->getTotal()));
            $item->setTotalTax(\CoreShop::getTools()->roundPrice($cartItem->getTotalTax()));

            $productTaxes = $cartItem->getProductTaxCalculator();

            if ($productTaxes instanceof TaxCalculator) {
                $productTaxes = $productTaxes->getTaxes();
                $itemTaxes = new Object\Fieldcollection();
                $itemTaxAmounts = $cartItem->getProductTaxAmount(true);

                foreach ($productTaxes as $tax) {
                    $itemTax = Order\Tax::create();

                    $itemTax->setName($tax->getName());
                    $itemTax->setRate($tax->getRate());
                    $itemTax->setAmount(\CoreShop::getTools()->roundPrice($itemTaxAmounts[$tax->getId()]));

                    $itemTaxes->add($itemTax);
                }

                $item->setTaxes($itemTaxes);
            }
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
        $this->setPriceRuleFieldCollection($cart->getPriceRuleFieldCollection());

        if ($this->getPriceRuleFieldCollection() instanceof Object\Fieldcollection) {
            foreach ($this->getPriceRuleFieldCollection()->getItems() as $ruleItem) {
                if ($ruleItem instanceof \CoreShop\Model\PriceRule\Item) {
                    $rule = $ruleItem->getPriceRule();

                    if ($rule instanceof PriceRule) {
                        $ruleItem->setDiscount($rule->getDiscount());
                    }
                }
            }
        }

        $this->setItems($items);
        $this->save();

        //Store Order into cart for statistic purpose
        $cart->setOrder($this);
        $cart->save();

        if ($this->getPriceRule() instanceof PriceRule) {
            $this->getPriceRule()->applyOrder($this);
        }

        return true;
    }

    /**
     * Update Order Item and recalc total and taxes
     *
     * @param Item $item
     * @param $amount
     * @param $priceWithoutTax
     * @throws \Pimcore\Model\Element\ValidationException
     */
    public function updateOrderItem(Item $item, $amount, $priceWithoutTax)
    {
        $currentPrice = $item->getPriceWithoutTax();
        $currentAmount = $item->getAmount();

        $item->setAmount($amount);
        $item->setPriceWithoutTax($priceWithoutTax);

        //Recalc Tax
        $totalTax = 0;

        foreach ($item->getTaxes() as $tax) {
            $taxValue = (($tax->getRate() / 100) * $item->getPriceWithoutTax());
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
        $note = $item->createNote('coreshop-updateOrderItem');
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
    public function updateOrderSummary()
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

        //Recaluclate Subtotal and taxes
        foreach ($this->getItems() as $item) {
            $subTotalTax += $item->getTotalTax();
            $subTotal += $item->getTotal();

            foreach ($item->getTaxes() as $tax) {
                $addTax($tax->getRate(), $tax->getAmount());
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

        $note = $this->createNote("coreshop-updateOrderSummary");
        $note->setTitle($translate->translate('coreshop_note_updateOrderSummary'));
        $note->setDescription($translate->translate('coreshop_note_updateOrderSummary_description'));

        if ($currentTotal != $this->getTotal()) {
            $note->addData('fromTotal', 'text', $currentTotal);
            $note->addData('toTotal', 'text', $this->getTotal());
        }

        $note->save();
    }

    /**
     * Create a new Payment.
     *
     * @param CorePayment $provider
     * @param $amount
     * @param bool $paid
     *
     * @return Payment
     */
    public function createPayment(CorePayment $provider, $amount, $paid = false)
    {
        $payment = Payment::create();
        $payment->setKey(uniqid());
        $payment->setPublished(true);
        $payment->setParent(Object\Service::createFolderByPath($this->getFullPath().'/payments/'));
        $payment->setAmount($amount);
        $payment->setTransactionIdentifier(uniqid());
        $payment->setProvider($provider->getIdentifier());
        $payment->setDatePayment(Date::now());
        $payment->setPayed($paid);
        $payment->save();

        $this->addPayment($payment);

        $translate = \CoreShop::getTools()->getTranslate();

        $note = $this->createNote('coreshop-order-payment');
        $note->setTitle(sprintf($translate->translate('coreshop_note_order_payment'), $provider->getName(), \CoreShop::getTools()->formatPrice($amount)));
        $note->setDescription(sprintf($translate->translate('coreshop_note_order_payment_description'), $provider->getName(), \CoreShop::getTools()->formatPrice(($amount))));
        $note->addData('provider', 'text', $provider->getName());
        $note->addData('amount', 'text', \CoreShop::getTools()->formatPrice($amount));
        $note->save();

        return $payment;
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
            $payments = array();
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
            if($item->getProduct() instanceof Product) {
                $weight += ($item->getAmount() * $item->getProduct()->getWeight());
            }
        }

        return $weight;
    }

    /**
     * Returns Customers shipping address.
     *
     * @return Address|bool
     */
    public function getCustomerShippingAddress()
    {
        if ($this->getShippingAddress() instanceof Address) {
            return $this->getShippingAddress();
        }

        return false;
    }

    /**
     * Returns Customers billing address.
     *
     * @return Address|bool
     */
    public function getCustomerBillingAddress()
    {
        if ($this->getBillingAddress() instanceof Address) {
            return $this->getBillingAddress();
        }

        return false;
    }

    /**
     * checks if shipping and billing addresses are the same.
     *
     * @todo: Only need to check for address-id now
     * @returns boolean
     */
    public function isShippingAndBillingAddressEqual()
    {
        $shipping = $this->getCustomerShippingAddress();
        $billing = $this->getCustomerBillingAddress();

        $billingVars = $billing->getObjectVars();
        $shippingVars = $shipping->getObjectVars();

        foreach ($shippingVars as $key => $value) {
            if ($key === 'fieldname') {
                continue;
            }

            if (array_key_exists($key, $billingVars)) {
                if (!is_object($value)) {
                    if ($billingVars[$key] !== $value) {
                        return false;
                    }
                } else {
                    if ($value instanceof Object\AbstractObject) {
                        if ($value->getId() !== $billingVars[$key]->getId()) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
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
     * Pimcore: When save is called from Pimcore, check for changes of the OrderState.
     *
     * @return int
     */
    public function save()
    {
        Version::disable();

        if (isset($_REQUEST['data'])) {
            try {
                $data = \Zend_Json::decode($_REQUEST['data']);

                if (isset($data['orderState'])) {
                    Cache::clearTag('object_'.$this->getId());
                    \Zend_Registry::set('object_'.$this->getId(), null);

                    $orderStep = Order\State::getById($data['orderState']);
                    $originalOrder = self::getById($this->getId());

                    unset($_REQUEST['data']);

                    if ($orderStep instanceof Order\State) {
                        if ($orderStep->getId() !== $originalOrder->getOrderState()->getId()) {
                            $orderStep->processStep($originalOrder);
                        }
                    }
                }
            } catch (\Exception $ex) {
                Logger::error($ex);
            }
        }

        $orderState = $this->getOrderState();

        if ($orderState instanceof Order\State) {
            if ($orderState->getInvoice()) {
                $this->getInvoice(); //Re-Generate Invoice if it does not exist
            }
        }

        Version::enable();

        parent::save();
    }

    /**
     * Get Invoice for Order.
     *
     * @param bool $renewInvoice Recreate Invoice?
     *
     * @return bool|mixed|Document
     */
    public function getInvoice($renewInvoice = false)
    {
        //Check if invoice has already been generated
        $document = $this->getProperty('invoice');

        if ($document instanceof Document && !$renewInvoice) {
            return $document;
        }

        return Invoice::generateInvoice($this);
    }

    /**
     * Create a note for this order.
     *
     * @param $type string
     *
     * @return Note $note
     */
    public function createNote($type)
    {
        $note = new Note();
        $note->setElement($this);
        $note->setDate(time());
        $note->setType($type);

        if (\Pimcore::inAdmin()) {
            $user = Authentication::authenticateSession();
            if ($user instanceof PimcoreUser) {
                $note->setUser($user->getId());
            }
        }

        return $note;
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
     * @return Order\State
     *
     * @throws ObjectUnsupportedException
     */
    public function getOrderState()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Order\State $orderState
     *
     * @throws ObjectUnsupportedException
     */
    public function setOrderState($orderState)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
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
     * @param Date $orderDate
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
    public function getTrackingCode()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $trackingCode
     *
     * @throws ObjectUnsupportedException
     */
    public function setTrackingCode($trackingCode)
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
     * @return PriceRule|null
     *
     * @throws ObjectUnsupportedException
     */
    public function getPriceRule()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param PriceRule $priceRule
     *
     * @throws ObjectUnsupportedException
     */
    public function setPriceRule($priceRule)
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
     * @return string|null
     *
     * @throws ObjectUnsupportedException
     */
    public function getVoucher()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $voucher
     *
     * @throws ObjectUnsupportedException
     */
    public function setVoucher($voucher)
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
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getPayments()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param mixed $payments
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
}
