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
