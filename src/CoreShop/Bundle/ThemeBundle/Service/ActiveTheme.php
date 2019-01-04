<?php

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
