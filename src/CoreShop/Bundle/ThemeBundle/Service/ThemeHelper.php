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

namespace CoreShop\Bundle\ThemeBundle\Service;

use Sylius\Bundle\ThemeBundle\Context\SettableThemeContext;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

final class ThemeHelper implements ThemeHelperInterface
{
    private ThemeRepositoryInterface $themeRepository;
    private SettableThemeContext $themeContext;

    public function __construct(
        ThemeRepositoryInterface $themeRepository,
        SettableThemeContext $themeContext
    ) {
        $this->themeRepository = $themeRepository;
        $this->themeContext = $themeContext;
    }

    public function useTheme(string $themeName, \Closure $function)
    {
        $backupTheme = $this->themeContext->getTheme();
        $this->themeContext->setTheme(
            $this->themeRepository->findOneByName($themeName)
        );

        $result = $function();

        if ($backupTheme) {
            $this->themeContext->setTheme($backupTheme);
        }

        return $result;
    }
}
