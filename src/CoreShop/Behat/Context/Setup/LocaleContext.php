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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Component\Locale\Context\FixedLocaleContext;

final class LocaleContext implements Context
{
    public function __construct(private FixedLocaleContext $fixedLocaleContext)
    {
    }

    /**
     * @Given /^the site operates on locale "([^"]+)"$/
     */
    public function setLocale($locale): void
    {
        $this->fixedLocaleContext->setLocale($locale);
    }
}
