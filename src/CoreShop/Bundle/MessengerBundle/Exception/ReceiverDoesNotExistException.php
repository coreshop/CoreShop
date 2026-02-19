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

namespace CoreShop\Bundle\MessengerBundle\Exception;

use Symfony\Component\Messenger\Exception\RuntimeException;

final class ReceiverDoesNotExistException extends RuntimeException
{
    public function __construct(
        string $receiverName,
        array $availableReceivers = [],
    ) {
        $message = sprintf('The receiver "%s" does not exist.', $receiverName);
        if (\count($availableReceivers)) {
            $message .= sprintf(' Valid receivers are: %s.', implode(', ', $availableReceivers));
        }

        parent::__construct($message);
    }
}
