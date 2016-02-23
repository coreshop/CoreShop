<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace Pimcore\Model\Document\Tag;

use Pimcore\Tool;
use Pimcore\Model;
use Pimcore\ExtensionManager;
use Pimcore\Model\Document;

class Coreshoparea extends Area
{
    /**
     * @return array
     */
    public function getAreaDirs()
    {
        return ExtensionManager::getBrickDirectories($this->getThemeAreaDir());
    }

    public function getBrickConfigs()
    {
        return ExtensionManager::getBrickConfigs($this->getThemeAreaDir());
    }

    protected function getThemeAreaDir()
    {
        return CORESHOP_TEMPLATE_PATH . "/areas";
    }
}
