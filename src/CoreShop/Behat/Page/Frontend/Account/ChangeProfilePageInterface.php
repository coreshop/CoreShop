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
use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface ChangeProfilePageInterface extends PageInterface
{
    public function specifyFirstname(?string $firstname = null): void;

    public function specifyLastname(?string $lastname = null): void;

    public function specifyEmail(?string $email = null): void;

    public function specifyConfirmationEmail(?string $email = null): void;

    public function save(): void;

    /**
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor(string $element, string $message): bool;
}
