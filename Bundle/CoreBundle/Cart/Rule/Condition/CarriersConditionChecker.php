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
*/

namespace CoreShop\Bundle\CoreBundle\Cart\Rule\Condition;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class CarriersConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid($subject, array $configuration)
    {
        /*
         * @var $subject CartInterface
         */
        Assert::isInstanceOf($subject, CartInterface::class);

        if (!$subject->getCarrier() instanceof CarrierInterface) {
            return false;
        }

        return in_array($subject->getCarrier()->getId(), $configuration['carriers']);
    }
}
