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

namespace CoreShop\Bundle\StoreBundle\Theme;

use CoreShop\Bundle\ThemeBundle\Service\ActiveThemeInterface;
use CoreShop\Bundle\ThemeBundle\Service\ThemeNotResolvedException;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;

final class StoreThemeResolver implements \CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface
{
    /**
     * @var ActiveThemeInterface
     */
    private $activeTheme;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var RepositoryInterface
     */
    private $storeRepository;

    /**
     * @param ActiveThemeInterface  $activeTheme
     * @param StoreContextInterface $storeContext
     * @param RepositoryInterface   $storeRepository
     */
    public function __construct(
        ActiveThemeInterface $activeTheme,
        StoreContextInterface $storeContext,
        RepositoryInterface $storeRepository
    ) {
        $this->activeTheme = $activeTheme;
        $this->storeContext = $storeContext;
        $this->storeRepository = $storeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveTheme(/*ActiveThemeInterface $activeTheme*/)
    {
        if (\func_num_args() === 0) {
            trigger_error(
                'Calling CoreShop\Bundle\ThemeBundle\Service\ThemeResolverInterface::resolveTheme without the CoreShop\Bundle\ThemeBundle\Service\ActiveThemeInterface Service is deprecated since 2.1 and will be removed in 3.0.',
                E_USER_DEPRECATED
            );
            $activeTheme = $this->activeTheme;
        } else {
            $activeTheme = func_get_arg(0);
        }

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
