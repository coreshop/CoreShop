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

namespace CoreShop\Bundle\ThemeBundle\Service;

use Sylius\Bundle\ThemeBundle\Context\SettableThemeContext;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

final class ThemeHelper implements ThemeHelperInterface
{
    public function __construct(
        private ThemeRepositoryInterface $themeRepository,
        private SettableThemeContext $themeContext,
    ) {
    }

    public function useTheme(string $themeName, \Closure $function)
    {
        $backupTheme = $this->themeContext->getTheme();
        $theme = $this->themeRepository->findOneByName($themeName);

        if ($theme) {
            $this->themeContext->setTheme($theme);
        }

        $result = $function();

        if ($backupTheme) {
            $this->themeContext->setTheme($backupTheme);
        }

        return $result;
    }
}
