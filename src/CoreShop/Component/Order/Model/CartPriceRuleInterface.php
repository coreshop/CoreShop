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

use CoreShop\Component\Resource\Model\TranslatableInterface;
use CoreShop\Component\Rule\Model\RuleInterface;
use Doctrine\Common\Collections\Collection;

interface CartPriceRuleInterface extends RuleInterface, TranslatableInterface
{
    /**
     * @param string|null $language
     *
     * @return string
     */
    public function getLabel(string $language = null);

    /**
     * @param string      $label
     * @param string|null $language
     */
    public function setLabel(string $label, string $language = null);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return bool
     */
    public function getIsVoucherRule();

    /**
     * @param bool $isVoucherRule
     */
    public function setIsVoucherRule($isVoucherRule);

    /**
     * @return Collection|CartPriceRuleVoucherCodeInterface[]
     */
    public function getVoucherCodes();

    /**
     * @return bool
     */
    public function hasVoucherCodes();

    /**
     * @param CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode
     */
    public function addVoucherCode(CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode);

    /**
     * @param CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode
     */
    public function removeVoucherCode(CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode);

    /**
     * @param CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode
     *
     * @return bool
     */
    public function hasVoucherCode(CartPriceRuleVoucherCodeInterface $cartPriceRuleVoucherCode);
}
