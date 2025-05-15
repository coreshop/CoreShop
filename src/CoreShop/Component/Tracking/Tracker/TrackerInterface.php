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

namespace CoreShop\Component\Tracking\Tracker;

interface TrackerInterface
{
    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): void;

    public function trackProduct($product): void;

    public function trackProductImpression($product): void;

    public function trackCartAdd($cart, $product, float $quantity = 1.0): void;

    public function trackCartRemove($cart, $product, float $quantity = 1.0): void;

    public function trackCheckoutStep($cart, $stepIdentifier = null, bool $isFirstStep = false, $checkoutOption = null): void;

    public function trackCheckoutComplete($order): void;
}
