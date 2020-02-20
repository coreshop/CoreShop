<?php
declare(strict_types=1);

namespace CoreShop\Bundle\ShippingBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

class CarrierController extends ResourceController
{
    public function getConfigAction()
    {
        $taxStrategies = $this->getParameter('coreshop.shipping.tax.strategies');

        $convertTaxStrategies = [];
        foreach ($taxStrategies as $taxStrategy) {
            $convertTaxStrategies[] = [
                'name' => $taxStrategy,
            ];
        }

        return $this->viewHandler->handle(
            [
                'success' => true,
                'taxStrategies' => $convertTaxStrategies
            ]
        );
    }
}
