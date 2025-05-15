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

namespace CoreShop\Bundle\ShippingBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CarrierController extends ResourceController
{
    public function getConfigAction(): JsonResponse
    {
        /**
         * @var array $strategies
         */
        $strategies = $this->getParameter('coreshop.shipping.tax_calculation_strategies');

        $convertedStrategies = [];
        foreach ($strategies as $strategy) {
            $convertedStrategies[] = [
                'value' => $strategy,
                // key length has a maximum
                'label' => 'coreshop_shipping_tax_strategy_' . $strategy,
            ];
        }

        return $this->viewHandler->handle(
            [
                'success' => true,
                'taxCalculationStrategies' => $convertedStrategies,
            ],
        );
    }
}
