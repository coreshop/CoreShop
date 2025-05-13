<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

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

    public function waitTillLoaded(): void
    {
        $this->getDocument()->waitFor(10000000, function () {
            return $this->getDocument()->find('css', 'body.coreshop_loaded') && $this->getDocument()->find('css', '#pimcore_menu_coreshop_main');
        });
    }

    public function hasLogoutButton(): bool
    {
        return $this->getDocument()->has('css', '#pimcore_logout');
    }

    public function hasPimcoreTabWithId(string $id): bool
    {
        return $this->getDocument()->has('css', '#pimcore_panel_tabs #' . $id);
    }

    public function openResource(string $application, string $resource): void
    {
        $this->getSession()->executeScript(
            sprintf(
                'coreshop.global.resource.open(\'%s\', \'%s\');',
                $application,
                $resource,
            ),
        );
    }
}
