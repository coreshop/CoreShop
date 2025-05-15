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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\MessengerBundle\Controller;

use CoreShop\Bundle\MessengerBundle\Messenger\FailedMessageRejecter;
use CoreShop\Bundle\MessengerBundle\Messenger\FailedMessageRetryer;
use Symfony\Component\HttpFoundation\Request;

/**
 * @psalm-suppress InternalClass
 */
class MessageController extends \Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController
{
    public function deleteStoredMessageAction(Request $request, FailedMessageRejecter $failedMessageRejecter)
    {
        $this->checkPermission('coreshop_permission_messenger');

        $id = (int) $request->request->get('id');
        $receiver = (string) $request->attributes->get('receiverName');

        try {
            $failedMessageRejecter->rejectStoredMessage($receiver, $id);
        } catch (\Exception $exception) {
            //Ignore
        }

        return $this->json(['success' => true]);
    }

    public function retryFailedMessageAction(Request $request, FailedMessageRetryer $failedMessageRetryer)
    {
        $this->checkPermission('coreshop_permission_messenger');

        $id = (int) $request->request->get('id');
        $receiver = (string) $request->attributes->get('receiverName');

        try {
            $failedMessageRetryer->retryFailedMessage($receiver, $id);
        } catch (\Exception $exception) {
            //Ignore
        }

        return $this->json(['success' => true]);
    }
}
