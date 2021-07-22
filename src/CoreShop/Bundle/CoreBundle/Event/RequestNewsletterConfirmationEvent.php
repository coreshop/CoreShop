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

namespace CoreShop\Bundle\CoreBundle\Event;

use CoreShop\Component\Core\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class RequestNewsletterConfirmationEvent extends Event
{
    private CustomerInterface $customer;
    private string $confirmLink;

    public function __construct(CustomerInterface $customer, $confirmLink)
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
