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

namespace CoreShop\Behat\Service;

use CoreShop\Component\Core\Order\OrderMailProcessorInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Pimcore\Model\Document\Email;

final class OrderMailProcessorSpy implements OrderMailProcessorInterface
{
    private ?Email $sentEmailDocument = null;

    public function sendOrderMail(Email $emailDocument, OrderInterface $order, bool $sendInvoices = false, bool $sendShipments = false, array $params = []): bool
    {
        $this->sentEmailDocument = $emailDocument;

        return true;
    }

    public function getSentEmailDocument(): ?Email
    {
        return $this->sentEmailDocument;
    }
}
