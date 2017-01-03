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

use CoreShop\Controller\Action\Admin;
use Pimcore\Tool as PimTool;

/**
 * Class CoreShop_Admin_ProductSpecificPriceController
 */
class CoreShop_Admin_ProductSpecificPriceController extends Admin
{
    public function init()
    {
        parent::init();
    }

    public function getConfigAction()
    {
        $this->_helper->json([
            'success' => true,
            'conditions' => \CoreShop\Model\Product\SpecificPrice::getConditionDispatcher()->getTypeKeys(),
            'actions' => \CoreShop\Model\Product\SpecificPrice::getActionDispatcher()->getTypeKeys(),
        ]);
    }
}
