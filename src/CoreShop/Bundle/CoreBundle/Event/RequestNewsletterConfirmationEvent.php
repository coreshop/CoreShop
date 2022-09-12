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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Event;

use CoreShop\Component\Core\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class RequestNewsletterConfirmationEvent extends Event
{
    public function __construct(
        private CustomerInterface $customer,
        private $confirmLink,
    ) {
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
