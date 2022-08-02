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

namespace CoreShop\Behat\Service\Exception;

use CoreShop\Behat\Service\NotificationType;

final class NotificationExpectationMismatchException extends \RuntimeException
{
    public function __construct(
        NotificationType $expectedType,
        $expectedMessage,
        $code = 0,
        \Exception $previous = null
    ) {
        $message = sprintf(
            'Expected *%s* notification with a "%s" message was not found',
            $expectedType,
            $expectedMessage
        );

        parent::__construct($message, $code, $previous);
    }
}
