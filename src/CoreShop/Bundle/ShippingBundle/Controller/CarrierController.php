<?php
declare(strict_types=1);

namespace CoreShop\Bundle\ShippingBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;

class CarrierController extends ResourceController
{
    public function getConfigAction()
    {
        $taxStrategies = $this->getParameter('coreshop.shipping.tax.strategies');

        $convertTaxStrategies = [];
        foreach ($taxStrategies as $taxStrategy) {
            $convertTaxStrategies[] = [
                'value' => $taxStrategy,
                // key length has a maximum
                'label' => 'coreshop_shipping_tax_strtgy_' . $taxStrategy
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
