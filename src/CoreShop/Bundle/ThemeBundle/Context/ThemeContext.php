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
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class ThemeContext implements ThemeContextInterface
{
    private ThemeResolverInterface $resolver;
    private ThemeRepositoryInterface $themeRepository;
    private PimcoreContextResolver $pimcoreContext;
    private RequestStack $requestStack;

    public function __construct(
        ThemeResolverInterface $resolver,
        ThemeRepositoryInterface $themeRepository,
        PimcoreContextResolver $pimcoreContext,
        RequestStack $requestStack,
    )
    {
        $this->resolver = $resolver;
        $this->themeRepository = $themeRepository;
        $this->pimcoreContext = $pimcoreContext;
        $this->requestStack = $requestStack;
    }

    public function getTheme(): ?ThemeInterface
    {
        $request = $this->requestStack->getMasterRequest();

        if (!$request) {
            return null;
        }

        if ($this->pimcoreContext->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_ADMIN)) {
            return null;
        }

        try {
            return $this->themeRepository->findOneByName($this->resolver->resolveTheme());
        } catch (ThemeNotResolvedException $exception) {
            return null;
        }
    }
}
