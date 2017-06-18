<?php

namespace CoreShop\Bundle\CurrencyBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use CoreShop\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormError;
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
                $this->entityManager->persist($resource);
            }
            
            $this->entityManager->flush();

            $this->eventDispatcher->dispatchPostEvent('save', $this->metadata, $resource, $request);

            return $this->viewHandler->handle(['data' => $resource, 'success' => true], ['group' => 'Detailed']);
        }

        $errors = [];

        /**
         * @var $e FormError
         */
        foreach ($handledForm->getErrors(true, true) as $e) {
            $errorMessageTemplate = $e->getMessageTemplate();
            foreach ($e->getMessageParameters() as $key => $value) {
                $errorMessageTemplate = str_replace($key, $value, $errorMessageTemplate);
            }

            $errors[] = sprintf('%s: %s', $e->getOrigin()->getConfig()->getName(), $errorMessageTemplate);
        }

        return $this->viewHandler->handle(['success' => false, 'message' => implode(PHP_EOL, $errors)]);
    }
}