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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class LinkGeneratorContext implements Context
{
    public function __construct(
        private RouterInterface $router,
    ) {
    }

    /**
     * @Then /^the generated url for (object) should be "([^"]+)"/
     */
    public function theGeneratedUrlForObjectShouldBe(Concrete $object, $url): void
    {
        $generatedUrl = $object->getClass()->getLinkGenerator()->generate($object, ['_locale' => 'en']);

        Assert::eq(
            $generatedUrl,
            $url,
            sprintf(
                'The generated URL should be "%s" but it is "%s" instead',
                $generatedUrl,
                $url,
            ),
        );
    }

    /**
     * @Then /^the generated url for (object) with route "([^"]+)" should be "([^"]+)"/
     */
    public function theGeneratedUrlForObjectWithRouteShouldBe(Concrete $object, $routeName, $url): void
    {
        $generatedUrl = $object->getClass()->getLinkGenerator()->generate($object, ['_locale' => 'en', 'route' => $routeName]);

        Assert::eq(
            $generatedUrl,
            $url,
            sprintf(
                'The generated URL should be "%s" but it is "%s" instead',
                $generatedUrl,
                $url,
            ),
        );
    }

    /**
     * @Then /^the generated url for route "([^"]+)" should be "([^"]+)"/
     */
    public function theGeneratedUrlForRouteShouldBe($route, $url): void
    {
        $generatedUrl = $this->router->generate($route, ['_locale' => 'en']);

        Assert::eq(
            $generatedUrl,
            $url,
            sprintf(
                'The generated URL should be "%s" but it is "%s" instead',
                $generatedUrl,
                $url,
            ),
        );
    }
}
