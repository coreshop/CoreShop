<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace Pimcore\Model\Document\Tag;

use Pimcore\ExtensionManager;

/**
 * Class Coreshoparea
 * @package Pimcore\Model\Document\Tag
 */
class Coreshoparea extends Area
{
    /**
     * get area dirs.
     *
     * @return array
     */
    public function getAreaDirs()
    {
        return ExtensionManager::getBrickDirectories($this->getThemeAreaDir());
    }

    /**
     * get bricks config.
     *
     * @return array|mixed
     */
    public function getBrickConfigs()
    {
        return ExtensionManager::getBrickConfigs($this->getThemeAreaDir());
    }

    /**
     * get theme area dir.
     *
     * @return string
     */
    protected function getThemeAreaDir()
    {
        return CORESHOP_TEMPLATE_PATH.'/areas';
    }
}
