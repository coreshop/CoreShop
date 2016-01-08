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

class CoreShop_Admin_TestController extends \Pimcore\Controller\Action\Admin
{
    public function invoiceAction() {
        $locale = new Zend_Locale("de");
        Zend_Locale::setDefault($locale);
        Zend_Registry::set("Zend_Locale", $locale);

        $order = \Pimcore\Model\Object\CoreShopOrder::getById(1363);
        $doc = \CoreShop\Model\Invoice::generateInvoice($order);

        header('Cache-Control: public');
        header('Content-Type: application/pdf');

        echo $doc->getData();
        exit;
    }

    public function installAction() {
        \CoreShop\Plugin::enableTheme("default");
    }

    public function localAction() {
        $orderState = \CoreShop\Model\OrderState::getById(2);

        $orderState->setEmailDocument("/en/shop/email/payment", "en");
        $orderState->setEmailDocument("/de/shop/email/payment", "de");
        $orderState->save();
    }

    public function local2Action()
    {
        $list = new \CoreShop\Model\OrderState\Listing();
        $states = $list->load();

        foreach($states as $state) {
            echo $state->getEmailDocument("de") . " " . $state->getEmailDocument("en") . "<br/>";
        }

        exit;
    }

    public function testInstallAction() {
        $install = new \CoreShop\Plugin\Install();

        //$install->installObjectData("orderStates");
        $install->installDocuments("documents");
    }
}