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

interface ActiveThemeInterface
{
    /**
     * @param string $activeTheme
     */
    public function setActiveTheme($activeTheme);

    /**
     * @return string
     */
    public function getActiveTheme();

    /**
     * @return array
     */
    public function getThemes();

    /**
     * @param array $themes
     */
    public function setThemes(array $themes);

    /**
     * @param string $theme
     */
    public function addTheme($theme);

    /**
     * @param array $themes
     */
    public function addThemes(array $themes);

    /**
     * @param string $theme
     */
    public function removeTheme($theme);

    /**
     * @param array $themes
     */
    public function removeThemes(array $themes);
}
