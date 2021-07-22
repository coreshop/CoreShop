<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Locale\Context\FixedLocaleContext;

final class LocaleContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private FixedLocaleContext $fixedLocaleContext;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        FixedLocaleContext $fixedLocaleContext
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->fixedLocaleContext = $fixedLocaleContext;
    }

    /**
     * @Given /^the site operates on locale "([^"]+)"$/
     */
    public function setLocale($locale): void
    {
        $this->fixedLocaleContext->setLocale($locale);
    }
}
