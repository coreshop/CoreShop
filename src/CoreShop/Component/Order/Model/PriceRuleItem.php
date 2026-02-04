<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreFieldcollection;

abstract class PriceRuleItem extends AbstractPimcoreFieldcollection implements PriceRuleItemInterface
{
    public function getId(): ?int
    {
        return null;
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
