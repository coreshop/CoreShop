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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
namespace CoreShop\Model;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Cart\PriceRule;
use CoreShop\Model\Order\Item;
use CoreShop\Model\Order\Payment;
use CoreShop\Model\Plugin\Payment as CorePayment;
use CoreShop\Model\User\Address;
use CoreShop\Plugin;
use CoreShop\Tool;
use Pimcore\Cache;
use Pimcore\Date;
use Pimcore\Model\Asset\Document;
use Pimcore\Model\Element\Note;
use Pimcore\Model\Object;
use Pimcore\Model\User as PimcoreUser;
use Pimcore\Model\Version;
use Pimcore\Tool\Authentication;

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
     * @return Object\Folder
     * @throws \Exception
     */
    public static function getPathForNewOrder() {
        return Object\Service::createFolderByPath('/coreshop/orders/'.date('Y/m/d'));
    }

    /**
     * Import a Cart to the Order.
     *
     * @param Cart $cart
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function importCart(Cart $cart)
    {
        $items = array();
        $i = 1;

        foreach ($cart->getItems() as $cartItem) {
            $item = Item::create();
            $item->setKey($i);
            $item->setParent(Object\Service::createFolderByPath($this->getFullPath().'/items/'));
            $item->setPublished(true);

            $item->setProduct($cartItem->getProduct());
            $item->setWholesalePrice($cartItem->getProduct()->getWholesalePrice());
            $item->setRetailPrice($cartItem->getProduct()->getRetailPrice());
            $item->setPrice($cartItem->getProduct()->getPrice());
            $item->setPriceWithoutTax($cartItem->getProduct()->getPriceWithoutTax());
            $item->setAmount($cartItem->getAmount());
            $item->setExtraInformation($cartItem->getExtraInformation());
            $item->setIsGiftItem($cartItem->getIsGiftItem());
            $item->setTotal(Tool::roundPrice($cartItem->getAmount() * $cartItem->getProduct()->getPrice()));
            $item->setTotalTax(Tool::roundPrice($cartItem->getAmount() * $cartItem->getProduct()->getTaxAmount()));

            $productTaxes = $cartItem->getProduct()->getTaxCalculator();

            if ($productTaxes instanceof TaxCalculator) {
                $productTaxes = $productTaxes->getTaxes();
                $itemTaxes = new Object\Fieldcollection();
                $itemTaxAmounts = $cartItem->getProduct()->getTaxAmount(true);

                foreach ($productTaxes as $tax) {
                    $itemTax = Order\Tax::create();

                    $itemTax->setName($tax->getName());
                    $itemTax->setRate($tax->getRate());
                    $itemTax->setAmount(Tool::roundPrice($itemTaxAmounts[$tax->getId()]));

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
            $tax->setAmount(Tool::roundPrice($taxAmount));

            $taxes->add($tax);
        }

        $this->setTaxes($taxes);
        $this->setDiscount($cart->getDiscount());
        $this->setPriceRule($cart->getPriceRule());
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
     * @throws \Exception
     * @throws \Pimcore\Model\Element\ValidationException
     */
    public function updateOrderItem(Item $item, $amount, $priceWithoutTax) {
        $item->setAmount($amount);
        $item->setPriceWithoutTax($priceWithoutTax);

        //Recalc Tax
        $totalTax = 0;
        $taxes = new Object\Fieldcollection();
        foreach($item->getTaxes() as $tax) {
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
        foreach($allItems as &$oldItem) {
            if($item->getId() === $oldItem->getId()) {
                $oldItem = $item;
            }
        }

        $this->setItems($allItems);

        $this->updateOrderSummary();
    }

    /**
     * Update Order Summary and Taxes
     */
    public function updateOrderSummary() {
        $totalTax = 0;
        $subTotalTax = 0;
        $subTotal = 0;
        $taxRateValues = [];

        $addTax = function ($rate, $amount) use (&$taxRateValues) {
            if (!array_key_exists((string)$rate, $taxRateValues)) {
                $taxRateValues[(string)$rate] = 0;
            }

            $taxRateValues[(string)$rate] += $amount;
        };

        //Recaluclate Subtotal and taxes
        foreach($this->getItems() as $item) {
            $subTotalTax += $item->getTotalTax();
            $subTotal += $item->getTotal();

            foreach($item->getTaxes() as $tax) {
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
        if($this instanceof Object\CoreShopOrder) {
            if($this->getPaymentFeeTaxRate() > 0) {
                $addTax($this->getPaymentFeeTaxRate(), $this->getPaymentFeeTax());
            }

            if($this->getShippingTaxRate()) {
                $addTax($this->getShippingTaxRate(), $this->getShippingTax());
            }
        }

        foreach($this->getTaxes() as $tax) {
            if(array_key_exists((string)$tax->getRate(), $taxRateValues)) {
                $tax->setAmount($taxRateValues[(string)$tax->getRate()]);
            }
        }

        $this->save();
    }

    /**
     * Create a new Payment.
     *
     * @param CorePayment $provider
     * @param $amount
     * @param bool $paid
     *
     * @return Payment
     *
     * @throws \Exception
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

        $translate = Tool::getTranslate();

        $note = $this->createNote('coreshop-order-payment');
        $note->setTitle(sprintf($translate->translate('coreshop_note_order_payment'), $provider->getName(), Tool::formatPrice($amount)));
        $note->setDescription(sprintf($translate->translate('coreshop_note_order_payment_description'), $provider->getName(), Tool::formatPrice(($amount))));
        $note->addData('provider', 'text', $provider->getName());
        $note->addData('amount', 'text', Tool::formatPrice($amount));
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
     * Calculates the subtotal of the Order.
     *
     * @return float
     */
    public function getSubtotalWithoutTax()
    {
        $total = 0;

        foreach ($this->getItems() as $item) {
            $total += $item->getTotalWithoutTax();
        }

        return $total;
    }

    /**
     * Calculates the subtotal of the Order.
     *
     * @return float
     */
    public function getSubtotal()
    {
        $total = 0;

        foreach ($this->getItems() as $item) {
            $total += $item->getTotal();
        }

        return $total;
    }

    /**
     * Calculates the total of the Order.
     *
     * @return int
     */
    public function getTotal()
    {
        $subtotal = $this->getSubtotal();
        $shipping = $this->getShipping();
        $discount = $this->getDiscount();
        $paymentFee = $this->getPaymentFee();

        return ($subtotal + $shipping + $paymentFee) - $discount;
    }

    /**
     * Returns the total payed amount for the Order.
     *
     * @return float|int
     *
     * @throws UnsupportedException
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
     * @return int
     */
    public function getTotalWeight()
    {
        $weight = 0;

        foreach ($this->getItems() as $item) {
            $weight += ($item->getAmount() * $item->getProduct()->getWeight());
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
        $address = $this->getShippingAddress()->getItems();

        if (count($address) > 0) {
            return $address[0];
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
        $address = $this->getBillingAddress()->getItems();

        if (count($address) > 0) {
            return $address[0];
        }

        return false;
    }

    /**
     * checks if shipping and billing addresses are the same.
     *
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
     * @throws UnsupportedException
     */
    public function getPaymentProviderObject()
    {
        $paymentProvider = $this->getPaymentProvider();

        return Plugin::getPaymentProvider($paymentProvider);
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
                \Logger::error($ex);
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
    public function getOrderStateHistory() {
        $noteList = new Note\Listing();
        /* @var \Pimcore\Model\Element\Note\Listing $noteList */

        $noteList->addConditionParam('type = ?', 'coreshop-orderstate');
        $noteList->addConditionParam('cid = ?', $this->getId());

        $noteList->setOrderKey('date');
        $noteList->setOrder('desc');

        return $noteList->load();
    }

    /**
     * set discount for order
     * this method has to be overwritten in Pimcore Object.
     *
     * @param State $state
     *
     * @throws UnsupportedException
     */
    public function setOrderState($state)
    {
        throw new UnsupportedException('setOrderState is not supported for '.get_class($this));
    }

    /**
     * set discount for order
     * this method has to be overwritten in Pimcore Object.
     *
     * @param float $discount
     *
     * @throws UnsupportedException
     */
    public function setDiscount($discount)
    {
        throw new UnsupportedException('setDiscount is not supported for '.get_class($this));
    }

    /**
     * returns discount for order
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return float
     */
    public function getDiscount()
    {
        throw new UnsupportedException('getDiscount is not supported for '.get_class($this));
    }

    /**
     * returns customer for order
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return User
     */
    public function getCustomer()
    {
        throw new UnsupportedException('getCustomer is not supported for '.get_class($this));
    }

    /**
     * returns shipping for order
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return float
     */
    public function getShipping()
    {
        throw new UnsupportedException('getShipping is not supported for '.get_class($this));
    }

    /**
     * returns paymentFee for order
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return float
     */
    public function getPaymentFee()
    {
        throw new UnsupportedException('getPaymentFee is not supported for '.get_class($this));
    }

    /**
     * set PriceRule for order
     * this method has to be overwritten in Pimcore Object.
     *
     * @param PriceRule $priceRule
     *
     * @throws UnsupportedException
     */
    public function setPriceRule($priceRule)
    {
        throw new UnsupportedException('setPriceRule is not supported for '.get_class($this));
    }

    /**
     * set items for order
     * this method has to be overwritten in Pimcore Object.
     *
     * @param Item[] $items
     *
     * @throws UnsupportedException
     */
    public function setItems($items)
    {
        throw new UnsupportedException('setItems is not supported for '.get_class($this));
    }

    /**
     * returns payments
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return Payment[]
     */
    public function getPayments()
    {
        throw new UnsupportedException('getPayments is not supported for '.get_class($this));
    }

    /**
     * sets payments
     * this method has to be overwritten in Pimcore Object.
     *
     * @param Payment[] $payments
     *
     * @throws UnsupportedException
     */
    public function setPayments($payments)
    {
        throw new UnsupportedException('setPayments is not supported for '.get_class($this));
    }

    /**
     * returns orderitems
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return Item[]
     */
    public function getItems()
    {
        throw new UnsupportedException('getItems is not supported for '.get_class($this));
    }

    /**
     * shipping address.
     *
     * @throws UnsupportedException
     *
     * @return \Pimcore\Model\Object\Fieldcollection
     */
    public function getShippingAddress()
    {
        throw new UnsupportedException('getShippingAddress is not supported for '.get_class($this));
    }

    /**
     * billing address.
     *
     * @throws UnsupportedException
     *
     * @return \Pimcore\Model\Object\Fieldcollection
     */
    public function getBillingAddress()
    {
        throw new UnsupportedException('getBillingAddress is not supported for '.get_class($this));
    }

    /**
     * payment provider Token.
     *
     * @throws UnsupportedException
     *
     * @return string
     */
    public function getPaymentProvider()
    {
        throw new UnsupportedException('getPaymentProvider is not supported for '.get_class($this));
    }

    /**
     * Get OrderState.
     *
     * @throws UnsupportedException
     *
     * @return Order\State
     */
    public function getOrderState()
    {
        throw new UnsupportedException('getOrderStates is not supported for '.get_class($this));
    }

    /**
     * Get Taxes.
     *
     * @throws UnsupportedException
     *
     * @return Object\Fieldcollection
     */
    public function getTaxes()
    {
        throw new UnsupportedException('getTaxes is not supported for '.get_class($this));
    }

    /**
     * Get TrackingCode.
     *
     * @throws UnsupportedException
     *
     * @return string
     */
    public function getTrackingCode()
    {
        throw new UnsupportedException('getTrackingCode is not supported for '.get_class($this));
    }

    /**
     * Get Carrier.
     *
     * @throws UnsupportedException
     *
     * @return Carrier|null
     */
    public function getCarrier()
    {
        throw new UnsupportedException('getCarrier is not supported for '.get_class($this));
    }
}
