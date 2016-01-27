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

namespace CoreShop;

use CoreShop\Model\Configuration;

class Version {

    /**
     * @var array $config
     */
    protected static $config = null;

    /**
     * @return array
     */
    protected static function getPluginConfig() {
        return Configuration::getPluginConfig();
    }

    /**
     * @return string
     */
    public static function getVersion() {
        return self::getPluginConfig()->plugin->pluginVersion;
    }

    /**
     * @return int
     */
    public static function getBuildNumber() {
        return self::getPluginConfig()->plugin->pluginRevision;
    }

    /**
     * @return string
     */
    public static function getGitRevision() {
        return self::getPluginConfig()->plugin->pluginGitRevision;
    }

    /**
     * @return int
     */
    public static function getPluginTimestamp() {
        return self::getPluginConfig()->plugin->pluginBuildTimestamp;
    }


}
