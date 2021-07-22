<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Event;

use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\Event;

final class RequestNewsletterConfirmationEvent extends Event
{
    /**
     * @var CustomerInterface
     */
    private $customer;

    /**
     * @var string
     */
    private $confirmLink;

    /**
     * @param CustomerInterface $customer
     * @param string            $confirmLink
     */
    public function __construct(CustomerInterface $customer, $confirmLink)
    {
        $this->customer = $customer;
        $this->confirmLink = $confirmLink;
    }

    /**
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return string
     */
    public function getConfirmLink()
    {
        return $this->confirmLink;
    }
}
