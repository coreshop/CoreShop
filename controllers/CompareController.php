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
use CoreShop\Library\Deposit;
use CoreShopTemplate\Controller\Action;
use Pimcore\Model\Object;
use Pimcore\Model\Object\CoreShopProduct;

class CoreShop_CompareController extends Action
{

    /**
     * @var Deposit
     */
    protected $deposit;

    /**
     * @var int
     */
    protected $maxCompareElements = 3;

    public function init()
    {
        parent::init();
        $this->disableLayout();
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->prepareCompareList();
    }

    public function addAction()
    {
        $product_id = $this->getParam("product", null);
        $product = CoreShopProduct::getById($product_id);

        $isAllowed = true;
        $result = Plugin::getEventManager()->trigger('compare.preAdd', $this, array("product" => $product, "deposit" => $this->deposit, "request" => $this->getRequest()), function ($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }

        if ($isAllowed) {
            if ($product instanceof CoreShopProduct && $product->getEnabled() && $product->getAvailableForOrder()) {
                $checkAvailability = $this->deposit->allowedToAdd($product->getId());

                if ($checkAvailability === true) {
                    //add compare element to session
                    $this->deposit->add($product->getId());

                    $this->_helper->json(array("success" => true, "compareList" => $this->deposit->toArray()));
                } else {
                    if ($checkAvailability == 'limit_reached') {
                        $message = $this->view->translate('You reached the limit of products to compare.');
                    } elseif ($checkAvailability == 'already_added') {
                        $message = $this->view->translate('This product is already in your compare list.');
                    } else {
                        $message = 'Error: ' . $checkAvailability;
                    }
                    $this->_helper->json(array("success" => false, "message" => $message ));
                }
            }
        } else {
            $this->_helper->json(array("success" => false, "message" => 'not allowed'));
        }

        $this->_helper->json(array("success" => false, "compareList" => $this->deposit->toArray()));
    }

    public function removeAction()
    {
        $product_id = $this->getParam("product", null);
        $product = CoreShopProduct::getById($product_id);

        $isAllowed = true;
        $result = Plugin::getEventManager()->trigger('compare.preRemove', $this, array("product" => $product, "deposit" => $this->deposit, "request" => $this->getRequest()), function ($v) {
            return is_bool($v);
        });

        if ($result->stopped()) {
            $isAllowed = $result->last();
        }

        if ($isAllowed) {
            if ($product instanceof CoreShopProduct) {
                $this->deposit->remove($product->getId());
                $this->_helper->json(array("success" => true, "compareList" => $this->deposit->toArray()));
            }
        } else {
            $this->_helper->json(array("success" => false, "message" => 'not allowed'));
        }

        $this->_helper->json(array("success" => false, "compareList" => $this->deposit->toArray()));
    }

    public function listAction()
    {
        $this->enableLayout();

        $message = null;
        $error = false;

        $this->view->headTitle($this->view->translate("Compare List"));

        $productIds = $this->deposit->toArray();

        $products = array();
        $compareValues = array();

        if (!empty($productIds)) {
            if (count($productIds) < 2) {
                $error = true;
                $message = $this->view->translate("you need at least 2 products to start comparing.");
            }

            $list = new Object\CoreShopProduct\Listing();

            $list->setCondition("oo_id IN (" . rtrim(str_repeat('?,', count($productIds)), ',').")", $productIds);

            $products = $list->getObjects();

            $dings = Plugin::getEventManager()->trigger('compare.products', $this, array("products" => $products, "language" => $this->language, "request" => $this->getRequest()), function ($v) {
                return $v;
            });

            if ($dings->stopped()) {
                $compareValues = $dings->last();
            }
        } else {
            $error = true;
            $message = $this->view->translate("no products to compare");
        }

        $this->view->error = $error;
        $this->view->message = $message;

        $this->view->compareProducts = $products;
        $this->view->compareValues = $compareValues;
    }

    protected function prepareCompareList()
    {
        $this->deposit = new Deposit();
        $this->deposit->setNamespace('compare')->setLimit($this->maxCompareElements);
    }
}
