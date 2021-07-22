<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ThemeBundle\Service;

use Zend\Stdlib\PriorityQueue;

final class CompositeThemeResolver implements ThemeResolverInterface
{
    /**
     * @var PriorityQueue|ThemeResolverInterface[]
     */
    private $themeResolvers;

    /**
     * @var ActiveThemeInterface $activeTheme
     */
    private $activeTheme;

    /**
     * @param ActiveThemeInterface $activeTheme
     */
    public function __construct(ActiveThemeInterface $activeTheme)
    {
        $this->themeResolvers = new PriorityQueue();
        $this->activeTheme = $activeTheme;
    }

    /**
     * @param ThemeResolverInterface $themeResolver
     * @param int                    $priority
     */
    public function register(ThemeResolverInterface $themeResolver, $priority = 0)
    {
        $this->themeResolvers->insert($themeResolver, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveTheme(/*ActiveThemeInterface $activeTheme*/)
    {
        $activeTheme = null;

        if (\func_num_args() === 0) {
            trigger_error(
                'Calling CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface::resolveTheme without the CoreShop\Bundle\ThemeBundle\Service\ActiveThemeInterface Service is deprecated since 2.1 and will be removed in 3.0.',
                E_USER_DEPRECATED
            );
            $activeTheme = $this->activeTheme;
        } else {
            $activeTheme = func_get_arg(0);
        }

        foreach ($this->themeResolvers as $themeResolver) {
            try {
                $themeResolver->resolveTheme($activeTheme);
            } catch (ThemeNotResolvedException $exception) {
                continue;
            }
        }
    }
}

class_alias(CompositeThemeResolver::class, 'CoreShop\Bundle\StoreBundle\Theme\ThemeResolver');
