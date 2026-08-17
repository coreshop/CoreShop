<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\MessengerBundle\Messenger;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class FailureReceiversRepository implements FailureReceiversRepositoryInterface
{
    public function __construct(
        private ContainerInterface $failureSenders,
        private array $receiverNames,
    ) {
    }

    public function getReceiversWithFailureReceivers()
    {
        $receivers = [];

        foreach ($this->receiverNames as $receiverName) {
            if ($this->failureSenders->has($receiverName)) {
                $receivers[] = $receiverName;
            }
        }

        return $receivers;
    }

    public function getFailureReceiver(string $receiverName): TransportInterface
    {
        return $this->failureSenders->get($receiverName);
    }
}
