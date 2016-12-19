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

use CoreShop\Model\Wishlist;
use CoreShop\Controller\Action;

/**
 * Class CoreShop_WishlistController
 */
class CoreShop_WishlistController extends Action
{
    /**
     * @var Wishlist
     */
    protected $model;

    public function init()
    {
        parent::init();

        $this->disableLayout();
    }

    public function preDispatch()
    {
        parent::preDispatch();

        $this->prepareWishlist();
    }

    public function addAction()
    {
        $product_id = $this->getParam('product', null);
        $product = \CoreShop\Model\Product::getById($product_id);

        if ($product instanceof \CoreShop\Model\Product && $product->getEnabled() && $product->getAvailableForOrder()) {
            $checkAvailability = $this->model->allowedToAdd($product->getId());

            if ($checkAvailability === true) {
                $this->model->add($product->getId());

                $this->_helper->json(['success' => true, 'wishlist' => $this->model->getWishlist()]);
            } else {
                if ($checkAvailability == 'limit_reached') {
                    $message = $this->view->translate('You reached the limit of products to your wishlist.');
                } elseif ($checkAvailability == 'already_added') {
                    $message = $this->view->translate('This product is already in your wishlist list.');
                } else {
                    $message = 'Error: '.$checkAvailability;
                }

                $this->_helper->json(['success' => false, 'message' => $message]);
            }
        }

        $this->_helper->json(['success' => false, 'wishlist' => $this->model->getWishlist()]);
    }

    public function removeAction()
    {
        $product_id = $this->getParam('product', null);
        $product = \CoreShop\Model\Product::getById($product_id);

        if ($product instanceof \CoreShop\Model\Product) {
            $this->model->remove($product->getId());

            $this->_helper->json(['success' => true, 'wishlist' => $this->model->getWishlist()]);
        }

        $this->_helper->json(['success' => false, 'wishlist' => $this->model->getWishlist()]);
    }

    public function listAction()
    {
        $this->enableLayout();

        $this->view->headTitle($this->view->translate('Wishlist'));

        $productIds = $this->model->getWishlist();
        $products = [];

        if (!empty($productIds)) {
            $list = \CoreShop\Model\Product::getList();
            $list->setCondition('oo_id IN ('.rtrim(str_repeat('?,', count($productIds)), ',').')', $productIds);

            $products = $list->getObjects();
        }

        $this->view->products = $products;
    }

    protected function prepareWishlist()
    {
        $this->model = new Wishlist();
    }
}
