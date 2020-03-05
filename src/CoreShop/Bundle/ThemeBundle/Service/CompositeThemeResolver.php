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

use Zend\Stdlib\PriorityQueue;

final class CompositeThemeResolver implements ThemeResolverInterface
{
    private $themeResolvers;

    public function __construct()
    {
        $this->themeResolvers = new PriorityQueue();
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
    public function resolveTheme(ActiveThemeInterface $activeTheme): void
    {
        foreach ($this->themeResolvers as $themeResolver) {
            try {
                $themeResolver->resolveTheme($activeTheme);
            } catch (ThemeNotResolvedException $exception) {
                continue;
            }
        }
    }
}
