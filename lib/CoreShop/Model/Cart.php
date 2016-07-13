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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Model;

use CoreShop\Exception;
use CoreShop\Exception\UnsupportedException;
use CoreShop\Mail;
use CoreShop\Model\Cart\Item;
use CoreShop\Model\Plugin\Payment as PaymentPlugin;
use CoreShop\Model\Plugin\Payment;
use CoreShop\Model\PriceRule\Action\FreeShipping;
use CoreShop\Model\User\Address;
use CoreShop\Plugin;
use CoreShop\Tool;
use CoreShop\Model\Cart\PriceRule;
use Pimcore\Date;
use Pimcore\Model\Document;
use Pimcore\Model\Object\Service;
use CoreShop\Maintenance\CleanUpCart;

/**
 * Class Cart
 * @package CoreShop\Model
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
        $cartSession = Tool::getSession();
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
            if (Tool::getUser() instanceof User) {
                $cart->setUser(Tool::getUser());
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
            if ($item->getProduct()->getIsDownloadProduct() !== 'yes') {
                return true;
            }
        }

        return false;
    }

    /**
     * calculates discount for the cart.
     *
     * @return int
     */
    public function getDiscount()
    {
        $priceRule = $this->getPriceRule();

        if ($priceRule instanceof PriceRule) {
            return $priceRule->getDiscount();
        }

        return 0;
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
                $subtotal += $item->getAmount() * ($item->getProduct()->getPrice());
            } else {
                $subtotal += $item->getAmount() * ($item->getProduct()->getPrice(false));
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
            $subtotal += ($item->getAmount() * $item->getProduct()->getTaxAmount());
        }

        return $subtotal;
    }

    /**
     * Returns array with key=>value for tax and value.
     *
     * @return array
     */
    public function getTaxes()
    {
        $usedTaxes = array();

        $addTax = function (Tax $tax) use (&$usedTaxes) {
            if (!array_key_exists($tax->getId(), $usedTaxes)) {
                $usedTaxes[$tax->getId()] = array(
                    'tax' => $tax,
                    'amount' => 0,
                );
            }
        };

        foreach ($this->getItems() as $item) {
            $taxCalculator = $item->getProduct()->getTaxCalculator();

            if ($taxCalculator instanceof TaxCalculator) {
                $taxes = $taxCalculator->getTaxes();

                foreach ($taxes as $tax) {
                    $addTax($tax);
                }

                $taxesAmount = $taxCalculator->getTaxesAmount($item->getProduct()->getPrice(false) * $item->getAmount(), true);

                foreach ($taxesAmount as $id => $amount) {
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
     * @throws UnsupportedException
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

        $carrier = Carrier::getCheapestCarrierForCart($this);

        if ($carrier instanceof Carrier) {
            return $carrier;
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
            $freeShippingCurrency = Tool::convertToCurrency($freeShippingCurrency, Tool::getCurrency());

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

            if ($this->getPriceRule() instanceof PriceRule) {
                foreach ($this->getPriceRule()->getActions() as $action) {
                    if ($action instanceof FreeShipping) {
                        return $this->$cacheKey = 0;
                    }
                }
            }

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
        $paymentProvider = Plugin::getPaymentProvider($this->getPaymentModule());

        return $paymentProvider;
    }

    /**
     * Calculate the payment fee.
     *
     * @param $useTaxes boolean use taxes
     *
     * @return float
     */
    public function getPaymentFee($useTaxes = true)
    {
        $paymentProvider = $this->getPaymentProvider();

        if ($paymentProvider instanceof PaymentPlugin) {
            return $paymentProvider->getPaymentFee($this, $useTaxes);
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
        $subtotalTax = $this->getSubtotalTax();
        $shippingTax = $this->getShippingTax();
        $paymentTax = $this->getPaymentFeeTax();

        return $subtotalTax + $shippingTax + $paymentTax;
    }

    /**
     * calculates the total of the cart.
     *
     * @return float
     */
    public function getTotal()
    {
        $subtotal = $this->getSubtotal(false);
        $discount = $this->getDiscount();
        $shipping = $this->getShipping(false);
        $totalTax = $this->getTotalTax();
        $payment = $this->getPaymentFee();

        return ($subtotal + $shipping + $payment + $totalTax) - $discount;
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
     * Removes an existing PriceRule from the cart.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function removePriceRule()
    {
        if ($this->getPriceRule() instanceof PriceRule) {
            $this->getPriceRule()->unApplyRules();

            $this->setPriceRule(null);
            $this->save();
        }

        return true;
    }

    /**
     * Adds a new PriceRule to the Cart.
     *
     * @param \CoreShop\Model\Cart\PriceRule $priceRule
     *
     * @throws Exception
     */
    public function addPriceRule(PriceRule $priceRule)
    {
        $this->removePriceRule();
        $this->setPriceRule($priceRule);
        $this->getPriceRule()->applyRules($this);

        if ($this->getId()) {
            $this->save();
        }
    }

    /**
     * Returns Customers shipping address.
     *
     * @return Address|bool
     */
    public function getCustomerShippingAddress()
    {
        if ($this->getShippingAddress()) {
            $address = $this->getShippingAddress()->getItems();

            if (count($address) > 0) {
                return $address[0];
            }
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
        if ($this->getBillingAddress()) {
            $address = $this->getBillingAddress()->getItems();

            if (count($address) > 0) {
                return $address[0];
            }
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
        \Logger::info('Create order for cart '.$this->getId());

        $orderNumber = Order::getNextOrderNumber();

        if (is_null($language)) {
            if (\Zend_Registry::isRegistered("Zend_Locale")) {
                $language = \Zend_Registry::get('Zend_Locale');
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
        $order->setShippingAddress($this->getShippingAddress());
        $order->setBillingAddress($this->getBillingAddress());
        $order->setPaymentProviderToken($paymentModule->getIdentifier());
        $order->setPaymentProvider($paymentModule->getName());
        $order->setPaymentProviderDescription($paymentModule->getDescription());
        $order->setOrderDate(new Date());
        $order->setCurrency(Tool::getCurrency());
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
        $order->save();
        $order->importCart($this);

        if ($totalPayed > 0) {
            $order->createPayment($paymentModule, $totalPayed, true);
        }

        $state->processStep($order);
        
        //Send Confirmation to customer
        $emailDocument = "/" . $order->getLang() . Configuration::get("SYSTEM.MAIL.CONFIRMATION");
        $emailDocument = Document::getByPath($emailDocument);
        
        Mail::sendOrderMail($emailDocument, $order, $order->getOrderState(), true);

        Plugin::actionHook('order.created', array('order' => $order));

        return $order;
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

        \Logger::log('CoreShop cart cleanup: start');
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
                            \Logger::log('CoreShop cart cleanup: remove cart ('.$cart->getId().')');
                        }
                    }

                    Configuration::set('SYSTEM.CART.AUTO_CLEANUP.LAST_RUN', time());
                }
            } catch (\Exception $e) {
                \Logger::log('CoreShop cart cleanup error: '.$e->getMessage());
            }
        }
    }

    /**
     * returns array cart items
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
     * returns active price rule for cart
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return PriceRule
     */
    public function getPriceRule()
    {
        throw new UnsupportedException('getPriceRule is not supported for '.get_class($this));
    }

    /**
     * set shop
     * this method has to be overwritten in Pimcore Object.
     *
     * @param $shop
     *
     * @throws UnsupportedException
     */
    public function setShop($shop)
    {
        throw new UnsupportedException('setShop is not supported for '.get_class($this));
    }

    /**
     * returns active price rule for cart
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return Shop
     */
    public function getShop()
    {
        throw new UnsupportedException('getShop is not supported for '.get_class($this));
    }

    /**
     * sets price rule for this cart
     * this method has to be overwritten in Pimcore Object.
     *
     * @param $priceRule
     *
     * @throws UnsupportedException
     *
     * @return PriceRule
     */
    public function setPriceRule($priceRule)
    {
        throw new UnsupportedException('setPriceRule is not supported for '.get_class($this));
    }

    /**
     * returns user for this cart
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return User
     */
    public function getUser()
    {
        throw new UnsupportedException('getUser is not supported for '.get_class($this));
    }

    /**
     * returns carrier for this cart
     * this method has to be overwritten in Pimcore Object.
     *
     * @throws UnsupportedException
     *
     * @return null|Carrier
     */
    public function getCarrier()
    {
        throw new UnsupportedException('getCarrier is not supported for '.get_class($this));
    }

    /**
     * Prepare to sleep.
     *
     * @return array
     */
    public function __sleep()
    {
        $this->shipping = null;

        return parent::__sleep();
    }
}
