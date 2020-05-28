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

namespace CoreShop\Bundle\StoreBundle\Theme;

use CoreShop\Bundle\ThemeBundle\Service\ActiveThemeInterface;
use CoreShop\Bundle\ThemeBundle\Service\ThemeNotResolvedException;
use CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;

final class StoreThemeResolver implements ThemeResolverInterface
{
    private $storeContext;
    private $storeRepository;

    public function __construct(
        StoreContextInterface $storeContext,
        RepositoryInterface $storeRepository
    ) {
        $this->storeContext = $storeContext;
        $this->storeRepository = $storeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveTheme(ActiveThemeInterface $activeTheme): void
    {
        $themes = [];

        /**
         * @var StoreInterface $store
         */
        foreach ($this->storeRepository->findAll() as $store) {
            $storeTheme = $store->getTemplate();

            if ($storeTheme) {
                $themes[] = $storeTheme;
            }
        }

        $activeTheme->addThemes($themes);

        try {
            $store = $this->storeContext->getStore();

            if ($theme = $store->getTemplate()) {
                $activeTheme->setActiveTheme($theme);

                return;
            }
        } catch (StoreNotFoundException $exception) {
            throw new ThemeNotResolvedException($exception);
        }

        throw new ThemeNotResolvedException();
    }
}
