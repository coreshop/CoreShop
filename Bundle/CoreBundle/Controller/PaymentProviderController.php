<?php

namespace CoreShop\Bundle\CoreBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;

class PaymentProviderController extends ResourceController
{
    /**
     * Get Gateway Factories.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getConfigAction()
    {
        $factoryResults = [];

        foreach (array_keys($this->getParameter('coreshop.gateway_factories')) as $factory) {
            $factoryResults[] = [
                'type' => $factory,
                'name' => $factory,
            ];
        }

        return $this->viewHandler->handle(
            [
                'success' => true,
                'factories' => $factoryResults,
            ]
        );
    }
}