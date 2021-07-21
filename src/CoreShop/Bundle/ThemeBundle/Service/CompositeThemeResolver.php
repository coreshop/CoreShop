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

declare(strict_types=1);

namespace CoreShop\Bundle\ThemeBundle\Service;

use Laminas\Stdlib\PriorityQueue;

final class CompositeThemeResolver implements ThemeResolverInterface
{
    private PriorityQueue $themeResolvers;

    public function __construct()
    {
        $this->themeResolvers = new PriorityQueue();
    }

    public function register(ThemeResolverInterface $themeResolver, int $priority = 0): void
    {
        $this->themeResolvers->insert($themeResolver, $priority);
    }

    public function resolveTheme(): string
    {
        foreach ($this->themeResolvers as $themeResolver) {
            try {
                return $themeResolver->resolveTheme();
            } catch (ThemeNotResolvedException $exception) {
                continue;
            }
        }

        throw new ThemeNotResolvedException();
    }
}
