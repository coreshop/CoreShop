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
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Webmozart\Assert\Assert;

final class MenuContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private MenuProviderInterface $menuProvider;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        MenuProviderInterface $menuProvider
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->menuProvider = $menuProvider;
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
