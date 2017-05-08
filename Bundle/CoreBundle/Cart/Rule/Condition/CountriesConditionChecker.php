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

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Core\Model\CountryInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use Webmozart\Assert\Assert;

class CountriesConditionChecker implements ConditionCheckerInterface
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

        if (!$subject->getCustomer() instanceof CustomerInterface) {
            return false;
        }

        if (!$subject->getInvoiceAddress() instanceof AddressInterface) {
            return false;
        }

        if (!$subject->getInvoiceAddress()->getCountry() instanceof CountryInterface) {
            return false;
        }

        return in_array($subject->getInvoiceAddress()->getCountry()->getId(), $configuration['countries']);
    }
}
