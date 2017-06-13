<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\FrontendBundle\Controller;

use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends FrontendController
{
    public function headerAction(Request $request)
    {
        return $this->render('CoreShopFrontendBundle:Customer:_header.html.twig', [
            'catalogMode' => false,
            'customer' => $this->getCustomer(),
        ]);
    }

    public function footerAction()
    {
        return $this->render('CoreShopFrontendBundle:Customer:_footer.html.twig', [
            'catalogMode' => false,
            'customer' => $this->getCustomer(),
        ]);
    }

    public function profileAction()
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->render('CoreShopFrontendBundle:Customer:profile.html.twig', [
            'customer' => $customer,
        ]);
    }

    public function ordersAction()
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->render('CoreShopFrontendBundle:Customer:orders.html.twig', [
            'customer' => $customer,
            'orders' => $this->get('coreshop.repository.order')->findByCustomer($this->getCustomer())
        ]);
    }

    public function orderDetailAction(Request $request)
    {
        $orderId = $request->get('order');
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $order = $this->get('coreshop.repository.order')->find($orderId);

        if (!$order instanceof OrderInterface) {
            return $this->redirectToRoute('coreshop_customer_orders');
        }

        if (!$order->getCustomer() instanceof CustomerInterface || $order->getCustomer()->getId() !== $customer->getId()) {
            return $this->redirectToRoute('coreshop_customer_orders');
        }

        return $this->render('CoreShopFrontendBundle:Customer:order_detail.html.twig', [
            'customer' => $customer,
            'order' => $order
        ]);
    }

    public function addressesAction()
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->render('CoreShopFrontendBundle:Customer:addresses.html.twig', [
            'customer' => $customer,
        ]);
    }

    protected function getCustomer()
    {
        try {
            return $this->get('coreshop.context.customer')->getCustomer();
        } catch (\Exception $ex) {

        }

        return null;
    }
}
