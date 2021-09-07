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

namespace CoreShop\Behat\Service;

use CoreShop\Behat\Service\Exception\NotificationExpectationMismatchException;
use Webmozart\Assert\Assert;

final class NotificationChecker implements NotificationCheckerInterface
{
    private NotificationAccessorInterface $notificationAccessor;

    public function __construct(NotificationAccessorInterface $notificationAccessor)
    {
        $this->notificationAccessor = $notificationAccessor;
    }

    public function checkNotification(string $message, NotificationType $type): void
    {
        foreach ($this->notificationAccessor->getMessageElements() as $messageElement) {
            if (
                false !== strpos($messageElement->getText(), $message) &&
                $messageElement->hasClass($this->resolveClass($type))
            ) {
                return;
            }
        }

        throw new NotificationExpectationMismatchException($type, $message);
    }

    private function resolveClass(NotificationType $type): string
    {
        $typeClassMap = [
            'error' => 'alert-danger',
            'info' => 'alert-info',
            'success' => 'alert-success',
        ];

        Assert::keyExists($typeClassMap, $type->__toString());

        return $typeClassMap[$type->__toString()];
    }
}
