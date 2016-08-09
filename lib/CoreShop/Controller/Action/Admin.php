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

namespace CoreShop\Controller\Action;

/**
 * Class Admin
 * @package CoreShop\Controller\Action
 */
class Admin extends \Pimcore\Controller\Action\Admin
{
    public function init()
    {
        parent::init();

        $this->logCoreShopUsageStatistics();
    }

    /**
     * @throws \Zend_Json_Exception
     */
    protected function logCoreShopUsageStatistics()
    {
        $params = [];
        $disallowedKeys = ["_dc", "module", "controller", "action", "password"];
        foreach ($this->getAllParams() as $key => $value) {
            if (is_json($value)) {
                $value = \Zend_Json::decode($value);
                if (is_array($value)) {
                    array_walk_recursive($value, function (&$item, $key) {
                        if (strpos($key, "pass") !== false) {
                            $item = "*************";
                        }
                    });
                }
                $value = \Zend_Json::encode($value);
            }


            if (!in_array($key, $disallowedKeys) && is_string($value)) {
                $params[$key] = (strlen($value) > 40) ? substr($value, 0, 40) . "..." : $value;
            }
        }

        \Pimcore\Log\Simple::log("coreshop-usagelog",
            ($this->getUser() ? $this->getUser()->getId() : "0") . "|" .
            $this->getParam("module") . "|" .
            $this->getParam("controller") . "|" .
            $this->getParam("action")."|" . @json_encode($params));
    }
}
