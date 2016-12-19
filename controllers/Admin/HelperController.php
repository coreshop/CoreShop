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

use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_HelperController
 */
class CoreShop_Admin_HelperController extends Admin
{
    public function getOrderAction()
    {
        $orderNumber = $this->getParam('orderNumber');

        if ($orderNumber) {
            $list = \CoreShop\Model\Order::getList();
            $list->setCondition('orderNumber = ? OR orderNumber = ?', [$orderNumber, \CoreShop\Model\Order::getValidOrderNumber($orderNumber)]);

            $orders = $list->getObjects();

            if (count($orders) > 0) {
                $this->_helper->json(['success' => true, 'id' => $orders[0]->getId()]);
            }
        }

        $this->_helper->json(['success' => false]);
    }

    public function getLanguagesAction()
    {
        $locales = \Pimcore\Tool::getSupportedLocales();
        $languageOptions = [];
        foreach ($locales as $short => $translation) {
            if (!empty($short)) {
                $languageOptions[] = [
                    'language' => $short,
                    'display' => $translation." ($short)",
                ];
                $validLanguages[] = $short;
            }
        }

        $this->_helper->json(['languages' => $languageOptions]);
    }
}
