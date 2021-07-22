<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Page\Frontend\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use CoreShop\Behat\Page\Frontend\AbstractFrontendPage;

class RequestPasswordResetPage extends AbstractFrontendPage implements RequestPasswordResetPageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_customer_password_reset_request';
    }

    /**
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor(string $element, string $message): bool
    {
        $errorLabel = $this->getElement($element)->getParent()->find('css', '[data-test-validation-error]');

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '[data-test-validation-error]');
        }

        return $message === $errorLabel->getText();
    }

    public function reset(): void
    {
        $this->getElement('reset_button')->click();
    }

    public function specifyEmail(?string $email): void
    {
        $this->getElement('email')->setValue($email);
    }

    public function specifyUsername(?string $username): void
    {
        $this->getElement('username')->setValue($username);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '[data-test-reset-email]',
            'username' => '[data-test-reset-username]',
            'reset_button' => '[data-test-request-password-reset-button]',
        ]);
    }
}
