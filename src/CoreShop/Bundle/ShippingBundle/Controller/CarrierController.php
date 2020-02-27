<?php
declare(strict_types=1);

namespace CoreShop\Bundle\ShippingBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;

class CarrierController extends ResourceController
{
    public function getConfigAction()
    {
        $strategies = $this->getParameter('coreshop.shipping.tax_calculation_strategies');

        $convertedStrategies = [];
        foreach ($strategies as $strategy) {
            $convertedStrategies[] = [
                'value' => $strategy,
                // key length has a maximum
                'label' => 'coreshop_shipping_tax_strtgy_' . $strategy
            ];
        }

        return $this->viewHandler->handle(
            [
                'success' => true,
                'taxCalculationStrategies' => $convertedStrategies
            ]
        );
    }
}
