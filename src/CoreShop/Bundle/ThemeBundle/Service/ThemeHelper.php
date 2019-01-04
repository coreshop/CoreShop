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

namespace CoreShop\Bundle\ThemeBundle\Service;

final class ThemeHelper implements ThemeHelperInterface
{
    /**
     * @var ThemeResolverInterface
     */
    private $themeResolver;

    /**
     * @var ActiveThemeInterface
     */
    private $activeTheme;

    /**
     * @param ThemeResolverInterface $themeResolver
     * @param ActiveThemeInterface   $activeTheme
     */
    public function __construct(
        ThemeResolverInterface $themeResolver,
        ActiveThemeInterface $activeTheme
    ) {
        $this->themeResolver = $themeResolver;
        $this->activeTheme = $activeTheme;
    }

    /**
     * @param string   $themeName
     * @param \Closure $function
     *
     * @return mixed
     */
    public function useTheme($themeName, \Closure $function)
    {
        try {
            $this->themeResolver->resolveTheme($this->activeTheme);

            $backupTheme = $this->activeTheme->getActiveTheme();
            $this->activeTheme->setActiveTheme($themeName);

            $result = $function();

            if (in_array($backupTheme, $this->activeTheme->getThemes())) {
                $this->activeTheme->setActiveTheme($backupTheme);
            } else {
                $this->activeTheme->setActiveTheme('standard');
            }

            return $result;
        } catch (ThemeNotResolvedException $exception) {
            return $function();
        }
    }
}

class_alias(ThemeHelper::class, 'CoreShop\Bundle\StoreBundle\Theme\ThemeHelper');
