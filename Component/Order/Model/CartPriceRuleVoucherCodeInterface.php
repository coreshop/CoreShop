<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
*/

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;

interface CartPriceRuleVoucherCodeInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return \DateTime
     */
    public function getCreationDate();

    /**
     * @param \DateTime $creationDate
     */
    public function setCreationDate($creationDate);

    /**
     * @return boolean
     */
    public function getUsed();

    /**
     * @param boolean $used
     */
    public function setUsed($used);

    /**
     * @return int
     */
    public function getUses();

    /**
     * @param int $uses
     */
    public function setUses($uses);

    /**
     * @return CartPriceRuleInterface
     */
    public function getCartPriceRule();

    /**
     * @param CartPriceRuleInterface $cartPriceRule
     */
    public function setCartPriceRule($cartPriceRule);
}
