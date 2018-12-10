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

namespace CoreShop\Component\Shipping\Validator;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;
use Zend\Stdlib\PriorityQueue;

class CompositeShippableCarrierValidator implements ShippableCarrierValidatorInterface
{
    /**
     * @var PriorityQueue|ShippableCarrierValidatorInterface[]
     */
    private $shippableCarrierValidator;

    public function __construct()
    {
        $this->shippableCarrierValidator = new PriorityQueue();
    }

    /**
     * @param ShippableCarrierValidatorInterface $shippableCarrierValidator
     * @param int                                $priority
     */
    public function addValidator(ShippableCarrierValidatorInterface $shippableCarrierValidator, $priority = 0)
    {
        $this->shippableCarrierValidator->insert($shippableCarrierValidator, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function isCarrierValid(CarrierInterface $carrier, ShippableInterface $shippable, AddressInterface $address)
    {
        foreach ($this->shippableCarrierValidator as $shippableCarrierValidator) {
            $isValid = $shippableCarrierValidator->isCarrierValid($carrier, $shippable, $address);

            if (false === $isValid) {
                return false;
            }
        }

        return true;
    }
}
