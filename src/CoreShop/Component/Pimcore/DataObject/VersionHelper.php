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

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\Version;

class VersionHelper
{
    /**
     * This function enables usage of versioning in Pimcore and resets the state of versioning automatically
     * after your functions is finished.
     *
     * @param \Closure $function
     * @param bool     $enabled
     *
     * @return mixed
     */
    public static function useVersioning(\Closure $function, $enabled = true)
    {
        $backup = Version::$disabled;

        if ($enabled) {
            Version::enable();
        } else {
            Version::disable();
        }

        $result = $function();

        if ($backup) {
            Version::disable();
        } else {
            Version::enable();
        }

        return $result;
    }
}

class_alias(VersionHelper::class, 'CoreShop\Component\Pimcore\VersionHelper');
