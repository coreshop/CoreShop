<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\Event\AdminCustomerCreationEvent;
use CoreShop\Bundle\OrderBundle\Events;
use CoreShop\Bundle\OrderBundle\Form\Type\AdminCustomerCreationType;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Component\Address\Model\AddressesAwareInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Address\Model\DefaultAddressAwareInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Resource\Service\FolderCreationServiceInterface;
use Pimcore\File;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerCreationController extends PimcoreController
{
    public function createCustomerAction(
        Request $request,
        FolderCreationServiceInterface $folderCreationService,
        ErrorSerializer $errorSerializer
    ): Response
    {
        $form = $this->get('form.factory')->createNamed('', AdminCustomerCreationType::class);

        if ($request->getMethod() === 'POST') {
            $form = $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                /**
                 * @var CustomerInterface $customer
                 */
                $customer = $data['customer'];

                /**
                 * @var AddressInterface $address
                 */
                $address = $data['address'];

                $customer->setParent(
                    $folderCreationService->createFolderForResource(
                        $customer,
                        ['suffix' => mb_strtoupper(mb_substr($customer->getLastname(), 0, 1))]
                    )
                );

                $customer->setPublished(true);
                $customer->setKey(File::getValidFilename($customer->getEmail()));
                $customer->setKey(Service::getUniqueKey($customer));
                $customer->save();

                $address->setPublished(true);
                $address->setKey(uniqid());
                $address->setParent(
                    $folderCreationService->createFolderForResource(
                        $address,
                        ['prefix' => $customer->getFullPath()]
                    )
                );
                $address->save();

                if ($customer instanceof DefaultAddressAwareInterface) {
                    $customer->setDefaultAddress($address);
                }

                if ($customer instanceof AddressesAwareInterface) {
                    $customer->addAddress($address);
                }

                $this->get('event_dispatcher')->dispatch(
                    new AdminCustomerCreationEvent($customer, $data),
                    Events::ADMIN_CUSTOMER_CREATION,
                );

                $customer->save();

                return $this->viewHandler->handle(['success' => true, 'id' => $customer->getId()]);
            }

            return $this->viewHandler->handle(
                [
                    'success' => false,
                    'message' => $errorSerializer->serializeErrorFromHandledForm($form),
                ]
            );
        }

        return $this->viewHandler->handle(['success' => false, 'message' => 'Method not supported, use POST']);
    }
}
