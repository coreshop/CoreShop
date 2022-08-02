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

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreFieldcollection;

abstract class ProposalCartPriceRuleItem extends AbstractPimcoreFieldcollection implements ProposalCartPriceRuleItemInterface
{
    public function getId()
    {
        return $this->getObject()->getId() . '_cart_price_rule_' . $this->getIndex();
    }

    public function getDiscount(bool $withTax = true): int
    {
        return $withTax ? $this->getDiscountGross() : $this->getDiscountNet();
    }

    public function setDiscount(int $discount, bool $withTax = true)
    {
        $withTax ? $this->setDiscountGross($discount) : $this->setDiscountNet($discount);
    }

    public function getDiscountNet(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setDiscountNet(int $discountNet)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getDiscountGross(): int
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function setDiscountGross(int $discountGross)
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
