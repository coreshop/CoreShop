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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use Webmozart\Assert\Assert;

final class ThemeContext implements Context
{
    public function __construct(
        private ThemeResolverInterface $themeResolver,
    ) {
    }

    /**
     * @Then /^the current theme name should be "([^"]+)"$/
     */
    public function currentThemeNameIs(string $currentThemeName): void
    {
        $theme = $this->themeResolver->resolveTheme();

        Assert::same($theme, $currentThemeName);
    }
}
