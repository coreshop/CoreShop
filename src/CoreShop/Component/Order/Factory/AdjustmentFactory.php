<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Order\Factory;

use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;

class AdjustmentFactory implements AdjustmentFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @param FactoryInterface $adjustmentFactory
     */
    public function __construct(FactoryInterface $adjustmentFactory)
    {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(): AdjustmentInterface
    {
        return $this->adjustmentFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createWithData(string $type, string $label, int $amountGross, int $amountNet, bool $neutral = false)
    {
        $adjustment = $this->createNew();
        $adjustment->setTypeIdentifier($type);
        $adjustment->setLabel($label);
        $adjustment->setAmount($amountGross, $amountNet);
        $adjustment->setNeutral($neutral);

        return $adjustment;
    }
}
