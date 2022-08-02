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

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Event;

use CoreShop\Component\Customer\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class AdminCustomerCreationEvent extends Event
{
    private array $data;

    public function __construct(private CustomerInterface $customer, array $data)
    {
        $this->data = $data;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
