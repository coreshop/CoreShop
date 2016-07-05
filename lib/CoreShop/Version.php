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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop;

use CoreShop\Model\Configuration;

/**
 * Class Version
 * @package CoreShop
 */
class Version
{
    /**
     * CoreShop Config File.
     *
     * @var array
     */
    protected static $config = null;

    /**
     * Get CoreShop Plugin Config.
     *
     * @return array
     */
    protected static function getPluginConfig()
    {
        return Configuration::getPluginConfig()->plugin;
    }

    /**
     * Get CoreShop Version.
     *
     * @return string
     */
    public static function getVersion()
    {
        return self::getPluginConfig()->pluginVersion;
    }

    /**
     * Get CoreShop Build.
     *
     * @return int
     */
    public static function getBuildNumber()
    {
        return self::getPluginConfig()->pluginRevision;
    }

    /**
     * Get CoreShop Git Revision.
     *
     * @return string
     */
    public static function getGitRevision()
    {
        return self::getPluginConfig()->pluginGitRevision;
    }

    /**
     * Get CoreShop Build Timestamp.
     *
     * @return int
     */
    public static function getPluginTimestamp()
    {
        return self::getPluginConfig()->pluginBuildTimestamp;
    }
}
