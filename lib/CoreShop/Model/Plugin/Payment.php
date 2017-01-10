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

namespace CoreShop\Model\Plugin;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Carrier;
use CoreShop\Model\Cart;
use CoreShop\Model\Order;
use CoreShop\Model\Order\State;
use CoreShop\Model\Tax;
use CoreShop\Model\TaxCalculator;
use CoreShop\Plugin;
use Pimcore\Date;
use Pimcore\Model\Object\Service;
use Pimcore\Model\Staticroute;

/**
 * Class Payment
 * @package CoreShop\Model\Plugin
 */
abstract class Payment implements AbstractPlugin
{
    /**
     * Check if available for cart.
     *
     * @param Cart $cart
     * @returns boolean if available
     */
    public function isAvailable(Cart $cart)
    {
        return true;
    }

    /**
     * Get Payment Fee.
     *
     * @param Cart $cart
     * @param bool $useTaxes
     *
     * @return int
     */
    public function getPaymentFee(Cart $cart, $useTaxes = true)
    {
        return 0;
    }

    /**
     * get payment fee tax.
     *
     * @param Cart $cart
     *
     * @return float
     */
    public function getPaymentFeeTax(Cart $cart)
    {
        $taxCalculator = $this->getPaymentTaxCalculator($cart);

        if ($taxCalculator) {
            return $taxCalculator->getTaxesAmount($this->getPaymentFee($cart, false));
        }

        return 0;
    }

    /**
     * Get Payment Fee Tax Rate.
     *
     * @param Cart $cart
     *
     * @return float
     */
    public function getPaymentFeeTaxRate(Cart $cart)
    {
        $taxCalculator = $this->getPaymentTaxCalculator($cart);

        if ($taxCalculator) {
            return $taxCalculator->getTotalRate();
        }

        return 0;
    }

    /**
     * get payment taxes.
     *
     * @param Cart $cart
     *
     * @return float
     */
    public function getPaymentFeeTaxesAmount(Cart $cart)
    {
        $fee = $this->getPaymentFee($cart, false);

        $taxCalculator = $this->getPaymentTaxCalculator($cart);

        if ($taxCalculator) {
            return $taxCalculator->getTaxesAmount($fee);
        }

        return 0;
    }

    /**
     * get tax calculator for this payment provider.
     *
     * @param Cart $cart
     *
     * @return null|TaxCalculator
     */
    public function getPaymentTaxCalculator(Cart $cart)
    {
        return null;
    }

    /**
     * Process Payment.
     *
     * @param Cart $cart
     *
     * @throws UnsupportedException
     */
    public function process(Cart $cart)
    {
        throw new UnsupportedException('');
    }

    /**
     * Processes the Payment async, this method returns the process module, controller, action
     *
     * @param Cart $cart
     * @throws UnsupportedException
     *
     * @return array
     */
    public function processAsync(Cart $cart)
    {
        throw new UnsupportedException('');
    }

    /**
     * assemble route with zend router.
     *
     * @param $module string module name
     * @param $action string action name
     * @param $params array additional params
     *
     * @return string
     */
    public function url($module, $action, $params = [])
    {
        $params = array_merge($params, ["mod" => $module, "act" => $action, "lang" => (string) \Zend_Registry::get("Zend_Locale")]);

        $url = \CoreShop::getTools()->url($params, \CoreShop::getTools()->getPaymentRoute());
        $url = str_replace("/" . strtolower($this->getName()), "/" . $this->getName(), $url);

        return $url;
    }
}
