<?php

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