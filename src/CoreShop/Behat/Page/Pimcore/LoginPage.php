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

namespace CoreShop\Behat\Page\Pimcore;

class LoginPage extends AbstractPimcorePage implements LoginPageInterface
{
    public function getRouteName(): string
    {
        return 'pimcore_admin_login';
    }

    public function logIn(): void
    {
        $this->findOrThrow('css', 'button[type=submit]')->click();
        $this->waitForPimcore(10000000000);
    }

    public function specifyPassword(string $password): void
    {
        $this->findOrThrow('css', 'input[name=password]')->setValue($password);
    }

    public function specifyUsername(string $username): void
    {
        $this->findOrThrow('css', 'input[name=username]')->setValue($username);
    }
}
