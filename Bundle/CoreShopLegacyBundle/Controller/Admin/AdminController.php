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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Controller\Admin;

/**
 * Class AdminController
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Controller
 */
class AdminController extends \Pimcore\Bundle\PimcoreAdminBundle\Controller\AdminController
{
    /**
     * Extract the action name.
     *
     * @return string
     */
    public function getActionName()
    {
        $action = $this->get('_controller');
        $action = explode('::', $action);

        // use this line if you want to remove the trailing "Action" string
        //return isset($action[1]) ? preg_replace('/Action$/', '', $action[1]) : false;

        return str_replace("Action", "", $action[1]);
    }
}
