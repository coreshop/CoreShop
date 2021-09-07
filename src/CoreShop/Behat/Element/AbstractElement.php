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

namespace CoreShop\Behat\Element;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Element\Element;

abstract class AbstractElement extends Element
{
    protected function findOrThrow($selector, $locator): NodeElement
    {
        $element = $this->getDocument()->find($selector, $locator);

        if (null === $element) {
            throw new ElementNotFoundException(
                $this->getSession(),
                null,
                $selector,
                $locator
            );
        }

        return $element;
    }
}
