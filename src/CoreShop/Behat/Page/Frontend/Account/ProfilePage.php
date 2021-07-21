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

use CoreShop\Behat\Page\Frontend\AbstractFrontendPage;

class ProfilePage extends AbstractFrontendPage implements ProfilePageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_customer_profile';
    }

    public function hasCustomerName(string $name): bool
    {
        return $this->hasValueInCustomerSection($name);
    }

    public function hasCustomerEmail(string $email): bool
    {
        return $this->hasValueInCustomerSection($email);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'customer' => '[data-test-customer-information]',
        ]);
    }

    private function hasValueInCustomerSection(string $value): bool
    {
        $customerText = $this->getElement('customer')->getText();

        return stripos($customerText, $value) !== false;
    }
}
