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
use CoreShop\Exception;
use CoreShop\Exception\ObjectUnsupportedException;
use CoreShop\Mail;
use CoreShop\Model\Cart\Item;
use CoreShop\Model\Plugin\Payment as PaymentPlugin;
use CoreShop\Model\Plugin\Payment;
use CoreShop\Model\PriceRule\Action\FreeShipping;
use CoreShop\Model\User\Address;
use CoreShop\Model\Cart\PriceRule;
use Pimcore\Date;
use Pimcore\Logger;
use Pimcore\Model\Document;
use Pimcore\Model\Object\Fieldcollection;
use Pimcore\Model\Object\Service;
use CoreShop\Maintenance\CleanUpCart;
use Pimcore\Model\Object\Listing;

/**
 * Class Cart
 * @package CoreShop\Model
 *
 * @method static Listing\Concrete getByItems ($value, $limit = 0)
 * @method static Listing\Concrete getByCarrier ($value, $limit = 0)
 * @method static Listing\Concrete getByPriceRule ($value, $limit = 0)
 * @method static Listing\Concrete getByCustomIdentifier ($value, $limit = 0)
 * @method static Listing\Concrete getByOrder ($value, $limit = 0)
 * @method static Listing\Concrete getByPaymentModule ($value, $limit = 0)
 * @method static Listing\Concrete getByShop ($value, $limit = 0)
 * @method static Listing\Concrete getByUser ($value, $limit = 0)
 * @method static Listing\Concrete getByShippingAddress ($value, $limit = 0)
 * @method static Listing\Concrete getByBillingAddress ($value, $limit = 0)
 */
class Cart extends Base
{
    /**
     * Pimcore Object Class.
     *
     * @var string
     */
    public static $pimcoreClass = 'Pimcore\\Model\\Object\\CoreShopCart';

    /**
     * @var float shipping costs
     */
    protected $shipping;

    /**
     * @var float shipping without tax
     */
    protected $shippingWithoutTax;

    /**
     * Return Cart by custom identifier.
     *
     * @param $transactionIdentification
     *
     * @return bool|Cart
     */
    public static function findByCustomIdentifier($transactionIdentification)
    {
        $list = self::getByCustomIdentifier($transactionIdentification);

        $carts = $list->load();

        if (count($carts) > 0) {
            return $carts[0];
        }

        return false;
    }

    /**
     * Get all existing Carts.
     *
     * @return array Cart
     */
    public static function getAll()
    {
        $list = self::getList();

        return $list->load();
    }

    /**
     * Prepare a Cart.
     *
     * @param bool $persist
     *
     * @return Cart
     *
     * @throws Exception
     */
    public static function prepare($persist = false)
    {
        $createNew = true;
        $cartSession = \CoreShop::getTools()->getSession();
        $cart = null;

        if ($cartSession->cartObj) {
            if ($cartSession->cartObj instanceof self) {
                $createNew = false;
                $cart = $cartSession->cartObj;
            }
        }

        if ($createNew) {
            $cart = self::create();
            $cart->setKey(uniqid());
            $cart->setPublished(true);
            $cart->setShop(Shop::getShop());
        }

        if ($cart instanceof Cart) {
            if (\CoreShop::getTools()->getUser() instanceof User) {
                $cart->setUser(\CoreShop::getTools()->getUser());
            }

            if ($persist) {
                $cartsFolder = Service::createFolderByPath('/coreshop/carts/' . date('Y/m/d'));
                $cart->setParent($cartsFolder);
                $cart->save();
            }
        }

        return $cart;
    }

    /**
     * Check if Cart has any physical items.
     *
     * @return bool
     */
    public function hasPhysicalItems()
    {
        foreach ($this->getItems() as $item) {
            if (!$item->getIsVirtualProduct()) {
                return true;
            }
        }

        return false;
    }

    /**
     * calculates discount for the cart.
     *
     * @param boolean $withTax
     *
     * @return int
     */
    public function getDiscount($withTax = true)
    {
        $priceRule = $this->getPriceRules();
        $discount = 0;

        foreach ($priceRule as $ruleItem) {
            if ($ruleItem instanceof \CoreShop\Model\PriceRule\Item) {
                $rule = $ruleItem->getPriceRule();

                if ($rule instanceof PriceRule) {
                    $discount += $rule->getDiscount($withTax);
                }
            }
        }

        return $discount;
    }

    /**
     * calculates the discount tax
     *
     * @return number
     */
    public function getDiscountTax() {
        return abs($this->getDiscount(true) - $this->getDiscount(false));
    }

    /**
     * calculates the subtotal for the cart.
     *
     * @param bool $useTaxes use taxes
     *
     * @return float
     */
    public function getSubtotal($useTaxes = true)
    {
        $subtotal = 0;

        foreach ($this->getItems() as $item) {
            if ($useTaxes) {
                $subtotal += $item->getTotal();
            } else {
                $subtotal += $item->getTotal(false);
            }
        }

        return $subtotal;
    }

    /**
     * calculates the subtotal tax for the cart.
     *
     * @return float
     */
    public function getSubtotalTax()
    {
        $subtotal = 0;

        foreach ($this->getItems() as $item) {
            $subtotal += $item->getTotalTax();
        }

        return $subtotal;
    }

    /**
     * Returns array with key=>value for tax and value.
     *
     * @param $applyDiscountToTaxValues
     *
     * @return array
     */
    public function getTaxes($applyDiscountToTaxValues = true)
    {
        $usedTaxes = array();

        $discountTax = $this->getDiscountTax();
        $subtotalEt = $this->getSubtotal(false);

        $addTax = function (Tax $tax) use (&$usedTaxes) {
            if (!array_key_exists($tax->getId(), $usedTaxes)) {
                $usedTaxes[$tax->getId()] = array(
                    'tax' => $tax,
                    'amount' => 0,
                );
            }
        };

        foreach ($this->getItems() as $item) {
            $taxCalculator = $item->getProductTaxCalculator();

            if ($taxCalculator instanceof TaxCalculator) {
                $taxes = $taxCalculator->getTaxes();

                foreach ($taxes as $tax) {
                    $addTax($tax);
                }

                $itemTotal = $item->getTotal(false);
                $itemTotalPercentage = (100 / $subtotalEt) * $itemTotal;
                $itemDiscountedTax = abs(($discountTax / 100) * $itemTotalPercentage);

                $taxesAmount = $taxCalculator->getTaxesAmount($itemTotal, true);
                $totalTaxRate = $taxCalculator->getTotalRate();

                foreach ($taxesAmount as $id => $amount) {
                    if($applyDiscountToTaxValues) {
                        $tax = Tax::getById($id);

                        $taxAmountPercentage = ((100 / $totalTaxRate) * $tax->getRate()) / 100;

                        $amount -= ($itemDiscountedTax * $taxAmountPercentage);
                    }

                    $usedTaxes[$id]['amount'] += $amount;
                }
            }
        }

        $shippingProvider = $this->getShippingProvider();

        if ($shippingProvider instanceof Carrier) {
            $shippingTax = $this->getShippingProvider()->getTaxCalculator();

            if ($shippingTax instanceof TaxCalculator) {
                foreach ($shippingTax->getTaxes() as $tax) {
                    $addTax($tax);
                }

                $taxesAmount = $shippingTax->getTaxesAmount($this->getShipping(false), true);

                foreach ($taxesAmount as $id => $amount) {
                    $usedTaxes[$id]['amount'] += $amount;
                }
            }
        }

        $paymentProvider = $this->getPaymentProvider();

        if ($paymentProvider instanceof PaymentPlugin) {
            if ($paymentProvider->getPaymentTaxCalculator($this) instanceof TaxCalculator) {
                foreach ($paymentProvider->getPaymentTaxCalculator($this) as $tax) {
                    $addTax($tax);
                }

                $taxesAmount = $paymentProvider->getPaymentTaxCalculator($this)->getTaxesAmount($paymentProvider->getPaymentFee($this, false), true);

                foreach ($taxesAmount as $id => $amount) {
                    $usedTaxes[$id]['amount'] += $amount;
                }
            }
        }

        return $usedTaxes;
    }

    /**
     * get shipping carrier for cart (if non selected, get cheapest).
     *
     * @return null|Carrier
     *
     * @throws ObjectUnsupportedException
     */
    public function getShippingProvider()
    {
        if (count($this->getItems()) === 0) {
            return null;
        }

        //check for existing shipping
        if ($this->getCarrier() instanceof Carrier) {
            return $this->getCarrier();
        }

        if($this->hasPhysicalItems()) {
            $carrier = Carrier::getCheapestCarrierForCart($this);

            if ($carrier instanceof Carrier) {
                return $carrier;
            }
        }

        return null;
    }

    /**
     * get Shipping costs for specific carrier.
     *
     * @param Carrier $carrier
     * @param bool    $useTax
     *
     * @return float
     */
    public function getShippingCostsForCarrier(Carrier $carrier, $useTax = true)
    {
        $freeShippingCurrency = floatval(Configuration::get('SYSTEM.SHIPPING.FREESHIPPING_PRICE'));
        $freeShippingWeight = floatval(Configuration::get('SYSTEM.SHIPPING.FREESHIPPING_WEIGHT'));

        if (isset($freeShippingCurrency) && $freeShippingCurrency > 0) {
            $freeShippingCurrency = \CoreShop::getTools()->convertToCurrency($freeShippingCurrency, \CoreShop::getTools()->getCurrency());

            if ($this->getSubtotal() >= $freeShippingCurrency) {
                return 0;
            }
        }

        if (isset($freeShippingWeight) && $freeShippingWeight > 0) {
            if ($this->getTotalWeight() >= $freeShippingWeight) {
                return 0;
            }
        }

        return $carrier->getDeliveryPrice($this, $useTax);
    }

    /**
     * Check if this cart is free shipping
     *
     * @return bool
     */
    public function isFreeShipping()
    {
        $priceRuleCollection = $this->getPriceRules();

        foreach ($priceRuleCollection as $ruleItem) {
            $rule = $ruleItem->getPriceRule();

            if ($rule instanceof PriceRule) {
                foreach ($rule->getActions() as $action) {
                    if ($action instanceof FreeShipping) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * calculates shipping costs for the cart.
     *
     * @param $useTax boolean include taxes
     *
     * @return float
     */
    public function getShipping($useTax = true)
    {
        $cacheKey = $useTax ? 'shipping' : 'shippingWithoutTax';

        if (is_null($this->$cacheKey)) {
            $this->$cacheKey = 0;

            if ($this->getShippingProvider() instanceof Carrier) {
                $this->$cacheKey = $this->getShippingCostsForCarrier($this->getShippingProvider(), $useTax);
            }
        }

        return $this->$cacheKey;
    }

    /**
     * get shipping tax rate.
     *
     * @return int
     */
    public function getShippingTaxRate()
    {
        if ($this->getShippingProvider() instanceof Carrier) {
            return $this->getShippingProvider()->getTaxRate($this);
        }

        return 0;
    }

    /**
     * calculates shipping tax for the cart.
     *
     * @return float
     */
    public function getShippingTax()
    {
        if ($this->getShippingProvider() instanceof Carrier) {
            return $this->getShippingProvider()->getTaxAmount($this);
        }

        return 0;
    }

    /**
     * Get Payment Provider.
     *
     * @return PaymentPlugin
     */
    public function getPaymentProvider()
    {
        $paymentProvider = \CoreShop::getPaymentProvider($this->getPaymentModule());

        return $paymentProvider;
    }

    /**
     * Calculate the payment fee.
     *
     * @param $withTax boolean use taxes
     *
     * @return float
     */
    public function getPaymentFee($withTax = true)
    {
        $paymentProvider = $this->getPaymentProvider();

        if ($paymentProvider instanceof PaymentPlugin) {
            return $paymentProvider->getPaymentFee($this, $withTax);
        }

        return 0;
    }

    /**
     * get payment fee tax rate.
     *
     * @return float
     */
    public function getPaymentFeeTaxRate()
    {
        $paymentProvider = $this->getPaymentProvider();

        if ($paymentProvider instanceof PaymentPlugin) {
            return $paymentProvider->getPaymentFeeTaxRate($this);
        }

        return 0;
    }

    /**
     * Calculate the payment fee tax.
     *
     * @return float
     */
    public function getPaymentFeeTax()
    {
        $paymentProvider = $this->getPaymentProvider();

        if ($paymentProvider instanceof PaymentPlugin) {
            return $paymentProvider->getPaymentFeeTax($this);
        }

        return 0;
    }

    /**
     * get all taxes.
     *
     * @return float
     */
    public function getTotalTax()
    {
        $totalWithTax = $this->getTotal();
        $totalWithoutTax = $this->getTotal(false);

        return abs($totalWithTax - $totalWithoutTax);
    }

    /**
     * calculates the total of the cart.
     *
     * @param boolean $withTax get price with tax or without
     *
     * @return float
     */
    public function getTotal($withTax = true)
    {
        $subtotal = $this->getSubtotal($withTax);
        $discount = $this->getDiscount($withTax);
        $shipping = $this->getShipping($withTax);
        $payment = $this->getPaymentFee($withTax);

        return ($subtotal + $shipping + $payment) - $discount;
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
            $weight += $item->getWeight();
        }

        return $weight;
    }

    /**
     * finds the CartItem for a Product.
     *
     * @param Product $product
     *
     * @return bool
     *
     * @throws Exception
     */
    public function findItemForProduct(Product $product)
    {
        if (!$product instanceof Product) {
            throw new Exception('$product must be instance of Product');
        }

        foreach ($this->getItems() as $item) {
            if ($item->getProduct()->getId() == $product->getId()) {
                return $item;
            }
        }

        return false;
    }

    /**
     * Changes the quantity of a Product in the Cart.
     *
     * @param Product    $product
     * @param int        $amount
     * @param bool|false $increaseAmount
     * @param bool|true  $autoAddPriceRule
     *
     * @return bool|Item
     *
     * @throws Exception
     */
    public function updateQuantity(Product $product, $amount = 0, $increaseAmount = false, $autoAddPriceRule = true)
    {
        if (!$product instanceof Product) {
            throw new Exception('$product must be instance of Product');
        }

        $item = $this->findItemForProduct($product);

        if ($item instanceof Item) {
            if ($amount <= 0) {
                $this->removeItem($item);

                return false;
            } else {
                $newAmount = $amount;

                if ($increaseAmount === true) {
                    $currentAmount = $item->getAmount();

                    if (is_integer($currentAmount)) {
                        $newAmount = $currentAmount + $amount;
                    }
                }

                $item->setAmount($newAmount);
                $item->save();
            }
        } else {
            $items = $this->getItems();

            if (!is_array($items)) {
                $items = array();
            }

            $item = Item::create();
            $item->setKey(uniqid());
            $item->setParent($this);
            $item->setAmount($amount);
            $item->setProduct($product);
            $item->setPublished(true);
            $item->save();

            $items[] = $item;

            $this->setItems($items);
            $this->save();
        }

        if ($autoAddPriceRule) {
            PriceRule::autoAddToCart();
        }

        //Clear Cache of Product Price, cause a PriceRule could change the price
        $product->clearPriceCache();
        $this->checkCarrierValid();

        return $item;
    }

    /**
     * Adds a new item to the cart.
     *
     * @param Product $product
     * @param int     $amount
     *
     * @return bool|Item
     *
     * @throws Exception
     */
    public function addItem(Product $product, $amount = 1)
    {
        $this->prepare(true);

        return $this->updateQuantity($product, $amount, true);
    }

    /**
     * Removes a item from the cart.
     *
     * @param Item $item
     */
    public function removeItem(Item $item)
    {
        $item->getProduct()->clearPriceCache();
        
        $item->delete();

        $this->checkCarrierValid();
    }

    /**
     * Modifies the quantity of a CartItem.
     *
     * @param Item $item
     * @param $amount
     *
     * @return bool|Item
     *
     * @throws Exception
     */
    public function modifyItem(Item $item, $amount)
    {
        return $this->updateQuantity($item->getProduct(), $amount, false);
    }

    /**
     * Check if carrier is still valid
     */
    public function checkCarrierValid() {
        if($this->getCarrier() instanceof Carrier) {
            $carrierValid = true;

            if(!$this->getShippingAddress() instanceof Address) {
                $carrierValid = false;
            }
            else if(!$this->getCarrier()->checkCarrierForCart($this, $this->getShippingAddress())) {
                $carrierValid  = false;
            }

            if(!$carrierValid) {
                $this->setCarrier(null);
                $this->save();
            }
        }
    }

    /**
     * Removes an existing PriceRule from the cart.
     *
     * @param PriceRule $priceRule
     *
     * @return bool
     *
     * @throws Exception
     */
    public function removePriceRule($priceRule)
    {
        if ($priceRule instanceof PriceRule) {
            $priceRule->unApplyRules();

            $priceRules = $this->getPriceRuleFieldCollection();

            foreach ($priceRules->getItems() as $index => $rule) {
                if ($rule->getPriceRule()->getId() === $priceRule->getId()) {
                    $priceRules->remove($index);
                    break;
                }
            }
            $this->setPriceRules($priceRules->getItems());
            $this->save();
        }

        return true;
    }

    /**
     * Adds a new PriceRule to the Cart.
     *
     * @param \CoreShop\Model\Cart\PriceRule $priceRule
     * @param string $voucherCode Voucher Token
     *
     * @throws Exception
     */
    public function addPriceRule(PriceRule $priceRule, $voucherCode)
    {
        $priceRules = $this->getPriceRules();
        $exists = false;

        foreach ($priceRules as $ruleItem) {
            $rule = $ruleItem->getPriceRule();

            if ($rule instanceof PriceRule) {
                if ($rule->getId() === $priceRule->getId()) {
                    $exists = true;
                    break;
                }
            }
        }

        if (!$exists) {
            $priceRuleData = \CoreShop\Model\PriceRule\Item::create();

            $priceRuleData->setPriceRule($priceRule);
            $priceRuleData->setVoucherCode($voucherCode);

            $fieldCollection = $this->getPriceRuleFieldCollection() instanceof Fieldcollection ? $this->getPriceRuleFieldCollection() : new Fieldcollection();
            $fieldCollection->add($priceRuleData);

            $this->setPriceRules($fieldCollection->getItems());

            $priceRule->applyRules($this);

            if ($this->getId()) {
                $this->save();
            }
        }
    }

    /**
     * @return \CoreShop\Model\PriceRule\Item[]
     */
    public function getPriceRules()
    {
        $collection = $this->getPriceRuleFieldCollection();

        if ($collection instanceof Fieldcollection) {
            $priceRules = [];

            foreach($collection->getItems() as $priceRule) {
                if($priceRule instanceof \CoreShop\Model\PriceRule\Item) {
                    if($priceRule->getPriceRule()->getActive()) {
                        $priceRules[] = $priceRule;
                    }
                }
            }

            return $priceRules;
        }

        return [];
    }

    /**
     * @param \CoreShop\Model\PriceRule\Item[] $priceRules
     */
    public function setPriceRules($priceRules)
    {
        $fieldCollection = new Fieldcollection();
        $fieldCollection->setItems($priceRules);

        $this->setPriceRuleFieldCollection($fieldCollection);
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
     * get customers taxation address.
     *
     * @return bool|Address
     */
    public function getCustomerAddressForTaxation()
    {
        $taxationAddress = Configuration::get('SYSTEM.BASE.TAXATION.ADDRESS');

        if (!$taxationAddress) {
            $taxationAddress = 'shipping';
        }

        if ($taxationAddress === 'shipping') {
            return $this->getCustomerShippingAddress();
        }

        return $this->getCustomerBillingAddress();
    }

    /**
     * Creates order for cart
     *
     * @param Order\State $state
     * @param Payment $paymentModule
     * @param $totalPayed
     * @param $language
     *
     * @return Order
     *
     * @throws Exception
     */
    public function createOrder(Order\State $state, Payment $paymentModule, $totalPayed = 0, $language = null)
    {
        Logger::info('Create order for cart '.$this->getId());

        $orderNumber = Order::getNextOrderNumber();

        if (is_null($language)) {
            if (\Zend_Registry::isRegistered("Zend_Locale")) {
                $language = \CoreShop::getTools()->getLocale();
            } else {
                throw new Exception("language not found in registry and not set as param");
            }
        }

        $orderClass = Order::getPimcoreObjectClass();
        $parentFolder = $orderClass::getPathForNewOrder();

        $order = Order::create();
        $order->setKey(\Pimcore\File::getValidFilename($orderNumber));
        $order->setOrderNumber($orderNumber);
        $order->setParent($parentFolder);
        $order->setPublished(true);
        $order->setLang($language);
        $order->setCustomer($this->getUser());
        $order->setPaymentProviderToken($paymentModule->getIdentifier());
        $order->setPaymentProvider($paymentModule->getName());
        $order->setPaymentProviderDescription($paymentModule->getDescription());
        $order->setOrderDate(new Date());

        if (\Pimcore\Config::getFlag("useZendDate")) {
            $order->setOrderDate(Date::now());
        } else {
            $order->setOrderDate(Carbon::now());
        }
        $order->setCurrency(\CoreShop::getTools()->getCurrency());
        $order->setShop($this->getShop());

        if ($this->getCarrier() instanceof Carrier) {
            $order->setCarrier($this->getCarrier());
            $order->setShipping($this->getShipping());
            $order->setShippingWithoutTax($this->getShipping(false));
            $order->setShippingTaxRate($this->getShippingTaxRate());
            $order->setShippingTax($this->getShippingTax());
        } else {
            $order->setShipping(0);
            $order->setShippingTaxRate(0);
            $order->setShippingWithoutTax(0);
            $order->setShippingTax(0);
        }

        $order->setPaymentFee($this->getPaymentFee());
        $order->setPaymentFeeTax($this->getPaymentFeeTax());
        $order->setPaymentFeeWithoutTax($this->getPaymentFee(false));
        $order->setPaymentFeeTaxRate($this->getPaymentFeeTaxRate());
        $order->setTotalTax($this->getTotalTax());
        $order->setTotal($this->getTotal());
        $order->setSubtotal($this->getSubtotal());
        $order->setSubtotalWithoutTax($this->getSubtotal(false));
        $order->setSubtotalTax($this->getSubtotalTax());

        if(\CoreShop::getTools()->getVisitor() instanceof Visitor) {
            $order->setVisitorId(\CoreShop::getTools()->getVisitor()->getId());
        }

        $order->save();

        if($this->getShippingAddress() instanceof Address) {
            $order->setShippingAddress($this->copyAddress($order, $this->getShippingAddress(), "shipping"));
        }

        if($this->getBillingAddress() instanceof Address) {
            $order->setBillingAddress($this->copyAddress($order, $this->getBillingAddress(), "billing"));
        }

        $order->importCart($this);

        if ($totalPayed > 0) {
            $order->createPayment($paymentModule, $totalPayed, true);
        }

        $state->processStep($order);
        
        //Send Confirmation to customer
        $orderMail = Configuration::get("SYSTEM.MAIL.CONFIRMATION." . strtoupper($order->getLang()));
        
        if($orderMail) {
            $emailDocument = Document::getByPath($orderMail);

            if($emailDocument instanceof Document\Email) {
                Mail::sendOrderMail($emailDocument, $order, $order->getOrderState(), true);
            }
        }

        //Remove transaction to prevent double ordering
        $this->setCustomIdentifier(NULL);
        $this->save();

        \CoreShop::actionHook('order.created', array('order' => $order));

        return $order;
    }

    /**
     * Copy Address to order
     *
     * @param Order $order
     * @param Address|null $address
     * @param string $type
     * @return Address
     */
    public function copyAddress(Order $order, Address $address = null, $type = "shipping") {
        Service::loadAllObjectFields($address);

        $newAddress = clone $address;
        $newAddress->setId(null);
        $newAddress->setParent($order->getPathForAddresses());
        $newAddress->setKey($type);
        $newAddress->save();

        return $newAddress;
    }

    /**
     * maintenance job.
     */
    public static function maintenance()
    {
        $lastMaintenance = Configuration::get('SYSTEM.CART.AUTO_CLEANUP.LAST_RUN');

        //initial.
        if (is_null($lastMaintenance)) {
            $lastMaintenance = time() - 90000; //t-25h
        }

        $timeDiff = time() - $lastMaintenance;

        Logger::log('CoreShop cart cleanup: start');
        //since maintenance runs every 5 minutes, we need to check if the last update was 24 hours ago
        if ($timeDiff > 24 * 60 * 60) {
            $cleanUpParams = array();

            $days = Configuration::get('SYSTEM.CART.AUTO_CLEANUP.OLDER_THAN_DAYS');
            $anonCart = Configuration::get('SYSTEM.CART.AUTO_CLEANUP.DELETE_ANONYMOUS');
            $userCart = Configuration::get('SYSTEM.CART.AUTO_CLEANUP.DELETE_USER');

            if (!is_null($days)) {
                $cleanUpParams['olderThanDays'] = (int) $days;
            }
            if ($anonCart) {
                $cleanUpParams['deleteAnonymousCart'] = true;
            }
            if ($userCart) {
                $cleanUpParams['deleteUserCart'] = true;
            }

            try {
                $cleanUpCart = new CleanUpCart();
                $cleanUpCart->setOptions($cleanUpParams);

                if (!$cleanUpCart->hasErrors()) {
                    $elements = $cleanUpCart->getCartElements();

                    if (count($elements) > 0) {
                        foreach ($elements as $cart) {
                            $cleanUpCart->deleteCart($cart);
                            Logger::log('CoreShop cart cleanup: remove cart ('.$cart->getId().')');
                        }
                    }

                    Configuration::set('SYSTEM.CART.AUTO_CLEANUP.LAST_RUN', time());
                }
            } catch (\Exception $e) {
                Logger::log('CoreShop cart cleanup error: '.$e->getMessage());
            }
        }
    }

    /**
     * Adds existing order to cart (re-ordering)
     *
     * @param Order $order
     * @param bool $removeExistingItems
     */
    public function addOrderToCart(Order $order, $removeExistingItems = false) {
        if($removeExistingItems) {
            foreach($this->getItems() as $item) {
                $this->removeItem($item);
            }
        }

        foreach($order->getItems() as $item) {
            if($item->getProduct() instanceof Product) {
                $this->addItem($item->getProduct(), $item->getAmount());
            }
        }
    }

    /**
     * @return string
     */
    public function getCacheKey() {
        $fingerprint = $this->getId();

        foreach ($this->getItems() as $item) {
            if ($item instanceof Cart\Item) {
                $fingerprint .= $item->getAmount() . $item->getProduct()->getId();
            }
        }

        return $fingerprint;
    }

    /**
     *
     */
    public function __sleep()
    {
        $parentVars = parent::__sleep();

        $finalVars = [];
        $notAllowedFields = ['shippingWithoutTax', 'shipping'];

        foreach ($parentVars as $key) {
            if (!in_array($key, $notAllowedFields)) {
                $finalVars[] = $key;
            }
        }

        return $finalVars;
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
     * @return Carrier|null
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
     * @return Fieldcollection|null
     *
     * @throws ObjectUnsupportedException
     */
    public function getPriceRuleFieldCollection()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param Fieldcollection $priceRules
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
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getCustomIdentifier()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $customIdentifier
     *
     * @throws ObjectUnsupportedException
     */
    public function setCustomIdentifier($customIdentifier)
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @return Order|null
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
     * @return string
     *
     * @throws ObjectUnsupportedException
     */
    public function getPaymentModule()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param string $paymentModule
     *
     * @throws ObjectUnsupportedException
     */
    public function setPaymentModule($paymentModule)
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
     * @return User|null
     *
     * @throws ObjectUnsupportedException
     */
    public function getUser()
    {
        throw new ObjectUnsupportedException(__FUNCTION__, get_class($this));
    }

    /**
     * @param User $user
     *
     * @throws ObjectUnsupportedException
     */
    public function setUser($user)
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
}
