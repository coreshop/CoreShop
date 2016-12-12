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

use CoreShop\Controller\Action;

/**
 * Class CoreShop_HelperController
 */
class CoreShop_HelperController extends Action
{
    public function changeCurrencyAction()
    {
        $currencyId = $this->getParam("currency");
        $currency = \CoreShop\Model\Currency::getById($currencyId);

        if ($currency instanceof \CoreShop\Model\Currency)
        {
            $this->session->currencyId = $this->getParam('currency');

            if(\CoreShop::getTools()->getCart()->getId() > 0) {
                \CoreShop::getTools()->getCart()->setCurrency($currency);
                \CoreShop::getTools()->getCart()->save();
            }
        }

        $redirect = $this->getParam('redirect', \CoreShop::getTools()->url(array('language' => $this->lang), 'coreshop_index'));

        $this->redirect($redirect);
    }

    public function changeDisplayPricesWithTaxAction() {
        $displayPricesWithTax = boolval($this->getParam("displayPricesWithTax", true));

        $session = \CoreShop::getTools()->getSession();
        $session->displayPricesWithTax = $displayPricesWithTax;

        $this->_helper->json([
            "success" => true
        ]);
    }
}
