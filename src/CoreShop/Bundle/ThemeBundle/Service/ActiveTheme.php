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

namespace CoreShop\Bundle\ThemeBundle\Service;

class ActiveTheme implements ActiveThemeInterface
{
    protected $activeTheme;
    protected $themes = array();

    public function __construct(\Liip\ThemeBundle\ActiveTheme $activeTheme)
    {
        $this->activeTheme = $activeTheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveTheme(): ?string
    {
        return $this->activeTheme->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setActiveTheme($activeTheme): void
    {
        $this->activeTheme->setName($activeTheme);
    }

    /**
     * @return array
     */
    public function getThemes(): array
    {
        return $this->themes;
    }

    /**
     * {@inheritdoc}
     */
    public function setThemes(array $themes): void
    {
        $this->themes = $themes;

        $this->sync();
    }

    /**
     * {@inheritdoc}
     */
    public function addTheme($theme): void
    {
        if (!in_array($theme, $this->themes, true)) {
            $this->themes[] = $theme;

            $this->sync();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeTheme($theme): void
    {
        if (in_array($theme, $this->themes, true)) {
            $this->themes = array_filter($this->themes, function ($e) use ($theme) {
                return $e !== $theme;
            });

            $this->sync();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addThemes(array $themes): void
    {
        foreach ($themes as $theme) {
            $this->addTheme($theme);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeThemes(array $themes): void
    {
        foreach ($themes as $theme) {
            $this->removeTheme($theme);
        }
    }

    protected function sync()
    {
        $this->activeTheme->setThemes($this->themes);
    }
}
