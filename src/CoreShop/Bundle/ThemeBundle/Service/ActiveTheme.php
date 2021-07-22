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

namespace CoreShop\Bundle\ThemeBundle\Service;

class ActiveTheme implements ActiveThemeInterface
{
    /**
     * @var \Liip\ThemeBundle\ActiveTheme
     */
    protected $activeTheme;

    /**
     * @var array
     */
    protected $themes = array();

    /**
     * @param \Liip\ThemeBundle\ActiveTheme $activeTheme
     */
    public function __construct(\Liip\ThemeBundle\ActiveTheme $activeTheme)
    {
        $this->activeTheme = $activeTheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveTheme()
    {
        return $this->activeTheme->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setActiveTheme($activeTheme)
    {
        $this->activeTheme->setName($activeTheme);
    }

    /**
     * @return array
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * {@inheritdoc}
     */
    public function setThemes(array $themes)
    {
        $this->themes = $themes;

        $this->sync();
    }

    /**
     * {@inheritdoc}
     */
    public function addTheme($theme)
    {
        if (!in_array($theme, $this->themes, true)) {
            $this->themes[] = $theme;

            $this->sync();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeTheme($theme)
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
    public function addThemes(array $themes)
    {
        foreach ($themes as $theme) {
            $this->addTheme($theme);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeThemes(array $themes)
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
