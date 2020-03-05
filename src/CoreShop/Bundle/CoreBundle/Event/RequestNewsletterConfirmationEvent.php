<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Event;

use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\Event;

final class RequestNewsletterConfirmationEvent extends Event
{
    private $customer;
    private $confirmLink;

    public function __construct(CustomerInterface $customer, string $confirmLink)
    {
        $this->customer = $customer;
        $this->confirmLink = $confirmLink;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function getConfirmLink(): string
    {
        return $this->confirmLink;
    }
}
