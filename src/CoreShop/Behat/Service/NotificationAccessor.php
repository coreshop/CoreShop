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

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;

final class NotificationAccessor implements NotificationAccessorInterface
{
    public function __construct(private Session $session)
    {
    }

    public function getMessageElements(): array
    {
        $messageElements = $this->session->getPage()->findAll('css', '.coreshop-flash-message');

        if (empty($messageElements)) {
            throw new ElementNotFoundException($this->session->getDriver(), 'message element', 'css', '.coreshop-flash-message');
        }

        return $messageElements;
    }
}
