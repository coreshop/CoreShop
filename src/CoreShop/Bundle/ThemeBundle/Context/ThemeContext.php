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

declare(strict_types=1);

namespace CoreShop\Bundle\ThemeBundle\Context;

use CoreShop\Bundle\ThemeBundle\Service\ThemeNotResolvedException;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

final class ThemeContext implements ThemeContextInterface
{
    private $resolver;
    private $themeRepository;

    public function __construct(ThemeResolverInterface $resolver, ThemeRepositoryInterface $themeRepository)
    {
        $this->resolver = $resolver;
        $this->themeRepository = $themeRepository;
    }

    public function getTheme(): ?ThemeInterface
    {
        try {
            return $this->themeRepository->findOneByName($this->resolver->resolveTheme());
        } catch (ThemeNotResolvedException $exception) {
            return null;
        }
    }
}
