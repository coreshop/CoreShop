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

use CoreShop\Bundle\AddressBundle\Form\Type\AddressType;
use CoreShop\Bundle\CustomerBundle\Form\Type\CustomerType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends FrontendController
{
    public function headerAction(Request $request)
    {
        return $this->renderTemplate('CoreShopFrontendBundle:Customer:_header.html.twig', [
            'catalogMode' => false,
            'customer' => $this->getCustomer(),
        ]);
    }

    public function footerAction()
    {
        return $this->renderTemplate('CoreShopFrontendBundle:Customer:_footer.html.twig', [
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

        return $this->renderTemplate('CoreShopFrontendBundle:Customer:profile.html.twig', [
            'customer' => $customer,
        ]);
    }

    public function ordersAction()
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        return $this->renderTemplate('CoreShopFrontendBundle:Customer:orders.html.twig', [
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

        return $this->renderTemplate('CoreShopFrontendBundle:Customer:order_detail.html.twig', [
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

        return $this->renderTemplate('CoreShopFrontendBundle:Customer:addresses.html.twig', [
            'customer' => $customer,
        ]);
    }

    public function addressAction(Request $request)
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $addressId = $request->get('address');
        $address = $this->get('coreshop.repository.address')->find($addressId);

        if (!$address instanceof AddressInterface) {
            $address = $this->get('coreshop.factory.address')->createNew();
        } else {
            if (!$customer->hasAddress($address)) {
                return $this->redirectToRoute('coreshop_customer_addresses');
            }
        }

        $form = $this->get('form.factory')->createNamed('address', AddressType::class, $address);

        $redirect = $request->get('_redirect', $this->generateUrl('coreshop_customer_addresses'));
        $form->get('_redirect')->setData($redirect);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isValid()) {
                $address = $handledForm->getData();

                $address->setPublished(true);
                $address->setKey(uniqid());
                $address->setParent($this->get('coreshop.object_service')->createFolderByPath(sprintf('/%s/%s', $customer->getFullPath(), $this->getParameter('coreshop.folder.address'))));
                $address->save();

                $customer->addAddress($address);
                $customer->save();

                return $this->redirect($handledForm->get('_redirect')->getData());
            }
        }

        return $this->renderTemplate('CoreShopFrontendBundle:Customer:address.html.twig', [
            'address' => $address,
            'customer' => $customer,
            'form' => $form->createView()
        ]);
    }

    public function addressDeleteAction(Request $request)
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $addressId = $request->get('address');
        $address = $this->get('coreshop.repository.address')->find($addressId);

        if (!$address instanceof AddressInterface) {
            return $this->redirectToRoute('coreshop_customer_addresses');
        } else {
            if (!$customer->hasAddress($address)) {
                return $this->redirectToRoute('coreshop_customer_addresses');
            }
        }

        $address->delete();

        return $this->redirectToRoute('coreshop_customer_addresses');
    }

    public function settingsAction(Request $request)
    {
        $customer = $this->getCustomer();

        if (!$customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_index');
        }

        $form = $this->get('form.factory')->createNamed('', CustomerType::class, $customer, [
            'customer' => $customer->getId(),
            'allow_default_address' => true
        ]);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isValid()) {
                $customer = $handledForm->getData();
                $customer->save();

                return $this->redirectToRoute('coreshop_customer_profile');
            }
        }

        return $this->renderTemplate('CoreShopFrontendBundle:Customer:settings.html.twig', [
            'customer' => $customer,
            'form' => $form->createView()
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
