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

namespace CoreShop\Bundle\CoreShopLegacyBundle\Plugin\Controller;

/**
 * Class Admin
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Plugin\Controller
 */
class Admin extends \CoreShop\Bundle\CoreShopLegacyBundle\Controller\Action\Admin
{
    /**
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        $notRestrictedActions = [];

        if (!in_array($this->getParam('action'), $notRestrictedActions)) {
            $this->checkPermission('coreshop_permission_plugins');
        }
    }
}
