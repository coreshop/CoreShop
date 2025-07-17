<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Behat\Page\Pimcore;

use Behat\Mink\Driver\PantherDriver;

class LoginPage extends AbstractPimcorePage implements LoginPageInterface
{
    public function getRouteName(): string
    {
        return 'pimcore_admin_login';
    }

    public function logIn(): void
    {
        $this->findOrThrow('css', 'button[type=submit]')->click();
        usleep(4000000);

        if ($this->getSession()->getDriver() instanceof PantherDriver) {
            $this->getSession()->getDriver()->getClient()->refreshCrawler();
        }
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
