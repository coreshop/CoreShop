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

use Behat\Mink\Driver\PantherDriver;
use Behat\Mink\Exception\ElementNotFoundException;
use CoreShop\Behat\Page\Frontend\AbstractFrontendPage;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class ChangeProfilePage extends AbstractFrontendPage implements ChangeProfilePageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_customer_settings';
    }

    public function checkValidationMessageFor(string $element, string $message): bool
    {
        $errorLabel = $this->getElement($element)->getParent()->find('css', '[data-test-validation-error]');

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '[data-test-validation-error]');
        }

        return $message === $errorLabel->getText();
    }

    public function specifyFirstname(?string $firstname = null): void
    {
        $this->getElement('firstname')->setValue($firstname);
    }

    public function specifyLastname(?string $lastname = null): void
    {
        $this->getElement('lastname')->setValue($lastname);
    }

    public function specifyEmail(?string $email = null): void
    {
        $this->getElement('email')->setValue($email);
    }

    public function specifyConfirmationEmail(?string $email = null): void
    {
        $this->getElement('confirmation_email')->setValue($email);
    }

    public function save(): void
    {
        $this->getElement('save_changes')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'save_changes' => '[data-test-save-changes]',
            'firstname' => '[data-test-firstname]',
            'lastname' => '[data-test-lastname]',
            'email' => '[data-test-email-first]',
            'confirmation_email' => '[data-test-email-second]',
        ]);
    }
}
