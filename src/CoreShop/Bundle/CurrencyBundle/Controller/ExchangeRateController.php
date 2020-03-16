<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CurrencyBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\EventDispatcher;
use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Bundle\ResourceBundle\Controller\ResourceFormFactory;
use CoreShop\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use CoreShop\Bundle\ResourceBundle\Form\Helper\ErrorSerializer;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExchangeRateController extends ResourceController
{
    public function exchangeRateSaveAction(
        Request $request,
        ResourceFormFactory $resourceFormFactory,
        EventDispatcher $eventDispatcher,
        ErrorSerializer $formErrorSerializer,
        ViewHandlerInterface $viewHandler
    ): Response
    {
        $resource = $this->repository->find($request->get('id'));

        if (!$resource instanceof ResourceInterface) {
            $resource = $this->factory->createNew();
        }

        $form = $resourceFormFactory->create($this->metadata, $resource);
        $handledForm = $form->handleRequest($request);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $handledForm->isValid()) {
            $resource = $form->getData();

            $eventDispatcher->dispatchPreEvent('save', $this->metadata, $resource, $request);

            if (!$resource->getId()) {
                $this->manager->persist($resource);
            }

            $this->manager->flush();

            $eventDispatcher->dispatchPostEvent('save', $this->metadata, $resource, $request);

            return $viewHandler->handle(['data' => $resource, 'success' => true], ['group' => 'Detailed']);
        }

        $errors = $formErrorSerializer->serializeErrorFromHandledForm($handledForm);

        return $viewHandler->handle(['success' => false, 'message' => implode(PHP_EOL, $errors)]);
    }
}
