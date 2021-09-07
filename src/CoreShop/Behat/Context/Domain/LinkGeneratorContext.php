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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Pimcore\Routing\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;
use Webmozart\Assert\Assert;

final class LinkGeneratorContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private LinkGeneratorInterface $linkGenerator;

    public function __construct(SharedStorageInterface $sharedStorage, LinkGeneratorInterface $linkGenerator)
    {
        $this->sharedStorage = $sharedStorage;
        $this->linkGenerator = $linkGenerator;
    }

    /**
     * @Then /^the generated url for (object) should be "([^"]+)"/
     */
    public function theGeneratedUrlForObjectShouldBe(Concrete $object, $url): void
    {
        $generatedUrl = $this->linkGenerator->generate($object, null, ['_locale' => 'en']);

        Assert::eq(
            $generatedUrl,
            $url,
            sprintf(
                'The generated URL should be "%s" but it is "%s" instead',
                $generatedUrl,
                $url
            )
        );
    }

    /**
     * @Then /^the generated url for (object) with route "([^"]+)" should be "([^"]+)"/
     */
    public function theGeneratedUrlForObjectWithRouteShouldBe(Concrete $object, $routeName, $url): void
    {
        $generatedUrl = $this->linkGenerator->generate($object, $routeName, ['_locale' => 'en']);

        Assert::eq(
            $generatedUrl,
            $url,
            sprintf(
                'The generated URL should be "%s" but it is "%s" instead',
                $generatedUrl,
                $url
            )
        );
    }

    /**
     * @Then /^the generated url for route "([^"]+)" should be "([^"]+)"/
     */
    public function theGeneratedUrlForRouteShouldBe($route, $url): void
    {
        $generatedUrl = $this->linkGenerator->generate(null, $route, ['_locale' => 'en']);

        Assert::eq(
            $generatedUrl,
            $url,
            sprintf(
                'The generated URL should be "%s" but it is "%s" instead',
                $generatedUrl,
                $url
            )
        );
    }
}
