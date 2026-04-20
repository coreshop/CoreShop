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

namespace CoreShop\Bundle\TestBundle\Page;

use Behat\Mink\Element\NodeElement;
use CoreShop\Bundle\TestBundle\Service\DriverHelper;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage as BaseSymfonyPage;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;

abstract class SymfonyPage extends BaseSymfonyPage implements SymfonyPageInterface
{
    protected function getElement(string $name, array $parameters = []): NodeElement
    {
        DriverHelper::waitForPageToLoad($this->getSession());

        return parent::getElement($name, $parameters);
    }

    public function verifyRoute(array $requiredUrlParameters = []): void
    {
        $url = $this->getDriver()->getCurrentUrl();
        $path = $this->stripFrontController((string) parse_url($url, PHP_URL_PATH));

        $matchedRoute = $this->router->match($path);

        if ($matchedRoute['_route'] !== $this->getRouteName()) {
            throw new UnexpectedPageException(sprintf(
                "Matched route '%s' does not match the expected route '%s' for URL '%s'",
                $matchedRoute['_route'],
                $this->getRouteName(),
                $url,
            ));
        }

        foreach ($requiredUrlParameters as $key => $value) {
            if (!isset($matchedRoute[$key]) || (string) $matchedRoute[$key] !== (string) $value) {
                throw new UnexpectedPageException(sprintf(
                    "Required route parameter '%s' with value '%s' not found in URL '%s'",
                    $key,
                    $value,
                    $url,
                ));
            }
        }
    }

    protected function verifyUrl(array $urlParameters = []): void
    {
        $url = $this->getDriver()->getCurrentUrl();
        $path = $this->stripFrontController((string) parse_url($url, PHP_URL_PATH));

        $matchedRoute = $this->router->match($path);

        if (isset($matchedRoute['_locale'])) {
            $urlParameters += ['_locale' => $matchedRoute['_locale']];
        }

        if ($this->getSession()->getCurrentUrl() !== $this->getUrl($urlParameters)) {
            throw new UnexpectedPageException(sprintf(
                'Expected to be on "%s" but found "%s" instead',
                $this->getUrl($urlParameters),
                $this->getSession()->getCurrentUrl(),
            ));
        }
    }

    private function stripFrontController(string $path): string
    {
        return preg_replace(
            '#^/(app(_dev|_test|_test_cached)?|index(_test|_test_precision)?)\.php/#',
            '/',
            $path,
        );
    }
}
