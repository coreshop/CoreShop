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

use CoreShop\Plugin;
use CoreShop\Tool;
use CoreShop\Model\Currency;


use Pimcore\Controller\Action\Admin;

class CoreShop_Admin_InstallController extends Admin
{
    public function installThemeAction() {
        $install = new Plugin\Install();

        $install->installTheme();

        $this->_helper->json(array("success" => true));
    }

    public function installThemeDemoDataAction() {
        //$install = new Plugin\Install();
        //$install->installThemeDemo();

        $products = \CoreShop\Model\Product::getAll();
        $taxRule = \CoreShop\Model\TaxRuleGroup::getById(2);

        foreach($products as $pr) {
            $pr->setTaxRule($taxRule);
            $pr->save();
        }

        $this->_helper->json(array("success" => true));
    }

    public function installAction()
    {
        try
        {
            $install = new Plugin\Install();

            PLugin::getEventManager()->trigger('install.pre', null, array("installer" => $install));

            $install->executeSQL("v-0.1");

            //install Data
            $install->installObjectData("orderStates");
            $install->installDocuments("documents");

            $countryTaxClass = $install->createClass("CoreShopCountryTax");

            $fcSpecificAddress = $install->createFieldCollection("CoreShopProductSpecificPrice");
            $fcUserAddress = $install->createFieldcollection('CoreShopUserAddress');

            // create object classes
            $categoryClass = $install->createClass('CoreShopCategory');
            $productClass = $install->createClass('CoreShopProduct');
            $cartClass = $install->createClass('CoreShopCart');
            $cartItemClass = $install->createClass('CoreShopCartItem');
            $userClass = $install->createClass("CoreShopUser");

            $orderItemClass = $install->createClass("CoreShopOrderItem");
            $paymentClass = $install->createClass("CoreShopPayment");
            $orderClass = $install->createClass("CoreShopOrder");

            // create root object folder with subfolders
            $coreShopFolder = $install->createFolders();
            // create custom view for blog objects
            $install->createCustomView($coreShopFolder, array(
                $productClass->getId(),
                $categoryClass->getId(),
                $cartClass->getId(),
                $cartItemClass->getId(),
                $userClass->getId(),
                $orderItemClass->getId(),
                $orderClass->getId(),
                $paymentClass->getId(),
                $countryTaxClass->getId()
            ));
            // create static routes
            $install->createStaticRoutes();
            // create predefined document types
            //$install->createDocTypes();

            $install->createImageThumbnails();
            $install->installTheme();

            Plugin::getEventManager()->trigger('install.post', null, array("installer" => $install));

            $install->setConfigInstalled();

            $success = true;
        }
        catch(Exception $e)
        {
            \Logger::crit($e);

            $success = false;
        }

        $this->_helper->json(array("success" => $success));
    }
}