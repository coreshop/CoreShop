<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Shipping\Model;

interface ShippableItemInterface
{
    /**
     * @return int
     */
    public function getTotal(bool $withTax = true);

    public function setTotal(int $total, bool $withTax = true);

    /**
     * @return float
     */
    public function getWidth();

    /**
     * @return float
     */
    public function getHeight();

    /**
     * @return float
     */
    public function getDepth();

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @return mixed
     */
    public function getProduct();
}
