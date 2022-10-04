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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Webmozart\Assert\Assert;

final class MenuContext implements Context
{
    public function __construct(
        private MenuProviderInterface $menuProvider,
    ) {
    }

    /**
     * @Then /^the menu "([^"]+)" should have a child with ID "([^"]+)"$/
     */
    public function menuHasAChild(string $menu, string $childId): void
    {
        Assert::isInstanceOf($this->menuProvider->get($menu)->getChild($childId), ItemInterface::class);
    }

    /**
     * @Then /^the menu "([^"]+)" child with id "([^"]+)" should have a child with ID "([^"]+)"$/
     */
    public function menuChildHasAChild(string $menu, string $parentId, string $childId): void
    {
        $this->menuHasAChild($menu, $parentId);

        Assert::isInstanceOf($this->menuProvider->get($menu)->getChild($parentId)->getChild($childId), ItemInterface::class);
    }
}
