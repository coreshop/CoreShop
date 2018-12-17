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

namespace CoreShop\Bundle\ThemeBundle\Service;

use Liip\ThemeBundle\ActiveTheme;
use Pimcore\Model\Site;

final class ThemeResolver implements ThemeResolverInterface
{
    /**
     * @var ActiveTheme
     */
    private $activeTheme;

    /**
     * @param ActiveTheme $activeTheme
     */
    public function __construct(
        ActiveTheme $activeTheme
    ) {
        $this->activeTheme = $activeTheme;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveTheme()
    {
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

        if (!in_array('standard', $themes)) {
            $themes[] = 'standard';
        }

        $this->activeTheme->setThemes($themes);

        try {
            $currentSite = Site::getCurrentSite();

            if ($theme = $currentSite->getRootDocument()->getKey()) {
                $this->activeTheme->setName($theme);
            }
        } catch (\Exception $exception) {

        }
    }
}

class_alias(ThemeResolver::class, 'CoreShop\Bundle\StoreBundle\Theme\ThemeResolver');