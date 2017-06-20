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

use CoreShop\Bundle\CoreBundle\Form\Type\CustomerRegistrationType;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use Pimcore\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegisterController extends FrontendController
{
    public function registerAction(Request $request)
    {
        $customer = $this->getCustomer();

        if ($customer instanceof CustomerInterface) {
            return $this->redirectToRoute('coreshop_customer_profile');
        }

        $form = $this->get('form.factory')->createNamed('', CustomerRegistrationType::class);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true)) {
            $handledForm = $form->handleRequest($request);

            if ($handledForm->isValid()) {
                $formData = $handledForm->getData();

                $customer = $formData['customer'];
                $address = $formData['address'];

                if (!$customer instanceof \CoreShop\Component\Core\Model\CustomerInterface ||
                    !$address instanceof AddressInterface
                ) {
                    return $this->render('CoreShopFrontendBundle:Register:register.html.twig', [
                        'form' => $form->createView()
                    ]);
                }

                $customer->setPublished(true);
                $customer->setParent($this->get('coreshop.object_service')->createFolderByPath(sprintf('/%s/%s', $this->getParameter('coreshop.folder.customer'), substr($customer->getLastname(), 0, 1))));
                $customer->setKey(File::getValidFilename($customer->getEmail()));
                $customer->setIsGuest(false);
                $customer->save();

                $address->setPublished(true);
                $address->setKey(uniqid());
                $address->setParent($this->get('coreshop.object_service')->createFolderByPath(sprintf('/%s/%s', $customer->getFullPath(), $this->getParameter('coreshop.folder.address'))));
                $address->save();

                $customer->addAddress($address);
                $customer->save();

                $token = new UsernamePasswordToken($customer, null, 'coreshop_frontend', $customer->getCustomerGroups());
                $this->get('security.token_storage')->setToken($token);

                return $this->redirectToRoute('coreshop_customer_profile');
            }
        }

        return $this->render('CoreShopFrontendBundle:Register:register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @return bool|CustomerInterface|null
     */
    protected function getCustomer()
    {
        try {
            return $this->get('coreshop.context.customer')->getCustomer();
        } catch (\Exception $ex) {

        }

        return null;
    }
}
