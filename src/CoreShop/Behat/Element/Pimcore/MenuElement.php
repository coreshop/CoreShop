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

namespace CoreShop\Behat\Element\Pimcore;

use CoreShop\Behat\Element\AbstractElement;

class MenuElement extends AbstractElement implements MenuElementInterface
{
    public function hasMenuWithIdentifier(string $id): bool
    {
        return $this->getDocument()->has('css', '#' . $id);
    }

    public function openMenuWithIdentifier(string $id): void
    {
        $this->getDocument()->find('css', '#' . $id)->click();
    }

    public function aMenuIsOpen(): bool
    {
        return $this->getOpenMenu() !== null;
    }

    public function openMenuHasItems(int $count): bool
    {
        $menu = $this->getOpenMenu();

        return count($menu->findAll('css', '.x-menu-item')) === $count;
    }

    public function hoverOverItemWithName(string $name): void
    {
        $menu = $this->getOpenMenu();

        foreach ($menu->findAll('css', '.x-menu-item') as $item) {
            $sub = $item->find('css', '.x-menu-item-text');

            if (!$sub) {
                continue;
            }

            if ($name === $sub->getText()) {
                $sub->mouseOver();
                sleep(1);
            }
        }
    }

    public function twoMenusShouldBeOpen(): bool
    {
        $count = 0;

        foreach ($this->getDocument()->findAll('css', '.pimcore_navigation_flyout') as $element) {
            if (!str_contains($element->getAttribute('style'), 'visiblity')) {
                ++$count;
            }
        }

        return $count === 2;
    }

    protected function getOpenMenu()
    {
        foreach ($this->getDocument()->findAll('css', '.pimcore_navigation_flyout') as $element) {
            if (!str_contains($element->getAttribute('style'), 'visiblity')) {
                return $element;
            }
        }

        return null;
    }
}
