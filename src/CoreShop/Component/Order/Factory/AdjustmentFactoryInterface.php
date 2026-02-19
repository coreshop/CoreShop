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

namespace CoreShop\Component\Order\Factory;

use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;

interface AdjustmentFactoryInterface extends FactoryInterface
{
    public function createWithData(string $type, string $label, int $amountGross, int $amountNet, bool $neutral = false): AdjustmentInterface;
}
