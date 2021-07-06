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

namespace CoreShop\Bundle\CustomerBundle\Event;

use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @deprecated Deprecated since 2.2.0 and will be removed with 3.0.0. Has been replaced with UserBundle
 */
final class RequestPasswordChangeEvent extends Event
{
    private CustomerInterface $customer;
    private string $resetLink;

    public function __construct(CustomerInterface $customer, string $resetLink)
    {
        $this->customer = $customer;
        $this->resetLink = $resetLink;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function getResetLink(): string
    {
        return $this->resetLink;
    }
}
