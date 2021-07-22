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

namespace CoreShop\Bundle\CurrencyBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ExchangeRateController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        $resource = $this->repository->find($request->get('id'));

        if (!$resource instanceof ResourceInterface) {
            $resource = $this->factory->createNew();
        }

        $form = $this->resourceFormFactory->create($this->metadata, $resource);
        $handledForm = $form->handleRequest($request);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $handledForm->isValid()) {
            $resource = $form->getData();

            $this->eventDispatcher->dispatchPreEvent('save', $this->metadata, $resource, $request);

            if (!$resource->getId()) {
                $this->manager->persist($resource);
            }

            $this->manager->flush();

            $this->eventDispatcher->dispatchPostEvent('save', $this->metadata, $resource, $request);

            return $this->viewHandler->handle(['data' => $resource, 'success' => true], ['group' => 'Detailed']);
        }

        $errors = $this->formErrorSerializer->serializeErrorFromHandledForm($handledForm);

        return $this->viewHandler->handle(['success' => false, 'message' => implode(PHP_EOL, $errors)]);
    }
}
