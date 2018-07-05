<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\StoreBundle\Theme;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use Liip\ThemeBundle\ActiveTheme;

final class ThemeResolver implements ThemeResolverInterface
{
    /**
     * @var ActiveTheme
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
     * @param ActiveTheme           $activeTheme
     * @param StoreContextInterface $storeContext
     * @param RepositoryInterface   $storeRepository
     */
    public function __construct(
        ActiveTheme $activeTheme,
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
    public function resolveTheme()
    {
        $themes = [];

        foreach ($this->storeRepository->findAll() as $store) {
            $storeTheme = $store->getTemplate();

            if ($storeTheme) {
                $themes[] = $storeTheme;
            }
        }

        if (!in_array('standard', $themes)) {
            $themes[] = 'standard';
        }

        $this->activeTheme->setThemes($themes);

        try {
            $store = $this->storeContext->getStore();

            if ($theme = $store->getTemplate()) {
                $this->activeTheme->setName($theme);
            }
        } catch (StoreNotFoundException $exception) {
        }
    }
}
