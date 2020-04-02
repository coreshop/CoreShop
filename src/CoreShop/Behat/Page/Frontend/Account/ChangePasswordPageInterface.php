<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Page\Frontend\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use CoreShop\Behat\Page\Frontend\FrontendPageInterface;

interface ChangePasswordPageInterface extends FrontendPageInterface
{
    public function specifyCurrentPassword(string $password): void;

    public function specifyNewPassword(string $password): void;

    public function specifyConfirmationPassword(string $password): void;

    public function save(): void;

    /**
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor(string $element, string $message): bool;
}
