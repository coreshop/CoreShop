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

namespace CoreShop\Behat\Page\Frontend;

use Behat\Mink\Element\NodeElement;

class HomePage extends AbstractFrontendPage implements HomePageInterface
{
    public function getRouteName(): string
    {
        return 'coreshop_index';
    }

    public function getContent(): string
    {
        return $this->getDocument()->getContent();
    }

    public function logOut(): void
    {
        $this->getElement('logout_button')->click();
    }

    public function hasLogoutButton(): bool
    {
        return $this->hasElement('logout_button');
    }

    public function getActiveCurrency(): string
    {
        throw new \Exception('Not implemented yet');
    }

    public function getAvailableCurrencies(): array
    {
        throw new \Exception('Not implemented yet');
    }

    public function switchCurrency(string $currencyCode): void
    {
        $this->getElement('currency_selector')->click();

        $this->getElement('currency_selector_code', ['%code%' => $currencyCode])->click();
    }

    public function getActiveLocale(): string
    {
        throw new \Exception('Not implemented yet');
    }

    public function getAvailableLocales(): array
    {
        throw new \Exception('Not implemented yet');
    }

    public function switchLocale(string $localeCode): void
    {
        throw new \Exception('Not implemented yet');
    }

    public function getLatestProductsNames(): array
    {
        return array_map(
            function (NodeElement $element) {
                return $element->getText();
            },
            $this->getElement('latest_products')->findAll('css', '[data-test-product-name]')
        );
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'latest_products' => '[data-test-latest-products]',
            'logout_button' => '[data-test-logout-button]',
            'currency_selector' => '[data-test-currency-selector]',
            'currency_selector_code' => '[data-test-currency-selector-code="%code%"]',
        ]);
    }
}
