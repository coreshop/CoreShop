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

use CoreShop\Behat\Page\Frontend\AbstractFrontendPage;

class LoginPage extends AbstractFrontendPage implements LoginPageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_login';
    }

    public function hasValidationErrorWith(string $message): bool
    {
        return $this->getElement('validation_error')->getText() === $message;
    }

    public function logIn(): void
    {
        $this->getElement('login_button')->click();
    }

    public function specifyPassword(string $password): void
    {
        $this->getElement('password')->setValue($password);
    }

    public function specifyUsername(string $username): void
    {
        $this->getElement('username')->setValue($username);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'login_button' => '[data-test-login-button]',
            'password' => '[data-test-login-password]',
            'username' => '[data-test-login-username]',
            'validation_error' => '[data-test-flash-message="danger"]',
        ]);
    }
}
