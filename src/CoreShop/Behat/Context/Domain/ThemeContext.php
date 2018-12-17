<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use Liip\ThemeBundle\ActiveTheme;
use Webmozart\Assert\Assert;

final class ThemeContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ThemeResolverInterface
     */
    private $themeResolver;

    /**
     * @var ActiveTheme
     */
    private $activeTheme;

    /**
     * ThemeContext constructor.
     * @param SharedStorageInterface $sharedStorage
     * @param ThemeResolverInterface $themeResolver
     * @param ActiveTheme            $activeTheme
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ThemeResolverInterface $themeResolver,
        ActiveTheme $activeTheme
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->themeResolver = $themeResolver;
        $this->activeTheme = $activeTheme;
    }

    /**
     * @Then /^the current theme name should be "([^"]+)"$/
     */
    public function currentThemeNameIs(string $currentThemeName)
    {
        $this->themeResolver->resolveTheme();

        Assert::same($this->activeTheme->getName(), $currentThemeName);
    }
}
