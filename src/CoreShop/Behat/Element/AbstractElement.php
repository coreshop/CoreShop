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
                $locator,
            );
        }

        return $element;
    }
}
