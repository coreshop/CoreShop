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

use Pimcore\Model\Site;

final class PimcoreSiteThemeResolver implements ThemeResolverInterface
{
    /**
     * @var ActiveThemeInterface
     */
    private $activeTheme;

    /**
     * @param ActiveThemeInterface $activeTheme
     */
    public function __construct(
        ActiveThemeInterface $activeTheme
    ) {
        $this->activeTheme = $activeTheme;
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
        $list = new Site\Listing();
        $list->load();
        $sites = $list->getSites();

        /**
         * @var Site $site
         */
        foreach ($sites as $site) {
            $themes[] = $site->getRootDocument()->getKey();
        }

        $activeTheme->addThemes($themes);

        try {
            $currentSite = Site::getCurrentSite();

            if ($theme = $currentSite->getRootDocument()->getKey()) {
                $activeTheme->setActiveTheme($theme);
            }
        } catch (\Exception $exception) {
            throw new ThemeNotResolvedException($exception);
        }
    }
}
