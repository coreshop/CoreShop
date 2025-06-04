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

namespace CoreShop\Component\Taxation\Model;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TimestampableInterface;
use CoreShop\Component\Resource\Model\ToggleableInterface;
use CoreShop\Component\Resource\Model\TranslatableInterface;

interface TaxRateInterface extends ResourceInterface, TranslatableInterface, TimestampableInterface, ToggleableInterface
{
    public function getId(): ?int;

    /**
     * @return string
     */
    public function getName(?string $language = null);

    public function setName(string $name, ?string $language = null);

    /**
     * @return float
     */
    public function getRate();

    /**
     * @param float $rate
     */
    public function setRate($rate);
}
