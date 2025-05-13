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

namespace CoreShop\Behat\Page\Pimcore;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ElementNotFoundException;
use CoreShop\Bundle\TestBundle\Page\SymfonyPage;

abstract class AbstractPimcorePage extends SymfonyPage implements PimcorePageInterface
{
    protected static $additionalParameters = ['_locale' => 'en'];

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

    public function waitForPimcore($time = 10000, $condition = null): void
    {
        $start = microtime(true);
        $end = $start + $time / 1000.0;
        $conditions = [];
        if ($condition === null) {
            $defaultCondition = true;
            $conditions = [
                "document.readyState == 'complete'",
                "document.body.classList.contains('coreshop_loaded')",
            ];
            $condition = implode(' && ', $conditions);
        } else {
            $defaultCondition = false;
        }
        // Make sure the AJAX calls are fired up before checking the condition
        $this->getSession()->wait(100, false);
        $this->getSession()->wait($time, $condition);
        // Check if we reached the timeout unless the condition is false to explicitly wait the specified time
        if ($condition !== false && microtime(true) > $end) {
            if ($defaultCondition) {
                foreach ($conditions as $condition_item) {
                    $result = $this->getSession()->evaluateScript($condition_item);
                    if (!$result) {
                        throw new DriverException(
                            sprintf(
                                'Timeout of %d reached when checking on "%s"',
                                $time,
                                $condition_item,
                            ),
                        );
                    }
                }

                return;
            }

            throw new DriverException(sprintf('Timeout of %d reached when checking on %s', $time, $condition));
        }
    }
}
