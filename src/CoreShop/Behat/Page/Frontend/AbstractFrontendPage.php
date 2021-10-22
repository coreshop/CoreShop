<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Page\Frontend;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

abstract class AbstractFrontendPage extends SymfonyPage implements FrontendPageInterface
{
    protected static $additionalParameters = ['_locale' => 'en'];

    public function isOpenWithUri(string $uri): bool
    {
        return $this->getSession()->getCurrentUrl() !== $uri;
    }

    protected function getUrl(array $urlParameters = []): string
    {
        $urlParameters = array_merge($urlParameters, $this->getAdditionalParameters());

        return parent::getUrl($urlParameters);
    }

    protected function getAdditionalParameters(): array
    {
        return [];
    }

    public function tryToOpenWithUri(string $uri): void
    {
        $absoluteUrl = $this->makePathAbsolute($uri);
        $this->getSession()->visit($absoluteUrl);
    }
}
