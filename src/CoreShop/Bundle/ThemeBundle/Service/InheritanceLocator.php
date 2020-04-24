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

use Liip\ThemeBundle\ActiveTheme;
use Liip\ThemeBundle\Locator\FileLocator;
use Sylius\Bundle\ThemeBundle\Loader\CircularDependencyFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class InheritanceLocator extends FileLocator
{
    protected $kernel;
    protected $path;
    protected $basePaths = array();
    protected $pathPatterns;
    protected $activeTheme;
    protected $lastTheme;
    protected $inheritance;

    public function __construct(
        KernelInterface $kernel,
        ActiveTheme $activeTheme,
        $path = null,
        array $paths = array(),
        array $pathPatterns = array(),
        array $inheritance = []
    ) {
        parent::__construct($kernel, $activeTheme, $path, $paths, $pathPatterns);

        $this->inheritance = $inheritance;
    }

    protected function getPathsForBundleResource($parameters)
    {
        if (null === $this->lastTheme) {
            return parent::getPathsForBundleResource($parameters);
        }

        $paths = array();
        $hierarchy = $this->resolveHierarchy();

       foreach ($hierarchy as $theme) {
            $parameters['%current_theme%'] = $theme;

            $paths[] = strtr('%bundle_path%/Resources/themes/%current_theme%/%template%', $parameters);

            if (!empty($parameters['%dir%'])) {
                $paths[] = strtr('%dir%/themes/%current_theme%/%bundle_name%/%template%', $parameters);
            }
        }

        return array_merge($paths, parent::getPathsForBundleResource($parameters));
    }

    protected function getPathsForAppResource($parameters)
    {
        if (null === $this->lastTheme) {
            return parent::getPathsForAppResource($parameters);
        }

        $paths = array();
        $hierarchy = $this->resolveHierarchy();

        foreach ($hierarchy as $theme) {
            $parameters['%current_theme%'] = $theme;

            $paths[] = strtr("%app_path%/themes/%current_theme%/%template%", $parameters);
        }

        return array_merge($paths, parent::getPathsForAppResource($parameters));
    }

    protected function resolveHierarchy()
    {
        $this->checkCircularDependency($this->lastTheme);

        return $this->getThemeHierarchy($this->lastTheme);
    }

    protected function getThemeHierarchy(string $theme): array
    {
        $parents = [];
        $inheritance = isset($this->inheritance[$theme]) ? $this->inheritance[$theme]['parent_themes'] : [];

        foreach ($inheritance as $parent) {
            $parents = array_merge(
                $parents,
                $this->getThemeHierarchy($parent)
            );
        }

        return array_merge([$theme], $parents);
    }

    protected function checkCircularDependency(string $theme, array $previousThemes = []): void
    {
        $inheritance = isset($this->inheritance[$theme]) ? $this->inheritance[$theme]['parent_themes'] : [];

        if (0 === count($inheritance)) {
            return;
        }

        $previousThemes[] = $theme;
        foreach ($inheritance as $parent) {
            if (in_array($parent, $previousThemes, true)) {
                throw new \CoreShop\Bundle\ThemeBundle\Exception\CircularDependencyFoundException(array_merge($previousThemes, [$parent]));
            }

            $this->checkCircularDependency($parent, $previousThemes);
        }
    }
}
