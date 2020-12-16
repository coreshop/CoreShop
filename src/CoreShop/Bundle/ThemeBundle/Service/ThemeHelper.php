<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\ThemeBundle\Service;

use Sylius\Bundle\ThemeBundle\Context\SettableThemeContext;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

final class ThemeHelper implements ThemeHelperInterface
{
    private $themeContext;
    private $themeRepository;

    public function __construct(
        ThemeRepositoryInterface $themeRepository,
        SettableThemeContext $themeContext
    ) {
        $this->themeRepository = $themeRepository;
        $this->themeContext = $themeContext;
    }

    public function useTheme(string $themeName, \Closure $function): void
    {
        $this->themeContext->setTheme(
            $this->themeRepository->findOneByName($themeName)
        );
    }
}
