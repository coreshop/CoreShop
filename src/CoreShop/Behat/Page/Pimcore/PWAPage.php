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

use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;

class PWAPage extends AbstractPimcorePage implements PWAPageInterface
{
    public function getRouteName(): string
    {
        return 'pimcore_admin_index';
    }

    protected function verifyUrl(array $urlParameters = []): void
    {
        $url = preg_replace('/\?.*/', '', $this->getSession()->getCurrentUrl());

        if ($url !== $this->getUrl($urlParameters)) {
            throw new UnexpectedPageException(sprintf('Expected to be on "%s" but found "%s" instead', $this->getUrl($urlParameters), $this->getSession()->getCurrentUrl()));
        }
    }

    public function hasLogoutButton(): bool
    {
        return $this->getDocument()->has('css', '#pimcore_logout');
    }
}
