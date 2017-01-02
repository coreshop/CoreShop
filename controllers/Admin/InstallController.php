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

use CoreShop\Plugin;
use CoreShop\Controller\Action\Admin;

/**
 * Class CoreShop_Admin_InstallController
 */
class CoreShop_Admin_InstallController extends Admin
{
    public function installAction()
    {
        try {
            $install = new Plugin\Install();

            \Pimcore::getEventManager()->trigger('coreshop.install.pre', null, ['installer' => $install]);

            //install Data
            $install->installObjectData('threadStates', 'Messaging\\Thread\\');
            $install->installObjectData('threadContacts', 'Messaging\\');
            $install->installDocuments('documents');
            $install->installMessagingMails();
            $install->installMessagingContacts();
            $install->installWorkflow();
            $install->installMailRules();

            $install->createFieldCollection('CoreShopOrderTax');
            $install->createFieldCollection('CoreShopPriceRuleItem');

            // create object classes
            $manufacturer = $install->createClass('CoreShopManufacturer');
            $categoryClass = $install->createClass('CoreShopCategory');
            $productClass = $install->createClass('CoreShopProduct');
            $cartClass = $install->createClass('CoreShopCart');
            $cartItemClass = $install->createClass('CoreShopCartItem');
            $userClass = $install->createClass('CoreShopUser');
            $customerGroupClass = $install->createClass('CoreShopCustomerGroup');
            $userAddressClass = $install->createClass('CoreShopUserAddress');

            $orderItemClass = $install->createClass('CoreShopOrderItem');
            $paymentClass = $install->createClass('CoreShopPayment');
            $orderClass = $install->createClass('CoreShopOrder');

            $invoiceItemClass = $install->createClass('CoreShopOrderInvoiceItem');
            $invoiceClass = $install->createClass('CoreShopOrderInvoice');

            $shipmentItemClass = $install->createClass('CoreShopOrderShipmentItem');
            $shipmentClass = $install->createClass('CoreShopOrderShipment');

            // create root object folder with subfolders
            $coreShopFolder = $install->createFolders();
            // create custom view for blog objects
            $install->createCustomView($coreShopFolder, [
                $productClass->getId(),
                $categoryClass->getId(),
                $cartClass->getId(),
                $cartItemClass->getId(),
                $userClass->getId(),
                $userAddressClass->getId(),
                $customerGroupClass->getId(),
                $orderItemClass->getId(),
                $orderClass->getId(),
                $paymentClass->getId(),
                $customerGroupClass->getId(),
                $invoiceClass->getId(),
                $invoiceItemClass->getId(),
                $shipmentClass->getId(),
                $shipmentItemClass->getId(),
                $manufacturer->getId()
            ]);
            // create static routes
            $install->createStaticRoutes();
            // create predefined document types
            //$install->createDocTypes();

            $install->installAdminTranslations(PIMCORE_PLUGINS_PATH.'/CoreShop/install/translations/admin.csv');

            $install->createImageThumbnails();

            \Pimcore::getEventManager()->trigger('coreshop.install.post', null, ['installer' => $install]);

            $install->setConfigInstalled();

            $success = true;
        } catch (Exception $e) {
            \Pimcore\Logger::crit($e);
            throw $e;
            $success = false;
        }

        $this->_helper->json(['success' => $success]);
    }
}
