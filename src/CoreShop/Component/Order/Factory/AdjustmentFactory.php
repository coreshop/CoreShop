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

namespace CoreShop\Component\Order\Factory;

use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;

class AdjustmentFactory implements AdjustmentFactoryInterface
{
    public function __construct(
        private FactoryInterface $adjustmentFactory,
    ) {
    }

    public function createNew()
    {
        return $this->adjustmentFactory->createNew();
    }

    public function createWithData(string $type, string $label, int $amountGross, int $amountNet, bool $neutral = false): AdjustmentInterface
    {
        $adjustment = $this->createNew();
        $adjustment->setTypeIdentifier($type);
        $adjustment->setLabel($label);
        $adjustment->setAmount($amountGross, $amountNet);
        $adjustment->setNeutral($neutral);

        return $adjustment;
    }
}
