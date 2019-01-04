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

namespace CoreShop\Bundle\ThemeBundle\Collector;

use CoreShop\Bundle\ThemeBundle\Service\ActiveThemeInterface;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class ThemeCollector extends DataCollector
{
    /**
     * @var ActiveThemeInterface
     */
    private $activeTheme;

    /**
     * @var ThemeResolverInterface
     */
    private $themeResolver;

    /**
     * @param ActiveThemeInterface   $activeTheme
     * @param ThemeResolverInterface $themeResolver
     */
    public function __construct(
        ActiveThemeInterface $activeTheme,
        ThemeResolverInterface $themeResolver
    ) {
        $this->themeResolver = $themeResolver;
        $this->activeTheme = $activeTheme;

        $this->data = [
            'active_theme' => null,
            'themes' => [],
        ];
    }

    /**
     * @return string
     */
    public function getActiveTheme()
    {
        return $this->data['active_theme'];
    }

    /**
     * @return string[]
     */
    public function getThemes()
    {
        return $this->data['themes'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        try {
            $activeTheme = $this->activeTheme;

            $this->themeResolver->resolveTheme($activeTheme);

            $this->data['active_theme'] = $activeTheme->getActiveTheme();
            $this->data['themes'] = $activeTheme->getThemes();
        } catch (\Exception $exception) {
            //If some goes wrong, we just ignore it
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop.theme_collector';
    }
}
