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

namespace CoreShop\Component\Pimcore;

use Pimcore\Tool\Admin;

final class ResourceLoader
{
    /**
     * @param $resources
     * @param $minify
     *
     * @return array
     */
    public function loadResources($resources, $minify = false)
    {
        if (PIMCORE_DEVMODE || !$minify) {
            return $resources;
        }

        $scriptContents = '';

        foreach ($resources as $scriptUrl) {
            if (is_file(PIMCORE_WEB_ROOT.$scriptUrl)) {
                $scriptContents .= file_get_contents(PIMCORE_WEB_ROOT.$scriptUrl)."\n\n\n";
            }
        }

        return [Admin::getMinimizedScriptPath($scriptContents)];
    }
}

\class_alias(ResourceLoader::class, 'CoreShop\Bundle\ResourceBundle\Pimcore\ResourceLoader');
