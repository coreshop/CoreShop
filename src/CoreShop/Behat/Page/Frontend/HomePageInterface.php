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

namespace CoreShop\Behat\Page\Frontend;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface HomePageInterface extends SymfonyPageInterface
{
    public function getContent(): string;

    public function hasLogoutButton(): bool;

    public function logOut();

    public function getFullName(): string;

    public function getActiveCurrency(): string;

    public function getAvailableCurrencies(): array;

    public function switchCurrency(string $currencyCode): void;

    public function getActiveLocale(): string;

    public function getAvailableLocales(): array;

    public function switchLocale(string $localeCode): void;

    public function getLatestProductsNames(): array;
}
