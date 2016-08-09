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
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Maintenance;

use Pimcore\Tool;

/**
 * Class Log
 * @package CoreShop\Maintenance
 */
class Log {
    /**
     *
     */
    public static function maintenance()
    {
        $logFile = PIMCORE_LOG_DIRECTORY . "/coreshop-usagelog.log";

        if (is_file($logFile) && filesize($logFile) > 200000) {
            $data = gzencode(file_get_contents($logFile));
            $response = Tool::getHttpData("https://www.coreshop.org/usage-statistics/", [], ["data" => $data, "hostname" => Tool::getHostname()]);
            if (strpos($response, "true") !== false) {
                @unlink($logFile);
                \Logger::debug("Usage statistics are transmitted and logfile was cleaned");
            } else {
                \Logger::debug("Unable to send usage statistics");
            }
        }
    }
}