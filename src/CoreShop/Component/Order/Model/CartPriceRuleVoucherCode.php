<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Resource\Model\SetValuesTrait;
use CoreShop\Component\Resource\Model\TimestampableTrait;

class CartPriceRuleVoucherCode implements CartPriceRuleVoucherCodeInterface
{
    use TimestampableTrait;
    use SetValuesTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var bool
     */
    protected $used;

    /**
     * @var int
     */
    protected $uses;

    /**
     * @var CartPriceRuleInterface
     */
    protected $cartPriceRule;

    /**
     * @var bool
     */
    protected $isCreditCode = false;

    /**
     * @var int
     */
    protected $creditAvailable = 0;

    /**
     * @var CurrencyInterface|null
     */
    protected $creditCurrency;

    /**
     * @var int
     */
    protected $creditUsed = 0;

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getUsed()
    {
        return $this->used;
    }

    public function setUsed($used)
    {
        $this->used = $used;
    }

    public function getUses()
    {
        return $this->uses;
    }

    public function setUses($uses)
    {
        $this->uses = $uses;
    }

    public function isCreditCode()
    {
        return $this->isCreditCode;
    }

    public function setIsCreditCode($isCreditCode)
    {
        $this->isCreditCode = $isCreditCode;
    }

    public function getCreditAvailable()
    {
        return $this->creditAvailable;
    }

    public function setCreditAvailable($creditAvailable)
    {
        $this->creditAvailable = $creditAvailable;
    }

    public function getCreditCurrency()
    {
        return $this->creditCurrency;
    }

    public function setCreditCurrency(?CurrencyInterface $creditCurrency)
    {
        $this->creditCurrency = $creditCurrency;
    }

    public function getCreditUsed()
    {
        return $this->creditUsed;
    }

    public function setCreditUsed($creditUsed)
    {
        $this->creditUsed = $creditUsed;
    }

    public function getCartPriceRule()
    {
        return $this->cartPriceRule;
    }

    public function setCartPriceRule($cartPriceRule = null)
    {
        $this->cartPriceRule = $cartPriceRule;
    }
}
