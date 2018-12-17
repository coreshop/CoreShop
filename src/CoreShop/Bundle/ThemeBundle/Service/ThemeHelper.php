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

use Liip\ThemeBundle\ActiveTheme;

final class ThemeHelper implements ThemeHelperInterface
{
    /**
     * @var ThemeResolverInterface
     */
    private $themeResolver;

    /**
     * @var ActiveTheme
     */
    private $activeTheme;

    /**
     * @param ThemeResolverInterface $themeResolver
     * @param ActiveTheme            $activeTheme
     */
    public function __construct(
        ThemeResolverInterface $themeResolver,
        ActiveTheme $activeTheme
    ) {
        $this->themeResolver = $themeResolver;
        $this->activeTheme = $activeTheme;
    }

    /**
     * @param string   $themeName
     * @param \Closure $function
     * @return mixed
     */
    public function useTheme($themeName, \Closure $function)
    {
        $this->themeResolver->resolveTheme();

        $backupTheme = $this->activeTheme->getName();
        $this->activeTheme->setName($themeName);

        $result = $function();

        if (in_array($backupTheme, $this->activeTheme->getThemes())) {
            $this->activeTheme->setName($backupTheme);
        } else {
            $this->activeTheme->setName('standard');
        }

        return $result;
    }
}

class_alias(ThemeHelper::class, 'CoreShop\Bundle\StoreBundle\Theme\ThemeHelper');