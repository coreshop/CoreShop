<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\OrderBundle\Event\AdminAddressCreationEvent;
use CoreShop\Bundle\OrderBundle\Events;
use CoreShop\Bundle\OrderBundle\Form\Type\AdminAddressCreationType;
use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Component\Address\Model\AddressesAwareInterface;
use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Customer\Model\CustomerInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class AddressCreationController extends PimcoreController
{
    public function createAddressAction(
        Request $request,
        ObjectServiceInterface $objectService,
        ErrorSerializer $errorSerializer,
    ): Response {
        $form = $this->container->get('form.factory')->createNamed('', AdminAddressCreationType::class);

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

                $address->setPublished(true);
                $address->setKey(uniqid());
                $address->setParent($objectService->createFolderByPath(sprintf(
                    '/%s/%s',
                    $customer->getFullPath(),
                    (string) $this->getParameter('coreshop.folder.address'),
                )));
                $address->save();

                if ($customer instanceof AddressesAwareInterface) {
                    $customer->addAddress($address);
                }

                $this->container->get('event_dispatcher')->dispatch(
                    new AdminAddressCreationEvent($address, $customer, $data),
                    Events::ADMIN_ADDRESS_CREATION,
                );

                $customer->save();

                return $this->viewHandler->handle(['success' => true, 'id' => $address->getId()]);
            }

            return $this->viewHandler->handle(
                [
                    'success' => false,
                    'message' => $errorSerializer->serializeErrorFromHandledForm($form),
                ],
            );
        }

        return $this->viewHandler->handle(['success' => false, 'message' => 'Method not supported, use POST']);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            new SubscribedService('event_dispatcher', EventDispatcherInterface::class),
        ]);
    }
}
