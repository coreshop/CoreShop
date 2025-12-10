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

namespace CoreShop\Bundle\TestBundle\Service\Resolver;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Webmozart\Assert\Assert;

final class CurrentPageResolver implements CurrentPageResolverInterface
{
    public function __construct(
        private Session $session,
        private UrlMatcherInterface $urlMatcher,
    ) {
    }

    /**
     * @throws \LogicException
     */
    public function getCurrentPageWithForm(array $pages): SymfonyPageInterface
    {
        /**
         * @psalm-suppress PossiblyFalseArgument
         */
        $routeParameters = $this->urlMatcher->match(parse_url($this->session->getCurrentUrl(), \PHP_URL_PATH));

        Assert::allIsInstanceOf($pages, SymfonyPageInterface::class);

        foreach ($pages as $page) {
            if ($routeParameters['_route'] === $page->getRouteName()) {
                return $page;
            }
        }

        throw new \LogicException('Route name could not be matched to provided pages.');
    }
}
